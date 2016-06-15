<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */


Class Options_Tables extends Options_Core {

	public function __construct( $table='' ) {
		parent::__construct();
	}
  
  
  public function get_options( $info=array() ) {
    $options=array();
    $tables=$this->db->list_tables();
	  $tables=not_filter_by($tables,"cfg_");
	  $tables=not_filter_by($tables,"log_");
	  $tables=not_filter_by($tables,"rel_users");
		$options=$tables;
    array_unshift($options,'');
    $options=array_combine($options,$options);
    return $options;
  }
  


}
