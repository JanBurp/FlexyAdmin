<?php require_once(APPPATH."core/AdminController.php");
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

class Update extends AdminController {
  
  private $tags   = '../TAGS';
  private $db_map = 'db';
  
  private $updates=array();
  
  private $actions = array('sys','code','database','all');
  private $messages = array();
	
	public function __construct()	{
		parent::__construct();
    $this->load->model('svn');
    $this->load->model('updates/model_updates');
    $this->load->dbutil();
	}

  public function index() {
    if (!$this->user->is_super_admin()) {
      redirect($this->config->item('API_home'),'refresh');
    };
    
    $latest     = $this->_latest_tag();
    $latest_sql = $this->_latest_sql();
    
    $this->updates = array(
      'sys'        => array(
        'name'     => 'sys (build)',
        'current'  => $this->svn->get_revision(),
        'latest'   => $latest,
      ),
      'code'      => array(
        'name'    => 'code (update scripts)',
        'current' => $this->svn->get_revision(),
        'latest'  => $this->svn->get_last_update_file(),
      ),
      'database'  => array(
        'name'    => 'database (update sql)',
        'current' => $this->db->get_field('cfg_configurations','str_revision'),
        'latest'  => $latest_sql,
      ),
    );
    
    $update_all=false;
    foreach ($this->updates as $key => $versions) {
      $update=false;
      if (is_numeric($versions['latest']) and is_numeric($versions['current'])) {
        $update = ($versions['latest']<=$versions['current']);
      }
      $this->updates[$key]['update'] = $update;
      $update_all = ($update_all or $update);
    }
    
    $action=$this->uri->get(3);
    if ($action and in_array($action,$this->actions)) {
      $method='_update_'.$action;
      $this->$method();
    }
    
    $this->load->view('admin/update',array('updates'=>$this->updates,'update_all'=>$update_all,'messages'=>$this->messages));
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
  
  
  private function _add_message($type,$message,$glyphicon='',$class='') {
    $this->messages[$type][]=array(
      'message'   => $message,
      'glyphicon' => $glyphicon,
      'class'     => $class
    );
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
  
  private function _update_database() {
		// load all update sql files
		$updates=read_map('db','sql',FALSE,FALSE);
		$updates=array_keys($updates);
		$updates=filter_by($updates,'update_');
		foreach ($updates as $key=>$file) {
			$fileRev=(int) substr($file,8,4);
			if ($fileRev<=$this->updates['database']['latest'])
				unset($updates[$key]);
			else {
				// load SQL
				$sql=file_get_contents('db/'.$file);
        $lines=explode("\n",$sql);
        $info=current($lines);
        $info=trim(trim($info,'#'));
        // Call SQL
        $result=$this->dbutil->import($sql);
        if ($result['errors']) {
          $error=current($result['errors']);
          $this->_add_message('database','<b>'.$file.'</b> - '.$info.' - <b class="text-danger">DB Error #'.$error['code'].': '.$error['message'].'</b>','glyphicon-remove btn-danger');
        }
        else {
          $this->_add_message('database','<b>'.$file.'</b> - '.$info,'glyphicon-ok btn-success');
        }
			}
		}
    if (empty($updates)) {
      $this->_add_message('database','<b>Up to date</b>','glyphicon-ok btn-success');
    }
  }
  
  private function _update_sys() {
    $this->_add_message('sys','<b>Up to date</b>','glyphicon-ok btn-success');
  }

  private function _update_code() {
    // Update models
		$updates=read_map('sys/flexyadmin/models/updates','php',FALSE,FALSE);
		$updates=array_keys($updates);
		$updates=filter_by($updates,'update_');
		foreach ($updates as $file) {
			$fileRev=(int) substr($file,7,4);
			if ($fileRev<=$this->updates['code']['latest'] -500)
				unset($updates[$key]);
			else {
        $model=remove_suffix($file,'.');
        $this->load->model('updates/'.$model);
        $messages=$this->$model->update();
        $this->messages=array_merge($this->messages,$messages);
      }
		}
    if (empty($updates)) {
      $this->_add_message('code','<b>Up to date</b>','glyphicon-ok btn-success');
    }
  }
  
}

?>
