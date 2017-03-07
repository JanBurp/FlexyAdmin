<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * Wrapper voor Data_Core en eventueel afgeleiden
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */


Class Data extends CI_Model {
  
  /**
   * Huidige tabel
   */
  private $table = '';
  
  /**
   * Alle al geladen objecten
   */
  private $models = array();
  
  /**
   * Cached resultaat van list_tables
   */
  private $_list_tables = FALSE;
  
  
	public function __construct() {
		parent::__construct();
	}
  
  
  /**
   * data->table()
   *
   * @param string $table 
   * @return $this
   * @author Jan den Besten
   */
  public function table( $table ) {
    if (empty($table)) {
      throw new ErrorException( __CLASS__.'->'.__METHOD__.'() table is not given.' );
      return $this;
    }
    $this->table = $table;
    
    // Als model nog niet is geladen, dan gaan we dat nu doen
    if (!isset($this->models[$table])) {
      
      // Controleer eerst of eigen model bestaat
      $model_name   = ucfirst($table);
      $model_exists = file_exists(SITEPATH.'models/data/'.$model_name.'.php');
      
      // Eerst eventueel Core_model laden, als eigen model niet bestaat, neemt die de naam over.
      $core_model = 'Core_'.strtolower($table);
      if ( file_exists(APPPATH.'models/data/'.$core_model.'.php') ) {
        if ($model_exists) {
          $this->load->model('data/'.$core_model);
        }
        else {
          $this->load->model('data/'.$core_model, $model_name);
          $this->models[$table] = $this->$model_name;
        }
      }
      
      // Dan eventueel eigen model
      if ( $model_exists ) {
        $this->load->model('data/'.$model_name);
        $this->models[$table] = $this->$model_name;
      }
      
      // Als nog steeds niet bestaat, dan gewoon als data_model
      if (!isset($this->models[$table])) {
        $this->models[$table] = new Data_core; // Elk model een eigen object, zodat maar één keer de settings ingesteld worden per aanroep.
      }
      
    }
    
    // Set table
    call_user_func_array( array($this->models[$table],'table'), array($table) );
    return $this;
  }


  /**
   * All other data_model methods
   *
   * @return mixed
   * @author Jan den Besten
   * @internal
   */
	public function __call( $method, $args ) {
    $table = $this->table;
    // Error if table/model not set and needed
    if (!isset($this->models[$table])) {
      throw new ErrorException( __CLASS__.'->'.$method.' model not set. Try using ->data->table() first.' );
    }
    // Alles in orde, roep de method aan
    else {
      $return = call_user_func_array( array($this->models[$table],$method), $args);
    }
    // Return $this als het het Data_core object is
    if (is_object($return) and isset($return->settings)) {
      return $this;
    }
    // Anders return de return value zelf
    return $return;
	}
  
  
  
  /**
   * cache list_tables
   *
   * @return array
   * @author Jan den Besten
   */
  public function list_tables() {
    if (!is_array($this->_list_tables)) {
      $this->_list_tables = $this->db->list_tables();
    }
    return $this->_list_tables;
  }
  
  
  
  /**
   * Check if table exists in cached list_tables
   *
   * @param string $table 
   * @return void
   * @author Jan den Besten
   */
  public function table_exists( $table ) {
    $tables = $this->list_tables();
    return (in_array( $table,$tables ));
  }
  
  
  
  

  

}
