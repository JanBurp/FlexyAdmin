<?php

require_once(APPPATH.'/tests/CITestCase.php');

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
  
  
  // public function testFilter_by_prefix() {
  //   $array = array('id','str_title','id_link','txt_text','id_example');
  //   $this->assertEquals( array(
  //       'id_link',
  //       'id_example',
  //     ), filter_by_prefix( $array,'id' ) );
  // }
  
  public function testArrayAddAfter() {
    $array = array( 'first','second','third','fourth' );
    
    // Normal
    // (bijna) Begin
    $new = array_add_after($array, 'first', 'between' );
    $expected = array( 'first','between','second','third','fourth' );
    $this->assertEquals( $expected, $new);

    // Tussenin
    $new = array_add_after($array,'second','between' );
    $expected = array( 'first','second','between','third','fourth' );
    $this->assertEquals( $expected, $new);

    // Eind
    $new = array_add_after($array,'fourth','between' );
    $expected = array( 'first','second','third','fourth','between' );
    $this->assertEquals( $expected, $new);

    // Array
    $new = array_add_after($array,'third',array('between','extra') );
    $expected = array( 'first','second','third',array('between','extra'),'fourth' );
    $this->assertEquals( $expected, $new);
    // assoc Array
    $new = array_add_after($array,'third',array('between'=>'value') );
    $expected = array( 'first','second','third',array('between'=>'value'),'fourth' );
    $this->assertEquals( $expected, $new);


    // Assoc
    $array = array_combine($array,$array);

    // (bijna) Begin
    $new = array_add_after($array,'first',array('between'=>'between') );
    $expected = array( 'first','between','second','third','fourth' );
    $this->assertEquals( array_combine($expected,$expected), $new);

    // Tussenin
    $new = array_add_after($array,'second',array('between'=>'between') );
    $expected = array( 'first','second','between','third','fourth' );
    $this->assertEquals( array_combine($expected,$expected), $new);

    // Eind
    $new = array_add_after($array,'fourth',array('between'=>'between') );
    $expected = array( 'first','second','third','fourth','between' );
    $this->assertEquals( array_combine($expected,$expected), $new);

  }


  public function testArrayAddBefore() {
    $array = array( 'first','second','third','fourth' );
    
    // Normaal

    // Begin
    $new = array_add_before($array,'first','between' );
    $expected = array( 'between','first','second','third','fourth' );
    $this->assertEquals( $expected, $new);

    // Tussenin
    $new = array_add_before($array,'second','between' );
    $expected = array( 'first','between','second','third','fourth' );
    $this->assertEquals( $expected, $new);

    // (bijna) Eind
    $new = array_add_before($array,'fourth','between' );
    $expected = array( 'first','second','third','between','fourth' );
    $this->assertEquals( $expected, $new);

    // Array
    $new = array_add_before($array,'fourth',array('between','extra') );
    $expected = array( 'first','second','third',array('between','extra'),'fourth' );
    $this->assertEquals( $expected, $new);
    // assoc Array
    $new = array_add_before($array,'fourth',array('between'=>'value') );
    $expected = array( 'first','second','third',array('between'=>'value'),'fourth' );
    $this->assertEquals( $expected, $new);


    // Assoc
    $array = array_combine($array,$array);

    // Begin
    $new = array_add_before($array,'first',array('between'=>'between') );
    $expected = array( 'between','first','second','third','fourth' );
    $this->assertEquals( array_combine($expected,$expected), $new);

    // Tussenin
    $new = array_add_before($array,'second',array('between'=>'between') );
    $expected = array( 'first','between','second','third','fourth' );
    $this->assertEquals( array_combine($expected,$expected), $new);

    // (bijna) Eind
    $new = array_add_before($array,'fourth',array('between'=>'between') );
    $expected = array( 'first','second','third','between','fourth' );
    $this->assertEquals( array_combine($expected,$expected), $new);
  }

}

?>