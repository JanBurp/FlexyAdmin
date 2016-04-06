<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data_model
 * 
 * Wrapper voor Data_Model_Core en eventueel afgeleiden
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */


Class Data_Model extends CI_Model {
  
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
   * data_model->table()
   *
   * @param string $table 
   * @return mixed
   * @author Jan den Besten
   */
  public function table( $table ) {
    $this->table = $table;
    return $this->data_model_core->table( $table );
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
    
    if (!isset($this->models[$table])) {
      // Test if table has own data model, if so load it
      if ( !empty($table) and file_exists(APPPATH.'models/data/'. ucfirst($table) .'.php')) {
        $this->load->model('data/'.$table);
        $this->models[$table] = $this->$table;
      }
      else {
        $this->models[$table] = $this->data_model_core;
      }
    }

    return call_user_func_array(array($this->models[$table],$method), $args);
	}
  
  
  

  

}
