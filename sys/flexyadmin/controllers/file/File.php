<?php require_once(APPPATH."core/FrontendController.php");

class File extends FrontEndController {
	
	function __construct()	{
		parent::__construct();
	}

  /**
   * Geeft het gevraagde bestand alleen terug als de user rechten daarvoor heeft.
   *
   * @param string $path 
   * @param string $file 
   * @return void
   * @author Jan den Besten
   */
  public function serve($path='',$file='') {
		if (!empty($path) and !empty($file)) {
			$fullpath=SITEPATH.'assets/'.$path.'/'.$file;
			if (file_exists($fullpath)) {
        if ($this->mediatable->has_serve_rights($path,$file)) {
          $type=get_suffix($file,'.');
          $this->output->set_content_type($type);
          $this->output->set_output(file_get_contents($fullpath));
          return;
        }
			}
		}
    show_404('page');
    return false;
  }

  /**
   * Download gevraagde bestand
   *
   * @param string $path 
   * @param string $file 
   * @return void
   * @author Jan den Besten
   */
	public function download($path='',$file='')	{
    // trace_([$path,$file]);
		if (!empty($path) and !empty($file)) {
			$fullpath=SITEPATH.'assets/'.$path.'/'.$file;
			if (file_exists($fullpath)) {
        $data=file_get_contents($fullpath);
		    $name=$file;
        $this->load->helper('download');
        force_download($name, $data);
			}
		}
	}
  
}


?>