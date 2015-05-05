<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extending the loader class
 * see the docs http://codeigniter.com/user_guide/general/core_classes.html
 * 
 * @ignore
 * @internal
 */

class MY_Loader extends CI_Loader {
	
	protected $_ci_plugin_paths	= array();

	public function __construct() {
		parent::__construct();
		$this->_ci_view_paths=array(SITEPATH.'views/'=>1,APPPATH.'views/'=>1);
		array_push($this->_ci_model_paths,SITEPATH);
		array_push($this->_ci_library_paths,SITEPATH);
		array_push($this->_ci_helper_paths,SITEPATH);
		$this->_ci_plugin_paths = array(APPPATH,SITEPATH);
	}
    
  /**
   * Test if file is allready loaded
   *
   * @param string $type 
   * @param string $name 
   * @return void
   * @author Jan den Besten
   */
  public function exist($type,$name) {
    $list=array();
    switch ($type) {
      case 'helper':
        $list=$this->_ci_helpers;
        break;
      case 'models':
        $list=$this->_ci_models;
        break;
    }
    return isset($list[$name]);
  }
    
    
    
    
	/**
	 * Load the Database Utilities Class
	 *
	 * @param	object	$db	Database object
	 * @param	bool	$return	Whether to return the DB Utilities class object or not
	 * @return	object
	 */
	public function dbutil($db = NULL, $return = FALSE)
	{
		$CI =& get_instance();

		if ( ! is_object($db) OR ! ($db instanceof CI_DB))
		{
			class_exists('CI_DB', FALSE) OR $this->database();
			$db =& $CI->db;
		}

		require_once(BASEPATH.'database/DB_utility.php');
		require_once(BASEPATH.'database/drivers/'.$db->dbdriver.'/'.$db->dbdriver.'_utility.php');
		$class = 'CI_DB_'.$db->dbdriver.'_utility';

		if ($return === TRUE)
		{
			return new $class($db);
		}
    
// Added from here, to extend the mysql_utility driver (JdB)
    $my_driver = config_item('subclass_prefix').'DB_'.$db->dbdriver.'_utility';
    $my_driver_file = APPPATH.'core/'.$my_driver.'.php';
    if (file_exists($my_driver_file)) {
      require_once($my_driver_file);
      $class = $my_driver;
    }
// Added stops here

		$CI->dbutil = new $class($db);
		return $this;
	}
   
}

?>
