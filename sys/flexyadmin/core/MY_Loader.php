<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * extending the loader class... needs this in config file
 * $config['subclass_prefix'] = 'MY_';
 * see the docs http://codeigniter.com/user_guide/general/core_classes.html
 * 
 * @package    default
 * @ignore
 * @internal
 */

class MY_Loader extends CI_Loader {
	
		var $_ci_plugin_paths	= array();

 		function __construct() {
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
    function exist($type,$name) {
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
		* Database Loader
		*
		* @access    public
		* @param    string    the DB credentials
		* @param    bool    whether to return the DB object
		* @param    bool    whether to enable active record (this allows us to override the config setting)
		* @return    object
		*
		* Description		http://codeigniter.com/wiki/Extending_Database_Drivers/
		* This change makes it possible to create a database extension (library) by loading MY_DB_db_driver.PHP
		*
		*/
		function database($params = '', $return = FALSE, $active_record = NULL)
		{
			// Do we even need to load the database class?
			if (class_exists('CI_DB') AND $return == FALSE AND $active_record == FALSE) {
				return FALSE;
			}

			require_once(BASEPATH.'database/DB'.'.php');

			// Load the DB class
			$db =& DB($params, $active_record);

			$my_driver = config_item('subclass_prefix').'DB_'.$db->dbdriver.'_driver';
			$my_driver_file = APPPATH.'core/'.$my_driver.'.php';

			if (file_exists($my_driver_file)) 
			{
				require_once($my_driver_file);
				$db = new $my_driver(get_object_vars($db));
			}

			if ($return === TRUE)
			{
				return $db;
			}

			// Grab the super object
			$CI =& get_instance();

			// Initialize the db variable.  Needed to prevent
			// reference errors with some configurations
			$CI->db = '';
			$CI->db = $db;
		}

		// --------------------------------------------------------------------

		/**
		 * Load the Utilities Class
		 *
		 * @access public
		 * @return string		
		 *
		 * Description		http://codeigniter.com/wiki/Extending_Database_Drivers/
		 * This change makes it possible to create a database utility extension (library) by loading MY_DB_db_mysql_utility.PHP
		 *
		 */		
		function dbutil()
		{
			if ( ! class_exists('CI_DB'))
			{
				$this->database();
			}

			$CI =& get_instance();

			// for backwards compatibility, load dbforge so we can extend dbutils off it
			// this use is deprecated and strongly discouraged
			$CI->load->dbforge();

			require_once(BASEPATH.'database/DB_utility'.'.php');
			require_once(BASEPATH.'database/drivers/'.$CI->db->dbdriver.'/'.$CI->db->dbdriver.'_utility'.'.php');
			$class = 'CI_DB_'.$CI->db->dbdriver.'_utility';

			// Added from here, to extend the mysql_utility driver (JdB)
	    $my_driver = config_item('subclass_prefix').'DB_'.$CI->db->dbdriver.'_utility';
	    $my_driver_file = APPPATH.'core/'.$my_driver.'.php';
	    if (file_exists($my_driver_file))
	    {
	        require_once($my_driver_file);
					$class = $my_driver;
	    }
			// Added stops here
			$CI->dbutil =  new $class();
		}

}

?>
