<?php

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;


/** \ingroup models
 * API user
 * 
 *    
 * @author Jan den Besten
 */

class Tools extends Api_Model {
  
	public function __construct() {
		parent::__construct();
    $this->load->dbutil();
    $this->load->driver('cache', array('adapter' => 'file'));
    $this->load->helper('download');
  }

  private function _filename() {
    $name=$this->data->table('tbl_site')->get_field("url_url");
    $name=str_replace(array('http://','https://','www.'),'',$name);
    $name=explode(".",$name);
    $name=$name[0];
    return date("Y-m-d").'_'.$name;
  }

  private function _sql($sql,$super_admin=TRUE) {
    // if (!$this->dbutil->is_safe_sql($sql)) return ['errors'=>[0=>['message'=>'Unsafe SQL.']]];
    if ($super_admin and !$this->flexy_auth->is_super_admin()) return ['errors'=>[0=>['message'=>'No Rights.']]];
    $result = $this->dbutil->import($sql);
    unset($result['queries']);
    $this->cache->clean();
    return $result;
  }

  private function _db_restore() {
    // Upload to cache
    $this->load->library('upload');
    $folder = $this->config->item('SITE') . 'cache/';
    $config = array(
      'upload_path'   => $folder,
      'allowed_types' => array('sql','zip','txt'),
    );
    $this->upload->config($config);
    if ($this->upload->upload_file( 'file' )) {
      $file = $this->upload->get_file();
    }
    else {
      $this->error_message = $this->upload->get_error(); 
      $this->result['error'] = $this->error_message;
      return $this->_result_ok();
    }

    $type = get_suffix($file,'.');
    if ($type=='zip') {
      $zip = new ZipArchive;
      $res = $zip->open($folder.$file);
      if ($res === TRUE) {
        $zip->extractTo($folder);
        $zip->close();
      }
      $file = remove_suffix($file,'.');
      $file = str_replace(array('_sql','_txt'),array('.sql','.txt'),$file);
    }

    // Read file
    $data = read_file($folder.$file);
    if (empty($data)) return $this->_result_status401();

    // Encrypt?
    if (get_suffix($file,'.')=='txt') {
      $key = Key::loadFromAsciiSafeString( $this->config->item('encryption_key') );
      $sql = Crypto::decrypt($data, $key);
    }
    else {
      $sql = $data;
    }
    if (empty($sql)) return $this->_result_status401();

    $this->result['data'] = $this->_sql($sql,false);
    return $this->_result_ok();
  }

  public function db_restore() {
    if (!$this->flexy_auth->can_backup()) return $this->_result_status401();
    return $this->_db_restore();
  }
  
  public function db_import() {
    if (!$this->flexy_auth->can_use_tools()) return $this->_result_status401();

    $sql = $this->args['sql'];
    unset($this->args['sql']);
    if (empty($sql)) return $this->_result_status401();

    $this->result['data'] = $this->_sql($sql,false);
    return $this->_result_ok();
  }

  public function db_export_form() {
    if (!$this->flexy_auth->can_use_tools()) return false;

    $tables = $this->data->list_tables();
    $tables_as_options = array();
    foreach ($tables as $table) {
      $tables_as_options[] = array(
        'value' => $table,
        'name'  => $table,
      );
    }

    $this->result['data'] = array(
      'filename' => $this->_filename(),
      'tables'   => $tables_as_options,
    );
    return $this->_result_ok();
  }




