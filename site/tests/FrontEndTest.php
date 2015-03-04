<?php

class FrontEndTest extends CIUnit_Framework_TestCase {

  private $settings=array();

    protected function setUp () {
      $this->CI->load->model('cfg');
      $this->CI->load->helper('language');
      // Load basic modules
      $this->CI->load->library('Menu');
      $this->CI->load->library('Forms');
      $this->CI->load->library('Email');
      $this->CI->load->library('Module');
      $this->CI->load->library('Ajax_module');
      $this->CI->load->model('formaction');

      $this->CI->config->load('unittest');
    }
    
    
    
    /**
     * Test of links op de pagina's wel werken
     *
     * @return void
     * @author Jan den Besten
     */
    public function test_links() {
      $menu = new Menu();
      $menu->set_menu_from_table();
      $pages = $menu->get_items();
      $this->_test_links_on_pages($menu,$pages);
    }
    private function _test_links_on_pages($menu,$pages) {
      foreach ($pages as $uri => $page) {
        $item=$menu->get_item($uri);
        $item=filter_by_key($item,'txt');
        $txt=current($item);
        $matches=array();
        if (preg_match_all("/a[\s]+[^>]*?href[\s]?=[\s\"\']+(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/", $txt, $matches)) {
          if (isset($matches[1])) {
            $links=$matches[1];
            foreach ($links as $url) {
              $test=test_url($url);
              $this->assertTrue( $test, $url .' verwijst niet goed door, of heeft geen goed emailadres, op pagina `'.$uri.'`' );
            }
          }
        }
        // subpages?
        if (isset($page['sub'])) {
          $this->_test_links_on_pages($menu,$page['sub']);
        }
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
      
      $this->assertTrue( true ); // just to be sure some test is done...
      

      $error_reporting=error_reporting();
      error_reporting(0);
      
      if (isset($formactions['formaction_mail'])) {
        if (! $this->CI->email->can_send()) {
          unset($formactions['formaction_mail']);
        }
      }
      
      foreach ($formactions as $formaction => $info) {
        
        $this->CI->load->model($formaction);
        $this->CI->$formaction->initialize($info['settings']);
        $this->CI->$formaction->fields($info['fields']);
        $data=$this->CI->db->random_data($info['fields']);
        $result=$this->CI->$formaction->go($data);
        
        $this->assertGreaterThanOrEqual(1,$result, el('message',$info,'Formaction `'.$formaction.'` did not give success as result.') );
      }
      error_reporting($error_reporting);

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
    


}

?>