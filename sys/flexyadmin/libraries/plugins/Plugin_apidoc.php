<?php 


class Plugin_apidoc extends Plugin {
  
  private $userguide = '';

	public function __construct() {
	   parent::__construct();
     $this->userguide = APPPATH.'../../'.$this->config('api_userguide_map');
	}

  public function _admin_api() {
    if (!$this->CI->flexy_auth->is_super_admin()) return false;

    $this->add_content('<h1>Create API documentation</h1>');

    // Backend API
    $this->add_content('<h2>Backend</h2>');    
    $this->_apidoc(APPPATH.'models/api','2-backend_api');

    // Frontend API
    $this->add_content('<h2>Frontend</h2>');    
    $this->_apidoc(SITEPATH.'models/api','4-frontend_api');
    
    
    return $this->content;
  }
  
  private function _apidoc($map,$destination) {

    $files=read_map($map,'php',false,false);
    unset($files['api_model.php']);
    
    $doc = '';
    foreach ($files as $name => $file) {
      $text = file_get_contents($file['path']);
      if (preg_match("/\/\*\*(.*)\*\//uUsm", $text,$matches)) {
        $md = $matches[1];
        $md = preg_replace("/^\s\* /uUsm", "", $md);
        $md = preg_replace("/- /uUsm", " - ", $md);
        $md = preg_replace("/^@(.*)\n/um", "", $md);
        $api  = "".str_replace('.php','',$name);
        $doc .= '#'.$api."\n\n" . $md . "\n\n";
      }
    }
    
    $filename = $map.'api.md';
    $filename = $this->userguide.$destination.'.md';
    write_file($filename,$doc);
    $this->add_content('<p>'.$filename.' created.</>');
  }
  
}

?>
