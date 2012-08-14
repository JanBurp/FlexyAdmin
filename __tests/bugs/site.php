<?php
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

  private function _menu_item( $uri, $parentUri='' ) {
    if ( !empty( $parentUri ) ) $uri=$parentUri.'/'.$uri;
    echo "<h3>$uri</h3>";
    // $this->setMaximumRedirects(0);
    $this->assertTrue( $this->get( $this->root.$uri ) );
    // $this->assertResponse('200');
    // $this->assertNoPattern("/id=\"error404\">/",'404');
    $this->assertNoPattern( "/<div class=\"FlexyAdminTrace\"/", 'TRACES?' );

    $this->_find_links( $uri );
  }


  private function _find_links( $uri ) {
    $this->browser->get( $this->root.$uri );
    $this->links=array_merge( $this->links, $this->browser->getUrls() );
  }

  public function test_links() {
    $this->links=array_unique( $this->links );
    $internal=array();
    $external=array();
    foreach ( $this->links as $link ) {
      if ( has_string( $this->root, $link ) )
        $internal[]=$link;
      else
        $external[]=$link;
    }
    trace_( $internal );
    trace_( $external );

  }



}
