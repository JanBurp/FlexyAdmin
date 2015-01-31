<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Met dit model kun je de basis database handelingen uitvoeren (CRUD)
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 * $HeadURL$ 
 */

class Crud extends CI_Model {

	public function __construct() {
		parent::__construct();
    $this->load->model('_crud');
	}
  
  /**
   * Wrapper for all crud methods
   *
   * @return mixed
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	public function __call($function, $args) {
    $table=$this->_crud->get_table();
    $args=el(0,$args,null);

    // trace_([$function,$args]);
    // Test if table has own crud model, is so call it
    if (!empty($table) and file_exists(APPPATH.'models/'.$table.'.php')) {
      // trace_('load and call '.$table);
      $this->load->model($table);
      return $this->$table->$function($args);
    }

    // No special model for this table -> normal crud action
    // trace_('crud'.$table);
    return $this->_crud->$function($args);
	}
  
  
  

}
