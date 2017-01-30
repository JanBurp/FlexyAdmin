<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Verwijderd onwenselijke bestanden in assets mappen en past rechten van mappen aan
 * 
 * - Maakt voor elke assets map een .htaccess aan die alleen toegestane bestanden toelaat
 * - Verwijderd alle bestanden die niet zijn toegestaan
 * - Wil je andere bestandstypen toelaten dan standaard? Maak een kopie van sys/flexyadmin/config/plugin_safe_assets.php in SITEPATH.config en pas aan naar wens.
 * 
 * Is actief:
 * 
 * - bij uitloggen
 * - bij aanpassingen aan instellingen van een assets map
 * - met de hand dmv een URL aanroep
 *
 * @author Jan den Besten
 */
class Plugin_safe_assets extends Plugin {

  private $forbidden=array();

  /**
   * Alle bestanden die zijn gecontroleerd
   */
  private $checked=array();
  
  /**
   * Alle bestanden die zijn verwijderd
   */
  private $removed=array();
  
  /**
   */
   function __construct() {
		parent::__construct();
    $this->forbidden=$this->CI->config->item('FILE_types_forbidden');
	}


  /**
   * Test (als administrator) of de rechten in orde zijn
   *
   * @param string $action 
   * @return string
   * @author Jan den Besten
   */
	public function _admin_homepage($action) {
    if (!$this->CI->flexy_auth->is_super_admin() or IS_LOCALHOST) return '';
    $out='';
    // Test alle rechten van de mappen en bestanden
		// - Normale bestanden 644 of 640
		// - Normale mappen 755 of 750
		// - Zeer kritische bestanden (database.php) 600
		// - Map met upload bestanden 766 / 666 / 707
    
    $files = array(
      'sitemap.xml'                       => 0100664,
      'robots.txt'                        => 0100644,
      SITEPATH.'config/database.php'      => 0100440,
      SITEPATH.'cache'                    => 0040774,
      SITEPATH.'stats'                    => 0040774,
      SITEPATH.'stats/.htaccess'          => 0100644,
    );
    $media = $this->CI->assets->get_assets_folders();
    $media[]=$this->CI->config->item('ASSETS').'_thumbcache';
    foreach ($media as $folder) {
      $files[$folder] = 0040776;              // 0776
      $files[$folder.'/.htaccess'] = 0100644; // 0664
    }
    ksort($files);

    foreach ($files as $file => $permissions) {
      $current_permissions = fileperms($file);
      if ($current_permissions!=$permissions) {
        $out.='<li>Permissions of <strong>'.$file.'</strong> should be '.substr(decoct($permissions),-3,3).' (are now '.substr(decoct($current_permissions),-3,3).')';
      }
    }
    if (!empty($out)) {
      $out=h('SAFETY ERROR: Check file permissions!',1,array('class'=>'error')).'<ul>'.$out.'</ul>';
    }
    return $out;
	}


  /**
   * Plugin werkt altijd bij logout
   *
   * @return void
   * @author Jan den Besten
   */
  function _admin_logout() {
		$logout=true;
    $logout_actions = $this->config('logout_actions');
    if ( el('cleanup_captha',$logout_actions,false) ) {
      $files = glob(SITEPATH.'assets/_thumbcache/captcha*');
      if (is_array($files)) {
        foreach( $files as $file ) {
          unlink($file);
        }
      }
    }
    if ( el('clean_all',$logout_actions,false) ) {
      $this->_safe_and_clean_all();
    }
    if (!empty($this->removed)) return $this->_show();
    return FALSE;
	}
	
  /**
   * Plugin kan ook met een URL worden aangeroepen
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   */
	function _admin_api($args=NULL) {
		if ($this->CI->flexy_auth->is_super_admin()) {
			$this->_safe_and_clean_all();
      return $this->_show();
		}
	}

  /**
   * Laat output zien dmv view
   *
   * @return string
   * @author Jan den Besten
   */
  private function _show() {
    return $this->CI->load->view('admin/plugins/safe_assets',array('checked'=>$this->checked,'removed'=>$this->removed), true);
  }


