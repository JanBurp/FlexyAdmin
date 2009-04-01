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

	function Edit() {
		parent::AdminController();
		// $this->load->model("flexy_data","fd");
	}

	function index() {
		$this->_set_content("EDIT");
		$this->_show_all();
	}

	/**
	 * Database edit controller
	 */

	function confirm($table,$id,$confirmed="") {
		if ($confirmed=="confirm") {
			$this->session->set_userdata("confirmed",true);
			$this->delete($table,$id);
		}
		else {
			$this->set_message("Not confirmed... ".anchor(api_uri('API_confirm',$table,$id,"confirm"),"confirm"));
			redirect(api_uri('API_view_grid',$table));
		}
	}

	function _has_media($table) {
		$path=$this->cfg->get('CFG_media_info',$table,"str_path");
		return ($path);
	}

	function delete($table,$id) {
		$confirmed=$this->session->userdata("confirmed");
		if ($this->has_rights($table,$id)) {
			$message="";
			if ($confirmed) {

				/**
				 * If there is media, delete them...
				 */
				$mediaPath=$this->_has_media($table);
				if (!empty($mediaPath)) {
					$this->load->model("file_manager");
					$fileManager=new file_manager($mediaPath);
					$types=$this->config->item('FIELDS_media_fields');

					// ok, get fields
					$this->db->where(pk(),$id);
					$query=$this->db->get($table);
					$row=$query->row_array();
					foreach ($row as $name=>$value) {
						$pre=get_prefix($name);
						if (in_array($pre,$types) and !empty($value)) {
							// ok found one, delete it
							$result=$fileManager->delete_file($value);
							if ($result) {
								log_("info","[FD] delete file '$value'");
								$message.="'$value' deleted. ";
							}
							else {
								log_("Error deleting file/dir '$value'.");
								$message.="'$value' error deleting! ";
							}
						}
					}
				}

				/**
				 * Remove database entry
				 */

				$this->db->where(pk(),$id);
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
				$this->load->library("editor_lists");
				$this->editor_lists->create_list("links");

				/**
				 * End messages
				 */
				$this->load->model("login_log");
				$this->login_log->update($table);

				$this->set_message("Item from '$table' deleted. $message");
			}
		}
		else {
			$this->set_message("Sorry, you don't have rights to do this.");
		}
		redirect(api_uri('API_view_grid',$table));
	}



}

?>
