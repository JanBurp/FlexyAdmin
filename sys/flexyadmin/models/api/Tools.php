<?php

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
  }

  public function db_backup() {
    if (!$this->flexy_auth->can_backup()) return $this->_result_status401();

    $tablesWithRights=$this->flexy_auth->get_table_rights();
    // select only data (not config)
    $tablesWithRights=array_combine($tablesWithRights,$tablesWithRights);
    $tablesWithRights=not_filter_by($tablesWithRights,"cfg");
    $tablesWithRights=not_filter_by($tablesWithRights,"log");
    unset($tablesWithRights["rel_users__rights"]);

    // create backup
    $prefs = array('tables'=> $tablesWithRights,'format'=>'sql');
    $sql = $this->dbutil->backup($prefs);
    $sql = $this->dbutil->clean_sql($sql);
    $sql = "# FlexyAdmin backup\n# User: '".$this->flexy_auth->get_user(null,'str_username')."'  \n# Date: ".date("d F Y")."\n\n".$sql;
    $filename=$this->_filename().'_backup'.'.sql';
    
    $this->result['data'] = array(
      'filename' => $filename,
      'sql'      => $sql,
    );    
    return $this->_result_ok();
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
    return $result;
  }

  public function db_restore() {
    if (!$this->flexy_auth->can_backup()) return $this->_result_status401();

    $sql = $this->args['sql'];
    unset($this->args['sql']);
    if (empty($sql)) return $this->_result_status401();

    $this->result['data'] = $this->_sql($sql,false);
    return $this->_result_ok();
  }

  public function db_export_form() {
    if (!$this->flexy_auth->is_super_admin()) return false;

    $tables = $this->data->list_tables();
    $tables_as_options = array();
    foreach ($tables as $table) {
      $tables_as_options[] = array(
        'value' => $table,
        'title' => $table,
      );
    }

    $this->result['data'] = array(
      'filename' => $this->_filename(),
      'tables'   => $tables_as_options,
    );
    return $this->_result_ok();
  }

  public function db_export() {
    if (!$this->flexy_auth->is_super_admin()) return false;

    $sql = '';
    $backup_prefs = array('format' => 'sql');
    switch ($this->args['export_type']) {
      case 'complete':
        $sql = $this->dbutil->backup($backup_prefs);
        break;
      case 'all':
        $tables = $this->data->list_tables();
        $tablesWithData       = not_filter_by($tables,array('log','cfg_sessions'));
        $backup_prefs = array('tables'=> $tablesWithData, 'format'=>'sql');
        $sql = $this->dbutil->backup($backup_prefs);
        $tablesWithStructure  = array_diff($tables,$tablesWithData);
        $backup_prefs = array( 'tables'=> $tablesWithStructure, 'format'=>'sql' ,'add_insert'  => FALSE);
        $sql .= $this->dbutil->backup($backup_prefs);
        break;
      case 'data':
        $tables = $this->data->list_tables();
        $tables = not_filter_by($tables,array('log','cfg'));
        $backup_prefs = array('tables'=> $tables, 'format'=>'sql');
        $sql = $this->dbutil->backup($backup_prefs);
        break;
      case 'select':
        $tables = $this->args['tables'];
        $backup_prefs = array('tables'=> $tables, 'format'=>'sql');
        $sql = $this->dbutil->backup($backup_prefs);
        break;
    }
    $sql = "# FlexyAdmin backup\n# User: '".$this->flexy_auth->get_user(null,'str_username')."'  \n# Date: ".date("d F Y")."\n\n" . $sql;

    $this->result['data'] = array(
      'filename' => $this->_filename().'_'.$this->args['export_type'].'.sql',
      'sql'      => $sql,
    );
    return $this->_result_ok();     
  }

  public function db_import() {
    if (!$this->flexy_auth->is_super_admin()) return $this->_result_status401();

    $sql = $this->args['sql'];
    unset($this->args['sql']);
    if (empty($sql)) return $this->_result_status401();

    $this->result['data'] = $this->_sql($sql,false);
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
