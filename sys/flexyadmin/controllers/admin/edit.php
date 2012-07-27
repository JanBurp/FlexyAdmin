<?
require_once(APPPATH."core/AdminController.php");

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

/**
 * Edit Controller Class
 *
 * This Controller updates (other than form updates) / deletes
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @version V1 0.1
 *
 */

class Edit extends AdminController {

	function __construct() {
		parent::__construct();
    $this->load->model('grid_set');
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
				$this->message->add_error("Not confirmed... ".anchor(api_uri('API_confirm',$table),"confirm"));
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
			$message="";
      
			if (!is_array($ids)) $ids=array($ids);

			if ($confirmed) {
        
        // Set items to delete
        $items=array();
        $where='';
  			foreach ($ids as $id) {
  				if ($this->user->has_rights($table,$id)>=RIGHTS_DELETE) {
            $items[]=$id;
            $where[]=array(PRIMARY_KEY=>$id);
          }
        }
      
        // Start Delete
        // Call _after_delete just for one (last) item (more is not needed)
				$this->db->where(PRIMARY_KEY,$id);
				$oldData=$this->db->get_row($table);
        if ($this->_after_delete($table,$oldData)) {
          // Delete all items
          $this->crud->table($table)->delete( $where );  
          // End messages
          $this->load->model("login_log");
          $this->login_log->update($table);
          $this->message->add(langp("delete_succes",$table) . $message);
        }
        else {
          $this->lang->load("rights");
          $this->message->add_error(lang("rights_no_rights"));
        }
			}

			$this->queu->run_calls();
			delete_all_cache();
		}
		

		$redirectUri=$this->grid_set->open_uri();
		if (!empty($info)) $redirectUri.='/info/'.$info;
		redirect($redirectUri);
	}



}

?>
