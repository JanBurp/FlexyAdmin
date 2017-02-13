<?php

require_once(APPPATH.'/tests/CITestCase.php');


class ReplaceTest extends CITestCase {
  
  protected function setUp ()  {
    $this->CI->load->model('search_replace','SR');
  }
  
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
    
    // 2e situatie
    $result = $this->CI->SR->media('test_01.jpg','test_11.jpg');

    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('test_01.jpg');
    $found = $this->CI->data->get_result();
    $this->assertEquals(0, count($found));

    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('test_11.jpg');
    $found = $this->CI->data->get_result();
    $this->assertEquals(3, count($found));

    // 3e situatie
    $this->CI->SR->media('test_11.jpg','test_01.jpg');
    
    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('test_01.jpg');
    $found = $this->CI->data->get_result();
    $this->assertEquals(3, count($found));
    
    // 4e situatie - verwijderd
    $this->CI->SR->media('test_01.jpg','');

    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('test_01.jpg');
    $found = $this->CI->data->get_result();
    $this->assertEquals(0, count($found));

    $this->CI->data->table('tbl_menu');
    $this->CI->data->like('<img src="_media/pictures/test_01.jpg" alt="test_01" />');
    $found = $this->CI->data->get_result();
    $this->assertEquals(0, count($found));

    

  }
  
  public function test_links() {
    
    // Begin situatie
    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('mailto:info@flexyadmin.com');
    $found = $this->CI->data->get_result();
    $this->assertEquals(1, count($found));
    
    $this->CI->data->table('tbl_links')->update( array('url_url'=>'mailto:test@flexyadmin.com'), 7);
    // $this->CI->SR->links('mailto:info@flexyadmin.com','mailto:test@flexyadmin.com');
    
    // 2e situatie
    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('mailto:info@flexyadmin.com');
    $found = $this->CI->data->get_result();
    $this->assertEquals(0, count($found));

    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('mailto:test@flexyadmin.com');
    $found = $this->CI->data->get_result();
    $this->assertEquals(1, count($found));
    
    // 3e situatie - verwijderd
    $this->CI->data->table('tbl_links')->delete( 7 );
    // $this->CI->SR->links('mailto:test@flexyadmin.com','');
    
    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('mailto:info@flexyadmin.com');
    $found = $this->CI->data->get_result();
    $this->assertEquals(0, count($found));

    $this->CI->data->table('tbl_menu');
    $this->CI->data->find('mailto:test@flexyadmin.com');
    $found = $this->CI->data->get_result();
    $this->assertEquals(0, count($found));
    
  }
  
  

}

?>