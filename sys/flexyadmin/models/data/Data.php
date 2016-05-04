<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * Wrapper voor Data_Model_Core en eventueel afgeleiden
 * 
 * @author: Jan den Besten
 * $Revision$
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
    $this->table = $table;
    
    // Load model; test if table has own data model
    if (!isset($this->models[$table])) {
      if ( !empty($table) and file_exists(APPPATH.'models/data/'. ucfirst($table) .'.php')) {
        $this->load->model('data/'.$table);
        $this->models[$table] = $this->$table;
      }
      else {
        $this->models[$table] = $this->data_core;
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
  
  
  

  

}
