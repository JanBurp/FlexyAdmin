<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */


Class Options_Fields extends Options_Core {

	public function __construct( $table='' ) {
		parent::__construct();
	}
  
  
  public function get_options( $info=array() ) {
    $options=array();
    $tables = $this->db->list_tables();
    foreach ($tables as $table) {
      $table_fields=$this->db->list_fields($table);
      // Speciale velden filter
      if ( el('field',$info)==='fields_media_fields') {
        $table_fields = filter_by($table_fields,'media');
      }
      // Maak er opties van
      foreach ($table_fields as $value) {
        $options[]=$table.'.'.$value;
      }
    }
    array_unshift($options,'');
    $options=array_combine($options,$options);
    return $options;
  }
  


}
