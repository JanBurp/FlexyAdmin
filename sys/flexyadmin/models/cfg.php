<?php /**
 * handles configuration settings form cfg tables in the database
 * It loads them on demand and reads them.
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 * @internal
 */

 class Cfg extends CI_Model {

 	/**
 	 * $data array (
 	 * 		$table 	=> array ()
 	 * 							)
 	 */
 	var $data=array();
	var $keys=array();
	var $isAdmin;

 	/**
 	 * Information for database fields
 	 */
 	var $fieldInfo=array();

 	function __construct() {
 		parent::__construct();
    $this->reset();
		$this->isAdmin=FALSE;
 	}
  
  function reset() {
 		$this->hasData=false;
 		$this->data=array();
		$this->keys=array(
			'cfg_'.$this->config->item('CFG_table')					=> array( 'key' => $this->config->item('CFG_table_name'), 'fields' => '`id`,`table`,`str_order_by`' ),
			'cfg_'.$this->config->item('CFG_field') 				=> array( 'key' => $this->config->item('CFG_field_name') ),
			'cfg_'.$this->config->item('CFG_media_info')		=> array( 'key' => array('path','fields_media_fields') ),
			'cfg_'.$this->config->item('CFG_img_info')			=> array( 'key' => 'path' ),
			'cfg_'.$this->config->item('cfg_admin_menu')		=> array( 'key' => 'id' )		
		);
  }


	function set_if_admin($isAdmin) {
		$this->isAdmin=$isAdmin;
	}

/**
 * function _name($table)
 *
 * Gives table name with cfg_ prefix
 *
 * @param string $table Name of config table (with or without prefix)
 * @return string Name of table with prefix(es)
 */

	function _name($table) {
		$pre=get_prefix($table);
		$cfg_pre=$this->config->item('CFG_table_prefix');
		if ($pre!=$cfg_pre)
			$table=$cfg_pre."_".$this->config->item($table);
		return strtolower($table);
	}

/**
 * function load($table,[$key])
 *
 * Loads data from table into data array
 *
 * @param string $table Name of config table
 * @param string $key fieldname wich will be the key to find data
 * @return bool true on succes
 */

	function load($table,$key='',$fields='') {
		// set default keys/fields if not given
		if (empty($key) and isset($this->keys[$table]['key'])) $key=$this->keys[$table]['key'];
		if (empty($fields)) {
			if ( ! $this->isAdmin and isset($this->keys[$table]['fields']))
				$fields=$this->keys[$table]['fields'];
			else
				$fields='*';
		}
		if (!empty($key) and !is_array($key)) $key=array($key);
		// trace_($table);
		// trace_($key);
		// trace_($fields);
		// trace_($this->keys);
		
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
 * function has_data($table)
 *
 * Checks if table is loaded or not
 *
 * @param string $table Name of table
 * @return bool true if data is loaded
 */

	function has_data($table) {
		$table=$this->_name($table);
		$out=(array_key_exists($table,$this->data));
		if ($out)
			log_("info","[Cfg] Check if data exists '$table' = 'Yes'");
		else
			log_("info","[Cfg] Check if data exists '$table' = 'No'");
		return $out;
	}


/**
 * function get(string $table, [$key], [$field])
 *
 * Gets data from configuration table in database.
 *
 * @param string 	$table 			Configuration table name
 * @param mixed 	[$key] 			Row or field of table
 * @param mixed 	[$field]		Field of table
 * @return array or string		If result is just one value it returns it as a string,
 * 														Otherwise the result is an assoc array with all elements or even an array with multiple rows and their assoc elements
 */

 	function get($table,$key="",$field="") {
		$table=$this->_name($table);
 		$out=NULL;
 		if (!$this->has_data($table)) {
 			$this->load($table);
 		}
 		if (isset($this->data[$table])) {
 			$data=el($table,$this->data);
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
        if (!empty($key_table) and !empty($key_field)) $key2='*.'.$key_field;
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
					$out=el($field,$key_data);
				}
 			}
 		}
		log_("info","[Cfg] Getting data '$table','$key','$field'");
 		return $out;
 	}



	/**
	 *
	 * function get_field_data($table)
	 *
	 * Use this method instead of field_data() to make sure MySql gives the right information
	 * see http://codeigniter.com/forums/viewthread/46418/
	 *
	 * @param string $table Tablename for which field data is asked
	 * @return array Array of the information
	 */
	function field_data($table,$key="",$value="") {
		if (!isset($this->fieldInfo[$table])) {
			$platform=$this->db->platform();
			if ($platform=="mysql") {
				$sql = "SHOW COLUMNS FROM `$table`";
				$query = $this->db->query($sql);
				foreach ($query->result() as $field) {
					/** Explanation of the ugly regex:
						*   match until first non '('
						*   then optionally match numbers '\d' inside brackets '\(', '\)
						*/
					preg_match('/([^(]+)(\((\d+)\))?/', $field->Type, $matches);
					$type           = sizeof($matches) > 1 ? $matches[1] : null;
					$max_length     = sizeof($matches) > 3 ? $matches[3] : null;
					$F              = new stdClass();
					$F->name        = $field->Field;
					$F->type        = $type;
					$F->default     = $field->Default;
					$F->max_length  = $max_length;
					$F->primary_key = ($field->Key == "PRI") ? 1 : 0;
//					$F->comment     = $field->Comment;
//					$F->collation   = $field->Collation;
					$F->extra       = $field->Extra;
					$info[] = $F;
				}
				$query->free_result();
			}
			else {
				/**
				 * other platforms just use the normal method
				 */
				$info=$qry->field_data();
			}
			/**
			 *  easier array format
			 */
			foreach ($info as $i) {
				$i=object2array($i);
				$out[$i["name"]]=$i;
			}
			$this->fieldInfo[$table]=$out;
		}
		else
			$out=$this->fieldInfo[$table];

		// return value depends on given params
		if (empty($value)) {
			if (empty($key))
				return $out;
			else
				return el($key,$out);
		}
		else
			return $out[$key][$value];
	}

 }
?>
