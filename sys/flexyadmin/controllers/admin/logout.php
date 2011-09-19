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


class Logout extends AdminController {

	var $homePage;

	function __construct() {
		parent::__construct();
		$this->homePage=$this->config->item('API_home');
	}

	function index() {
		// plugin logouts...
		$logout=true;
		foreach ($this->plugins as $plugin) {
			if (isset($this->$plugin) and method_exists($this->$plugin,'_admin_logout')) {
				if ($this->$plugin->_admin_logout()===false) $logout=false;
			}
		}
		
		// logout
		if ($logout) {
			$this->user->logout();
			$this->session->sess_destroy();
			if ($this->config->item('logout_to_site'))
				redirect();
			else
				redirect($this->homePage);
		}
		else {
			$this->_add_content(h('Logout',1));
			$this->_add_content(p('error').'Stopped logout, because there are important (red) messages.<br/>Try to logout for a second time. If the messages are still there, contact you\'re webmaste.'._p());
			$this->_show_all();
		}
	}

}

?>
