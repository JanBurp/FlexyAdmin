<?

/**
 * Geeft help pagina
 *
 * @package default
 * @author Jan den Besten
 */

class get_help extends ApiModel {
  
  var $needs = array(
    'page' => '',
  );
  
  var $lang;
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
    $this->load->model('ui');
    $this->load->helper('markdown');
    $this->lang=$this->session->userdata('language');
    return $this;
	}
  
  public function index() {
    if (!$this->logged_in()) return $this->_result_status401();
    
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
              // $help=h($this->ui->get(remove_prefix($match,'.')),3).$help;
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
