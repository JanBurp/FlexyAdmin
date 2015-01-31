<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * cfg_users
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 */
class cfg_users extends _crud {
  
	public function __construct() {
		parent::__construct();
		$this->table('cfg_users');
	}

  /**
   * Check if 'where=current' and is so give it the current user id and pass to normal crud
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   */
  public function get_row($args=array()) {
    if ($args['where']=='current') {
      $args['where']=$this->user->user_id;
    }
    return parent::get_row($args);
  }
  

}
