<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class Plugin_fill_many_with_foreign extends Plugin_ {

	// You can declare some properties here if needed

	function init($init=array()) {
		parent::init($init);
		// If you need methods like _after_update(), _after_delete(), set in the next line of which tables,fields,types this method must act.
		// $this->act_on();
	}
	
	//
	// Here you find short templates of possible methods
	//

	//
	// _admin_logout is a call that's made when user is logging out
	//
	// function _admin_logout() {
	//	 return true;
	// }
	
	
	
	// _admin_api is a call in admin:
	// admin/plugin/#plugin_name# 
	
	function _admin_api($args=false) {
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



	// These methods can be used to do some actions 

	// function _after_update() {
	// 	return $this->newData;
	// }

	// function _after_delete() {
	// 	return FALSE;
	// }
	
}

?>