<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */


Class Options_Fields extends CI_Model {

	public function __construct( $table='' ) {
		parent::__construct();
	}
  
  
  public function get_options( $info=array() ) {
    $options=array();
    $tables = $this->data->list_tables();
    if ( !$this->flexy_auth->is_super_admin() ) {
      $tables=not_filter_by($tables,"log_");
      $tables=not_filter_by($tables,"rel_users");
    }
    foreach ($tables as $table) {
      if ($this->flexy_auth->has_rights($table)) {
        $table_fields=$this->db->list_fields($table);
        // Speciale velden filter
        if ( el('field',$info)==='fields_media_fields') {
          $table_fields = filter_by($table_fields,'media');
        }
        if ( $table==='cfg_users' ) {
          $table_fields=array('str_username','email_email');
        }
        // Maak er opties van
        foreach ($table_fields as $value) {
          $options[]=$table.'.'.$value;
        }
      }
    }
    array_unshift($options,'');
    $options=array_combine($options,$options);
    return $options;
  }
  


}
