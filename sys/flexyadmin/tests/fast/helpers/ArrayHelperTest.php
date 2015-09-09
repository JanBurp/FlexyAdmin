<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class ArrayHelperTest extends CITestCase {

  protected function setUp() {
    $this->CI->load->helper('array');
  }

  public function testElement() { 
    
    $array = array(
        'color' => 'red',
        'shape' => 'round',
        'size' => ''
    ); 
    $expected = "red";
     
    $this->assertArrayHasKey('color', $array); 
    $this->assertEquals($expected, element('color', $array)); 
    $this->assertEquals('',element('size', $array)); 
    $this->assertNull(element('age', $array, NULL));
  }

  public function testElements() {
    
    $array = array(
        'color' => 'red',
        'shape' => 'round',
        'radius' => '10',
        'diameter' => '20'
    );
    
    $expected = array(
        'color' => 'red',
        'shape' => 'round',
        'height' => FALSE
    );
    
    $this->assertEquals($expected, 
        elements(array(
            'color',
            'shape',
            'height'
        ), $array));
  }
  
  
  // public function testRandomElement() {
  //   $quotes = array(
  //       "I find that the harder I work, the more luck I seem to have. - Thomas Jefferson",
  //       "Don't stay in bed, unless you can make money in bed. - George Burns",
  //       "We didn't lose the game; we just ran out of time. - Vince Lombardi",
  //       "If everything seems under control, you're not going fast enough. - Mario Andretti",
  //       "Reality is merely an illusion, albeit a very persistent one. - Albert Einstein",
  //       "Chance favors the prepared mind - Louis Pasteur",
  //       "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.",
  //       "At vero eos et accusam et justo duo dolores et ea rebum.",
  //       "Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.",
  //       "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.",
  //       "At vero eos et accusam et justo duo dolores et ea rebum.",
  //       "Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.",
  //       "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.",
  //       "At vero eos et accusam et justo duo dolores et ea rebum.",
  //       "Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.",
  //   );
  //
  //   $this->assertNotEquals(random_element($quotes), random_element($quotes));
  // }

}

?>