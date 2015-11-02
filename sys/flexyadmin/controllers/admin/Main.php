<?php require_once(APPPATH."core/AdminController.php");

/**
 * main Controller Class
 *
 * This Controller shows the startscreen
 * 
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Main extends AdminController {

	public function __construct() {
    parent::__construct();
	}

	public function index() {
		$this->load->model("grid");
		$this->lang->load("home");
    $this->_show_type("grid");

    // homepage plugin?
    $data['homeplugins']=$this->plugin_handler->call_plugins_homepage();

		// last login info
		$user=$this->user->get_user();
		$data["username"]=$user->str_username;
		$user_id=$user->id;
		$this->db->select("tme_login_time,str_changed_tables");
		$this->db->where("id_user",$user_id);
		$this->db->order_by("tme_login_time DESC");
		$query=$this->db->get($this->config->item('LOG_table_prefix')."_".$this->config->item('LOG_login'),5);
		$userData=$query->result_array();
		$query->free_result();
		// in grid
		$grid=new grid();
		foreach($userData as $k=>$d) {
			$userData[$k]=array(
				lang("home_date")		=>	str_replace(' ','&nbsp;',strftime("%a %e %B %Y - %R",strtotime($d["tme_login_time"]))),
				lang("home_changes")=>	str_replace("|",", ",$this->ui->get($d["str_changed_tables"]))
      );
		}
		$grid->set_data($userData,langp("home_last_login",ucfirst($data["username"])));
		$renderGrid=$grid->render("html","","grid home");
		$data["logindata"]=$this->load->view("admin/grid",$renderGrid,true);
		
		// Check if userdata is correct #TODO #BUSY
		// if (isset($user->email_email) and empty(trim($user->email_email))) $data['message']='geen email';
		// trace_($user);
		// trace_($data);
		
		$this->_set_content($this->load->view("admin/home",$data,true));
		$this->_show_all();
	}



}

?>
