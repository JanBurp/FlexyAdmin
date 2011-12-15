<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Upload extends CI_Upload {

	var $config;
	var $error;
	var $result;
	var $file_name;
	var $CI;

	function __construct($config=NULL)
	{
		parent::__construct($config);
		$this->config($config);
		$this->CI=NULL;
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
		if (isset($imageInfo['channels']) and isset($imageInfo['bits']) and function_exists('memory_get_usage') and function_exists('ini_set')) {
			$memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 / 1048576 * 2 ));
			$memoryLimit = (int) ini_get('memory_limit');
			$memoryUsage=memory_get_usage()/1048576;
			$memorySet=ceil($memoryUsage + $memoryNeeded);
			if (($memoryUsage + $memoryNeeded) > $memoryLimit) {
			  ini_set('memory_limit', $memorySet.'M');
			  return true;
			}
			else
				return false;
		}
		return true;
	}

	function upload_file($file="userfile") {
		$config=$this->config;
		// strace_($config);
		$goodluck=FALSE;
		$this->initialize($config);
		$this->do_upload($file);
		$this->error=$this->display_errors();
		// strace_($this->error);
		// strace_($_FILES);
		if (empty($this->error)) {
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
			}
		}
		else {
			// die($this->error);
			$goodluck=FALSE;
		}
		
		return $goodluck;
	}
	
	function auto_fill_fields($image,$path) {
		if (!isset($this->CI)) {
			$this->CI =& get_instance();
		}
		$CI=$this->CI;
		
		$uPath=str_replace($CI->config->item('ASSETS'),"",$path);
		$cfg=$CI->cfg->get('CFG_media_info',$uPath,'fields_autofill_fields');
		if (!empty($cfg)) {
			$fields=explode('|',$cfg);
			if (count($fields)>0) {
				foreach ($fields as $field) {
					$table=get_prefix($field,'.');
					$field=remove_prefix($field,'.');
					$fieldPre=get_prefix($field);
					if (empty($fieldPre)) $fieldPre=$field;
					$cleanName=str_replace('_',' ',get_file_without_extension($image));
					// TODO: database model maken voor dit soort dingen
					switch ($fieldPre) {
						case 'user':
							$CI->db->set('user',$CI->user_id);
							break;
						case 'media':
						case 'medias':
							$CI->db->set($field,$image);
							break;
						case 'dat':
							$CI->db->set($field,date("Y-m-d"));
							break;
						case 'str':
							$CI->db->set($field,$cleanName);
							break;
					}
				}
				$CI->db->insert($table);
			}
		}
		return TRUE;
	}
	
	function resize_image($image,$path) {
		$this->file_name=$image;
		$goodluck=TRUE;
		if (!isset($this->CI)) {
			$this->CI =& get_instance();
			$this->CI->load->library('image_lib');
		}
		$CI=$this->CI;
		
		$uPath=str_replace($CI->config->item('ASSETS'),"",$path);
		$cfg=$CI->cfg->get('CFG_img_info',$uPath);
		$currentSizes=getimagesize($path."/".$this->file_name);

		// trace_($currentSizes);
		// trace_($uPath);
		// trace_($cfg);
		// trace_($CI->cfg->data);

		// first resize copies
		$nr=1;
		while (isset($cfg["b_create_$nr"])) {
			if ($cfg["b_create_$nr"]!=FALSE) {
				// check if resize is not bigger than original (that would be strange)
				if ($currentSizes[0]<$cfg["int_width_$nr"] and $currentSizes[1]<$cfg["int_height_$nr"] ) {
					$cfg["int_width_$nr"]=$currentSizes[0];
					$cfg["int_height_$nr"]=$currentSizes[1];
				}
				$pre=$cfg["str_prefix_$nr"];
				$post=$cfg["str_suffix_$nr"];
				$ext=get_file_extension($this->file_name);
				$name=str_replace(".$ext","",$this->file_name);
				$copyName=$pre.$name.$post.".".$ext;
				$configResize['source_image'] 	= $path."/".$this->file_name;
				$configResize['maintain_ratio'] = TRUE;
				$configResize['width'] 					= $cfg["int_width_$nr"];
				$configResize['height'] 				= $cfg["int_height_$nr"];
				$configResize['new_image']			= $path."/".$copyName;
				$configResize['master_dim']			= 'auto';
				$this->_setMemory($currentSizes);
				// trace_($configResize);
				$CI->image_lib->initialize($configResize);
				if (!$CI->image_lib->resize()) {
					$this->error=$CI->image_lib->display_errors();
					// trace_($this->error);
					$goodluck=FALSE;
				}
				$CI->image_lib->clear();
				// trace_('Resized nr_'.$nr.' '.$configResize['new_image']);
			}
			$nr++;
		}

		// resize original
		if ($cfg["b_resize_img"]!=FALSE) {
			// check if resize is necessary
			if ($currentSizes[0]>$cfg["int_img_width"] or $currentSizes[1]>$cfg["int_img_height"] ) {
				$configResize['source_image'] 	= $path."/".$this->file_name;
				$configResize['maintain_ratio'] = TRUE;
				$configResize['width'] 					= $cfg["int_img_width"];
				$configResize['height'] 				= $cfg["int_img_height"];
				$configResize['new_image']			= "";
				$configResize['master_dim']			= 'auto';
				// set mem higher if needed
				$this->_setMemory($currentSizes);
				$CI->image_lib->initialize($configResize);
				if (!$CI->image_lib->resize()) {
					$this->error=$CI->image_lib->display_errors();
					// trace_($this->error);
					$goodluck=FALSE;
				}
				$CI->image_lib->clear();
				// trace_('Resized original');
			}
		}

		// create cached thumb for flexyadmin if cache map exists
		if (file_exists($CI->config->item('THUMBCACHE')) ) {
			$thumbSize=$CI->config->item('THUMBSIZE');
			if ($currentSizes[0]>$thumbSize[0] or $currentSizes[1]>$thumbSize[1]) { 
				$configResize['source_image'] 	= $path."/".$this->file_name;
				$configResize['maintain_ratio'] = TRUE;
				$configResize['width'] 					= $thumbSize[0];
				$configResize['height'] 				= $thumbSize[1];
				$configResize['new_image']			= $CI->config->item('THUMBCACHE').pathencode($path."/".$this->file_name,FALSE);
				$configResize['master_dim']			= 'auto';
				// set mem higher if needed
				$this->_setMemory($currentSizes);
				// trace_($configResize);
				$CI->image_lib->initialize($configResize);
				if (!$CI->image_lib->resize()) {
					$this->error=$CI->image_lib->display_errors();
					// trace_($this->error);
					$goodluck=FALSE;
				}
				$CI->image_lib->clear();
				// trace_('Resized thumb in cache '.$configResize['new_image']);
			}
		}
		return $goodluck;
	}


}
?>
