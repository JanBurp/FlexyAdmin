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
		// last login info
		$user_id=$this->session->userdata("user_id");
		$this->db->select("tme_login_time,str_changed_tables");
		$this->db->where("id_user",$user_id);
		$this->db->order_by("tme_login_time DESC");
		$query=$this->db->get("cfg_login_log",4);
		$userData=$query->result_array();
		// in grid
		$grid=new grid();
		foreach($userData as $k=>$d) {
			$userData[$k]=array(
											"Datum"					=>	strftime("%A %e %B %Y om %R",strtotime($d["tme_login_time"])),
											"Veranderingen"	=>	str_replace("|",", ",$this->uiNames->get($d["str_changed_tables"])) );
		}
		$grid->set_data($userData,"Laatste 5 login's");
		$renderGrid=$grid->render("html","","grid home");
		$data["logindata"]=$this->load->view("admin/grid",$renderGrid,true);
		$data["username"]=$this->session->userdata("user");

		// stats
		$this->db->select("str_uri as Adres, COUNT(`str_uri`) as Aantal");
		$this->db->group_by('Adres');
		$this->db->order_by("Aantal DESC");
		$stats=$this->db->get_results("cfg_stats",10);
		// trace_($stats);
		if (!empty($stats)) {
			// stats in grid
			$grid=new grid();
			$grid->set_data($stats,"10 meest bezochte pagina's");
			$renderGrid=$grid->render("html","","grid home");
			$data["stats"]=$this->load->view("admin/grid",$renderGrid,true);
		}
		else $data["stats"]="";
		
		$this->_set_content($this->load->view("admin/home",$data,true));
		$this->_show_all();
	}

}

?>
