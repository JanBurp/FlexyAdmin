<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */


Class Options_Tables extends CI_Model {

	public function __construct( $table='' ) {
		parent::__construct();
	}
  
  
  public function get_options( $info=array() ) {
    $options=array();
    $tables=$this->data->list_tables();
    foreach ($tables as $key => $table) {
      if ( !$this->flexy_auth->has_rights($table) ) unset($tables[$key]);
    }
    if ( !$this->flexy_auth->is_super_admin() ) {
      $tables=not_filter_by($tables,"log_");
      $tables=not_filter_by($tables,"rel_users");
    }
		$options=$tables;
    array_unshift($options,'');
    $options=array_combine($options,$options);
    return $options;
  }
  


}
