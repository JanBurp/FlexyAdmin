<?php require_once(APPPATH."core/FrontendController.php");

/** \ingroup controllers
 * Geeft het opgevraagde bestand als er rechten voor zijn. Met /file/serve, /file/download of /media, /media/download
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */


class File extends CI_Controller {
  
  // Always serve files from these folders
  private $serve_rights = array( 'css','fonts','js' );

  // Restricted admin files
  private $restricted_admin_files = array('main.build.js');
  
	
	function __construct()	{
		parent::__construct();
    $this->load->model( 'data/Data_Core','data_core' );
    $this->load->model( 'data/Data','data' );
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
      $fullpath = SITEPATH.'assets/'.$path.'/'.$file;
			if ( file_exists($fullpath) ) {
        $serve = false;
        if ( in_array($path,$this->serve_rights) ) $serve = true;
        if (!$serve) {
          if ($this->assets->has_serve_rights($path,$file)) $serve = true;
          if (!$serve) {
            $this->flexy_auth->login_with_authorization_header();
            if ($this->assets->has_serve_rights($path,$file)) $serve = true;
          }
        }
        if ( $serve ) {
          $type=get_suffix($file,'.');
          $this->output->set_content_type($type);
          $this->output->set_output(file_get_contents($fullpath));
          return;
        }
			}
		}
    header('HTTP/1.1 401 Unauthorized');
    return false;
  }
  
  public function thumb($path,$file) {
		if (!empty($path) and !empty($file)) {
      $fullpath = $this->config->item('THUMBCACHE').$path.'___'.$file;

      // Create thumb if not exists
      if ( !file_exists($fullpath) ) {
        $this->assets->resize_file($path,$file);
      }

      // Show if exists (now)
			if ( file_exists($fullpath) ) {
        if ( in_array($path,$this->serve_rights) or $this->assets->has_serve_rights($path,$file) ) {
          $type=get_suffix($file,'.');
          $this->output->set_content_type($type);
          $this->output->set_output(file_get_contents($fullpath));
          return;
        }
			}
		}
    header('HTTP/1.1 401 Unauthorized');
    return false;
  }
  
  
  // public function 
  
  /**
   * Serve admin assets
   *
   * @param string $path 
   * @param string $file 
   * @return void
   * @author Jan den Besten
   */
  public function admin_assets() {
    $args = func_get_args();
    $file = array_pop($args);
    $path = implode('/',$args);
		if (!empty($file)) {
      $fullpath = APPPATH.'assets/dist/'.$path.'/'.$file;
			if ( file_exists($fullpath) ) {
        $has_serve_rights = FALSE;
        if ( in_array($file,$this->restricted_admin_files) ) {
          if ( $this->flexy_auth->logged_in() and $this->flexy_auth->allowed_to_use_cms() ) {
            $has_serve_rights = TRUE;
          }
        }
        else {
          $has_serve_rights = TRUE;
        }
			}
      if ($has_serve_rights) {
        $type=get_suffix($file,'.');
        $this->output->set_content_type($type);
        $this->output->set_output(file_get_contents($fullpath));
        return;         
      }
		}
    header('HTTP/1.1 401 Unauthorized');
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
		if (!empty($path) and !empty($file)) {
			$fullpath=SITEPATH.'assets/'.$path.'/'.$file;
			if (file_exists($fullpath)) {
        $type = strtolower(get_suffix($file,'.'));
        if ( in_array($type,$this->config->item('FILE_types_img')) or in_array($type,$this->config->item('FILE_types_pdf')) ) {
          $this->output->set_content_type($type);
          $this->output->set_output(file_get_contents($fullpath));
        }
        else {
          $data=file_get_contents($fullpath);
          $name=$file;
          $this->load->helper('download');
          force_download($name, $data);
        }
			}
		}
	}
  
}


?>