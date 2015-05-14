<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup models
 * Met dit model kun je de basis database handelingen uitvoeren (CRUD)
 * (Wrapper van Crud_)
 * 
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Crud extends CI_Model {

	public function __construct() {
		parent::__construct();
    $this->load->model('crud_');
	}
  
  /**
   * Wrapper for _crud->table()
   *
   * @param string $table 
   * @param string $user_id 
   * @return mixed
   * @author Jan den Besten
   */
	public function table($table='',$user_id=FALSE) {
		return $this->crud_->table($table,$user_id);
	}


  /**
   * Wrapper for all other crud methods
   *
   * @return mixed
   * @author Jan den Besten
   * @internal
   */
	public function __call($function, $args) {
    $args=el(0,$args,null);
    $table=$this->crud_->get_table();

    // Test if table has own crud model, if so call it
    if (!empty($table) and file_exists(APPPATH.'models/'.$table.'.php')) {
      // trace_('load and call '.$table);
      $this->load->model($table);
      if ($args) return $this->$table->$function($args);
      return $this->$table->$function();
    }

    // No special model for this table -> normal crud action
    // trace_('crud'.$table);
    if ($args) return $this->crud_->$function($args);
    return $this->crud_->$function();
	}
  
  
  

}
