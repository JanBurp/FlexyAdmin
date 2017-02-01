<?php

require_once(APPPATH.'/tests/CITestCase.php');


class ReplaceTest extends CITestCase {
  
  protected function setUp ()  {
    $this->CI->load->model('search_replace','SR');
  }
  
  // private function _result($parent=false) {
  //   $this->CI->data->table($this->table);
  //   return $this->CI->data->get_result();
  // }

  // private function _dump_result($parent=false) {
  //   $this->CI->data->table($this->table);
  //   $this->CI->data->select('id,order,self_parent,uri');
  //   if ($parent) $this->CI->data->select('self_parent');
  //   $this->CI->data->order_by('order ASC');
  //   $result = $this->CI->data->get_result();
  //   echo "\n\n-- $this->table --\n";
  //   foreach ($result as $id => $row) {
  //     foreach ($row as $value) {
  //       echo $value.' | ';
  //     }
  //     echo "\n";
  //   }
  //   echo "\n";
  //   return $result;
  // }


  /**
   * Startsituatie testen
   *
   * @return void
   * @author Jan den Besten
   */
  public function test_media() {
    
    // Begin situatie
    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('test_01.jpg');
    $found = $this->CI->data->get_result();
    $this->assertEquals(3, count($found));
    
    $this->CI->SR->replace_all('test_01.jpg','test_11.jpg',array('txt','media','medias'));
    
    // 2e situatie
    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('test_01.jpg');
    $found = $this->CI->data->get_result();
    $this->assertEquals(0, count($found));
    
    $this->CI->SR->replace_all('test_11.jpg','test_01.jpg',array('txt','media','medias'));
    
    // 3e situatie
    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('test_01.jpg');
    $found = $this->CI->data->get_result();
    $this->assertEquals(3, count($found));

    
    
    
    


  }
  
  

}

?>