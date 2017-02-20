<?php 
require_once(APPPATH."core/AdminController.php");
// require_once(APPPATH."core/FrontendController.php");  // Load this also, so PHP can build documentation for this one also

/**
 * Build proces
 *
 * @author Jan den Besten
 */
 
class Apidoc extends AdminController {
  
	public function __construct() {
		parent::__construct();
	}

	public function index() {
    $this->_add_content('<h1>Build processes</h1>');
    $menuArray=array(
      array( 'uri'=>'_admin/__/apidoc', 'name' => 'Create Api doc' ),
    );
    $menu = new Menu();
    $menu->set_menu($menuArray);
    $this->_add_content($menu->render());
    $this->view_admin();
	}

  public function apidoc() {
    $this->_add_content('<h1>Create API documentation</h1>');
    
    $apiMapBackend=APPPATH.'models/api';
    $apiMapFrontend=SITEPATH.'models/api';
    
    // Algemene api doc
    $api=file_get_contents($this->userguide.'__doc/5_api/1-algemeen.dox');
    $api=str_replace('/*! \page algemeen Algemeen','',$api);
    write_file($this->userguide.'api/algemeen.md',$api);
    
    $this->_apidoc($apiMapBackend,'admin_api');
    $this->_apidoc($apiMapFrontend,'frontend_api');
    
    
    $this->view_admin();
  }
  
  private function _apidoc($map,$destination) {
    $files=read_map($map,'php',false,false);
    unset($files['api_model.php']);
    
    $doc = '';
    foreach ($files as $name => $file) {
      $text=file_get_contents($file['path']);
      if (preg_match("/\/\*\*(.*)\*\//uUsm", $text,$matches)) {
        $md=$matches[1];
        $md = preg_replace("/^\s\* /uUsm", "", $md);
        $md = preg_replace("/- /uUsm", " - ", $md);
        $md = preg_replace("/^@(.*)\n/um", "", $md);
        $api="`_api/".str_replace('.php','',$name).'`';
        $doc.=$api."\n".repeater("-",strlen($api))."\n".$md."\n---------------------------------------\n\n";
      }
    }
    
    $filename=$map.'api.md';
    $filename=$this->userguide.'api/'.$destination.'.md';
    write_file($filename,$doc);
    $this->_add_content('<p>'.$filename.' created.</>');
  }
  
}

?>
