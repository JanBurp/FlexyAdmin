<?php require_once(APPPATH."core/AdminController.php");

/**
 * $Author$
 * $Date$
 * $Revision$
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Info extends AdminController {

	function __construct() {
		parent::__construct();
    $this->load->model('svn');
	}

	function index() {
		// last login info
		$data["username"]=$this->session->userdata("user");
		$data["language"]=$this->session->userdata('language');
		$data["revision"]=$this->svn->get_revision();
		$this->_set_content($this->load->view("admin/info_".$data["language"],$data,true));
		$this->_show_type("info");
		$this->_show_all();
	}

	function php() {
		ob_start();                                                                                                       
		phpinfo();                                                                                                        
		$info = ob_get_contents();                                                                                        
		ob_end_clean();                                                                                                   
		$info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
		$info = str_replace('<table ', '<table class="grid" ', $info);
		$this->_show_type("phpinfo");
		$this->_add_content($info);
		$this->_show_all();
	}

	function license() {
    $license_file='sys/flexyadmin/flexyadmin_license';
    $lang=$this->user->language;
    if (file_exists($license_file.'_'.$lang.'.txt')) {
      $license_file.='_'.$lang;
    }
		$this->_set_content('<p class="small">'.str_replace("\n","<br/>",file_get_contents($license_file.'.txt'))."</p>");
		$this->_show_type("info");
		$this->_show_all();
	}

}

?>
