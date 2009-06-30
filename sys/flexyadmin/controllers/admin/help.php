<?
require_once(APPPATH."controllers/admin/MY_Controller.php");

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2008, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 * @filesource
 */

// ------------------------------------------------------------------------


class Help extends AdminController {

	function Help() {
		parent::AdminController();
	}

	function index() {
		// last login info
		$this->_set_content(h('Help',1));

		// help for tables
		$help=$this->_get_help('CFG_table');
		// help for media
		$help.=$this->_get_help('CFG_media_info');
		
		if (empty($help)) {
			$this->lang->load('help');
			$help=lang('help_no_help');
		}
		$this->_add_content($help);
		
		$this->_show_type("info");
		$this->_show_all();
	}
	
	function _get_help($helpTable,$helpField="txt_help") {
		$info=$this->cfg->get($helpTable);
		$help="";
		foreach ($info as $table => $row) {
			if (get_prefix($table)!="cfg" and strpos($table,'.')===FALSE and !empty($row[$helpField])) {
				$uiTable=$this->uiNames->get($table);
				$help.="<h2>".$uiTable."</h2>";				
				$help.=$row[$helpField];
				// fields
				if ($this->db->table_exists($table)) {
					$fields=$this->db->list_fields($table);
					foreach ($fields as $field) {
						$fieldInfo=$this->cfg->get('CFG_field',"$table.$field");
						if (isset($fieldInfo[$helpField]) and !empty($fieldInfo[$helpField]))
						$help.="<h3>$uiTable - ".$this->uiNames->get($field)."</h3>".$fieldInfo[$helpField];
						
					}
				}
				$help.="<p>&nbsp;</p>";
			}
		}
		return $help;
	}

}

?>
