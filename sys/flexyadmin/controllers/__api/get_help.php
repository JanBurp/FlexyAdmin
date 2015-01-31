<?php require_once(APPPATH."core/ApiController.php");

/**
 * Geeft help pagina
 *
 * @package default
 * @author Jan den Besten
 */


class get_help extends ApiController {
  
  var $args = array(
    'page' => '',
  );
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
    $this->load->helper('markdown');
    $this->load->model('ui');
    return $this;
	}
  
  public function index() {
    $title='Help';
    $commonHelp=$this->db->get_field('cfg_configurations','txt_help');
    $help = Markdown(file_get_contents(APPPATH.'views/help/01__Over_Help.html'));
    
    return $this->_result(array('title'=>$title,'common_help'=>$commonHelp,'help'=>$help));
  }

}


?>
