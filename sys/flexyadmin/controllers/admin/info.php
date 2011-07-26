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

class Info extends AdminController {

	function __construct() {
		parent::__construct();
	}

	function index() {
		// last login info
		$data["username"]=$this->session->userdata("user");
		$data["language"]=$this->session->userdata('language');
		$data["revision"]=$this->get_revision();
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
		$this->_set_content('<p class="small">'.str_replace("\n","<br/>",read_file('sys/flexyadmin/flexyadmin_license.txt'))."</p>");
		$this->_show_type("info");
		$this->_show_all();
	}

	// function code() {
	// 	if ($this->_has_key()) {
	// 		$this->load->library('form_validation');
	// 		$this->load->helper('html');
	// 		$this->load->helper('language');
	// 		$this->load->model("form");
	// 		$this->lang->load("update_delete");
	// 		$formData=array( "str_line" => array(	"validation"	=>  "required"),
	// 										 "str_code"	=> array(	"validation"	=>  "required", "value"=>"2009"));
	// 		$form=new form('admin/info/code');
	// 		$form->set_data($formData,"Code");
	// 		if ($form->validation()) {
	// 			$data=$form->get_data();
	// 			$c=$this->_encode($data['str_line']['value']);
	// 			$this->_set_content("'$c'");
	// 		}
	// 		else {
	// 			$this->_set_content($form->render());
	// 		}
	// 		$this->_show_type("code");
	// 	}
	// 	else
	// 		$this->_set_content("");
	// 	$this->_show_all();
	// }


	// function extra() {
	// 	$this->_set_content('<h1>Extra</h1>');
	// 	
	// 	$f=$this->db->get_result('tbl_fotoarchief');
	// 	foreach ($f as $key => $value) {
	// 		$c=$value['str_copyright'];
	// 		$cid=$this->db->get_field_where('tbl_copyright','id','str_naam',$c);
	// 		$this->db->set('id_copyright',$cid);
	// 		$this->db->where('id',$value['id']);
	// 		$this->db->update('tbl_fotoarchief');
	// 		$this->_add_content(p().$c.$cid._p());
	// 	}
	// 	
	// 	$this->_show_type("info");
	// 	$this->_show_all();		
	// }



}

?>
