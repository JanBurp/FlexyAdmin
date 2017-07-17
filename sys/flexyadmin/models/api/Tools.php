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
  }
  
  public function search() {
    if (!$this->has_args()) return $this->_result_wrong_args(); 
    if (!$this->flexy_auth->can_use_tools()) return $this->_result_status401();

    $search   = $this->args['search'];
    $replace  = el('replace',$this->args,'');
    $fields   = explode(',',trim(el('fields',$this->args,array())));
    $regex    = el('regex',$this->args,false);
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
              if ($this->db->field_exists($field,$table)) $fields[] = $table.'.'.$field;   
            }
          }
          else {
            $split = explode($field);
            if ( !$this->db->field_exists($split[1],$split[0])) unset($fields[$key]);
          }
        }
      }

      // Zoeken
      $result = array();
      $found_fields = array();
      foreach ($fields as $field) {
        $table = get_prefix($field,'.');
        $field = remove_prefix($field,'.');
          
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

              // if (!$test) $this->data->set($key,$newValue);  
            }
            // if (!$test) {
            //   $this->data->where($id);
            //   $res = $this->data->update();
            // }
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
