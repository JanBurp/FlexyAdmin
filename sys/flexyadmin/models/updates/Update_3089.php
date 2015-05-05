<?php 

/**
 * $Author$
 * $Date$
 * $Revision$
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 * @link http://www.flexyadmin.com
 */

class Update_3089 extends Model_updates {
  
  public	function __construct() {
    parent::__construct();
    $this->load->model('order');
  }
  
  public function update() {
    // Reset order of all tables with 'order' field
    $tables=$this->db->list_tables();
    foreach ($tables as $key => $table) {
      if (! $this->db->has_field($table,'order')) {
        unset($tables[$key]);
      }
      else {
        $count = $this->order->reset($table);
        $this->_add_message("Order of <b>`$table`</b> is reset",'glyphicon-ok btn-success');
      }
    }

    return parent::update();
  }

 }
?>
