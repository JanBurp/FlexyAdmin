<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Verwijderd onwenselijke bestanden in assets mappen en past rechten van mappen aan
 * 
 * - Maakt voor elke assets map een .htaccess aan die alleen toegestane bestanden toelaat
 * - Verwijderd alle bestanden die niet zijn toegestaan
 * - Wil je andere bestandstypen toelaten dan standaard? Maak een kopie van sys/flexyadmin/config/plugin_safe_assets.php in site/config en pas aan naar wens.
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
    $out='';
    if ($this->CI->user->is_super_admin()) {
      // Test alle rechten van de mappen en bestanden
      $files = array(
        'sitemap.xml' => 0100664,
        'site/cache'  => 0040755,
        'site/stats'  => 0040755,
      );

      foreach ($files as $file => $permissions) {
        $current_permissions = fileperms($file);
        if ($current_permissions!==$permissions) {
          // if (!chmod($file,$permissions))
          //   $out.='<li>Permissions of <strong>'.$file.'</strong> are set to '.substr(decoct($permissions),-3,3).' (where '.substr(decoct($current_permissions),-3,3).')';
          // else
            $out.='<li>Permissions of <strong>'.$file.'</strong> should be '.substr(decoct($permissions),-3,3).' (are now '.substr(decoct($current_permissions),-3,3).')';
        }
      }
      
    }
    if (!empty($out)) {
      $out=h('Check file permissions!',1,'warning').'<ul>'.$out.'</ul>';
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
    // Cleanup captcha's
    foreach( glob('site/assets/_thumbcache/captcha*') as $file ) {
      unlink($file);
    }
		$this->_safe_and_clean_all();
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
		if ($this->CI->user->is_super_admin()) {
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
				$filetypes=str_replace(',','|',$this->CI->cfg->get('cfg_media_info',$map,'str_types'));
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
    $serve_restricted=$this->CI->cfg->get('cfg_media_info',$map,'b_serve_restricted');
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
