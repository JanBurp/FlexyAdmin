<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// require_once(APPPATH.'models/data/Data_core.php');

/**
 * Wrapper around data/Core_res_assets
 * 
 * Kijk voor help bij /data/Res_assets
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Assets extends CI_Model {
  
  private $table = 'res_assets';
  
  public function __construct() {
    $this->load->model('data/Data');
    $this->load->model('data/Data_Core');
    $this->load->model('data/Core_res_assets');
  }
  
  /**
   * All Core_res_assets methods
   *
   * @return mixed
   * @author Jan den Besten
   * @internal
   */
	public function __call( $method, $args ) {
    $this->data->table($this->table);
    $return = call_user_func_array( array($this->data,$method), $args);
    // Return $this als het het Data_core object is
    if (is_object($return) and isset($return->settings)) {
      return $this;
    }
    // Anders return de return value zelf
    return $return;
	}
  
  
}
