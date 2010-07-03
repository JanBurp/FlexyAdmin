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


class Logout extends AdminController {

	var $homePage;

	function Logout() {
		parent::AdminController();
		$this->homePage=$this->config->item('API_home');
	}

	function index() {
		// plugin logouts...
		foreach ($this->plugins as $plugin) {
			if (isset($this->$plugin) and method_exists($this->$plugin,'_admin_logout')) $this->$plugin->_admin_logout();
		}
		
		// logout
		$this->session->sess_destroy();
		if ($this->db->has_field('cfg_configurations','b_logout_to_site') and $this->db->get_field('cfg_configurations','b_logout_to_site'))
			redirect();
		else
			redirect($this->homePage);
	}

}

?>
