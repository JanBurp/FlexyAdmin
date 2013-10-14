<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Deze plugin kan vreemde karakters vervangen naar juiste karakters. Dit kan soms voorkomen bij het omzetten van een oude database bijvoorbeeld.
 *
 * @package default
 * @author Jan den Besten
 */

class Plugin_strange_chars extends Plugin {
   
   public function __construct() {
     parent::__construct();
   }

	public function _admin_api($args=NULL) {
    $this->add_message('Replace strange chars in database...');
    
    $testFields = $this->_get_test_fields();
    foreach ($this->config['replace'] as $search => $replace) {
      $this->_replace($search,$replace,$testFields);
    }
    
    return $this->view();
	}

  private function _replace($search,$replace,$testFields) {
    foreach ($testFields as $f) {
      $table=$f['table'];
      $field=$f['field'];
      $this->CI->db->select(PRIMARY_KEY);
      $this->CI->db->select($field);
      $result=$this->CI->db->get_result($table);
      foreach($result as $id=>$row) {
        unset($row[PRIMARY_KEY]);
        foreach ($row as $key=>$txt) {
          $new=str_replace($search,$replace,$txt);
          if ($new!=$txt) {
            $this->add_message($table.'.'.$field.'['.$id.']  '.$search.' => '.$replace);
          }
          $this->CI->db->set($key,$new);  
        }
        $this->CI->db->where(PRIMARY_KEY,$id);
        $res=$this->CI->db->update($table);
      }
    }
  }

  private function _get_test_fields() {
		foreach ($this->config('fields') as $key=>$value) {
			$table=get_prefix($value,'.');
			$field=get_suffix($value,'.');
      if ($table=='*') {
        $tables=$this->CI->db->list_tables();
        $tables=filter_by($tables,'tbl');
        foreach ($tables as $table) {
          if ($this->CI->db->field_exists($field,$table)) $testFields[]=array('table'=>$table,'field'=>$field);
        }
      }
      else {
        $testFields[]=array('table'=>$table,'field'=>$field);
      }
    }
    return $testFields;
  }
  

}

?>