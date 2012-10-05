<?
require_once(APPPATH."core/AdminController.php");

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @copyright Copyright (c) 2008-2012, Jan den Besten
 * @link http://flexyadmin.com
 * @version V1 0.1
 * @filesource  */

// ------------------------------------------------------------------------


class Help extends AdminController {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$lang=$this->session->userdata('language');
		$commonHelp=$this->cfg->get('CFG_configurations','txt_help');
		$specificHelp=$this->ui->get_help();

    $map='sys/flexyadmin/views/help/';
    $helpFiles=read_map($map);
    $helpHTML='';
    foreach ($helpFiles as $file => $item) {
      $title=str_replace('_',' ',get_suffix(str_replace('.html','',$item['name']),'__'));
      if (!empty($title)) {
        $html=read_file($map.$file);
        $html=h($title).div('content').$html._div();
        $helpHTML.=$html;
      }
    }

		$this->_add_content($this->load->view("admin/help_".$lang,array('commonHelp'=>$commonHelp,'help'=>$helpHTML,'specificHelp'=>$specificHelp),true) );
		$this->_show_all();
	}

}

?>
