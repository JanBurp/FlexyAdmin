<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class Plugin_safe_assets extends Plugin {

  private $checked=array();
  private $removed=array();
  
  
	function __construct() {
		parent::__construct();
	}


	function _admin_logout() {
		$logout=true;
		$this->_safe_and_clean_all();
    if (!empty($this->removed)) return $this->_show();
    return FALSE;
	}
	
	function _admin_api($args=NULL) {
		if ($this->CI->user->is_super_admin()) {
			$this->_safe_and_clean_all();
      return $this->_show();
		}
	}

  private function _show() {
    return $this->CI->load->view('admin/plugins/safe_assets',array('checked'=>$this->checked,'removed'=>$this->removed), true);
  }



	function _after_update() {
		$types=$this->newData['str_types'];
		$map=$this->newData['path'];
		if ($this->config('create_htacces')) $this->_make_map_safe($map,$types);
		return $this->newData;
	}

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
			$removed=$this->_remove_forbidden_files($path,$allowed);
			if ($removed) {
        $this->removed[$path]=$removed;
				$someRemoved = true;
			}
		}
		return $someRemoved;
	}


	// Change safety .htaccess
	function _make_map_safe($path,$types) {
		$types=strtolower($types).'|'.strtoupper($types);
		$htaccess="Order Allow,Deny\nDeny from all\n<Files ~ \"\.(".$types.")$\">\nAllow from all\n</Files>\n";
		if (has_string('htc',$types)) {
			$htaccess.="\nAddType text/x-component .htc\n";
		}
		write_file($path.'/.htaccess',$htaccess);
	}
	
	// remove not allowed files
	function _remove_forbidden_files($path,$allowed,$forbidden='') {
		$removed=false;
		$allowed=explode('|',$allowed);
		$files=read_map($path);
		foreach ($files as $file => $value) {
      if (!is_dir($path.'/'.$file) and !in_array($value['type'],$allowed)) {
        unlink($path.'/'.$file);
				$removed[]=$file;
			}
		}
		return $removed;
	}
	
}

?>