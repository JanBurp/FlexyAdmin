<?
/**
 * FlexyAdmin V1
 *
 * cfg.php Created on 15-okt-2008
 *
 * @author Jan den Besten
 */


 /**
  * Class ui_names extends model
  *
  * This class handles all the UI-names for tables and fields
  *
  */

class ui_names extends Model {

	var $uiNames = array();

	function ui_names() 	{
		parent::Model();
		$this->load();
	}

	/**
	 * function load()
	 *
	 * Loads all ui names from cfg tables into $uiNames array
	 *
	 */
	function load() {
		$out=array();
		log_('info',"ui_names: loading");
		// table info
		$table=table_name($this->config->item('CFG_table_prefix')."_table_info");
		$tableName=$this->config->item('CFG_table_name');
		$tableUiName=$this->config->item('CFG_table_ui_name');
		if ($this->db->table_exists($table) and $this->db->field_exists($tableName,$table) and $this->db->field_exists($tableUiName,$table)) {
			$query=$this->db->select("$tableName,$tableUiName");
			$query=$this->db->get($table);
			foreach($query->result_array($query) as $row) {
				$out[$row[$tableName]]=$row[$tableUiName];
			}
			log_('info',"ui_names: table ui's loaded");
		}
		else
			log_('info',"ui_names: table ui didn't exists, or fieldnames not right.");
		// field info
		$table=table_name($this->config->item('CFG_table_prefix')."_field_info");
		$fieldName=$this->config->item('CFG_field_name');
		$fieldUiName=$this->config->item('CFG_field_ui_name');
		if ($this->db->table_exists($table) and $this->db->field_exists($fieldName,$table) and $this->db->field_exists($fieldUiName,$table)) {
			$query=$this->db->select("$fieldName,$fieldUiName");
			$query=$this->db->get($table);
			foreach($query->result_array($query) as $row) {
				$out[$row[$fieldName]]=$row[$fieldUiName];
			}
			log_('info',"ui_names: field ui's loaded");
		}
		else
			log_('info',"ui_names: field ui didn't exists, or fieldnames not right.");
		$this->uiNames=$out;
		return $out;
	}

	function get($name,$table="") {
		if (!is_array($name)) {
			$out=el($name,$this->uiNames,"");
			if (empty($out) and !empty($table)) $out=el($table.".".$name,$this->uiNames,"");
			if (empty($out)) $out=$this->create($name);
		}
		else {
			$out=array();
			foreach($name as $n=>$v) {
				$out[$n]=$this->get($v,$table);
			}
		}
		return $out;
	}

	function create($s) {
		if (is_foreign_key($s)) {
			return $this->get(foreign_table_from_key($s));
		}
		$s=remove_prefix($s);
		$s=str_replace("__","-",$s);
		$s=str_replace("_"," ",$s);
		$s=ucwords($s);
		return $s;
	}
}

?>
