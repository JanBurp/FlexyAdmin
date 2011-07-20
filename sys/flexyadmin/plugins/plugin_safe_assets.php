<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class Plugin_safe_assets extends Plugin_ {

	// You can declare some properties here if needed

	function init($init=array()) {
		parent::init($init);
		// If you need methods like _after_update(), _after_delete(), set in the next line of which tables,fields,types this method must act.
		$this->act_on(array('tables'=>'cfg_media_info'));
	}
	
	//
	// Here you find short templates of possible methods
	//

	function _admin_logout() {
		$logout=true;
		$this->_add_content(h($this->plugin,1));
		$logout=!$this->_safe_and_clean_all();
		return $logout;
	}
	
	function _admin_api($args=NULL) {
		$this->_add_content(h($this->plugin,1));
		$this->_safe_and_clean_all();
	}


	function _after_update() {
		$types=$this->newData['str_types'];
		$map=$this->newData['path'];
		$this->_make_map_safe($map,$types);
		return $this->newData;
	}

	function _safe_and_clean_all() {
		$someRemoved=false;
		$assets=$this->config->item('ASSETS');
		$images=implode('|',$this->config->item('FILE_types_img'));
		$flash=implode('|',$this->config->item('FILE_types_flash'));
		$allCfg=object2array($this->config);
		$allCfg=filter_by_key($allCfg['config'],'FILE_types_');
		unset($allCfg['FILE_types_forbidden']);
		$all='';
		foreach ($allCfg as $key => $value) {
			$all=add_string($all,implode('|',$value),'|');
		}
		// set static maps
		$specialMaps=array(	'bulk_upload'						=> $all,
												'site/stats'						=> "xml",
												$assets									=> "css|img|js",
												$assets.'_thumbcache'	=> $images,
												$assets.'lists'				=> "js",
												$assets.'css'					=> "css|htc|php|eot|svg|ttf|woff|otf",
												$assets.'img'					=> $images.'|'.$flash.'|ico', 
												$assets.'js'						=> "js|css|html|swf|".$images, 
												);
		// set user maps
		$maps=read_map($assets,'dir');
		$mapsToClean=$specialMaps;
		foreach ($maps as $map => $value) {
			$path=$assets.$map;
			if (!isset($mapsToClean[$path])) {
				$filetypes=str_replace(',','|',$this->cfg->get('cfg_media_info',$map,'str_types'));
				$mapsToClean[$path]=$filetypes;
			}
		}
		// Loop though all maps and make them safe and clen
		foreach ($mapsToClean as $path => $allowed) {
			$this->_make_map_safe($path,$allowed);
			$this->_add_content('<p>Created : '.$path.'/.htaccess ('.$allowed.')</p>');
			$removed=$this->_remove_forbidden_files($path,$allowed);
			if ($removed) {
				$removed=implode(',',$removed);
				$this->_add_content('<p class="error">Removed forbidden files ('.$removed.') from: '.$path.'</p>');
				$someRemoved = true;
			}
		}
		return $someRemoved;
	}


	// Change safety .htaccess
	function _make_map_safe($path,$types) {
		$types=strtolower($types).'|'.strtoupper($types);
		$htaccess="Order Allow,Deny\nDeny from all\n<Files ~ \"\.(".$types.")$\">\nAllow from all\n</Files>\n";
		write_file($path.'/.htaccess',$htaccess);
	}
	
	// remove forbidden files
	function _remove_forbidden_files($path,$allowed,$forbidden='') {
		$removed=false;
		if ($forbidden=='') $forbidden=$this->config->item('FILE_types_forbidden');
		$allowed=explode('|',$allowed);
		foreach ($forbidden as $key=>$value) {
			if (in_array($value,$allowed)) unset($forbidden[$key]);
		}
		$files=read_map($path);
		foreach ($files as $file => $value) {
			if (in_array($value['type'],$forbidden)) {
				unlink($path.'/'.$file);
				$removed[]=$file;
			}
		}
		return $removed;
	}
	
}

?>