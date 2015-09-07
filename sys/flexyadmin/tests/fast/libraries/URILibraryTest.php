<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class URILibraryTest extends CITestCase {

  private $uri;
  
  protected function setUp ()  {
    $this->CI->uri->set_uri('_unittest/LibrariesSuite');
  }
  
  public function testSegment () {
    echo "fast/libraries/URILibraryTest/".__METHOD__."\n";

    $segmentOne = "_unittest";
    $segmentTwo = "LibrariesSuite";
    
    $this->assertEquals($segmentOne, $this->CI->uri->segment(1));
    $this->assertEquals($segmentTwo, $this->CI->uri->segment(2));
    $this->assertNull($this->CI->uri->segment(4));
  }

  public function testSlashSegment() {
    echo "fast/libraries/URILibraryTest/".__METHOD__."\n";
    $this->assertEquals('_unittest/', $this->CI->uri->slash_segment(1));
    $this->assertEquals('/_unittest', $this->CI->uri->slash_segment(1, 'leading'));
    $this->assertEquals('/_unittest/', $this->CI->uri->slash_segment(1, 'both'));
  }

  public function testUriToAssoc() {
    echo "fast/libraries/URILibraryTest/".__METHOD__."\n";
    $array = array( '_unittest' => 'LibrariesSuite' );
    $this->assertEquals($array, $this->CI->uri->uri_to_assoc(1));
  }

  public function testAssocToUri() {
    echo "fast/libraries/URILibraryTest/".__METHOD__."\n";
    $array = array(
      '_unittest' => 'URILibraryTest'
    );
    $uri = "_unittest/URILibraryTest";
    $this->assertEquals($uri, $this->CI->uri->assoc_to_uri($array));
  }

  public function testUriString() {
    echo "fast/libraries/URILibraryTest/".__METHOD__."\n";
    
    $uriAsString = "_unittest/LibrariesSuite";
    $this->assertEquals($uriAsString, $this->CI->uri->uri_string());
  }

  public function testTotalSegments() {
    echo "fast/libraries/URILibraryTest/".__METHOD__."\n";
    
    $totalSegments = 2;
    $this->assertEquals($totalSegments, $this->CI->uri->total_segments());
  }

  public function testSegmentArray() {
    echo "fast/libraries/URILibraryTest/".__METHOD__."\n";
    
    $segmentsArray = array(
      1 => '_unittest',
      2 => 'LibrariesSuite',
    );
    $this->assertEquals($segmentsArray, $this->CI->uri->segment_array());
  }
}

?>