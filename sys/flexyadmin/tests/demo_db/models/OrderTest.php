<?php

require_once(APPPATH.'/tests/CITestCase.php');


class OrderTest extends CITestCase {
  
  private $table      = 'tbl_menu';
  private $test_sql   = "
UPDATE `tbl_menu` SET `order`='0', `self_parent`='0', `uri`='gelukt' WHERE `id`='1';
UPDATE `tbl_menu` SET `order`='1', `self_parent`='0', `uri`='blog' WHERE `id`='6';
UPDATE `tbl_menu` SET `order`='2', `self_parent`='0', `uri`='een_pagina' WHERE `id`='2';
UPDATE `tbl_menu` SET `order`='3', `self_parent`='2', `uri`='subpagina' WHERE `id`='3';
UPDATE `tbl_menu` SET `order`='4', `self_parent`='2', `uri`='nog_een_subpagina' WHERE `id`='5';
UPDATE `tbl_menu` SET `order`='5', `self_parent`='0', `uri`='contact' WHERE `id`='4';
";

  protected function setUp() :void   {
    $this->CI->load->model('order');
    $this->CI->load->dbutil();
    $this->CI->dbutil->import($this->test_sql);
  }
  
  protected function tearDown() :void  {
    $this->CI->order->reset($this->table);
    $this->CI->load->dbutil();
    $this->CI->dbutil->import($this->test_sql);
  }
  

  private function _result($parent=false) {
    $this->CI->data->table($this->table);
    $this->CI->data->select('id,order');
    if ($parent) $this->CI->data->select('self_parent');
    $this->CI->data->order_by('order ASC');
    return $this->CI->data->get_result();
  }
  
  private function _dump_result($parent=false) {
    $this->CI->data->table($this->table);
    $this->CI->data->select('id,order,self_parent,uri');
    if ($parent) $this->CI->data->select('self_parent');
    $this->CI->data->order_by('order ASC');
    $result = $this->CI->data->get_result();
    echo "\n\n-- $this->table --\n";
    foreach ($result as $id => $row) {
      foreach ($row as $value) {
        echo $value.' | ';
      }
      echo "\n";
    }
    echo "\n";
    return $result;
  }


