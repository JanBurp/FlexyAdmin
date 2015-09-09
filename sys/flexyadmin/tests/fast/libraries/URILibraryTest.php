<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class URILibraryTest extends CITestCase {

  private $uri;
  
  protected function setUp ()  {
    $this->CI->uri->set_uri('_unittest/LibrariesSuite');
  }
  
  public function testSegment () {

    $segmentOne = "_unittest";
    $segmentTwo = "LibrariesSuite";
    
    $this->assertEquals($segmentOne, $this->CI->uri->segment(1));
    $this->assertEquals($segmentTwo, $this->CI->uri->segment(2));
    $this->assertNull($this->CI->uri->segment(4));
  }

  public function testSlashSegment() {
    $this->assertEquals('_unittest/', $this->CI->uri->slash_segment(1));
    $this->assertEquals('/_unittest', $this->CI->uri->slash_segment(1, 'leading'));
    $this->assertEquals('/_unittest/', $this->CI->uri->slash_segment(1, 'both'));
  }

  public function testUriToAssoc() {
    $array = array( '_unittest' => 'LibrariesSuite' );
    $this->assertEquals($array, $this->CI->uri->uri_to_assoc(1));
  }

  public function testAssocToUri() {
    $array = array(
      '_unittest' => 'URILibraryTest'
    );
    $uri = "_unittest/URILibraryTest";
    $this->assertEquals($uri, $this->CI->uri->assoc_to_uri($array));
  }

  public function testUriString() {
    
    $uriAsString = "_unittest/LibrariesSuite";
    $this->assertEquals($uriAsString, $this->CI->uri->uri_string());
  }

  public function testTotalSegments() {
    
    $totalSegments = 2;
    $this->assertEquals($totalSegments, $this->CI->uri->total_segments());
  }

  public function testSegmentArray() {
    
    $segmentsArray = array(
      1 => '_unittest',
      2 => 'LibrariesSuite',
    );
    $this->assertEquals($segmentsArray, $this->CI->uri->segment_array());
  }
}

?>