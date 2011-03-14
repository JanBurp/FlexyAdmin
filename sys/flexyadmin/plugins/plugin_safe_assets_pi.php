<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class plugin_safe_assets extends plugin_ {

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
		$this->CI->_add_content(h($this->plugin,1));
		// loop through all asset maps to make them safe and clean
		$maps=read_map('site/assets','dir');
		$allready_safe=array('css','img','js');
		foreach ($maps as $map => $row) {
			if (!in_array($map,$allready_safe)) {
				$types=$this->CI->cfg->get('cfg_media_info',$map,'str_types');
				if (empty($types)) $types=$this->CI->config->item('FILE_types_img');
				$allowed='';
				if ($map=='lists') {
					$types='js';
					$allowed='js';
				}
				$removed=$this->_remove_forbidden_files($map,$allowed);
				if ($removed) {
					$logout=false;
					$removed=implode(',',$removed);
					$this->CI->_add_content('<p class="error">Removed forbidden files ('.$removed.') from: '.$map.'</p>');
				}
			}
		}
		return $logout;
	}
	
	
	
	function _admin_api($args=NULL) {
		$this->CI->_add_content(h($this->plugin,1));

		// loop through all asset maps to make them safe and clean

		// first normal maps
		$normalMaps=array(''						=>	"Order Allow,Deny\nDeny from all\n<Files ~ \"\.(css|img|js)$\">\nAllow from all\n</Files>\n",
											'_thumbcache'	=>	"Order Allow,Deny\nDeny from all\n<Files ~ \"\.(jpg|jpeg|gif|png)$\">\nAllow from all\n</Files>\n",
											'lists'				=>	"Order Allow,Deny\nDeny from all\n<Files ~ \"\.(js)$\">\nAllow from all\n</Files>\n",
											'css'					=>	"Order Allow,Deny\nDeny from all\n<Files ~ \"\.(css|htc)$\">\nAllow from all\n</Files>\n",
											'img'					=>	"Order Allow,Deny\nDeny from all\n<Files ~ \"\.(jpg|jpeg|gif|png)$\">\nAllow from all\n</Files>\n", 
											'js'					=>	"Order Allow,Deny\nDeny from all\n<Files ~ \"\.(js|css|html|jpg|jpeg|gif|png)$\">\nAllow from all\n</Files>\n", 
											);
		foreach ($normalMaps as $map => $htaccess) {
			$path=$this->CI->config->item('ASSETS').$map.'/.htaccess';
			write_file($path,$htaccess);
			$this->CI->_add_content('<p>Created : '.$path.'</p>');
		}

		// upload maps
		$maps=read_map('site/assets','dir');
		$allready_safe=array_keys($normalMaps);
		array_shift($allready_safe);
		foreach ($maps as $map => $row) {
			if (!in_array($map,$allready_safe)) {
				$types=$this->CI->cfg->get('cfg_media_info',$map,'str_types');
				if (empty($types)) $types=$this->CI->config->item('FILE_types_img');
				$allowed='';
				if ($map=='lists') {
					$types='js';
					$allowed='js';
				}
				$this->_make_map_safe($map,$types,$allowed);
				$this->CI->_add_content('<p>Created .htaccess for: '.$map.'</p>');
				$removed=$this->_remove_forbidden_files($map,$allowed);
				if ($removed) {
					$removed=implode(',',$removed);
					$this->CI->_add_content('<p class="error">Removed forbidden files ('.$removed.') from: '.$map.'</p>');
				}
			}
		}
	}



	function _after_update() {
		$types=$this->newData['str_types'];
		$map=$this->newData['path'];
		$this->_make_map_safe($map,$types);
		return $this->newData;
	}

	// Change safety .htaccess
	function _make_map_safe($map,$types,$allowed='',$forbidden='') {
		if (!is_array($types)) $types=explode(',',$types);
		if ($forbidden=='') $forbidden=$this->CI->config->item('FILE_types_forbidden');
		if ($allowed!='') {
			if (!is_array($allowed)) $allowed=array($allowed);
			foreach ($forbidden as $key=>$value) {
				if (in_array($value,$allowed)) unset($forbidden[$key]);
			}
		}
		foreach ($types as $key=>$type) {
			if (in_array($type,$forbidden)) unset($types[$key]);
		}
		$htaccess="Order Allow,Deny\nDeny from all\n<Files ~ \"\.(".implode('|',$types).")$\">\nAllow from all\n</Files>\n";
		$path=$this->CI->config->item('ASSETS').$map.'/.htaccess';
		write_file($path,$htaccess);
	}
	
	// remove forbidden files
	function _remove_forbidden_files($map,$allowed='',$forbidden='') {
		$removed=false;
		if ($forbidden=='') $forbidden=$this->CI->config->item('FILE_types_forbidden');
		if ($allowed!='') {
			if (!is_array($allowed)) $allowed=array($allowed);
			foreach ($forbidden as $key=>$value) {
				if (in_array($value,$allowed)) unset($forbidden[$key]);
			}
		}
		$path=$this->CI->config->item('ASSETS').$map;
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