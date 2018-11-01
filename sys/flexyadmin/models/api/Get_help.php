<?php

/**
 * API: Geeft help tekst
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Get_help extends Api_Model {
  
  var $needs = array();
  
	public function __construct($name='') {
		parent::__construct();
    $this->load->helper('markdown');
    return $this;
	}
  
  public function index() {
    $this->result['data'] = $this->_collect_help();
    return $this->_result_ok();
  }

  private function _collect_help() {
    // Lees alle help bestanden in en zet ze om naar HTML
    $lang = $this->flexy_auth->get_user(null,'str_language');
    $map=APPPATH.'views/help';
    $helpFiles=read_map($map);
    ksort($helpFiles);
    
    $help_items = array();
    foreach ($helpFiles as $file => $item) {
      $title = str_replace('_',' ',get_suffix(str_replace('.html','',$item['name']),'__'));
      if (!empty($title)) {
        $html = file_get_contents($item['path']);
        $matches=array();
        if (preg_match_all("/\[(.*)\]/uiUsm", $html,$matches)) {
          foreach ($matches[1] as $match) {
            $help=$this->lang->ui_help($match);
            if ($help) {
              $help=h( $this->lang->ui(remove_prefix($match,'.')),2 ).$help;
              $html=str_replace('['.$match.']',$help,$html);
            }
          }
        }
        $html = Markdown($html);
        $html = str_replace('sys/flexyadmin/assets/',$this->config->item('ADMINASSETS'),$html);
        
        $uri = remove_prefix(remove_suffix($file,'.'),'__');
        $help_items[$uri] = array(
          'uri'     => $uri,
          'title'   => $title,
          'content' => $html,
        );
      }
    }
    // $help = $this->load->view( 'admin/vue/help', array('items'=>$help_items), true );
    return $help_items;
  }

}


?>
