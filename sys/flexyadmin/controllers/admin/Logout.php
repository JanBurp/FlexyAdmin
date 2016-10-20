<?php require_once(APPPATH."core/AdminController.php");

/** \ingroup controllers
 * @author Jan den Besten
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
			$this->flexy_auth->logout();
			if ($this->config->item('logout_to_site'))
				redirect('');
			else
				redirect($this->homePage);
		}
		else {
			$this->_add_content(h('Logout',1));
			$this->_add_content(p('error').'Stopped logout, because there are important messages.<br/>Try to logout for a second time. If the messages are still there, contact you\'re webmaster.'._p());
      $this->_add_content($logoutMessages);
			$this->view_admin();
		}
	}

}

?>
