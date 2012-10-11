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

	public function index($page='') {
		$lang=$this->session->userdata('language');
    $this->load->helper('markdown');
    
		$commonHelp=$this->cfg->get('CFG_configurations','txt_help');

    $map='sys/flexyadmin/views/help';
    $helpFiles=read_map($map);
    ksort($helpFiles);
    $helpHTML='';
    foreach ($helpFiles as $file => $item) {
      $title=str_replace('_',' ',get_suffix(str_replace('.html','',$item['name']),'__'));
      if (!empty($title)) {
        $html=read_file($item['path']);
        $matches=array();
        if (preg_match_all("/\[(.*)\]/uiUsm", $html,$matches)) {
          foreach ($matches[1] as $match) {
            $help=$this->ui->get_help($match);
            if ($help) {
              $help=h($this->ui->get(remove_prefix($match,'.')),3).$help;
              $html=str_replace('['.$match.']',$help,$html);
            }
          }
        }
        $html=Markdown($html);
        $html=h($title).div('content').$html._div();
        $helpHTML.=$html;
      }
    }

		$this->_add_content($this->load->view("admin/help_".$lang,array('page'=>$page,'commonHelp'=>$commonHelp,'help'=>$helpHTML),true) );
		$this->_show_all();
	}

}

?>
