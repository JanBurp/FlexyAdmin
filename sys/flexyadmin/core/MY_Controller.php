<?php 
/**
 * MY_Controller Class
 *
 * This Controller Class handles authentication, loading basic data class
 *
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class MY_Controller extends CI_Controller {


	function __construct($isAdmin=false) {
		parent::__construct();
    
		if ($this->_check_if_flexy_database_exists())
			$this->_init_flexy_admin($isAdmin);
		else {
			// Database login correct, But no database found, Try to load the demodatabase
			$succes=false;
			// try to load latest demodatabase
			if (file_exists('db')) {
				$demoDB=read_map('db','sql',FALSE,FALSE);
				$demoDB=filter_by($demoDB,'flexyadmin_demo_');
				if ($demoDB) {
					$demoDB=current($demoDB);
					$demoDB=$demoDB['path'];
					$SQL=file_get_contents($demoDB);
					if ($SQL) {
            $lines=explode("\n",$SQL);
            $comments="";
            foreach ($lines as $k=>$l) {
              if (substr($l,0,1)=="#")  {
                if (strlen($l)>2)  $comments.=$l.br();
                unset($lines[$k]);
              }
            }
            $sql=implode("\n",$lines);
            $lines=preg_split('/;\n+/',$sql); // split at ; with EOL

            foreach ($lines as $key => $line) {
              $line=trim($line);
              if (!empty($line)) {
                $query=$this->db->query($line);
              }
            }
						$succes=TRUE;
            // Other Install options
            $this->_install();
            // Redirect
						redirect('admin', 'refresh');
					}
				}
			}

			if (!$succes) {
				show_error('Database login: correct.<br/>No tables (for flexyadmin) found.<br/>Tried to load demodatabase, no succes.');
			}
		}
	}

	private function _check_if_flexy_database_exists() {
		return ($this->db->table_exists('cfg_configurations') and $this->db->table_exists('cfg_sessions'));
	}

	private function _init_flexy_admin($isAdmin=false) {
    if ($this->config->item('PROFILER')) $this->output->enable_profiler(TRUE);
    // load db config
		$this->load->model('cfg');
		$this->cfg->set_if_admin($isAdmin);
	}
  
  /**
   * This installs basic FlexyAdmin config
   *
   * @return void
   * @author Jan den Besten
   */
  private function _install() {
    // Replace cookiename
    $sitename = $_SERVER['SCRIPT_FILENAME'];
    $sitename = str_replace('/index.php','',$sitename);
    $sitename = get_suffix($sitename,'/');
    $sitename = str_replace('.','_',$sitename);
    if (!empty($sitename)) {
      $file=SITEPATH.'config/config.php';
      $cfg=file_get_contents($file);
      // load config
      if (!empty($cfg)) {
        // Bestaat de $config['sess_cookie_name'] al?
        $count=0;
        $new = preg_replace("/config\['sess_cookie_name']\s=\s'(.*)';/uU", "config['sess_cookie_name'] = '".$sitename."';", $cfg,1,$count);
        if ($count==0) {
          // Bestond nog niet, aanmaken dus
          $new = preg_replace("/config\['uri_protocol'\]\s=\s([^;]*);/uUm", "$0\n\n/*\n|--------------------------------------------------------------------------\n| Session Variables\n|--------------------------------------------------------------------------\n|\n| 'session_cookie_name' = the name you want for the cookie - automatic set at install\n|\n*/\n\$config['sess_cookie_name'] = '".$sitename."';\n", $cfg,1,$count);
        }
        $new = str_replace("\r",'',$new);
        $result=file_put_contents($file,$new);
        if ($result) {
          log_message('info', 'FlexyAdmin Install: sess_cookie_name = '.$sitename);
        }
      }
    }
  }

  
  /**
   * Geeft de uri van een pagina met de gevraagde module
   *
   * @param string $module 
   * @param bool $full_uri[true]
   * @return string uri
   * @author Jan den Besten
   */
	public function find_module_uri($module,$full_uri=true) {
    find_module_uri($module,$full_uri);
	}
  

}


?>