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
    $this->load->model( 'assets' );
	}

  private function decode_args($args) {
    foreach ($args as $key => $arg) {
      $args[$key] = rawurldecode($arg);
    }
    return $args;
  }

  /**
   * Geeft het gevraagde bestand alleen terug als de user rechten daarvoor heeft.
   *
   * @param string $path 
   * @param string $file 
   * @return void
   * @author Jan den Besten
   */
  public function serve() {
    if ( func_num_args() >=2 ) {
      $args = $this->decode_args(func_get_args());
      $fullpath = $this->config->item('ASSETSFOLDER').join('/',$args);
      $path = array_shift($args);
      $file = join('/',$args);
      $fullfile = join('/',$args);

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
          $type = get_suffix($file,'.');
          $mime = get_mime_by_extension($file);
          
          // Hack for audio & video files - taken from - https://github.com/happyworm/smartReadFile/blob/master/smartReadFile.php
          if ( in_array($type,array_merge($this->config->item('FILE_types_sound'),$this->config->item('FILE_types_movies'))) ) {
            $size = filesize($fullpath);
            $time = date('r', filemtime($fullpath));
            $fm   = @fopen($fullpath, 'rb');
            if (!$fm) {
              header ("HTTP/1.1 505 Internal server error");
              return;
            }
            $begin  = 0;
            $end  = $size - 1;

            if (isset($_SERVER['HTTP_RANGE'])) {
              if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin  = intval($matches[1]);
                if (!empty($matches[2])) {
                  $end  = intval($matches[2]);
                }
              }
            }
            if (isset($_SERVER['HTTP_RANGE'])) {
              header('HTTP/1.1 206 Partial Content');
            }
            else {
              header('HTTP/1.1 200 OK');
            }

            header("Content-Type: $mime"); 
            header('Cache-Control: public, must-revalidate, max-age=0');
            // header('Pragma: no-cache');  
            header('Accept-Ranges: bytes');
            header('Content-Length:' . (($end - $begin) + 1));
            if (isset($_SERVER['HTTP_RANGE'])) {
              header("Content-Range: bytes $begin-$end/$size");
            }
            header("Content-Disposition: inline; filename=$file");
            header("Content-Transfer-Encoding: binary");
            header("Last-Modified: $time");
            
            $cur  = $begin;
            fseek($fm, $begin, 0);
            while(!feof($fm) && $cur <= $end && (connection_status() == 0)) {
              print fread($fm, min(1024 * 16, ($end - $cur) + 1));
              $cur += 1024 * 16;
            }

          }
          else {
            $this->output->set_content_type($mime);
            $this->output->set_output(file_get_contents($fullpath));
          }
          return;
        }
			}
		}
    header('HTTP/1.1 401 Unauthorized');
    return false;
  }
  
  public function thumb() {
		if ( func_num_args() >=2 ) {
      $args = $this->decode_args(func_get_args());
      $fullpath = $this->config->item('THUMBCACHE').join('___',$args);
      $path = array_shift($args);
      $file = join('/',$args);
      $fullfile = join('___',$args);

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
