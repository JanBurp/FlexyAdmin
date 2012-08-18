<?php


/**
 * undocumented class
 *
 * @package default
 * @author Jan den Besten
 * @link http://www.simpletest.org/en/web_tester_documentation.html
 */
class userguide extends CodeIgniterWebTestCase {

  var $browser;

  var $root='file://localhost/Users/jan/Sites/FlexyAdmin/FlexyAdminDEMO/';
  var $index=array();
  var $linksTable=array();
  var $links=array();

  public function __construct() {
    $this->browser = new SimpleBrowser();
  }

  public function setUp() {
    $toc=read_file('/Users/Jan/Sites/FlexyAdmin/FlexyAdminDEMO/userguide/FlexyAdmin/assets/js/toc.js');
    preg_match_all("/.*{([^;]*?)};/uUsm", $toc,$matches);
    $jsonIndex='{'.substr($matches[1][0],0,-3).'}';
    $this->index=json_decode($jsonIndex,true);
    
    $jsonLinks='{'.substr($matches[1][2],0,-3).'}';
    $this->linksTable=json_decode($jsonLinks,true);
  }

  public function tearDown() {
  }

  public function test_userguide() {
    $this->_show_head('Test Userguide');

    foreach ($this->index as $url => $value) {
      if ($url!='|') {
        $this->_menu_item($url);
      }
    }
  }

  private function _menu_item( $uri ) {
    $this->_show_item($uri);
    $this->assertTrue( $this->get($this->root.$uri) );
    $this->assertNoPattern( "/NOGDOEN/", 'NOGDOEN' );
    $this->_find_links( $this->root.$uri );
  }

  private function _find_links( $uri ) {
    $this->browser->get( $uri );
    $this->links=array_merge( $this->links, $this->browser->getUrls() );
  }

  public function test_links() {
    $this->_show_head('Links');
    $this->links=array_unique( $this->links );
    $internal=array();
    $external=array();
    foreach ( $this->links as $link ) {
      if (!has_string('#',$link)) {
        if ( has_string( $this->root, $link ) )
          $internal[]=$link;
        else
          $external[]=$link;
      }
    }
    
    $this->_show_head('Internal links:',2);
    $list=array();
    foreach ($internal as $url) {
      if (preg_match("/{(.*)?}/uUsm", $url, $match)) {
        // replace form table
        $key=str_replace('-',' ',$match[1]);
        if (isset($this->linksTable[$key])) {
          $url=$this->root.'userguide/FlexyAdmin/'.$this->linksTable[$key];
        }
        else {
          $this->assertTrue(isset($this->linksTable[$key]),' WRONG LINK: "'.$match[0].'"');
          unset($internal[$url]);
        }
      }
      $list[]='<a href="'.$url.'" target="_blank">'.$url.'</a>';
    }
    echo ul($list);

    
    $this->_show_head('External links:',2);
    $list=array();
    foreach ($external as $url) {
      // $this->assertTrue( $this->get( $url ) );
      // $this->assertNoPattern("/id=\"error404\">/",'NOT FOUND: '.$url.' ---- ');
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
