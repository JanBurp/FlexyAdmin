<?php

class URILibraryTest extends CIUnit_Framework_TestCase
{

    private $uri;
    private $suite=false;

    protected function setUp ()
    {
        $this->suite=($this->CI->uri->total_segments()==2);
    }


    // Test pass when default routing configuration is used 
    // example: http://www.example.com/index.php/_unittest/URILibraryTest or
    // http://www.example.com/_unittest/URILibraryTest
    
    public function testSegment ()
    {
        $segmentOne = "_unittest";
        $segmentTwo = "LibrariesSuite";
        
        $this->assertEquals($segmentOne, $this->CI->uri->segment(1));
        $this->assertEquals($segmentTwo, $this->CI->uri->segment(2));
        // $this->assertFalse($this->CI->uri->segment(4));
    }

    public function testSlashSegment ()
    { 

        $this->assertEquals('_unittest/', $this->CI->uri->slash_segment(1));
        $this->assertEquals('/_unittest', $this->CI->uri->slash_segment(1, 'leading'));
        $this->assertEquals('/_unittest/', $this->CI->uri->slash_segment(1, 'both'));
    }

    public function testUriToAssoc ()
    {
        $array = array( '_unittest' => 'LibrariesSuite' );
        if (!$this->suite) $array['URILibraryTest']='';
        $uri = "index.php/_unittest/LibrariesSuite";
        
        $this->assertEquals($array, $this->CI->uri->uri_to_assoc(1));
    }

    public function testAssocToUri ()
    {
        $array = array(
                '_unittest' => 'URILibraryTest'
        );
        $uri = "_unittest/URILibraryTest";
        $this->assertEquals($uri, $this->CI->uri->assoc_to_uri($array));
    }

    public function testUriString ()
    {
        $uriAsString = "_unittest/LibrariesSuite";
        if (!$this->suite) $uriAsString.='/URILibraryTest';
        $this->assertEquals($uriAsString, $this->CI->uri->uri_string());
    }

    public function testTotalSegments ()
    {
      if ($this->suite)
        $totalSegments = 2;
      else
        $totalSegments = 3;
        
      $this->assertEquals($totalSegments, $this->CI->uri->total_segments());
    }

    public function testSegmentArray ()
    {
        $segmentsArray = array(
                1 => '_unittest',
                2 => 'LibrariesSuite',
        );
        if (!$this->suite) $segmentsArray[3]='URILibraryTest';
        
        $this->assertEquals($segmentsArray, $this->CI->uri->segment_array());
    }
}

?>