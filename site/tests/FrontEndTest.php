<?php

class FrontEndTest extends CIUnit_Framework_TestCase {

  private $settings=array();

    protected function setUp () {
      $this->CI->load->helper('language');
      // Load basic modules
      $this->CI->load->library('Module');
      $this->CI->load->library('Ajax_module');
      $this->CI->load->library('Forms');
      $this->CI->load->library('Lorem');
      $this->CI->load->model('formaction');
      $this->CI->config->load('unittest');
    }
    
    
    /**
     * Test of er nog debughelper commando's zijn
     *
     * @return void
     * @author Jan den Besten
     */
    public function test_debug_code() {
      if ($this->CI->config->item('check_if_debug_code')) {
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
      else {
        $this->assertTrue(true);
      }
    }
    


    /**
     * Test (ajax)modules
     */
    public function test_modules()  {
      $page=$this->CI->config->item('page');
      $modules = $this->CI->config->item('modules');
      
      foreach ($modules as $name => $info) {
        $this->CI->load->library($name);
        // calls
        foreach ($info as $call => $assert) {
          $module=remove_suffix($call,'.');
          $method=get_suffix($call,'.');
          if (empty($method) or $method==$module) $method='index';
          // assert##( result , call($page) )
          $result=array_pop($assert);
          $assert=$assert[0];
          $this->$assert( $result, $this->CI->$module->$method($page) );
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
      
      $formactions=$this->CI->config->item('formactions');

      $error_reporting=error_reporting();
      error_reporting(0);
      foreach ($formactions as $formaction => $info) {

        $this->CI->load->model($formaction);
        $this->CI->$formaction->initialize($info['settings']);
        $this->CI->$formaction->fields($info['fields']);
        $data=$this->_random_form_data($info['fields']);
        $result=$this->CI->$formaction->go($data);
        
        $this->assertGreaterThanOrEqual(1,$result, el('message',$info,'Formaction `'.$formaction.'` did not give success as result.') );
      }
      error_reporting($error_reporting);

    }
    
    private function _random_form_data($fields) {
      $data=array();
      
      foreach ($fields as $name => $info) {
        $type=el('type',$info,get_prefix($name,'_'));
        $value='';
        switch ($type) {
          
          case 'b':
            $value=false;
            if (rand(0,1)==1) $value=true;
            break;
          
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