  /**
   * Plugin wordt ook aangeroepen als instellingen van een assets map wijzigen
   *
   * @return void
   * @author Jan den Besten
   */
	function _after_update() {
		$types=$this->newData['str_types'];
		$map=$this->newData['path'];
		if ($this->config('create_htaccess')) $this->_create_htaccess($map,$types);
		return $this->newData;
	}
  
  /**
   * De kern van de plugin
   *
   * @return void
   * @author Jan den Besten
   */
	function _safe_and_clean_all() {
		$someRemoved=false;
		$assets=$this->CI->config->item('ASSETS');
    // $images=implode('|',$this->CI->config->item('FILE_types_img'));
    // $flash=implode('|',$this->CI->config->item('FILE_types_flash'));
		$allCfg=object2array($this->CI->config);
		$allCfg=filter_by_key($allCfg['config'],'FILE_types_');
		unset($allCfg['FILE_types_forbidden']);
		$all='';
		foreach ($allCfg as $key => $value) {
			$all=add_string($all,implode('|',$value),'|');
		}
    
    $specialMaps = $this->config('file_types');
    // $specialMaps = array_unshift_assoc($specialMaps,'bulk_upload',$all);

    $noRecursion=array($assets);
		// set user maps
		$maps=read_map($assets,'dir',FALSE,FALSE);
		$mapsToClean=$specialMaps;
		foreach ($maps as $map => $value) {
			$path=$assets.$map;
			if (!isset($mapsToClean[$path])) {
				$filetypes = str_replace(',','|', $this->CI->assets->get_folder_settings(array($path,'types')));
				$mapsToClean[$path]=$filetypes;
			}
		}
    
		// Loop through all maps and make them safe and clen
		foreach ($mapsToClean as $path => $allowed) {
      if (!empty($allowed)) {
  			if ($this->config('create_htaccess')) {
  				$this->_create_htaccess($path,$allowed);
          $this->checked[$path]=$allowed;
  			}
  			$removed=$this->_remove_forbidden_files($path,$allowed,!in_array($path,$noRecursion));
  			if ($removed) {
          $this->removed[$path]=$removed;
  				$someRemoved = true;
  			}
      }
		}
		return $someRemoved;
	}


  /**
   * Maak .htacces aan
   *
   * @author Jan den Besten
   */
	function _create_htaccess($path,$types) {
		$types=strtolower($types).'|'.strtoupper($types);
    $map=get_suffix($path,'/');
    $serve_restricted = $this->CI->assets->get_folder_settings(array($map,'serve_restricted'));
    if ($serve_restricted) $types='';
		$htaccess="Order Allow,Deny\nDeny from all\n<Files ~ \"\.(".$types.")$\">\nAllow from all\n</Files>\n";
		if (has_string('htc',$types)) {
			$htaccess.="\nAddType text/x-component .htc\n";
		}
		write_file($path.'/.htaccess',$htaccess);
	}
	
  /**
   * Verwijderd ongewenste bestanden (recursief)
   *
   * @author Jan den Besten
   */
	function _remove_forbidden_files($path,$allowed=array(),$recursive=FALSE) {
		$removed=false;
    if (!is_array($allowed)) $allowed=explode('|',$allowed);
    $thisAllowed=$allowed;
    array_unshift($thisAllowed,'dir');      // Make sure maps are not deleted
		$files=read_map($path,'',FALSE,FALSE);
    $lowerFiles=read_map(strtolower($path),'',FALSE,FALSE);
    $files=array_merge($files,$lowerFiles);
		foreach ($files as $file => $value) {
      if (!is_dir($path.'/'.$file) and in_array($value['type'],$this->forbidden) and !in_array($value['type'],$thisAllowed)) {
        // unlink($path.'/'.$file);
				$removed[]=$value['path'];
			}
      if ($recursive and $value['type']=='dir') {
        $subRemoved=$this->_remove_forbidden_files($path.'/'.$file,$allowed,$recursive);
        if (is_array($subRemoved)) {
          if (is_array($removed))
            $removed=array_merge($removed,$subRemoved);
          else
            $removed=$subRemoved;
        }
      }
		}
    // if ($removed) trace_($removed);
		return $removed;
	}
	
}

?>
