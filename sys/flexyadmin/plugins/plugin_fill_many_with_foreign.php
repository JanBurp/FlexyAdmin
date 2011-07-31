<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Plugin_fill_many_with_foreign extends Plugin_ {

	function init($init=array()) {
		parent::init($init);
	}
	
	
	function _admin_api($args=false) {
		if ($this->user->is_super_admin()) {
			$this->_add_content(h($this->plugin,1));
			$goodArgs=false;
			if ($args) {
				if (isset($args[0])) $relTable=$args[0];
				if (isset($args[1])) {
					$foreignKey=$args[1];
					$table=get_prefix($foreignKey,'.');
					$foreignKey=get_postfix($foreignKey,'.');
					$thisKey='id_'.get_postfix($table);
				}
				if (isset($relTable) and isset($table) and isset($foreignKey)) $goodArgs=true;
				$this->_add_content(h("Filling '$relTable' from '$table.$foreignKey'.",2));
				// first emtpy many table
				$this->db->truncate($relTable);
				// now fill
				$this->db->select('id,'.$foreignKey);
				$data=$this->db->get_result($table);
				foreach ($data as $id => $row) {
					$this->db->set($thisKey,$id);
					$this->db->set($foreignKey,$row[$foreignKey]);
					$this->db->insert($relTable);
				}
			}
			if (!$goodArgs) {
				$this->_add_content('<p>Which many table and foreign key?</br></br>Give: /rel_xxxx__xxxx/tbl_xxx.id_xxx</p>');
			}
		}
	}


}

?>