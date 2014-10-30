<?php

class URILibraryTest extends CIUnit_Framework_TestCase
{

    private $uri;
    private $suite=false;

    protected function setUp ()
    {
        $this->get_instance()->load->library('uri');
        $this->uri = get_instance()->uri;
        $this->suite=($this->uri->total_segments()==2);
    }


    // Test pass when default routing configuration is used 
    // example: http://www.example.com/index.php/_unittest/URILibraryTest or
    // http://www.example.com/_unittest/URILibraryTest
    
    public function testSegment ()
    {
        $segmentOne = "_unittest";
        $segmentTwo = "LibrariesSuite";
        
        $this->assertEquals($segmentOne, $this->uri->segment(1));
        $this->assertEquals($segmentTwo, $this->uri->segment(2));
        $this->assertFalse($this->uri->segment(4));
    }

    public function testSlashSegment ()
    { 

        $this->assertEquals('_unittest/', $this->uri->slash_segment(1));
        $this->assertEquals('/_unittest', $this->uri->slash_segment(1, 'leading'));
        $this->assertEquals('/_unittest/', $this->uri->slash_segment(1, 'both'));
    }

    public function testUriToAssoc ()
    {
        $array = array( '_unittest' => 'LibrariesSuite' );
        if (!$this->suite) $array['URILibraryTest']='';
        $uri = "index.php/_unittest/LibrariesSuite";
        
        $this->assertEquals($array, $this->uri->uri_to_assoc(1));
    }

    public function testAssocToUri ()
    {
        $array = array(
                '_unittest' => 'URILibraryTest'
        );
        $uri = "_unittest/URILibraryTest";
        $this->assertEquals($uri, $this->uri->assoc_to_uri($array));
    }

    public function testUriString ()
    {
        $uriAsString = "/_unittest/LibrariesSuite";
        if (!$this->suite) $uriAsString.='/URILibraryTest';
        $this->assertEquals($uriAsString, $this->uri->uri_string());
    }

    public function testTotalSegments ()
    {
      if ($this->suite)
        $totalSegments = 2;
      else
        $totalSegments = 3;
        
      $this->assertEquals($totalSegments, $this->uri->total_segments());
    }

    public function testSegmentArray ()
    {
        $segmentsArray = array(
                1 => '_unittest',
                2 => 'LibrariesSuite',
        );
        if (!$this->suite) $segmentsArray[3]='URILibraryTest';
        
        $this->assertEquals($segmentsArray, $this->uri->segment_array());
    }
}

?>