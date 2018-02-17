<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/** \ingroup plugins
 * Laat laatste update info zien op homepage
 * 
 * @author Jan den Besten
 */

class Plugin_update extends Plugin {

  private $search='';
  private $from='';
  private $start='';
  private $end='';

  /**
   * @author Jan den Besten
   * @internal
   */
	public function __construct() {
		parent::__construct();
    $this->CI->load->model('version');
    $this->CI->lang->load('home');
  }

  /**
   * Log activity on homepage
   *
   * @return void
   * @author Jan den Besten
   */
  public function _admin_homepage() {
    return $this->show_last_update(true,'home_new_update');
  }

  public function _admin_api() {
    return $this->show_last_update();
  }

  private function show_last_update($last=false,$title='home_update_history') {
    if ( !$this->CI->flexy_auth->allowed_to_use_cms()) return false;

    $version      = $this->CI->version->get_version();
    $user_version = $this->CI->data->table('cfg_users')->get_last_version();
    if ($user_version=='') $user_version = $version;
    $this->CI->data->table('cfg_users')->update_last_version();

    if ( !$last or $user_version<$version) {
      $changelog = $this->CI->version->get_changelog();
      foreach ($changelog as $key => $log) {
        if ($last and $user_version>$log['version']) {
          unset($changelog[$key]);
        }
        else {
          $changelog[$key]['log'] = $this->_nicelog($log['log']);
        }
      }
      if ($last) {
        array_push($changelog,array('version'=>'','log'=>'<a class="btn btn-primary" href="_admin/plugin/update">show update history</a>'));
      }

      $gridData = array(
        'title'   => langp($title),
        'class'   => $last?'bg-warning':'',
        'headers' => array(
          'version' => lang('home_version'),
          'log'     => lang('home_changelog'),
        ),
        'data'    => $changelog,
      );
      return $this->CI->load->view("admin/grid",$gridData,true);
    }

    return false;
  }

  private function _nicelog($log) {
    $lang = $this->CI->language;
    if ($lang) {
      if (preg_match('/\['.$lang.'](.*)\[\/nl]/us', $log, $match)) {
        $log = $match[1];
        $log = str_replace("\n",'<br>',trim($log,"\n"));
        return $log;
      }
    }
    $log = preg_replace('/\[([^\]]*)\]\s*/u', '<h3>$1</h3>', $log);
    $log = str_replace("\n",'<br>',$log);
    return $log;
  }

}

?>
