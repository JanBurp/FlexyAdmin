<?
/**
 * Updating FlexyAdmin
 *
 * $Author$
 * $Date$
 * $Revision$
 * 
 * @package FlexyAdmin
 * @author: Jan den Besten
 * @copyright: Jan den Besten (c)
 * @link http://www.flexyadmin.com
 */

class Update extends MY_Controller {
  
  private $tags   = '../TAGS';
  private $db_map = 'db';
  
  private $updates=array();
  
  private $actions = array('sys','site','database','all');
  private $messages = array();
	
	public function __construct()	{
		parent::__construct();
    $this->load->model('svn');
	}

  public function index() {
    $latest     = $this->_latest_tag();
    $latest_sql = $this->_latest_sql();
    
    $this->updates = array(
      'sys'        => array(
        'name'     => 'sys (build)',
        'current'  => $this->svn->get_revision(),
        'latest'   => $latest,
      ),
      'site'      => array(
        'name'    => 'site (controller)',
        'current' => $this->svn->get_revision_of_file('site/controller.php'),
        'latest'  => $latest,
      ),
      'database'  => array(
        'name'    => 'database',
        'current' => $this->db->get_field('cfg_configurations','str_revision'),
        'latest'  => $latest_sql,
      ),
    );
    
    $update_all=false;
    foreach ($this->updates as $key => $versions) {
      $update=true;
      if (is_numeric($versions['latest']) and is_numeric($versions['current'])) {
        $update = ($versions['latest']>$versions['current']);
      }
      $this->updates[$key]['update'] = $update;
      $update_all = $update_all or $update;
    }
    
    // Action?
    $action=$this->input->get('action');
    if ($action and in_array($action,$this->actions)) {
      $method='_update_'.$action;
      $this->$method();
    }
    
    $this->load->view('update',array('updates'=>$this->updates,'update_all'=>$update_all,'messages'=>$this->messages));
  }
  
  private function _latest_tag() {
    return $this->_tag_from_files($this->tags);
  }

  private function _latest_sql() {
    return $this->_tag_from_files($this->db_map);
  }
  
  private function _tag_from_files($map) {
    if (file_exists($map)) {
      $files=scandir($this->db_map);
      if ($files) {
        sort($files);
        $last_file=end($files);
        $tag=remove_suffix($last_file,'.');
        $tag=get_suffix($tag,'_');
        $tag=substr($tag,1);
        return (int) $tag;
      }
    }
    return 'unkown';
  }
  
  
  private function _add_message($type,$message) {
    $this->messages[$type][]=$message;
  }
  
  
  /**
   * Update all parts
   *
   * @return void
   * @author Jan den Besten
   */
  private function _update_all() {
    $doActions=$this->actions;
    array_pop($doActions);
    foreach ($doActions as $action) {
      $method='_update_'.$action;
      $this->$method();
    }
  }
  
  
  private function _update_sys() {
    $this->_add_message('sys','<b>From '.$this->updates['sys']['current'].' to '.$this->updates['sys']['latest'].'</b>');
  }

  private function _update_site() {
    $this->_add_message('site','<b>From '.$this->updates['site']['current'].' to '.$this->updates['site']['latest'].'</b>');
  }
  
  private function _update_database() {
    $this->_add_message('database','<b>From '.$this->updates['database']['current'].' to '.$this->updates['database']['latest'].'</b>');
  }



}

?>
