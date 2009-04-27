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
		$this->_result("AJAX");
	}

	function _result($result) {
		echo $result;
	}

/**
 * order()
 *
 * Handles AJAX order requests
 * (GET = array of id[] within right order the new order of the table)
 */

	function order($table="") {
		if (empty($table)) {
			$this->_result("AJAX|order: No Table given");
		}
		else {
			$ids=$this->input->post("id");
			$this->load->model("order");
			$this->order->set_all($table,$ids);
		}
	}

/**
 * Handles AJAX request to edit a cell
 * Url holds all data
 */
 	function edit($table,$id,$field,$value) {
 		if ($this->has_rights($table,$id)) {
 			$this->db->set($field,$value);
 			$this->db->where(pk(),$id);
 			$this->db->update($table);
 		}
 		else
 			$this->_result("AJAX|cell| ERROR or No Rights: $table,$id,$field=$value");
 	}


}

?>
