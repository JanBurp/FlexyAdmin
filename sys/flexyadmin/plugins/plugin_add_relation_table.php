<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class Plugin_add_relation_table extends Plugin_ {

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
			if (isset($args[0])) $table1=$args[0];
			if (isset($args[1])) $table2=$args[1];
			if (isset($table1) and isset($table2)) $goodArgs=true;
			$relTable='rel_'.remove_prefix($table1).'__'.remove_prefix($table2);
			$this->dbforge->add_field('id');
			$fields=array(	'id_'.remove_prefix($table1)	=>	array('type'=>'INT','unsigned'=>TRUE),
											'id_'.remove_prefix($table2)	=>	array('type'=>'INT','unsigned'=>TRUE));
			$this->dbforge->add_field($fields);
			$this->dbforge->create_table($relTable,TRUE);
			$this->_add_content(h("Created '$relTable' from '$table1' and '$table2'.",2));
		}
		if (!$goodArgs) {
			$this->_add_content('<p>Add relation table, for which table(s)?</br></br>Give: /tbl_xxx/tbl_xxx</p>');
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