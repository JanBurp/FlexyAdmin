<?php require_once(APPPATH."core/AdminController.php");

/** \ingroup controllers
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Help extends AdminController {

	public function __construct() {
		parent::__construct();
    $this->load->helper('markdown');
	}

	public function index($page='') {
		$lang=$this->flexy_auth->get_user()['str_language'];
		$commonHelp=$this->cfg->get('CFG_configurations','txt_help');

    // Lees alle help bestanden in en zet ze om naar HTML
    $map=APPPATH.'views/help';
    $helpFiles=read_map($map);
    ksort($helpFiles);
    
    $help_items = array();
    // $current_uri = $this->uri->uri_string();
    foreach ($helpFiles as $file => $item) {
      $title=str_replace('_',' ',get_suffix(str_replace('.html','',$item['name']),'__'));
      if (!empty($title)) {
        $html=file_get_contents($item['path']);
        $matches=array();
        if (preg_match_all("/\[(.*)\]/uiUsm", $html,$matches)) {
          foreach ($matches[1] as $match) {
            $help=$this->ui->get_help($match);
            if ($help) {
              $help=h( $this->ui->get(remove_prefix($match,'.')) ).$help;
              $html=str_replace('['.$match.']',$help,$html);
            }
          }
        }
        $html=Markdown($html);
        $html=str_replace('sys/flexyadmin/assets/',$this->config->item('ADMINASSETS'),$html);
        
        // $html=$this->load->view('admin/vue/card', array('title'=>$title, 'content'=>$html),true );
        // $helpHTML.=$html;
        
        $uri = remove_prefix(remove_suffix($file,'.'),'__');
        $help_items[$uri] = array(
          'uri'     => $uri,
          'title'   => $title,
          'content' => $html,
        );
      }
    }
    
		$content = $this->load->view( 'admin/vue/help', array('items'=>$help_items), true );
    
		$this->view_admin('',array('content'=>$content));
	}

}

?>
