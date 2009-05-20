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

	function _create_rights($userId) {
		$this->db->select("id");
		$this->db->where("id",$userId);
		$this->db->add_many();
		$result=$this->db->get_result("cfg_users");
		$allrights=$result[$userId]["rel_users__rights"];
		$rights=array();
		foreach ($allrights as $id => $value) {
			unset($value['id']);
			unset($value['id_rights']);
			unset($value['id_users']);
			if ($value['b_all_users'])
				$value['user_rights']="";
			else
				$value['user_rights']=$value['rights'];
			$rights[$id]=$value;
		}
		// trace_($rights);
		return $rights;
	}

	function check_login() {
		$check=false;
		$user=$this->input->post("user",TRUE);
		if (!empty($user)) {
			$pwd=$this->input->post("password",TRUE);
			if (!empty($pwd)) {
				// check in database
				$this->db->select("id,str_language,str_filemanager_view");
				$this->db->where("str_user_name",$user);
				$this->db->where("gpw_user_pwd",$pwd);
				$query=$this->db->get($this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_users'));
				$row=$query->row();
				if (!empty($row)) {
					$check=TRUE;
					// set session
					$this->session->set_userdata("user_id",$row->id);
					$this->session->set_userdata("user",$user);
					$this->session->set_userdata("language",$row->str_language);
					$this->session->set_userdata("fileview",$row->str_filemanager_view);
					$this->session->set_userdata("rights",$this->_create_rights($row->id));
					// set login log
					$this->load->helper('date');
					$this->db->set('id_user',$row->id);
					$this->db->set('tme_login_time',standard_date('DATE_W3C',time()));
					$this->db->set('ip_login_ip',$this->session->userdata('ip_address'));
					$this->db->insert($this->config->item('LOG_table_prefix')."_".$this->config->item('LOG_login'));
				}
			}
		}
		redirect($this->config->item('API_home'));
	}

	function logout() {
		$this->session->sess_destroy();
		if ($this->db->has_field('cfg_configurations','b_logout_to_site') and $this->db->get_field('cfg_configurations','b_logout_to_site'))
			redirect();
		else
			redirect($this->config->item('API_home'));
	}

}

?>
