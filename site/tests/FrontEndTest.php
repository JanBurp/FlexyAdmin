<?php

class FrontEndTest extends CIUnit_Framework_TestCase {

    protected function setUp () {
      $this->CI->load->helper('language');
      // Load basic modules
      $this->CI->load->library('Module');
      $this->CI->load->library('Ajax_module');
      $this->CI->load->library('Forms');
      $this->CI->load->library('Lorem');
      $this->CI->load->model('formaction');
    }

    public function test_modules()  {
      $page=array(
        'id'        => 1,
        'uri'       => 'home',
        'str_title' => 'Test',
        'txt_text'  => '<h1>Test</h1>',
      );

      // Example module
      $this->CI->load->library('Example');
      $this->assertEquals( '<h1>Example Module</h1>', $this->CI->example->index($page) );
      $this->assertEquals( '<h1>Example Module.Other</h1>', $this->CI->example->other($page) );
      
      // Ajax example module
      $this->CI->load->library('Ajax_example');
      $this->assertEquals( '{"_message":"Ajax_example","_module":"example","_success":true}', $this->CI->ajax_example->index($page) );
      $this->assertEquals( '{"_message":"Ajax_example","_method":"other","_module":"example","_success":true}', $this->CI->ajax_example->other($page) );
    }


    /**
     * Test of er nog debughelper commando's zijn
     *
     * @return void
     * @author Jan den Besten
     */
    public function test_debug_code() {
      $files=read_map('site','php',true,false,false,false);
      unset($files['sys/flexyadmin/helpers/debug_helper.php']);
      foreach ($files as $file) {
        $lines=file($file['path']);
        foreach ($lines as $key => $line) {
          $found=preg_match("/^\s*\s*(trace_|trace_if|strace_|backtrace_|xdebug_break)\(/u", $line);
          $this->assertLessThan(1,$found, 'Debug helper found in `<i><b>'.$file['path'].'</i></b>` at line '.($key+1).':<br><code>'.$line.'</code>');
        }
      }
    }
    
    
    /**
     * Test formactions
     *
     * @return void
     * @author Jan den Besten
     */
    public function test_formactions()  {
      
      $formactions = array( 
        'formaction_mail' => array(
          'settings' => array(
            'to' => 'test@flexyadmin.com',
          ),
          'fields' => array(
            'str_name'		  => array( 'label'=> 'Name', 'validation'=>'required' ),
            'email_email'	  => array( 'label'=> 'Email', 'validation'=>'required' ),
            'txt_text'	    => array( 'label'=> 'Text', 'type'=>'txt', 'validation'=>'required' ),
          ),
          'message' => 'Formaction `formaction_mail` did not give success as result. Is a Email server ready?'
        ),
      );
      

      $error_reporting=error_reporting();
      error_reporting(0);
      foreach ($formactions as $formaction => $info) {

        $this->CI->load->model($formaction);
        $this->CI->$formaction->initialize($info['settings']);
        $this->CI->$formaction->fields($info['fields']);
        $data=$this->_random_form_data($info['fields']);
        $result=$this->CI->$formaction->go($data);
        
        $this->assertTrue($result, el('message',$info,'Formaction `'.$formaction.'` did not give success as result.') );
      }
      error_reporting($error_reporting);

    }
    
    private function _random_form_data($fields) {
      $data=array();
      
      foreach ($fields as $name => $info) {
        $type=el('type',$info,get_prefix($name,'_'));
        $value='';
        switch ($type) {
          
          case 'email':
            $value=strtolower(random_string('alpha',rand(2,8)).'@'.random_string('alpha',rand(2,8)).'.'.random_string('alpha',rand(2,3)));
            break;
          
          case 'txt':
          case 'stx':
            if (rand(1,2)>1)
              $value=$this->CI->lorem->getContent(rand(50,500),'html');
            else
              $value=$this->CI->lorem->getContent(rand(10,50),'plain');
            break;
            
          case 'str':
          default:
            $value=$this->CI->lorem->getContent(rand(1,3),'plain');
            break;
        }
        $data[$name]=$value;
      }
      
      return $data;
    }




}

?>