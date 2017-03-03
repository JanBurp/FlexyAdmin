<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Wrapper around data/Res_assets (Data)
 * 
 * Kijk voor help bij /data/Res_assets
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Assets extends CI_Model {
  
  private $table = 'res_assets';
  
  public function __construct() {
    $this->load->model('data/data');
  }
  
  /**
   * All Res_assets methods
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
