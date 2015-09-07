<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extending the loader class
 * See the docs http://codeigniter.com/user_guide/general/core_classes.html
 * 
 * @internal
 */

class MY_Loader extends CI_Loader {
	
  /**
   * List of loaded views
   *
   * @return array
   */
  protected $_ci_views = array();
  
  /**
   * Plugin paden
   *
   * @var array
   */
	protected $_ci_plugin_paths	= array();
  
  private $CI;


  /**
   * Maak de paden in orde
   *
   * @author Jan den Besten
   */
	public function __construct() {
		parent::__construct();
    $this->CI=& get_instance();
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
	 * Zelfde als CodeIgniter, maar gebruikt `parser` als dat globaal is ingesteld.
	 *
	 * @param	string	$view	View name
	 * @param	array	$vars	An associative array of data
	 *				to be extracted for use in the view
	 * @param	bool	$return	Whether to return the view output
	 *				or leave it to the Output class
	 * @return	object|string
	 */
	public function view($view, $vars = array(), $return = FALSE) {
    $result=parent::view($view,$vars,$return);
    if ($this->CI->config->item('use_parser',false) and isset($this->CI->parser) and is_string($result)) {
      $result=$this->CI->parser->parse_string($result,$vars,$return);
    }
    return $result;
	}
  
  
  /**
   * Unloads a model
   *
   * @param string $name 
   * @return bool
   * @author Jan den Besten
   */
  public function unload_model($name) {
    unset($this->CI->$name);
    $key = array_search($name,$this->_ci_models);
    unset($this->_ci_models[$key]);
    return ($key!==FALSE);
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
		if ( ! is_object($db) OR ! ($db instanceof CI_DB))
		{
			class_exists('CI_DB', FALSE) OR $this->database();
			$db =& $this->CI->db;
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

		$this->CI->dbutil = new $class($db);
		return $this;
	}
  
  
  
  /**
   * HIERONDER ALLEMAAL VOOR DevBar:
   */
  
  
  
  /**
   * List of loaded helpers
   *
   * @return array
   */
  public function get_helpers()
  {
      return $this->_ci_helpers;
  }

  /**
   * List of loaded views
   *
   * @return array
   */
  public function get_views()
  {
      return $this->_ci_views;
  }

  public function get_models(){
      return $this->_ci_models;
  }

  /**
   * Internal CI Data Loader
   *
   * Used to load views and files.
   *
   * Variables are prefixed with _ci_ to avoid symbol collision with
   * variables made available to view files.
   *
   * @used-by    CI_Loader::view()
   * @used-by    CI_Loader::file()
   *
   * @param    array $_ci_data Data to load
   *
   * @return    object
   */
  protected function _ci_load($_ci_data)
  {
      // Set the default data variables
      foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val) {
          $$_ci_val = isset($_ci_data[$_ci_val]) ? $_ci_data[$_ci_val] : false;
      }

      $file_exists = false;

      // Set the path to the requested file
      if (is_string($_ci_path) && $_ci_path !== '') {
          $_ci_x = explode('/', $_ci_path);
          $_ci_file = end($_ci_x);
      } else {
          $_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
          $_ci_file = ($_ci_ext === '') ? $_ci_view . '.php' : $_ci_view;

          foreach ($this->_ci_view_paths as $_ci_view_file => $cascade) {
              if (file_exists($_ci_view_file . $_ci_file)) {
                  $_ci_path = $_ci_view_file . $_ci_file;
                  $file_exists = true;
                  break;
              }

              if (!$cascade) {
                  break;
              }
          }
      }

      if (!$file_exists && !file_exists($_ci_path)) {
          show_error('Unable to load the requested file: ' . $_ci_file);
      }

      // This allows anything loaded using $this->load (views, files, etc.)
      // to become accessible from within the Controller and Model functions.
      $_ci_CI =& get_instance();
      foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var) {
          if (!isset($this->$_ci_key)) {
              $this->$_ci_key =& $_ci_CI->$_ci_key;
          }
      }

      /*
       * Extract and cache variables
       *
       * You can either set variables using the dedicated $this->load->vars()
       * function or via the second parameter of this function. We'll merge
       * the two types and cache them so that views that are embedded within
       * other views can have access to these variables.
       */
      if (is_array($_ci_vars)) {
          $this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
      }

      extract($this->_ci_cached_vars);

      /*
       * Buffer the output
       *
       * We buffer the output for two reasons:
       * 1. Speed. You get a significant speed boost.
       * 2. So that the final rendered template can be post-processed by
       *	the output class. Why do we need post processing? For one thing,
       *	in order to show the elapsed page load time. Unless we can
       *	intercept the content right before it's sent to the browser and
       *	then stop the timer it won't be accurate.
       */
      ob_start();

      // If the PHP installation does not support short tags we'll
      // do a little string replacement, changing the short tags
      // to standard PHP echo statements.
      if (!is_php('5.4') && !ini_get('short_open_tag') && config_item('rewrite_short_tags') === true && function_usable('eval')) {
          echo eval('?>' . preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
      } else {
          include($_ci_path); // include() vs include_once() allows for multiple views with the same name
      }

      // New : Add the the loaded view file to the list
      $this->_ci_views[$_ci_path] = $_ci_file;

      log_message('info', 'File loaded: ' . $_ci_path);

      // Return the file data if requested
      if ($_ci_return === true) {
          $buffer = ob_get_contents();
          @ob_end_clean();

          return $buffer;
      }

      /*
       * Flush the buffer... or buff the flusher?
       *
       * In order to permit views to be nested within
       * other views, we need to flush the content back out whenever
       * we are beyond the first level of output buffering so that
       * it can be seen and included properly by the first included
       * template and any subsequent ones. Oy!
       */
      if (ob_get_level() > $this->_ci_ob_level + 1) {
          ob_end_flush();
      } else {
          $_ci_CI->output->append_output(ob_get_contents());
          @ob_end_clean();
      }

      return $this;
  }
  
   
}

?>
