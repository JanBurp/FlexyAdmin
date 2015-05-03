<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Deze plugin ververst de volgorde van een tabel met 'order'
 *
 * @package default
 * @author Jan den Besten
 */
class Plugin_reorder extends Plugin {
   
  public function __construct() {
    parent::__construct();
  }

  public function _admin_api($args=NULL) {
    $table=el(0,$args);
    if ($table) {
      $this->add_message($table.' is re-ordered');
      $this->CI->load->model('order');
      $this->CI->order->reset($table);
    }
    else {
      $this->add_message('use: /table');
    }
    return $this->view();
  }

}

?>