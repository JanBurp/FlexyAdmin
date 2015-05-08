<?php require_once(APPPATH."core/AdminController.php");

/**
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */
class Logout extends AdminController {

	var $homePage;

	function __construct() {
		parent::__construct();
		$this->homePage=$this->config->item('API_home');
	}

	function index() {
		$logoutMessages=$this->plugin_handler->call_plugins_logout();
		
		// logout
		if (!$logoutMessages) {
			$this->user->logout();
			if ($this->config->item('logout_to_site'))
				redirect('','refresh');
			else
				redirect($this->homePage, 'refresh');
		}
		else {
			$this->_add_content(h('Logout',1));
			$this->_add_content(p('error').'Stopped logout, because there are important messages.<br/>Try to logout for a second time. If the messages are still there, contact you\'re webmaster.'._p());
      $this->_add_content($logoutMessages);
			$this->_show_all();
		}
	}

}

?>
