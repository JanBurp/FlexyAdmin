<?php

/** \ingroup controllers
 * CLI controller
 * 
 * Running CLI:
 * php index.php _cli _command_
 *
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

class Cli extends CI_Controller {
	
	public function __construct()	{
		parent::__construct();
    $this->load->library('flexy_auth');
	}
  
  public function index() {
    $args=func_get_args();
    $model=array_shift($args);
    $method='index';
    
    // does CLI model exists?
    if (file_exists(APPPATH.'/models/cli/'.ucfirst($model).'.php') or file_exists(SITEPATH.'/models/cli/'.ucfirst($model).'.php')) {
      
      // auth?
      $num  = count($args);
      if ($num>=3 and $args[$num-3]=='login') {
        $password = array_pop($args);
        $username = array_pop($args);
        $this->flexy_auth->login( $username, $password );
      }
      
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
    $this->load->library('documentation');
    echo "FlexyAdmin cli commands:".PHP_EOL.PHP_EOL;
    
    $clis = scan_map('sys/flexyadmin/models/cli',$types='php',FALSE);
    foreach ($clis as $cli) {
      $doc = $this->documentation->get($cli);
      echo $doc['name'].PHP_EOL;
      echo repeater('-',strlen($doc['name'])).PHP_EOL;
      echo $doc['long'];
      echo PHP_EOL.PHP_EOL;
    }
  }
  


}

?>