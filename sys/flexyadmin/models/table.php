<?
/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @copyright Copyright (c) 2008, Jan den Besten
 * @link http://flexyadmin.com
 * @version V1 0.1
 * @filesource  */

// ------------------------------------------------------------------------

class Table extends CI_Model {

	var $table;
	var $pk;
	var $owner; // restricted to users..
	var $id;
	var $resultError;

	function __construct() {
		parent::__construct();
		$this->init();
	}

	function init($table="") {
		log_("info","[TABLE] init table '$table'");
		$this->table=$table;
		$this->pk = ID;
		$this->set_owner();
		$this->resultError=0;
	}
	
	function set_owner($user=NULL) {
		$this->owner=$user;
	}
	
	/*========== Common ===========*/
	
	function insert_id() {
		return $this->id;
	}

	function insert_row($row) {

		$this->id=$this->db->insert_id()
		return $result;
	}
	
	function update_row($id,$row) {
		$this->id=$id;
		
		return $result;
	}
	
	function update_field($id,$field,$value) {
		$this->id=$id;
		
		return $result;
	}
	
	/*=========== Uri =============*/
	
	/*=========== Re-Order ========*/
	

}
	
?>