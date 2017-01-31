<?php

/**
 * API: Geeft een help pagina voor in de backend van FlexyAdmin
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class get_help extends Api_Model {
  
  var $needs = array(
    'page' => '',
  );
  
  var $lang;
  
  /**
   */
	public function __construct($name='') {
		parent::__construct();
    $this->load->model('ui');
    $this->load->helper('markdown');
    $this->lang=$this->flexy_auth->get_user()['str_language'];
    return $this;
	}
  
  public function index() {
    if (!$this->logged_in()) return $this->_result_status401();
    
		$commonHelp = $this->data->table('cfg_configurations')->get_field('txt_help');
    $map='sys/flexyadmin/views/help';
    $helpFiles=read_map($map);
    ksort($helpFiles);

    $helpHTML='';
    foreach ($helpFiles as $file => $item) {
      $title=str_replace('_',' ',get_suffix(str_replace('.html','',$item['name']),'__'));
      if (!empty($title)) {
        $html=file_get_contents($item['path']);
        $matches=array();
        if (preg_match_all("/\[(.*)\]/uiUsm", $html,$matches)) {
          foreach ($matches[1] as $match) {
            $help=$this->lang->ui_help($match);
            if ($help) {
              // $help=h($this->lang->ui(remove_prefix($match,'.')),3).$help;
              $html=str_replace('['.$match.']',$help,$html);
            }
          }
        }
        $html=Markdown($html);
        $helpHTML.=$html;
      }
    }
    
    // RESULT
    $this->result['data']=array(
      'title' =>'Help',
      'common_help' => $commonHelp,
      'help' => $helpHTML
    );
    return $this->_result_ok();
  }

}


?>
