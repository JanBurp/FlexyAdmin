<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Upload extends CI_Upload {

	var $config;
	var $error;
	var $result;
	var $file_name;

	function MY_Upload($config=NULL)
	{
		parent::CI_Upload($config);
		$this->config($config);
	}

	function config($config) {
		if (!isset($config['upload_path'])) 	$config['upload_path'] = assets();
		if (!isset($config['allowed_types']))	$config['allowed_types'] = "jpg|jpeg|gif|png|mp3|wav|ogg|wma|pdf";
		$this->config=$config;
		$this->resized=FALSE;
	}

	function get_error() {
		return $this->error;
	}

	function get_result() {
		return $this->result;
	}

	function get_file() {
		return $this->file_name;
	}

	function _setMemory($imageInfo) {
		if (isset($imageInfo['channels']) and isset($imageInfo['bits']) ) {
			$memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65);
			$memoryLimit = (int) ini_get('memory_limit')*1048576;
			if ((memory_get_usage() + $memoryNeeded) > $memoryLimit) {
			  ini_set('memory_limit', ceil((memory_get_usage() + $memoryNeeded + $memoryLimit)/1048576).'M');
			  return true;
			}
			else
				return false;
		}
		return true;
	}

/**
 * function upload_file()
 *
 * Uploads a file and if a image resize if asked for
 *
 * @return result
 */
	function upload_file($file="userfile") {
		$config=$this->config;
		$this->initialize($config);
		$this->do_upload($file);
		$this->error=$this->display_errors();
		$this->result=$this->data();
		$this->file_name=$this->result["file_name"];
		$cleanName=clean_file_name($this->file_name);
		if ($cleanName!=$this->file_name) {
			rename($config["upload_path"]."/".$this->file_name, $config["upload_path"]."/".$cleanName);
			$this->file_name=$cleanName;
		}
		// trace_($this->file_name);
		$goodluck=empty($this->error);
		if (!$goodluck) {
			log_("info","[UPLOAD] error while uploaded: '$this->file_name' [$this->error]");
		}
		else {
			log_("info","[UPLOAD] uploaded: '$this->file_name'");
			/**
			 * Is image and need to be resized/copied?
			 */
			$CI =& get_instance();
			$CI->load->library('image_lib');
			$uPath=str_replace($CI->config->item('ASSETS'),"",$config["upload_path"]);
			$cfg=$CI->cfg->get('CFG_img_info',$uPath);
			// first resize copies
			$currentSizes=getimagesize($config["upload_path"]."/".$this->file_name);
			$memSet=FALSE;
			$nr=1;
			while (isset($cfg["b_create_$nr"])) {
				if ($cfg["b_create_$nr"]!=FALSE) {
					// check if resize is not bigger than original (that would be strange)
					if ($currentSizes[0]<$cfg["int_width_$nr"] and $currentSizes[1]<$cfg["int_height_$nr"] ) {
						$cfg["int_width_$nr"]=$currentSizes[0];
						$cfg["int_height_$nr"]=$currentSizes[1];
					}
					$pre=$cfg["str_prefix_$nr"];
					$post=$cfg["str_postfix_$nr"];
					$ext=get_file_extension($this->file_name);
					$name=str_replace(".$ext","",$this->file_name);
					$copyName=$pre.$name.$post.".".$ext;
					$config['source_image'] 	= $config["upload_path"]."/".$this->file_name;
					$config['maintain_ratio'] = TRUE;
					$config['width'] 					= $cfg["int_width_$nr"];
					$config['height'] 				= $cfg["int_height_$nr"];
					$config['new_image']			= $config["upload_path"]."/".$copyName;
					$config['master_dim']			= 'auto';
					// set mem higher if needed
					if (!$memSet) $memSet=$this->_setMemory($currentSizes);
					$CI->image_lib->initialize($config);
					if (!$CI->image_lib->resize()) {
						$this->error=$CI->image_lib->display_errors();
						$goodluck=FALSE;
					}
				}
				$nr++;
			}
			// resize original
			if ($cfg["b_resize_img"]!=FALSE) {
				// check if resize is necessary
				if ($currentSizes[0]>$cfg["int_img_width"] or $currentSizes[1]>$cfg["int_img_height"] ) {
					$config['source_image'] 	= $config["upload_path"]."/".$this->file_name;
					$config['maintain_ratio'] = TRUE;
					$config['width'] 					= $cfg["int_img_width"];
					$config['height'] 				= $cfg["int_img_height"];
					$config['new_image']			= "";
					$config['master_dim']			= 'auto';
					// set mem higher if needed
					if (!$memSet) $memSet=$this->_setMemory($currentSizes);
					$CI->image_lib->initialize($config);
					if (!$CI->image_lib->resize()) {
						$this->error=$CI->image_lib->display_errors();
						$goodluck=FALSE;
					}
				}
			}
			// create cached thumb for flexyadmin if cache map exists
			if (file_exists($CI->config->item('THUMBCACHE')) ) {
				$thumbSize=$CI->config->item('THUMBSIZE');
				if ($currentSizes[0]>$thumbSize[0] or $currentSizes[1]>$thumbSize[1]) { 
					$config['source_image'] 	= $config["upload_path"]."/".$this->file_name;
					$config['maintain_ratio'] = TRUE;
					$config['width'] 					= $thumbSize[0];
					$config['height'] 				= $thumbSize[1];
					$config['new_image']			= $CI->config->item('THUMBCACHE')."/".pathencode($config["upload_path"]."/".$this->file_name,FALSE);
					$config['master_dim']			= 'auto';
					// set mem higher if needed
					if (!$memSet) $memSet=$this->_setMemory($currentSizes);
					$CI->image_lib->initialize($config);
					if (!$CI->image_lib->resize()) {
						$this->error=$CI->image_lib->display_errors();
						$goodluck=FALSE;
					}
				}
			}
		}
		return $goodluck;
	}

}
?>
