<?php


/**
 * undocumented class
 *
 * @package default
 * @author Jan den Besten
 * @link http://www.simpletest.org/en/web_tester_documentation.html
 */
class site extends CodeIgniterWebTestCase {

  var $browser;

  var $root='http://localhost/FlexyAdmin/FlexyAdminDEMO/';
  var $menu;
  var $links=array();

  public function __construct() {
    $this->browser = new SimpleBrowser();
  }

  public function setUp() {
    $menu = new Menu();
    $menu->set_current( '' );
    $menu->set_menu_from_table();
    $this->menu=$menu->menu;
  }

  public function tearDown() {
  }

  public function test_menu() {
    $this->_show_head('Test Menu &amp; content');
    $this->_menu( $this->menu );
  }

  private function _menu( $menu, $parentUri='' ) {
    foreach ( $menu as $page ) {
      $uri=$page['uri'];
      $this->_menu_item( $uri, $parentUri );
      if ( isset( $page['sub'] ) ) {
        $this->_menu( $page['sub'], $uri );
      }
    }
  }

  private function _menu_item( $uri, $parentUri='', $userguide=FALSE ) {
    if ( !empty( $parentUri ) ) $uri=$parentUri.'/'.$uri;
    $this->_show_item($uri);
    // $this->setMaximumRedirects(0);
    $this->assertTrue( $this->get( $this->root.$uri ) );
    // $this->assertResponse('200');
    // $this->assertNoPattern("/id=\"error404\">/",'404');
    $this->assertNoPattern( "/<div class=\"FlexyAdminTrace\"/", 'TRACE_?' );
    $this->assertNoPattern( "/id=\"fallback_module\"/", 'FALLBACK MODULE' );

    $this->_find_links( $uri );
  }

  private function _find_links( $uri ) {
    $this->browser->get( $this->root.$uri );
    $this->links=array_merge( $this->links, $this->browser->getUrls() );
  }

  public function test_links() {
    $this->_show_head('Links');
    $this->links=array_unique( $this->links );
    $internal=array();
    $external=array();
    foreach ( $this->links as $link ) {
      if ( has_string( $this->root, $link ) )
        $internal[]=$link;
      else
        $external[]=$link;
    }
    
    $this->_show_head('External links:',2);
    $list=array();
    foreach ($external as $url) {
      // $this->assertTrue( $this->get( $url ) );
      // $this->assertNoPattern("/id=\"error404\">/",'NOT FOUND: '.$url.' ---- ');
      $list[]='<a href="'.$url.'" target="_blank">'.$url.'</a>';
    }
    echo ul($list);

    $this->_show_head('Internal links:',2);
    $list=array();
    foreach ($internal as $url) {
      $this->assertTrue( $this->get( $url ) );
      $this->assertNoPattern("/id=\"error404\">/",'NOT FOUND: '.$url.' ---- ');
      $list[]='<a href="'.$url.'" target="_blank">'.$url.'</a>';
    }
    echo ul($list);

  }


  private function _show_head($head,$h=1) {
    echo '<h'.$h.'>'.$head.'</h'.$h.'>';
  }

  private function _show_item($head) {
    echo '<div class="main"><h3>'.$head.'</h3></div>';
  }



}
