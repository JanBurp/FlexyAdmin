<?php

/** \ingroup models
 * Voor alle cfg_ tabellen
 *
 * @author Jan den Besten
 */

 class Cfg extends CI_Model {

 	/**
 	 * $data array (
 	 * 		$table 	=> array ()
 	 * 							)
 	 */
 	private $data=array();
	private $keys=array();
	private $isAdmin;


 	public function __construct() {
 		parent::__construct();
    $this->reset();
		$this->isAdmin=FALSE;
 	}
  
  public function reset() {
 		$this->hasData = false;
 		$this->data = array();
		$this->keys = array(
			'cfg_table_info' => array( 'key' => 'table', 'fields' => '`id`,`order`,`table`,`str_order_by`,`int_max_rows`' ),
			'cfg_field_info' => array( 'key' => 'field_field' ),
			'cfg_media_info' => array( 'key' => array('path','fields_media_fields') ),
			'cfg_img_info'   => array( 'key' => 'path' ),
		);
  }


	public function set_if_admin($isAdmin) {
		$this->isAdmin=$isAdmin;
	}

  /**
   * Gives table name with cfg_ prefix
   *
   * @param string $table Name of config table (with or without prefix)
   * @return string Name of table with prefix(es)
   */
	private function _name($table) {
		$pre=get_prefix($table);
		$cfg_pre=$this->config->item('CFG_table_prefix');
		if ($pre!=$cfg_pre)
			$table=$cfg_pre."_".$this->config->item($table);
		return strtolower($table);
	}

/**
 * Loads data from table into data array
 *
 * @param string $table Name of config table
 * @param string $key  default='' fieldname wich will be the key to find data
 * @param string $fields default=''
 * @return bool true on succes
 */
	public function load($table,$key='',$fields='') {
		// set default keys/fields if not given
		if (empty($key) and isset($this->keys[$table]['key'])) $key=$this->keys[$table]['key'];
		if (empty($fields)) {
			$fields='*';
		}
		if (!empty($key) and !is_array($key)) $key=array($key);
		
		$out=false;
		$table=$this->_name($table);
    
		log_("info","[Cfg] Loading config table '$table'");
		if ($this->db->table_exists($table)) {
			$sql="SELECT $fields FROM `$table`";
			$query=$this->db->query($sql);
			$data=array();
			foreach ($query->result_array() as $row) {
				if (empty($key)) {
					if (isset($row['id']))
						$data[$row['id']]=$row;
					else
						$data[]=$row;
				}
				else {
					foreach($key as $k) {
						if (!empty($row[$k])) {
							$subkeys=$row[$k];
							$subkeys=explode("|",$subkeys);
							foreach($subkeys as $sk) {
								$data[$sk]=$row;
								//$data[$row[$k]]=$row;
							}
						}
					}
				}
			}
      
			$query->free_result();
			$this->data[$table]=$data;
			$out=true;
		}
		else {
			log_("info","[Cfg] Config table '$table' doesn't exists");
			$this->data[$table]=array();
		}
		// add CFG tables
		$cfg=$this->config->item("CFG_");
		if (isset($cfg[$table])) {
			$this->data[$table]=array_merge($this->data[$table],$cfg[$table]);
		}

		return $out;
	}
  
  /**
   * Stel een specifiek item in
   *
   * @param string $table 
   * @param string $key 
   * @param string $field 
   * @param string $set 
   * @return void
   * @author Jan den Besten
   */
  public function set_item($table,$key,$field,$set) {
    if (!$this->has_data($table)) $this->load($table);
    $this->data[$table][$key][$field]=$set;
    return $this;
  }
  

  /**
   * public function has_data($table)
   *
   * Checks if table is loaded or not
   *
   * @param string $table Name of table
   * @return bool true if data is loaded
   */
	public function has_data($table) {
		$table=$this->_name($table);
		$out=(array_key_exists($table,$this->data));
		if ($out)
			log_("info","[Cfg] Check if data exists '$table' = 'Yes'");
		else
			log_("info","[Cfg] Check if data exists '$table' = 'No'");
		return $out;
	}


  /**
   * Gets data from configuration table in database.
   *
   * @param string 	$table 			Configuration table name
   * @param mixed 	$key 			  default='' Row or field of table
   * @param mixed 	$field		  default='' Field of table
   * @param mixed   $default    default=NULL
   * @return array or string		If result is just one value it returns it as a string,
   * 														Otherwise the result is an assoc array with all elements or even an array with multiple rows and their assoc elements
   */
 	public function get($table,$key="",$field="",$default=NULL) {
		$table=$this->_name($table);
 		$out=$default;
 		if (!$this->has_data($table)) {
 			$this->load($table);
 		}
 		if (isset($this->data[$table])) {
 			$data=el($table,$this->data,$default);
 			if (empty($key)) {
 				$out=$data;
 			}
 			else {
        if ($table=='cfg_configurations' and empty($field)) {
          $field=$key;
          $key=1;
        }
        // Combine all data for this key
        $key_data=NULL;
        $key_table=get_prefix($key,'.');
        $key_field=get_suffix($key,'.');
        $key2='';$key2_data=NULL;
        if (!empty($key_field)) $key2='*.'.$key_field;
        if ($key==$key2) $key2='';
        $key_data=el($key,$data);
        if (!empty($key2)) {
          $key2_data=el($key2,$data);
          if (!empty($key2_data)) {
            if (empty($key_data))
              $key_data=$key2_data;
            else
              $key_data=array_merge($key2_data,$key_data);
          }
        }
        
 				if (empty($field)) {
 					$out=$key_data;
           // if (!isset($out)) $out=el($key,current($data));
 				}
 				else {
           // $data=el($key,$data);
					$out=el($field,$key_data,$default);
				}
 			}
 		}
		log_("info","[Cfg] Getting data '$table','$key','$field'");
 		return $out;
 	}



 }
?>
