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

/**
 * Ajax Controller Class
 *
 * This Controller handles all AJAX requests
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Ajax extends BasicController {

	function Ajax() {
		parent::BasicController();
	}

	function index() {
		$this->_result();
	}

	function _result($result="",$p="") {
		if (!empty($result)) {
			$this->lang->load("ajax");
			$result=langp($result,$p);
		}
		echo $result;
	}

/**
 * order()
 *
 * Handles AJAX order requests
 * (GET = array of id[] within right order the new order of the table)
 */

	function order($table="") {
		$result='';
		if (!empty($table) and $this->db->table_exists($table)) {
			if ($this->_has_key($table) and $this->has_rights($table)>=RIGHTS_EDIT) {
				$ids=$this->input->post("id");
				if ($ids) {
					$this->load->model("order");
					$this->order->set_all($table,$ids);
					$result='';
				}
				else {
					$result='ajax_error_wrong_parameters';
				}
			}
			else {
				$result='ajax_error_no_rights';
			}
		}
		else {
			$result='ajax_error_wrong_parameters';
		}
		$this->_result($result);
	}

/**
 * Handles AJAX request to edit a cell
 * Url holds all data
 */
 	function edit($table="",$id="",$field="",$value="") {
		$result='';
		if (!empty($table) and ($id!="") and !empty($field)) {
			if ($this->db->table_exists($table) and $this->db->field_exists($field,$table)) {
 				if ($this->_has_key($table) and $this->has_rights($table,$id)>=RIGHTS_EDIT) {
		 			$this->db->set($field,$value);
		 			$this->db->where(pk(),$id);
		 			$this->db->update($table);
				}
				else $result='ajax_error_no_rights';		 		
	 		}
			else $result='ajax_error_wrong_parameters';
		}
		else $result='ajax_error_wrong_parameters';
		$this->_result($result);
 	}


}

?>
