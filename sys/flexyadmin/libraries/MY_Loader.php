<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * extending the loader class... needs this in config file
 * $config['subclass_prefix'] = 'MY_';
 * see the docs http://codeigniter.com/user_guide/general/core_classes.html
 * 
 * @package    default
 */


class MY_Loader extends CI_Loader {

    function MY_Loader() {
        parent::CI_Loader();
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
		function database($params = '', $return = FALSE, $active_record = FALSE)
		{
			// Do we even need to load the database class?
			if (class_exists('CI_DB') AND $return == FALSE AND $active_record == FALSE)
			{
				return FALSE;
			}

			require_once(BASEPATH.'database/DB'.EXT);

			// Load the DB class
			$db =& DB($params, $active_record);

			$my_driver = config_item('subclass_prefix').'DB_'.$db->dbdriver.'_driver';
			$my_driver_file = APPPATH.'libraries/'.$my_driver.EXT;

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
			// Assign the DB object to any existing models
			$this->_ci_assign_to_models();
		}

		// --------------------------------------------------------------------

		/**
		 * Load the Utilities Class
		 *
		 * @access	public
		 * @return	string		
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

			require_once(BASEPATH.'database/DB_utility'.EXT);
			require_once(BASEPATH.'database/drivers/'.$CI->db->dbdriver.'/'.$CI->db->dbdriver.'_utility'.EXT);
			$class = 'CI_DB_'.$CI->db->dbdriver.'_utility';

			// Added from here, to extend the mysql_utility driver (JdB)
	    $my_driver = config_item('subclass_prefix').'DB_'.$CI->db->dbdriver.'_utility';
	    $my_driver_file = APPPATH.'libraries/'.$my_driver.EXT;
	    if (file_exists($my_driver_file))
	    {
	        require_once($my_driver_file);
					$class = $my_driver;
	    }
			// Added stops here

			$CI->dbutil =& instantiate_class(new $class());

			$CI->load->_ci_assign_to_models();
		}





    /**
     * This function lets users load and instantiate models given a path outside default CI map
     * updated model function to allow calling of remote files.
     * @access    public
     * @param    string    the name of the class
     * @param    string    name for the model
     * @param    bool    database connection
     * @return    void
     **/
    function model($model, $name = '', $db_conn = FALSE){
    
        $model = str_replace(EXT,'',$model);

        if (is_array($model))
        {
            foreach($model as $babe)
            {
                $this->model($babe);    
            }
            return;
        }

        if ($model == '')
        {
            return;
        }
    
        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (strpos($model, '/') === FALSE)
        {
            $path = '';
        }
        else
        {
            $x = explode('/', $model);
            $model = end($x);            
            unset($x[count($x)-1]);
            $path = implode('/', $x).'/';
        }

        if ($name == '')
        {
            $name = $model;
        }
        
        if (in_array($name, $this->_ci_models, TRUE))
        {
            return;
        }
        
        $CI =& get_instance();
        if (isset($CI->$name))
        {
            show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
        }
    
        $model = strtolower($model);
        
        $local_file = false;
        $remote_file = false;
        
        if ( ! file_exists(APPPATH.'models/'.$path.$model.EXT))
        {
            $local_file = false;
        }
        else
        {
            $local_file = true;
        }

        if ( !$local_file && ! file_exists($path.$model.EXT))
        {
            $remote_file = true;
        }
        else
        {
            $remote_file = true;
        }
        
        if(!$local_file && !$remote_file)
        {
            show_error('Unable to locate the model you have specified:<br />'.$path.$model);
        }
        
        if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
        {
            if ($db_conn === TRUE)
                $db_conn = '';
        
            $CI->load->database($db_conn, FALSE, TRUE);
        }
    
        if ( ! class_exists('Model'))
        {
            load_class('Model', FALSE);
        }
        
        if($local_file)
        {
            require_once(APPPATH.'models/'.$path.$model.EXT);
        }
        else
        {
            require_once($path.$model.EXT);
        }
        
        $model = ucfirst($model);
                
        $CI->$name = new $model();
        $CI->$name->_assign_libraries();
        
        $this->_ci_models[] = $name;    
    }




		/**
		 * Extensions by Jan den Besten, 2009
		 * See: http://codeigniter.com/forums/viewthread/73545/
		 */

		// Same as view() method: loads a view. But with this one you can give the path of the view, which makes it possible to load a view outside the standard directory structure.
		function my_view($v,$path,$var=array(),$return=false) {
			$file_ext = pathinfo($v,PATHINFO_EXTENSION);
			$v = ($file_ext == '') ? $v.EXT : $v;

			$data=array(
			    '_ci_path' => $path.'/'.$v,
					'_ci_vars' => $this->_ci_object_to_array($var),
					'_ci_return' => $return
			);
			return $this->_ci_load($data);
		}
		function site_view($v,$var=array(),$return=false) {
			return $this->my_view($v,"site/views",$var,$return);
		}


		/**
		 * Load SitePlugin
		 *
		 * This function loads the specified plugin.
		 *
		 * @access	public
		 * @param	array
		 * @return	void
		 */
		
		// Load a plugin from given path. Makes it possible to load plugins from site/plugins.
		
		function site_plugin($plugins = array(),$path)
		{
			if ( ! is_array($plugins)) {
				$plugins = array($plugins);
			}

			foreach ($plugins as $plugin)	{	
				$plugin = strtolower(str_replace(EXT, '', str_replace('_pi', '', $plugin)).'_pi');		
				if (isset($this->_ci_plugins[$plugin]))	{
					continue;
				}

				if (file_exists($path.'/'.$plugin.EXT)) {
					include_once($path.'/'.$plugin.EXT);	
				}
				else {
					show_error('Unable to load the requested file: '.$path.'/'.$plugin.EXT);
				}

				$this->_ci_plugins[$plugin] = TRUE;
				log_message('debug', 'Plugin loaded: '.$plugin);
			}		
		}



}

?>
