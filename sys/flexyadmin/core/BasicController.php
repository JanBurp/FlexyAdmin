<?php 
/**
 * BasicController Class extends MY_Controller
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class BasicController extends MY_Controller {

	var $user_name;
	var $user_id;
	var $language;
	var $plugins;

	public function __construct($isAdmin=false) {
		parent::__construct($isAdmin);
		$this->load->library('session');
		$this->load->library('flexy_auth');

		if ( !$this->flexy_auth->logged_in()) {
      if (!$this->input->is_ajax_request()) $this->output->set_status_header('401');
      return;
		}

		// ok move on...
		$this->load->model('plugin_handler');
    $this->load->model('message');
    $this->message->init();
    $this->load->model('create_uri');
		$this->load->helper("language");

    $this->language = $this->flexy_auth->get_user(NULL,'str_language');
		$lang = $this->language."_".strtoupper($this->language);
		setlocale(LC_ALL, $lang);

		$this->plugin_handler->init_plugins();
	}

}

?>