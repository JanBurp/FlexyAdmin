<?
require_once(APPPATH."core/AdminController.php");

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
	}

	function index() {
		$this->_show_all();
	}


	private function _open_grid_set() {
		$set=$this->grid_set=$this->session->userdata('grid_set');
		return $set;
	}
	
	private function _open_grid_set_uri() {
		$set=$this->_open_grid_set();
		$uri=api_uri('API_view_grid',$set['table']);
		unset($set['table']);
		foreach ($set as $key => $value) {
			if (!empty($value)) $uri.="/$key/$value";
		}
		return $uri;
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


	function delete($table="",$ids="",$info='') {
		$confirmed=$this->session->userdata("confirmed");
		if (!empty($table) and (!empty($ids)) and $this->db->table_exists($table)) {
			
			$this->lang->load("update_delete");
			$this->load->model('queu');
			
			if (!is_array($ids)) $ids=array($ids);
			foreach ($ids as $id) {
				if ($this->user->has_rights($table,$id)>=RIGHTS_DELETE) {
					$message="";
					if ($confirmed) {

						$this->db->where(PRIMARY_KEY,$id);
						$oldData=$this->db->get_row($table);

						if ($this->_after_delete($table,$oldData)) {
							$this->crud->table($table)->delete( array(PRIMARY_KEY=>$id) );	

							/**
							 * End messages
							 */
							$this->load->model("login_log");
							$this->login_log->update($table);
							$this->set_message(langp("delete_succes",$table) . $message);
						}
					}
				}
				else {
					$this->lang->load("rights");
					$this->set_message(lang("rights_no_rights"));
				}
			}

			$this->queu->run_calls();
			delete_all_cache();
		}
		

		$redirectUri=$this->_open_grid_set_uri();
		if (!empty($info)) $redirectUri.='/info/'.$info;
		redirect($redirectUri);
	}



}

?>
