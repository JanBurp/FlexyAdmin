<?php require_once(APPPATH."core/AjaxController.php");

/** \ingroup controllers
 * API Controller
 *
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

class Cli extends CI_Controller {
	
	public function __construct()	{
		parent::__construct();
	}
  
  public function index() {
    $args=func_get_args();
    $model=array_shift($args);
    $method='index';
    
    // does CLI model exists?
    if (file_exists(APPPATH.'/models/cli/'.ucfirst($model).'.php') or file_exists(SITEPATH.'/models/cli/'.ucfirst($model).'.php')) {
      // Load Model
      $this->load->model('cli/'.$model);
      // Call model/method
      echo call_user_func_array( array($this->$model,$method), $args );
    }
    else {
      $this->_help();
    }
  }
  
  /**
   * Shows help of all cli commands
   *
   * @return void
   * @author Jan den Besten
   */
  private function _help() {
    echo "FlexyAdmin cli commands:".PHP_EOL.PHP_EOL;
    $clis = scan_map('sys/flexyadmin/models/cli',$types='php',FALSE);
    foreach ($clis as $cli) {
      $file   = get_suffix($cli,'/');
      $class  = str_replace('.php','',$file);
      $this->load->model('cli/'.$class);
      echo $this->$class->help();
      echo PHP_EOL;
    }
  }
  


}

?>