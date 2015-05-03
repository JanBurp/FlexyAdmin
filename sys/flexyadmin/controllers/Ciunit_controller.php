<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."core/FrontendController.php");
  
/**
 * CodeIgniter controller for displaying the web interface of CIUnit
 *
 * @author     Agop Seropyan <agopseropyan@gmail.com>
 * @since      File available since Release 1.0.0
 */
class CIUnit_Controller extends FrontendController
{
    
    public function __construct() {
      parent::__construct();
    }

    public function index () {
        // Which testcase?
        $testCase = $this->uri->get_last();
        if ($testCase=='_unittest') $testCase='';
      
        // Add ciunit package to codeigniter path
        // $this->load->add_package_path(APPPATH.'third_party/ciunit', FALSE);
        $this->load->config('ciunut');

        // Load library
        $this->load->library('ciunit');
        $this->load->helper('url');
        
        $data['test_tree'] = $this->ciunit->getTestCollection();

        // Menu
        $this->load->library('menu');
        $menu=new Menu();
        $menu->set('framework','bootstrap');
        $menu->set_menu_from_filetree($data['test_tree']);
        $data['test_menu']=$menu->render(NULL,'',1,'_unittest');
        $data['run_failure'] = '';
        
        // Check against environment 
        if(ENVIRONMENT == 'testing' || ENVIRONMENT == 'development') { 
            
            if($testCase != '') {
                $this->ciunit->run($testCase);
                
                if($this->ciunit->runWasSuccessful()) {
                    $data['runner'] = $this->ciunit->getRunner();  
                    
                    $this->load->view('ciunit/index', $data);
                    return;
                }
            }
            else {
                if($this->ciunit->getRunFailure() == NULL) { 
                    $this->load->view('ciunit/index', $data);
                    return;
                }
            }  
            
             $data['run_failure'] = sprintf("Error: %s", $this->ciunit->getRunFailure());
   
        } 
        else if(ENVIRONMENT == 'production') {  
             $data['run_failure'] = "Unit Testing is not available in production environment!";
        } 
         
         
        $this->load->view('ciunit/error', $data);
        
        // Restore view path for versions < 2.1.0
        if(substr(CI_VERSION, 0, 3) == '2.0') {
            $this->load->_ci_view_path = $orig_view_path;
        }
        
        // Restore paths
        // $this->load->remove_package_path(APPPATH.'third_party/ciunit');
    }
}


/* End of file ciunit_controller.php */
/* Location: ./application/controllers/ciunit_controller.php */