  /**
   * Startsitiet testen
   *
   * @return void
   * @author Jan den Besten
   */
  public function testStart() {

    // Start situatie
    $expected = array(
      '1'=>array( 'id'=>'1', 'order'=>'0' ),
      '6'=>array( 'id'=>'6', 'order'=>'1' ),
      '2'=>array( 'id'=>'2', 'order'=>'2' ),
      '3'=>array( 'id'=>'3', 'order'=>'3' ),
      '5'=>array( 'id'=>'5', 'order'=>'4' ),
      '4'=>array( 'id'=>'4', 'order'=>'5' ),
    );
    $result = $this->_result();
    $this->assertEquals($expected, $result);

    // Reset
    $this->CI->order->reset($this->table);
    $result = $this->_result();
    $expected = array(
      '1'=>array( 'id'=>'1', 'order'=>'0' ),
      '6'=>array( 'id'=>'6', 'order'=>'1' ),
      '2'=>array( 'id'=>'2', 'order'=>'2' ),
      '3'=>array( 'id'=>'3', 'order'=>'3' ),
      '5'=>array( 'id'=>'5', 'order'=>'4' ),
      '4'=>array( 'id'=>'4', 'order'=>'5' ),
    );
    $this->assertEquals($expected, $result);
  }
  
  
  /**
   * Test volgende
   *
   * @return void
   * @author Jan den Besten
   */
  public function testVolgende() {
    
    // Volgende
    $this->assertEquals(6, $this->CI->order->get_next_order($this->table) );
    $result = $this->_result();
    $expected = array(                        // nog steeds oud, want nog niets veranderd
      '1'=>array( 'id'=>'1', 'order'=>'0' ),
      '6'=>array( 'id'=>'6', 'order'=>'1' ),
      '2'=>array( 'id'=>'2', 'order'=>'2' ),
      '3'=>array( 'id'=>'3', 'order'=>'3' ),
      '5'=>array( 'id'=>'5', 'order'=>'4' ),
      '4'=>array( 'id'=>'4', 'order'=>'5' ),
    ); 
    $this->assertEquals($expected, $result);
    
    // Volgende Branch
    $this->assertEquals(5, $this->CI->order->get_next_order($this->table,2) );
    $result = $this->_result();
    $expected = array(
      '1'=>array( 'id'=>'1', 'order'=>'0' ),
      '6'=>array( 'id'=>'6', 'order'=>'1' ),
      '2'=>array( 'id'=>'2', 'order'=>'2' ),
      '3'=>array( 'id'=>'3', 'order'=>'3' ),
      '5'=>array( 'id'=>'5', 'order'=>'4' ),
      '4'=>array( 'id'=>'4', 'order'=>'6' ),
    );
    $this->assertEquals($expected, $result);

    // Volgende Branch (waar geen branch is)
    $this->assertEquals(1, $this->CI->order->get_next_order($this->table,1) );
    $result = $this->_result();
    $expected = array(
      '1'=>array( 'id'=>'1', 'order'=>'0' ),
      '6'=>array( 'id'=>'6', 'order'=>'2' ),
      '2'=>array( 'id'=>'2', 'order'=>'3' ),
      '3'=>array( 'id'=>'3', 'order'=>'4' ),
      '5'=>array( 'id'=>'5', 'order'=>'5' ),
      '4'=>array( 'id'=>'4', 'order'=>'7' ),
    );
    $this->assertEquals($expected, $result);

    
  }
  
  
  /**
   * Test direct setten van items
   *
   * @return void
   * @author Jan den Besten
   */
  public function testSet() {
    
    // Set all - 1
    $ids=array(1,6,2,5,4,3);
    $return = $this->CI->order->set_all($this->table,$ids);
    $result = $this->_result();
    $expected = array(
      '1'=>array( 'id'=>'1', 'order'=>'0' ),
      '6'=>array( 'id'=>'6', 'order'=>'1' ),
      '2'=>array( 'id'=>'2', 'order'=>'2' ),
      '5'=>array( 'id'=>'5', 'order'=>'3' ),
      '4'=>array( 'id'=>'4', 'order'=>'4' ),
      '3'=>array( 'id'=>'3', 'order'=>'5' ),
    ); 
    $this->assertEquals($expected, $result);
    $expected = array(
      '0'=>array( 'id'=>'1', 'order'=>'0' ),
      '1'=>array( 'id'=>'6', 'order'=>'1' ),
      '2'=>array( 'id'=>'2', 'order'=>'2' ),
      '3'=>array( 'id'=>'5', 'order'=>'3' ),
      '4'=>array( 'id'=>'4', 'order'=>'4' ),
      '5'=>array( 'id'=>'3', 'order'=>'5' ),
    );
    $this->assertEquals($expected, $return);

    // Set all - 2
    $ids=array(6,2,5,4,3,1);
    $this->CI->order->set_all($this->table,$ids);
    $result = $this->_result();
    $expected = array(
      '6'=>array( 'id'=>'6', 'order'=>'0' ),
      '2'=>array( 'id'=>'2', 'order'=>'1' ),
      '5'=>array( 'id'=>'5', 'order'=>'2' ),
      '4'=>array( 'id'=>'4', 'order'=>'3' ),
      '3'=>array( 'id'=>'3', 'order'=>'4' ),
      '1'=>array( 'id'=>'1', 'order'=>'5' ),
    ); 
    $this->assertEquals($expected, $result);

    // Set all - 3 shift 
    $ids=array(6,2,5,4,3,1);
    $return = $this->CI->order->set_all($this->table,$ids,2);
    $result = $this->_result();
    $expected = array(
      '6'=>array( 'id'=>'6', 'order'=>'2' ),
      '2'=>array( 'id'=>'2', 'order'=>'3' ),
      '5'=>array( 'id'=>'5', 'order'=>'4' ),
      '4'=>array( 'id'=>'4', 'order'=>'5' ),
      '3'=>array( 'id'=>'3', 'order'=>'6' ),
      '1'=>array( 'id'=>'1', 'order'=>'7' ),
    ); 
    $this->assertEquals($expected, $result);
    $expected = array(
      '0'=>array( 'id'=>'6', 'order'=>'2' ),
      '1'=>array( 'id'=>'2', 'order'=>'3' ),
      '2'=>array( 'id'=>'5', 'order'=>'4' ),
      '3'=>array( 'id'=>'4', 'order'=>'5' ),
      '4'=>array( 'id'=>'3', 'order'=>'6' ),
      '5'=>array( 'id'=>'1', 'order'=>'7' ),
    ); 
    $this->assertEquals($expected, $return);
    
    // Set all - 4
    $ids=array(1,6,2,5,4,3);
    $this->CI->order->set_all($this->table,$ids);
    $result = $this->_result();
    $expected = array(
      '1'=>array( 'id'=>'1', 'order'=>'0' ),
      '6'=>array( 'id'=>'6', 'order'=>'1' ),
      '2'=>array( 'id'=>'2', 'order'=>'2' ),
      '5'=>array( 'id'=>'5', 'order'=>'3' ),
      '4'=>array( 'id'=>'4', 'order'=>'4' ),
      '3'=>array( 'id'=>'3', 'order'=>'5' ),
    ); 
    $this->assertEquals($expected, $result);

    // Set all - Reset
    $ids=array(1,6,2,3,5,4);
    $this->CI->order->set_all($this->table,$ids);
    $result = $this->_result(true);
    $expected = array(
      '1'=>array( 'id'=>'1', 'order'=>'0', 'self_parent' => 0 ),
      '6'=>array( 'id'=>'6', 'order'=>'1', 'self_parent' => 0 ),
      '2'=>array( 'id'=>'2', 'order'=>'2', 'self_parent' => 0 ),
      '3'=>array( 'id'=>'3', 'order'=>'3', 'self_parent' => 2 ),
      '5'=>array( 'id'=>'5', 'order'=>'4', 'self_parent' => 2 ),
      '4'=>array( 'id'=>'4', 'order'=>'5', 'self_parent' => 0 ),
    ); 
    $this->assertEquals($expected, $result);

    // Set 1 => 5 (geen kinderen)
    $new = $this->CI->order->set($this->table,1,5);
    $this->assertEquals( 5, $new );
    $result = $this->_result(true);
    $expected = array(
      '6'=>array( 'id'=>'6', 'order'=>'0', 'self_parent' => 0 ),
      '2'=>array( 'id'=>'2', 'order'=>'1', 'self_parent' => 0 ),
      '3'=>array( 'id'=>'3', 'order'=>'2', 'self_parent' => 2 ),
      '5'=>array( 'id'=>'5', 'order'=>'3', 'self_parent' => 2 ),
      '4'=>array( 'id'=>'4', 'order'=>'4', 'self_parent' => 0 ),
      '1'=>array( 'id'=>'1', 'order'=>'5', 'self_parent' => 0 ),
    );
    $this->assertEquals($expected, $result);

    // Set 4 => 0
    $new = $this->CI->order->set($this->table,4,0);
    $this->assertEquals( 0, $new );
    $result = $this->_result(true);
    $expected = array(
      '4'=>array( 'id'=>'4', 'order'=>'0', 'self_parent' => 0 ),
      '6'=>array( 'id'=>'6', 'order'=>'1', 'self_parent' => 0 ),
      '2'=>array( 'id'=>'2', 'order'=>'2', 'self_parent' => 0 ),
      '3'=>array( 'id'=>'3', 'order'=>'3', 'self_parent' => 2 ),
      '5'=>array( 'id'=>'5', 'order'=>'4', 'self_parent' => 2 ),
      '1'=>array( 'id'=>'1', 'order'=>'5', 'self_parent' => 0 ),
    );
    $this->assertEquals($expected, $result);
    
    // Set 2 => 0 (met kinderen)
    $new = $this->CI->order->set($this->table,2,0);
    $this->assertEquals( 0, $new );
    $result = $this->_result(true);
    $expected = array(
      '2'=>array( 'id'=>'2', 'order'=>'0', 'self_parent' => 0 ),
      '3'=>array( 'id'=>'3', 'order'=>'1', 'self_parent' => 2 ),
      '5'=>array( 'id'=>'5', 'order'=>'2', 'self_parent' => 2 ),
      '4'=>array( 'id'=>'4', 'order'=>'3', 'self_parent' => 0 ),
      '6'=>array( 'id'=>'6', 'order'=>'4', 'self_parent' => 0 ),
      '1'=>array( 'id'=>'1', 'order'=>'5', 'self_parent' => 0 ),
    );
    $this->assertEquals($expected, $result);

    // Set 2 => 3 (met kinderen)
    $new = $this->CI->order->set($this->table,2,3);
    $this->assertEquals( 3, $new );
    $result = $this->_result(true);
    $expected = array(
      '4'=>array( 'id'=>'4', 'order'=>'0', 'self_parent' => 0 ),
      '6'=>array( 'id'=>'6', 'order'=>'1', 'self_parent' => 0 ),
      '1'=>array( 'id'=>'1', 'order'=>'2', 'self_parent' => 0 ),
      '2'=>array( 'id'=>'2', 'order'=>'3', 'self_parent' => 0 ),
      '3'=>array( 'id'=>'3', 'order'=>'4', 'self_parent' => 2 ),
      '5'=>array( 'id'=>'5', 'order'=>'5', 'self_parent' => 2 ),
    );
    $this->assertEquals($expected, $result);

    // Set 1 => 4 (wordt een kind)
    $new = $this->CI->order->set($this->table,1,4);
    $this->assertEquals( 4, $new );
    $result = $this->_result(true);
    $expected = array(
      '4'=>array( 'id'=>'4', 'order'=>'0', 'self_parent'=>0 ),
      '6'=>array( 'id'=>'6', 'order'=>'1', 'self_parent'=>0 ),
      '2'=>array( 'id'=>'2', 'order'=>'2', 'self_parent'=>0 ),
      '3'=>array( 'id'=>'3', 'order'=>'3', 'self_parent'=>2 ),
      '1'=>array( 'id'=>'1', 'order'=>'4', 'self_parent'=>2 ),
      '5'=>array( 'id'=>'5', 'order'=>'5', 'self_parent'=>2 ),
    );
    $this->assertEquals($expected, $result);

    // Set 1 => 1 (wordt geen kind)
    $new = $this->CI->order->set($this->table,1,1);
    $this->assertEquals( 1, $new );
    $result = $this->_result(true);
    $expected = array(
      '4'=>array( 'id'=>'4', 'order'=>'0', 'self_parent'=>0 ),
      '1'=>array( 'id'=>'1', 'order'=>'1', 'self_parent'=>0 ),
      '6'=>array( 'id'=>'6', 'order'=>'2', 'self_parent'=>0 ),
      '2'=>array( 'id'=>'2', 'order'=>'3', 'self_parent'=>0 ),
      '3'=>array( 'id'=>'3', 'order'=>'4', 'self_parent'=>2 ),
      '5'=>array( 'id'=>'5', 'order'=>'5', 'self_parent'=>2 ),
    );
    $this->assertEquals($expected, $result);

    // Set 1 => 3 (word een kind)
    $new = $this->CI->order->set($this->table,1,3);
    $this->assertEquals( 3, $new );
    $result = $this->_result(true);
    $expected = array(
      '4'=>array( 'id'=>'4', 'order'=>'0', 'self_parent'=>0 ),
      '6'=>array( 'id'=>'6', 'order'=>'1', 'self_parent'=>0 ),
      '2'=>array( 'id'=>'2', 'order'=>'2', 'self_parent'=>0 ),
      '1'=>array( 'id'=>'1', 'order'=>'3', 'self_parent'=>2 ),
      '3'=>array( 'id'=>'3', 'order'=>'4', 'self_parent'=>2 ),
      '5'=>array( 'id'=>'5', 'order'=>'5', 'self_parent'=>2 ),
    );
    $this->assertEquals($expected, $result);

    // Set 2 => 0 (met alle kinderen)
    $new = $this->CI->order->set($this->table,2,0);
    $this->assertEquals( 0, $new );
    $result = $this->_result(true);
    $expected = array(
      '2'=>array( 'id'=>'2', 'order'=>'0', 'self_parent'=>0 ),
      '1'=>array( 'id'=>'1', 'order'=>'1', 'self_parent'=>2 ),
      '3'=>array( 'id'=>'3', 'order'=>'2', 'self_parent'=>2 ),
      '5'=>array( 'id'=>'5', 'order'=>'3', 'self_parent'=>2 ),
      '4'=>array( 'id'=>'4', 'order'=>'4', 'self_parent'=>0 ),
      '6'=>array( 'id'=>'6', 'order'=>'5', 'self_parent'=>0 ),
    );
    $this->assertEquals($expected, $result);

    // Set 2 => 1 (met alle kinderen)
    $new = $this->CI->order->set($this->table,2,1);
    $this->assertEquals( 1, $new );
    $result = $this->_result(true);
    $expected = array(
      '4'=>array( 'id'=>'4', 'order'=>'0', 'self_parent'=>0 ),
      '2'=>array( 'id'=>'2', 'order'=>'1', 'self_parent'=>0 ),
      '1'=>array( 'id'=>'1', 'order'=>'2', 'self_parent'=>2 ),
      '3'=>array( 'id'=>'3', 'order'=>'3', 'self_parent'=>2 ),
      '5'=>array( 'id'=>'5', 'order'=>'4', 'self_parent'=>2 ),
      '6'=>array( 'id'=>'6', 'order'=>'5', 'self_parent'=>0 ),
    );
    $this->assertEquals($expected, $result);



  }
    
    
  // /**
  //  * Test verschuiven
  //  *
  //  * @return void
  //  * @author Jan den Besten
  //  */
  // public function testMove() {
  //   echo "demo_db/models/OrderTest/".__METHOD__."\n";
  //
  //   // Start situatie
  //   $expected = array(
  //     '1'=>array( 'id'=>'1', 'order'=>'0' ),
  //     '2'=>array( 'id'=>'2', 'order'=>'1' ),
  //     '3'=>array( 'id'=>'3', 'order'=>'2' ),
  //     '5'=>array( 'id'=>'5', 'order'=>'3' ),
  //     '4'=>array( 'id'=>'4', 'order'=>'4' ),
  //   );
  //   $result = $this->_result();
  //   $this->assertEquals($expected, $result);
  //
  //
  //   // Move up normal (in sub)
  //   $new = $this->CI->order->move_to($this->table,5,'up');
  //   $this->assertEquals( 2, $new);
  //   $result = $this->_result();
  //   $expected = array(
  //     '1'=>array( 'id'=>'1', 'order'=>'0' ),
  //     '2'=>array( 'id'=>'2', 'order'=>'1' ),
  //     '5'=>array( 'id'=>'5', 'order'=>'2' ),
  //     '3'=>array( 'id'=>'3', 'order'=>'3' ),
  //     '4'=>array( 'id'=>'4', 'order'=>'4' ),
  //   );
  //   $this->assertEquals($expected, $result);
  //
  //
  //   // Move up (with sub)
  //   $new = $this->CI->order->move_to($this->table,2,'up');
  //   $this->assertEquals( 0, $new);
  //   $result = $this->_result();
  //   $expected = array(
  //     // '2'=>array( 'id'=>'2', 'order'=>'0' ),
  //     // '1'=>array( 'id'=>'1', 'order'=>'1' ),
  //     // '5'=>array( 'id'=>'5', 'order'=>'2' ),
  //     // '3'=>array( 'id'=>'3', 'order'=>'3' ),
  //     // '4'=>array( 'id'=>'4', 'order'=>'4' ),
  //     '2'=>array( 'id'=>'2', 'order'=>'0' ),
  //     '5'=>array( 'id'=>'5', 'order'=>'1' ),
  //     '3'=>array( 'id'=>'3', 'order'=>'2' ),
  //     '1'=>array( 'id'=>'1', 'order'=>'3' ),
  //     '4'=>array( 'id'=>'4', 'order'=>'4' ),
  //   );
  //   $this->assertEquals($expected, $result);
  //
  //
  //   // Move down
  //   // $new = $this->CI->order->move_to($this->table,5,'down');
  //   // $result = $this->_result();
  //   // $expected = array(
  //   //   '1'=>array( 'id'=>'1', 'order'=>'0' ),
  //   //   '2'=>array( 'id'=>'2', 'order'=>'1' ),
  //   //   '4'=>array( 'id'=>'4', 'order'=>'2' ),
  //   //   '5'=>array( 'id'=>'5', 'order'=>'3' ),
  //   //   '3'=>array( 'id'=>'3', 'order'=>'4' ),
  //   // );
  //   // $this->assertEquals( 3, $new);
  //   // $this->assertEquals($expected, $result);
  //   //
  //   // // Move up ^^
  //   // $new = $this->CI->order->move_to($this->table,1,'up');
  //   // $result = $this->_result();
  //   // $expected = array(
  //   //   '1'=>array( 'id'=>'1', 'order'=>'0' ),
  //   //   '2'=>array( 'id'=>'2', 'order'=>'1' ),
  //   //   '4'=>array( 'id'=>'4', 'order'=>'2' ),
  //   //   '5'=>array( 'id'=>'5', 'order'=>'3' ),
  //   //   '3'=>array( 'id'=>'3', 'order'=>'4' ),
  //   // );
  //   // $this->assertEquals( 0, $new);
  //   // $this->assertEquals($expected, $result);
  //   //
  //   // // Move down vv
  //   // $new = $this->CI->order->move_to($this->table,3,'down');
  //   // $result = $this->_result();
  //   // $expected = array(
  //   //   '1'=>array( 'id'=>'1', 'order'=>'0' ),
  //   //   '2'=>array( 'id'=>'2', 'order'=>'1' ),
  //   //   '4'=>array( 'id'=>'4', 'order'=>'2' ),
  //   //   '5'=>array( 'id'=>'5', 'order'=>'3' ),
  //   //   '3'=>array( 'id'=>'3', 'order'=>'4' ),
  //   // );
  //   // $this->assertEquals( 4, $new);
  //   // $this->assertEquals($expected, $result);
  //   //
  //   //
  //   // // Move TOP
  //   // $new = $this->CI->order->move_to($this->table,3,'top');
  //   // $result = $this->_result();
  //   // $expected = array(
  //   //   '3'=>array( 'id'=>'3', 'order'=>'0' ),
  //   //   '1'=>array( 'id'=>'1', 'order'=>'1' ),
  //   //   '2'=>array( 'id'=>'2', 'order'=>'2' ),
  //   //   '4'=>array( 'id'=>'4', 'order'=>'3' ),
  //   //   '5'=>array( 'id'=>'5', 'order'=>'4' ),
  //   // );
  //   // $this->assertEquals( 0, $new);
  //   // $this->assertEquals($expected, $result);
  //   //
  //   // // Move BOTTOM
  //   // $new = $this->CI->order->move_to($this->table,2,'bottom');
  //   // $result = $this->_result();
  //   // $expected = array(
  //   //   '3'=>array( 'id'=>'3', 'order'=>'0' ),
  //   //   '1'=>array( 'id'=>'1', 'order'=>'1' ),
  //   //   '4'=>array( 'id'=>'4', 'order'=>'2' ),
  //   //   '5'=>array( 'id'=>'5', 'order'=>'3' ),
  //   //   '2'=>array( 'id'=>'2', 'order'=>'4' ),
  //   // );
  //   // $this->assertEquals( 4, $new);
  //   // $this->assertEquals($expected, $result);
  //   //
  //   //
  //   // // Move to start positions
  //   // $new = $this->CI->order->move_to($this->table,1,'top');
  //   // $new = $this->CI->order->move_to($this->table,2,1);
  //   // $new = $this->CI->order->move_to($this->table,3,2);
  //   // $new = $this->CI->order->move_to($this->table,5,3);
  //   // $new = $this->CI->order->move_to($this->table,4,'bottom');
  //   //
  //   // $result = $this->_result();
  //   // $expected = array(
  //   //   '1'=>array( 'id'=>'1', 'order'=>'0' ),
  //   //   '2'=>array( 'id'=>'2', 'order'=>'1' ),
  //   //   '3'=>array( 'id'=>'3', 'order'=>'2' ),
  //   //   '5'=>array( 'id'=>'5', 'order'=>'3' ),
  //   //   '4'=>array( 'id'=>'4', 'order'=>'4' ),
  //   // );
  //   // $this->assertEquals($expected, $result);
  //
  // }


}

?>