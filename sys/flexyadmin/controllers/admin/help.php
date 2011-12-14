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

// ------------------------------------------------------------------------


class Help extends AdminController {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$lang=$this->session->userdata('language');
		$commonHelp=$this->cfg->get('CFG_configurations','txt_help');
		$specificHelp=$this->ui->get_help();
		$this->_add_content($this->load->view("admin/help_".$lang,array('commonHelp'=>$commonHelp,'specificHelp'=>$specificHelp),true) );
		$this->_show_type("info");
		$this->_show_all();
	}

}

?>
