<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * cfg_users
 *
 * @author Jan den Besten
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
    if (el('where',$args)=='current') {
      $args['where']=$this->user->user_id;
    }
    // select only the safe fields, that user may change
    $args['select']=array('str_username','id_user_group','email_email','str_language');
    return parent::get_row($args);
  }
  

}
