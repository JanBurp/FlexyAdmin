<?
require_once(APPPATH."controllers/admin/MY_Controller.php");

/**
 * Special Controller Class
 *
 * This Controller shows a grid or form
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Special extends AdminController {

	function Show() {
		parent::AdminController();
	}

	function index() {

		/**
		 * Replace sometext in txt fields
		 */
		// $this->_add_content(h("Special: Replace in all txt_fields",1));
		// $find="gastenboekhttp://";
		// $replace="http://";
		// $this->_add_content("<p>Find:'$find'<br/>Replace: '$replace'</p>");
		// $tables=$this->db->list_tables();
		// foreach($tables as $table) {
		// 	if (get_prefix($table)==$this->config->item('TABLE_prefix')) {
		// 		$fields=$this->db->list_fields($table);
		// 		foreach ($fields as $field) {
		// 			if (get_prefix($field)=="txt") {
		// 				$this->db->select("id,$field");
		// 				$this->db->where("$field !=","");
		// 				$query=$this->db->get($table);
		// 				foreach($query->result_array() as $row) {
		// 					$thisId=$row["id"];
		// 					$txt=$row[$field];
		// 					$txt=str_replace($find,$replace,$txt);
		// 					$res=$this->db->update($table,array($field=>$txt),"id = $thisId");
		// 				}
		// 			}
		// 		}
		// 	}
		// }		
		
		$this->_show_all();
	}

}

?>
