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
 * user Controller Class
 *
 * This Controller logs user in or out
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class User extends Controller {

	function User() {
		parent::Controller();
		$this->load->library('session');
	}

	function login() {
		$this->load->view('admin/login');
	}

	function check_login() {
		$check=false;
		$user=$this->input->post("user",TRUE);
		if (!empty($user)) {
			$pwd=$this->input->post("password",TRUE);
			if (!empty($pwd)) {
				// check in database
				$this->db->select("id,str_table_rights,str_media_rights");
				$this->db->where("str_user_name",$user);
				$this->db->where("gpw_user_pwd",$pwd);
				$query=$this->db->get($this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_users'));
				$row=$query->row();
				if (!empty($row)) {
					$check=TRUE;
					// set session
					$this->session->set_userdata("user_id",$row->id);
					$this->session->set_userdata("user",$user);
					$this->session->set_userdata("table_rights",$row->str_table_rights);
					$this->session->set_userdata("media_rights",$row->str_media_rights);
					// set login log
					$this->load->helper('date');
					$this->db->set('id_user',$row->id);
					$this->db->set('tme_login_time',standard_date('DATE_W3C',time()));
					$this->db->set('ip_login_ip',$this->session->userdata('ip_address'));
					$this->db->insert($this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_login_log'));
				}
			}
		}
		redirect($this->config->item('API_home'));
	}

	function logout() {
		$this->session->unset_userdata("user");
		$this->session->unset_userdata("user_rights");
		redirect($this->config->item('API_home'));
	}

}

?>
