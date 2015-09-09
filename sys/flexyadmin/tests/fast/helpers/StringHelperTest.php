<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class StringHelperTest extends CITestCase {

  protected function setUp() {
    $this->CI->load->helper('string');
  }

  public function testJustTest() {
    
    $this->assertEquals(true,true);
  }
  
  public function testRepeater() {

    $actualString = "a";
    $expectedString = "aaaaaaaaaa";
    $this->assertEquals($expectedString, repeater($actualString, 10));
  }


  public function testTrimSlashes() {
    
    $actualString = "/this/that/theother/";
    $expectedString = "this/that/theother";
    $this->assertEquals($expectedString, trim_slashes($actualString));
  }

  public function testReduceDoubleSlashes() {
    
    $actualString = "http://example.com//index.php";
    $expectedString = "http://example.com/index.php";
    $this->assertEquals($expectedString, reduce_double_slashes($actualString));
  }

  public function testReduceMultiples() {
    
    $actualString = "Fred, Bill,, Joe, Jimmy";
    $expectedString = "Fred, Bill, Joe, Jimmy";
    $this->assertEquals($expectedString, reduce_multiples($actualString));
  }

  public function testQuotesToEntities() {
    
    $actualString = "Joe's \"dinner\"";
    $expectedString = "Joe&#39;s &quot;dinner&quot;";
    $this->assertEquals($expectedString, quotes_to_entities($actualString));
  }

  public function testStripQuotes() {
    
    $actualString = "Joe's \"dinner\"";
    $expectedString = "Joes dinner";
    $this->assertEquals($expectedString, strip_quotes($actualString));
  }

  public function testRandomString() {
    
    $this->assertNotEquals(random_string(), random_string());
  }

  public function testHasString() {
    
    $this->assertTrue( has_string('Test','HatsTestikidee') );
    $this->assertTrue( has_string('Test','Testikidee') );
    $this->assertTrue( has_string('Test','TestikideeTest') );
    $this->assertTrue( has_string('Test','gadfyaghTest') );
    $this->assertTrue( has_string('Test','Test') );

    $this->assertTrue( has_string('#show#','#show#<a href="txmt://open?url=file:///test.php&amp;line=100">test.php at 100</a>') );
    $this->assertFalse( has_string('Test',random_string()) );

    $this->assertTrue( has_string(array('Te','est'),'GsgiTeskhest') );

  }
  
  
  
  
}

?>