  public function fill() {
    if (!$this->has_args()) return $this->_result_wrong_args(); 
    if (!$this->flexy_auth->can_use_tools()) return $this->_result_status401();

    $aantal          = el('aantal',$this->args,0);
    $table           = el('table',$this->args);
    $fields          = el('fields',$this->args);
    $where           = el('where',$this->args);
    $value           = el('value',$this->args,0);
    $random          = $value=='{RANDOM}';
    $test            = el('test',$this->args,0);

    $result = array();
    if ($table) {

      if (!is_array($fields)) $fields=explode(',',$fields);

      // create rows in table
      if ( $aantal>0 ) {
        $this->data->table($table);
        $abstract_fields = $this->data->get_abstract_fields();
        $abstract_field  = current($abstract_fields);
        for ($i=0; $i < $aantal; $i++) { 
          $id='#';
          if (!$test) $id = $this->data->table($table)->set($abstract_field,random_string())->insert();
        }
      }

      // Relaties?
      // $relations = $this->data->table( $table )->get_setting(array('relations','many_to_many'));
      // if ($relations) {
      //   foreach ($relations as $relation) {
      //     array_push($fields,$table.'.rel_'.$relation['result_name']);
      //   }
      // }

      $this->data->table($table)->select( 'id' );
      if (!empty($where)) $this->data->where($where);
      $items = $this->data->get_result();
      foreach ($items as $id => $item) {
        $result[$id]       = array();
        $result[$id]['id'] = $id;

        foreach($fields as $field) {

          $item_value = $value;
          if ($random) $item_value = $this->data->table($table)->random_field_value( $field, $id );


          if (!$test) {
            $this->data->table($table)->where('id',$id);
            if (!empty($where)) $this->data->where($where);
            $this->data->set($field,$item_value);
            $this->data->update();
          }

          $result[$id][$field] = $item_value;
        }
      }

    }

    $this->result['data'] = array(
      'result'          => $result,
    );    
    return $this->_result_ok();
  }

  
  public function search() {
    if (!$this->has_args()) return $this->_result_wrong_args(); 
    if (!$this->flexy_auth->can_use_tools()) return $this->_result_status401();

    $search   = $this->args['search'];
    $replace  = el('replace',$this->args,'');
    $fields   = el('fields',$this->args,array());
    if (!is_array($fields)) $fields = explode(',',$fields);
    $regex    = el('regex',$this->args,false);
    if (!$regex) $search = preg_quote($search,'/');
    $test     = el('test',$this->args,true);

    if ($search) {

      // In welke velden?
      $tables = $this->data->list_tables();
      if (!$this->flexy_auth->is_super_admin()) $tables = not_filter_by($tables,array('cfg','log','res','rel'));

      // Alle velden
      if (empty($fields)) {
        foreach ($tables as $table) {
          $table_fields = $this->data->table($table)->list_fields();
          foreach ($table_fields as $field) {
            $fields[] = $table.'.'.$field;
          }
        }
      }
      else {
        // Geselecteerde velden (voeg eventueel tabellen toe)
        foreach ($fields as $key => $field) {
          if (strpos($field,'.')===false) {
            unset($fields[$key]);
            foreach ($tables as $table) {
              if ($this->db->field_exists($field,$table)) $fields[] = $table.'.'.trim($field);   
            }
          }
          else {
            $split = explode('.',trim($field));
            if ( !$this->db->field_exists(trim($split[1]),trim($split[0]))) unset($fields[$key]);
          }
        }
      }

      // Zoeken
      $result = array();
      $found_fields = array();
      foreach ($fields as $field) {
        $table = trim(get_prefix($field,'.'));
        $field = trim(remove_prefix($field,'.'));
          
        $data = $this->data->table($table)->select($field)->get_result();

        foreach( $data as $id => $row) {
          unset($row[$this->data->settings['primary_key']]);
          foreach ($row as $key => $value) {
            $count=0;
            $matches=FALSE;

            $oldErrorHandler = set_error_handler(array($this,"myErrorHandler"));
            $newValue = preg_replace( "/$search/", $replace, $value, -1, $count);
            if ($count>0) preg_match_all("/$search/", $value,$matches);
            set_error_handler($oldErrorHandler);

            if ($newValue!==$value) {
              $abstract = $this->data->table($table)->where($id)->select_abstract()->get_row();
              $abstract = $abstract['abstract'];
              $found_fields[] = $table.'.'.$key;
              $result[] = array(
                'table'       => $table,
                'field'       => $key,
                'primary_key' => $id,
                'abstract'    => $abstract,
                'value'       => $value,
                'newvalue'    => $newValue,
                'test'        => $test,
              );

              if (!$test) $this->data->set($key,$newValue);  
            }
            if (!$test) $this->data->where($id)->update();
          }
        }
      }
    }

    
    $this->result['data'] = array(
      'searched_fields' => $fields,
      'found_fields'    => $found_fields,
      'result'          => $result,
    );    
    return $this->_result_ok();
  }



  /**
   * Mooiere REGEX foutmelding
   */
  private function myErrorHandler($errno, $errstr, $errfile, $errline)  {
    if ($errno==E_WARNING and has_string('preg',$errstr)) {
      if (!$this->regex_error) {
        $this->content .= p('error').lang('bad_regex').' : '.$errstr._p();
        $this->regex_error=TRUE;
      }
      return true;
    }
    return false;
  }




}


?>
