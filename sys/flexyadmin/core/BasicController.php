<?php 
/**
 * BasicController Class extends MY_Controller
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @version V1 0.1
 * @ignore
 * @internal
 */

class BasicController extends MY_Controller {

	var $user_name;
	var $user_id;
	var $language;
	var $plugins;

	public function __construct($isAdmin=false) {
		parent::__construct($isAdmin);
		$this->load->library('session');
		$this->load->library('user');
		
		if ( ! $this->_user_logged_in()) {
      $this->output->set_status_header('401');
		}

		// ok move on...
		$this->load->model('plugin_handler');
    $this->load->model('message');
    $this->message->init();
    $this->load->model('create_uri');
		$this->load->helper("language");

		$lang=$this->language."_".strtoupper($this->language);
		setlocale(LC_ALL, $lang);

		$this->plugin_handler->init_plugins();
	}

	public function _user_logged_in() {
		$logged_in = $this->user->logged_in();
		if ($logged_in) {
			$this->user_id=$this->session->userdata("user_id");
			$this->user_name=$this->session->userdata("str_username");
			$this->language=$this->session->userdata("language");
			$this->user_group_id=$this->session->userdata("id_user_group");
		}
		return $logged_in;
	}

	function _init_plugin($table,$oldData=NULL,$newData=NULL) {
		$this->plugin_handler->set_data('old',$oldData);
		$this->plugin_handler->set_data('new',$newData);
		$this->plugin_handler->set_data('table',$table);
	}

	function _before_grid($table) {
		$this->_init_plugin($table,NULL,NULL);
		return $this->plugin_handler->call_plugins_before_grid_trigger();
	}

	function _after_delete($table,$oldData=NULL) {
		$this->_init_plugin($table,$oldData,NULL);
		return $this->plugin_handler->call_plugins_after_delete_trigger();
	}
	
	function _after_update($table,$oldData=NULL,$newData=NULL) {
		$this->_init_plugin($table,$oldData,$newData);
		$newData=$this->plugin_handler->call_plugins_after_update_trigger();
		return $newData;
	}

}

?>