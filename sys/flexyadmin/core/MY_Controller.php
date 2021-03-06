<?php
/**
 * MY_Controller Class
 *
 * This Controller Class handles authentication, loading basic data class
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class MY_Controller extends CI_Controller {


	public function __construct($isAdmin=false) {
		parent::__construct();

    /**
     * Install ??
     */
    if ($this->_check_if_flexy_database_exists()) {
      $this->_init_flexy_admin($isAdmin);
    }
    else {
      // Database login correct, But no database found, Try to load the demodatabase
      $succes=false;

      // try to load latest demodatabase
      $dbPath = str_replace('sys/flexyadmin/','',APPPATH).'db';
      if (file_exists($dbPath)) {
        $demoDB=scan_map($dbPath,'sql');
        $demoDB=filter_by($demoDB,$dbPath.'/flexyadmin_demo_');
        if ($demoDB) {
          $demoDB=current($demoDB);
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
            redirect($this->config->item('API_home'),REDIRECT_METHOD);
          }
        }
      }

      if (!$succes) {
        show_error('Database login: correct.<br/>No tables (for flexyadmin) found.<br/>Tried to load demodatabase, no succes.');
      }
    }

    /**
     * Force https?
     */
    if ($this->config->item('force_https')) {
      if( empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off" ){
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
      }
    }


    /**
     * Load extra's needed for front,back & ajax
     */
    $this->load->library('parent_module_plugin');
    $this->load->model('formaction');
    $this->load->helper('directory');
    $this->load->helper('file');
    $this->load->helper('form');
    $this->load->helper('help');
    $this->load->helper('html');
    $this->load->helper('img');
    $this->load->helper('text');
    $this->load->helper('language');

    /**
     * Load Data Model & Core tables
     */
    $this->load->model( 'data/Data_Core','data_core' );
    $this->load->model( 'data/Data','data' );
    $this->load->model( 'assets' );

    if (defined('PHPUNIT_TEST')) {
      if (SAFE_INSTALL) {
        $buildfile = '../sys/build.txt';
        $db_folder = '../db';
      }
      else {
        $buildfile = 'sys/build.txt';
        $db_folder = 'db';
      }
      echo "> FlexyAdmin ". read_file($buildfile) . "\n";
      echo "> Testing on PHP ". phpversion() . "\n";

      // Load test database
      $files = directory_map($db_folder);
      $key = in_array_like('unittest_',$files);
      if ($key) {
        $file = el($key,$files);
        if ($file) {
          $file=$db_folder.'/'.$file;
          if (file_exists($file)) {

            // DROP all current tables
            $this->load->dbforge();
            $tables = $this->db->list_tables();
            foreach ($tables as $table) {
              $this->dbforge->drop_table($table,TRUE);
            }
            echo "> dropped all current tables\n";

            // Import unittest database
            $testDB = read_file($file);
            $this->load->dbutil();
            $this->dbutil->import($testDB);
            echo "> Test database loaded. (".$file.")\n";
          }
        }
      }
      // Reset cache
      $this->cache->clean();
      echo "> Cache cleared.\n\n";
      return;
    }
	}

	private function _check_if_flexy_database_exists() {
		return ($this->db->table_exists('cfg_version') and $this->db->table_exists('cfg_sessions'));
	}

	private function _init_flexy_admin($isAdmin=false) {
    if ($this->config->item('PROFILER')) $this->output->enable_profiler(TRUE);
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
    $sitename = str_replace(array('/index.php','/public'),'',$sitename);
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

        // Replace encryption_key
        $root = str_replace('sys/flexyadmin/','',APPPATH);
        $key = shell_exec($root.'/sys/vendor/bin/generate-defuse-key');
        if ($key and strlen($key)>=136) {
          $key=trim($key,"\n");
          $new = preg_replace("/config\['encryption_key']\s=\s'(.*)';/uU", "config['encryption_key'] = '".$key."';", $new,1,$count);
          $new = str_replace("\r",'',$new);
          $result=file_put_contents($file,$new);
          if ($result) {
            log_message('info', 'FlexyAdmin Install: encryption_key generated');
          }
        }
      }
    }
  }


  /**
   * Geeft de uri van een pagina met de gevraagde module
   *
   * @param string $module
   * @param bool $full_uri default=true
   * @return string uri
   * @author Jan den Besten
   */
	public function find_module_uri($module) {
    find_module_uri($module);
	}


}


?>
