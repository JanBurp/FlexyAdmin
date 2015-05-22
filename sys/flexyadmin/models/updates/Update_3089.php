<?php 

/**
 * Update 3089
 * 
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
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
        $count = $this->order->reset($table,0,true);
        $this->_add_message("Order of <b>`$table`</b> is reset",'glyphicon-ok btn-success');
      }
    }

    return parent::update();
  }

 }
?>
