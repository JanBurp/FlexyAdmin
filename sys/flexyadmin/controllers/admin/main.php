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
 * main Controller Class
 *
 * This Controller shows the startscreen
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Main extends AdminController {

	function Main() {
		parent::AdminController();
	}

	function index() {
		//$this->_set_content($this->cfg->get('CFG_configurations',"txt_info"));
		$this->load->model("grid");
		$this->lang->load("home");

		// last login info
		$data["username"]=$this->session->userdata("user");
		$user_id=$this->session->userdata("user_id");
		$this->db->select("tme_login_time,str_changed_tables");
		$this->db->where("id_user",$user_id);
		$this->db->order_by("tme_login_time DESC");
		$query=$this->db->get($this->config->item('LOG_table_prefix')."_".$this->config->item('LOG_login'),4);
		$userData=$query->result_array();
		// in grid
		$grid=new grid();
		foreach($userData as $k=>$d) {
			$userData[$k]=array(
											lang("home_date")		=>	strftime("%A %e %B %Y om %R",strtotime($d["tme_login_time"])),
											lang("home_changes")=>	str_replace("|",", ",$this->uiNames->get($d["str_changed_tables"])) );
		}
		$grid->set_data($userData,langp("home_last_login",ucfirst($data["username"])));
		$renderGrid=$grid->render("html","","grid home");
		$data["logindata"]=$this->load->view("admin/grid",$renderGrid,true);
		// 
		// // stats
		// $this->db->select("str_uri as ".lang("home_page").", COUNT(`str_uri`) as ".lang("home_count"));
		// $this->db->group_by(lang("home_page"));
		// $this->db->order_by(lang("home_count")." DESC");
		// $stats=$this->db->get_results($this->config->item('LOG_table_prefix')."_".$this->config->item('LOG_stats'),10);
		// // trace_($stats);
		// if (!empty($stats)) {
		// 	// stats in grid
		// 	$grid=new grid();
		// 	$grid->set_data($stats,langp("home_top_ten"));
		// 	$renderGrid=$grid->render("html","","grid home");
		// 	$data["stats"]=$this->load->view("admin/grid",$renderGrid,true);
		// }
		// else $data["stats"]="";
		
		$this->_set_content($this->load->view("admin/home",$data,true));
		$this->_show_all();
	}

}

?>
