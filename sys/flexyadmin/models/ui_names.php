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

class ui_names extends CI_Model {

	var $uiNames = array();

	function __construct() 	{
		parent::__construct();
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
		$tableInfo=$this->cfg->get('CFG_table');
		foreach ($tableInfo as $table => $value) {
			if (!empty($value['str_ui_name'])) $out[$table]=$value['str_ui_name'];
		}
		// media info
		$tableInfo=$this->cfg->get('CFG_media_info');
		foreach ($tableInfo as $table => $value) {
			if (!empty($value['str_ui_name'])) $out[$table]=$value['str_ui_name'];
		}
		// field info
		$fieldInfo=$this->cfg->get('CFG_field');
		foreach ($fieldInfo as $field => $value) {
			if (!empty($value['str_ui_name'])) $out[$field]=$value['str_ui_name'];
		}		
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
	
	function replace_ui_names($s) {
		foreach($this->uiNames as $key=>$name) {
			$s=str_replace($key,$this->get($key),$s);
		}
		return $s;
	}
	
}

?>
