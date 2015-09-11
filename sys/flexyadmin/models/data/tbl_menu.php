<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

Class tbl_menu extends Data_Model {

  protected $primary_key      = PRIMARY_KEY;
  protected $table            = 'tbl_menu';
  protected $fields           = array('id','order','self_parent','uri','str_title','txt_text','str_module','stx_description','str_keywords');
  protected $relations        = array(
                                  'belongs_to'       => array(),
                                  'many_to_many'     => array(),
                                );
  protected $order_by         = '';
  protected $max_rows         = 0;
  protected $update_uris      = TRUE;
  protected $abstract_fields  = array();
  protected $abstract_filter  = '';
  protected $admin_grid       = array(
                                  'fields'            => array(),
                                  'relations'         => array(),
                                  'order_by'          => '',
                                  'jump_to_today'     => TRUE,
                                );
  protected $admin_form        = array(
                                  'fields'            => array(),
                                  'relations'         => array(),
                                  'fieldsets'         => array(),
                                );
  

  public function __construct() {
    parent::__construct();
  }

}
