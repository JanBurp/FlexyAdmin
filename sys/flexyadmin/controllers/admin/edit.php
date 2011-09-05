<?
require_once(APPPATH."core/MY_Controller.php");

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
 * Edit Controller Class
 *
 * This Controller updates (other than form updates) / deletes
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Edit extends AdminController {

	function __construct() {
		parent::__construct();
		// $this->load->model("flexy_data","fd");
	}

	function index() {
		$this->_show_all();
	}

	/**
	 * Database edit controller
	 */

	function confirm($table,$info='') {
		$confirmed=$this->input->post('confirm');
		$ids=$this->input->post('items');
		$ids=explode(':',$ids);
		if (!empty($table) and (!empty($ids)) and !empty($confirmed) and $this->db->table_exists($table)	) {
			if ($confirmed=="confirmed") {
				$this->session->set_userdata("confirmed",true);
				$this->delete($table,$ids,$info);
			}
			else {
				$this->set_message("Not confirmed... ".anchor(api_uri('API_confirm',$table),"confirm"));
				redirect(api_uri('API_view_grid',$table));
			}
		}
		$this->_show_all();
	}

	function _has_media($table) {
		$path=$this->cfg->get('CFG_media_info',$table,"path");
		return ($path);
	}

	function delete($table="",$ids="",$info='') {
		$confirmed=$this->session->userdata("confirmed");
		if (!empty($table) and (!empty($ids)) and $this->db->table_exists($table)) {
			if (!is_array($ids)) $ids=array($idss);
			foreach ($ids as $id) {
				if ($this->user->has_rights($table,$id)>=RIGHTS_DELETE) {
					$this->lang->load("update_delete");
					$message="";
					if ($confirmed) {
						//
						// First get current data
						//
						$this->db->where('id',$id);
						$oldData=$this->db->get_row($table);
						
						/**
						 * If there is media, delete them...
						 */
						$mediaPath=$this->_has_media($table);
						if (!empty($mediaPath)) {
							$this->load->model("file_manager");
							$fileManager=new file_manager($mediaPath);
							$types=$this->config->item('FIELDS_media_fields');

							// ok, get fields
							$this->db->where(PRIMARY_KEY,$id);
							$query=$this->db->get($table);
							$row=$query->row_array();
							foreach ($row as $name=>$value) {
								$pre=get_prefix($name);
								if (in_array($pre,$types) and !empty($value)) {
									// ok found one, delete it
									$result=$fileManager->delete_file($value);
									if ($result) {
										log_("info","[FD] delete file '$value'");
										$message.=langp("delete_value",$value );
									}
									else {
										log_("Error deleting file/dir '$value'.");
										$message.=langp("delete_value_error",$value);
									}
								}
							}
						}

						/**
						 * Check if it is a tree, if so, and has branches, move the branch up
						 */
						if ($this->db->has_field($table,"self_parent")) {
							$this->load->model("order");
							// get info from current
							$this->db->where(PRIMARY_KEY,$id);
							$this->db->select("order,self_parent");
							$row=$this->db->get_row($table);
							$parent=$row["self_parent"];
							$order=$row["order"];
							// get branches
							$this->db->where("self_parent",$id);
							$this->db->select(PRIMARY_KEY);
							$branches=$this->db->get_result($table);
							$count=count($branches);
							// shift order of branches in same branch
							$this->order->shift_up($table,$parent,$count,$order);
							// update branches
							foreach($branches as $branch=>$value) {
								$this->db->set("self_parent",$parent);
								$this->db->set("order",$order++);
								$this->db->where(PRIMARY_KEY,$value[PRIMARY_KEY]);
								$this->db->update($table);
							}
						}

						/**
						 * Remove database entry
						 */
						$this->db->where(PRIMARY_KEY,$id);
						$oldData=$this->db->get_row($table);
						$this->db->where(PRIMARY_KEY,$id);
						$this->db->delete($table);
						log_("info","[FD] delete item '$id' from '$table'");				

						/**
						 * Check if some data set in rel tables (if exists), if so delete them also
						 */
						$jTables=$this->db->get_many_tables($table);
						if (!empty($jTables)) {
							foreach ($jTables as $jt=>$jItem) {
								$this->db->where($jItem["id_this"],$id);
								$this->db->delete($jt);
							}
							log_("info","[FD] delete items from join tables ($table,$id)");
						}

						/**
						 * If special data, do special action:
						 * - tbl_links, remove the link from link.list
						 */
						$this->_after_delete($table,$oldData);
						// 
						// $this->load->library("editor_lists");
						// $this->editor_lists->create_list("links");

						/**
						 * End messages
						 */
						$this->load->model("login_log");
						$this->login_log->update($table);

						$this->set_message(langp("delete_succes",$table) . $message);
					}
				}
				else {
					$this->lang->load("rights");
					$this->set_message(lang("rights_no_rights"));
				}
			}
		}
		$redirectUri=api_uri('API_view_grid',$table);
		if (!empty($info)) $redirectUri.='/info/'.$info;
		redirect($redirectUri);
	}



}

?>
