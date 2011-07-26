<?
/**
 * FlexyAdmin V1
 *
 * cfg.php Created on 15-okt-2008
 *
 * @author Jan den Besten
 */


 /**
  * Class Cfg extends model
  *
  * This class handles configuration settings from tables in database.
  * It loads them on demand and reads them.
  *
  */

 class Cfg extends CI_Model {

 	/**
 	 * $data array (
 	 * 		$table 	=> array ()
 	 * 							)
 	 */
 	var $data=array();

 	/**
 	 * Information for database fields
 	 */
 	var $fieldInfo=array();

 	function __construct() {
 		parent::__construct();
 		$this->hasData=false;
 		$this->data=NULL;
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
		return $table;
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

	function load($table,$key="",$fields='*') {
		if (!empty($key) and !is_array($key)) $key=array($key);
		$out=false;
		$table=$this->_name($table);
		log_("info","[Cfg] Loading config table '$table'");
		if ($this->db->table_exists($table)) {
			$this->db->select($fields);
			$query=$this->db->get($table);
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
 				if (empty($field)) {
 					$out=el($key,$data);
 					if (!isset($out)) $out=el($key,current($data));
 				}
 				else {
 					$data=el($key,$data);
					$out=el($field,$data);
				}
 			}
 		}
		log_("info","[Cfg] Getting data '$table','$key','$field' = '$out'");
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
			/**
			 *TODO $CI weghalen bij object aanroepen (als er geen errors komen)
			 */
			$CI=$this;//&get_instance();
			$platform=$CI->db->platform();
			if ($platform=="mysql") {
				$sql = "SHOW COLUMNS FROM $table";
				$query = $CI->db->query($sql);
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
