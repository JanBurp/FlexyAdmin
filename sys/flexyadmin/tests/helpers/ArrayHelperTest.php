<?php

class ArrayHelperTest extends CIUnit_Framework_TestCase
{

    protected function setUp ()
    {
        $this->get_instance()->load->helper('array');
    }
    
    
    public function testEl() {
      $array = array(
        'color' => 'red',
        'shape' => 'round',
        'radius' => '10',
        'diameter' => '20',
        'more'    => array(
          'more_one' =>1,
          'more_two' =>2,
        ),
      );
      
      $this->assertArrayHasKey('color', $array); 
      $this->assertEquals( 'red' ,el('color', $array)); 
      $this->assertEquals( 'round', el('shape',$array,'default')); 

      $this->assertEquals( 'default', el('nop',$array,'default')); 
      $this->assertNull( el('nop', $array));
      
      $this->assertEquals( 1, el(array('more','more_one'), $array));
      $this->assertEquals( 2, el(array('more','more_two'), $array));
      $this->assertNull( el(array('more','more_three'), $array));
      $this->assertEquals( 'default', el(array('more','more_three'), $array,'default'));
      
      
      $config=array(
        'contact' => array(),
        'upload_demo' => array(),
        '__return.contact' => ''
       );
       
       $this->assertEquals( array(), el('contact', $config));
       $this->assertEquals( '', el('__return.contact', $config));
       $this->assertEquals( '', el('__return.contact', $config, 'page'));
      
    }



    public function testElement ()
    { 
        $array = array(
                'color' => 'red',
                'shape' => 'round',
                'size' => ''
        ); 
        $expected = "red";
         
        $this->assertArrayHasKey('color', $array); 
        $this->assertEquals($expected, element('color', $array)); 
        $this->assertEquals('', element('size',$array,'default')); 
        $this->assertEquals('default', element('nop',$array,'default')); 
        $this->assertNull(element('age', $array));
    }

    public function testElements ()
    {
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
    
    public function testRandomElement()
    {
        $quotes = array(
                "I find that the harder I work, the more luck I seem to have. - Thomas Jefferson",
                "Don't stay in bed, unless you can make money in bed. - George Burns",
                "We didn't lose the game; we just ran out of time. - Vince Lombardi",
                "If everything seems under control, you're not going fast enough. - Mario Andretti",
                "Reality is merely an illusion, albeit a very persistent one. - Albert Einstein",
                "Chance favors the prepared mind - Louis Pasteur"
        );
        
        $this->assertNotEquals(random_element($quotes), random_element($quotes));
    }
}

?>