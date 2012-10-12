<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verwijderd onwenselijke bestanden in assets mappen
 * 
 * - Maakt voor elke assets map een .htaccess aan die alleen toegestane bestanden toont
 * - Verwijderd alle bestanden die niet zijn toegestaan
 * 
 * Is actief:
 * 
 * - bij uitloggen
 * - bij aanpassingen aan instellingen van een assets map
 * - met de hand dmv een URL aanroep
 *
 * @package default
 * @author Jan den Besten
 */
class Plugin_safe_assets extends Plugin {

  /**
   * Alle bestanden die zijn gecontroleerd
   *
   * @var array
   * @ignore
   */
  private $checked=array();
  
  /**
   * Alle bestanden die zijn verwijderd
   *
   * @var array
   * @ignore
   */
  private $removed=array();
  
  /**
   * @ignore
   */
   function __construct() {
		parent::__construct();
	}


  /**
   * Plugin werkt altijd bij logout
   *
   * @return void
   * @author Jan den Besten
   * @ignore
   */
  function _admin_logout() {
		$logout=true;
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
   * @ignore
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
   * @ignore
   */
  private function _show() {
    return $this->CI->load->view('admin/plugins/safe_assets',array('checked'=>$this->checked,'removed'=>$this->removed), true);
  }


  /**
   * Plugin wordt ook aangeroepen als instellingen van een assets map wijzigen
   *
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function _after_update() {
		$types=$this->newData['str_types'];
		$map=$this->newData['path'];
		if ($this->config('create_htacces')) $this->_make_map_safe($map,$types);
		return $this->newData;
	}
  
  /**
   * De kern van de plugin
   *
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function _safe_and_clean_all() {
		$someRemoved=false;
		$assets=$this->CI->config->item('ASSETS');
		$images=implode('|',$this->CI->config->item('FILE_types_img'));
		$flash=implode('|',$this->CI->config->item('FILE_types_flash'));
		$allCfg=object2array($this->CI->config);
		$allCfg=filter_by_key($allCfg['config'],'FILE_types_');
		unset($allCfg['FILE_types_forbidden']);
		$all='';
		foreach ($allCfg as $key => $value) {
			$all=add_string($all,implode('|',$value),'|');
		}
		// set static maps
		$specialMaps=array(	'bulk_upload'						=> $all,
												SITEPATH.'stats'				=> 'xml',
												$assets									=> 'css|img|js',
												$assets.'_thumbcache'		=> $images,
												$assets.'lists'					=> 'js',
												$assets.'css'						=> 'css|htc|php|eot|svg|ttf|woff|otf',
												$assets.'img'						=> $images.'|'.$flash.'|ico', 
												$assets.'js'						=> 'js|css|html|swf|'.$images, 
												);
    $noRecursion=array($assets);
		// set user maps
		$maps=read_map($assets,'dir');
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
			if ($this->config('create_htacces')) {
				$this->_make_map_safe($path,$allowed);
        $this->checked[$path]=$allowed;
			}
			$removed=$this->_remove_forbidden_files($path,$allowed,'',!in_array($path,$noRecursion));
			if ($removed) {
        $this->removed[$path]=$removed;
				$someRemoved = true;
			}
		}
		return $someRemoved;
	}


  /**
   * Maak .htacces aan
   *
   * @author Jan den Besten
   * @ignore
   */
	function _make_map_safe($path,$types) {
		$types=strtolower($types).'|'.strtoupper($types);
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
   * @ignore
   */
	function _remove_forbidden_files($path,$allowed,$forbidden='',$recursive=FALSE) {
		$removed=false;
    if (!is_array($allowed)) $allowed=explode('|',$allowed);
    $thisAllowed=$allowed;
    array_unshift($thisAllowed,'dir');      // Make sure maps are not deleted
		$files=read_map($path);
    $lowerFiles=read_map(strtolower($path));
    $files=array_merge($files,$lowerFiles);
		foreach ($files as $file => $value) {
      if (!is_dir($path.'/'.$file) and !in_array($value['type'],$thisAllowed)) {
        unlink($path.'/'.$file);
				$removed[]=$value['path'];
			}
      if ($recursive and $value['type']=='dir') {
        $subRemoved=$this->_remove_forbidden_files($path.'/'.$file,$allowed,$forbidden,$recursive);
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