<?php

class LinksTest extends CIUnit_Framework_TestCase {

  protected function setUp () {
    $this->CI->load->library('Menu');
  }
    
    
    
  /**
   * Test of links op de pagina's wel werken
   *
   * @return void
   * @author Jan den Besten
   */
  public function test_links() {
    $menu = new Menu();
    $menu->set_menu_from_table();
    $pages = $menu->get_items();
    $this->_test_links_on_pages($menu,$pages);
  }
  private function _test_links_on_pages($menu,$pages) {
    foreach ($pages as $uri => $page) {
      $item=$menu->get_item($uri);
      $item=filter_by_key($item,'txt');
      $txt=current($item);
      $matches=array();
      if (preg_match_all("/a[\s]+[^>]*?href[\s]?=[\s\"\']+(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/", $txt, $matches)) {
        if (isset($matches[1])) {
          $links=$matches[1];
          foreach ($links as $url) {
            $test=test_url($url);
            $this->assertTrue( $test, $url .' verwijst niet goed door, of heeft geen goed emailadres, op pagina `'.$uri.'`' );
          }
        }
      }
      // subpages?
      if (isset($page['sub'])) {
        $this->_test_links_on_pages($menu,$page['sub']);
      }
    }
  }

}

?>