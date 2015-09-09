<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

Class tbl_menu extends Data_Model {

  protected $table            = __CLASS__;
  protected $fields           = array();
  protected $relations        = array();
  protected $order_by         = '';
  protected $max_rows         = 0;
  protected $update_uris      = TRUE;
  protected $abstract_fields  = array();
  protected $abstract_filter   = '';
  protected $admin_grid = array();
  protected $admin_form = array();
  
	public function __construct() {
		parent::__construct();
	}
  

}
