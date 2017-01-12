<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/** \ingroup libraries
 * Uitbreiding op [CI_Upload](http://codeigniter.com/user_guide/libraries/file_uploading.html)
 *
 * @author Jan den Besten
 */
class MY_Upload extends CI_Upload {

	protected $settings;
	protected $error='';
	protected $result;
  /**
   * Array met extra gecreerde bestanden door resizen
   */
  protected $extraFiles=array();
  
  /**
   */
	public function __construct($config=NULL) {
		parent::__construct($config);
		$this->config($config);
		$this->_CI->load->library('image_lib');
	}

  /**
   * Zelfde als origineel met wat extra automatische instellingen
   *
   * @param array $config 
   * @return void
   * @author Jan den Besten
   */
	public function config($config='') {
		if (!isset($config['upload_path'])) 	$config['upload_path'] = assets();
		if (!isset($config['allowed_types']))	$config['allowed_types'] = "jpg|jpeg|gif|png|mp3|wav|ogg|wma|pdf";
		$this->settings=$config;
		$this->resized=FALSE;
	}


  /**
   * Set the file name
   *
   * This function takes a filename/path as input and looks for the
   * existence of a file with the same name. If found, it will append a
   * number to the end of the filename to avoid overwriting a pre-existing file.
   *
   * JDB: Numbering is endless now, instead of 100.
   *
   * @param  string  $path
   * @param  string  $filename
   * @return  string
   */
  public function set_filename($path, $filename)
  {
    if ($this->encrypt_name === TRUE)
    {
      $filename = md5(uniqid(mt_rand())).$this->file_ext;
    }

    if ( ! file_exists($path.$filename))
    {
      return $filename;
    }

    $filename = str_replace($this->file_ext, '', $filename);

    $new_filename = '';
    // JDB Changes here: add timestamp to filename
    $i=(int)date('YmdHis');
    while (file_exists($path.$filename.'_'.$i.$this->file_ext)) { $i++; }
    $new_filename = $filename.'_'.$i.$this->file_ext;
    // Jdb To here

    if ($new_filename === '')
    {
      $this->set_error('upload_bad_filename');
      return FALSE;
    }
    else
    {
      return $new_filename;
    }
  }


  /**
   * Geeft foutmelding terug
   *
   * @return string
   * @author Jan den Besten
   */
	public function get_error() {
		return $this->error;
	}

  /**
   * Geeft resultaat terug
   *
   * @return mixed
   * @author Jan den Besten
   */
	public function get_result() {
		return $this->result;
	}

  /**
   * Geeft filename terug
   *
   * @return string
   * @author Jan den Besten
   */
	public function get_file() {
		return $this->file_name;
	}

  /**
   * Googeld met geheugeninstellingen om afbeeldingen te kunnen resizen
   *
   * @param string $imageInfo 
   * @return void
   * @author Jan den Besten
   * @internal
   */
	private function _setMemory($imageInfo) {
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



  /**
   * Download een bestand van een andere site (url) en stop die in de meegegeven map van je eigen site. Als het een afbeelding is wordt ook gecheckt of er meerdere formaten moeten worden gemaakt.
   * Wordt bijvoorbeeld gebruikt door de plugin die een wordpress site importeert om de afbeeldingen ook te importeren.
   *
   * @param string $url De url van het externe bestand
   * @param string $path assets map waar bestand in moet komen
   * @param string $prefix default='downloaded_' Prefix die aan het bestand moet worden toegevoegd.
   * @return string Nieuwe naam van bestand
   * @author Jan den Besten
   */
  public function download_and_add_file($url,$path,$prefix='downloaded_') {
    $name=$prefix.get_suffix($url,'/');
    $name=clean_file_name($name);
    $fullpath=$this->_CI->config->item('ASSETS').$path;
    $fullname=$fullpath.'/'.$name;
    
    $file = fopen ($url, "rb");
    if ($file) {
      $newf = fopen ($fullname, "wb");
      if ($newf)
      while(!feof($file)) {
        fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
      }
    }
    
    if ($file) { fclose($file); }
    if ($newf) { fclose($newf); }
    
    // if file exists, and more sizez needed, make them
    if (file_exists($fullname)) {
      $this->resize_image($name,$fullpath);
    }
    return $name;
  }


  /**
   * Upload een bestand
   *
   * @param string $file defaul='userfile' De veldnaam van het te uploaden bestand
   * @return bool TRUE als is gelukt
   * @author Jan den Besten
   */
	public function upload_file($file="userfile") {
		$goodluck=FALSE;
		$config=$this->settings;
		$this->initialize($config);
		$goodluck=$this->do_upload($file);
		if ($goodluck) {
      $this->error='';
			$this->result=$this->data();
			$this->file_name=$this->result['file_name'];
			$cleanName = clean_file_name($this->file_name);
      if (el('prefix',$config)) {
        $cleanName=el('prefix',$config).$cleanName;
      }
			if ($cleanName!=$this->file_name) {
				rename($config["upload_path"]."/".$this->file_name, $config["upload_path"]."/".$cleanName);
				$this->file_name=$cleanName;
			}
      // strace_($this->file_name);
			log_("info","[UPLOAD] uploaded: '$this->file_name'");
		}
    else {
      $this->error=$this->display_errors();
			log_("info","[UPLOAD] error while uploaded: '$this->file_name' [$this->error]");
    }
		return $goodluck;
	}
	
  
  /**
   * Controleert of afbeelding groot genoeg is
   *
   * @param string $file het afbeeldingsbestand
   * @param string $map pad naar bestand
   * @return bool TRUE als afbeelding groot genoeg is
   * @author Jan den Besten
   */
   public function check_size($file,$map) {
    $ok=FALSE;
    $size=getimagesize($map.'/'.$file);
    if (isset($size[0]) and isset($size[1])) {
  		$uPath=str_replace($this->_CI->config->item('ASSETS'),"",$map);
  		$cfg=$this->_CI->cfg->get('CFG_img_info',$uPath);
      if (isset($cfg['int_min_width']) and $cfg['int_min_width']>0 and isset($cfg['int_min_height']) and $cfg['int_min_height']>0) {
        // strace_($size);
        // strace_($cfg);
        // strace_($size[0]>=$cfg['int_min_width']);
        $ok=($size[0]>=$cfg['int_min_width'] and $size[1]>=$cfg['int_min_height'] );
      }
      else {
        $ok=TRUE;
      }
    }
    return $ok;
  }
  
  /**
   * Vult velden in database automatisch aan de hand van instellingen in **Media Info**
   *
   * @param string $image Bestand
   * @param string $path Pad naar bestand
   * @return bool
   * @author Jan den Besten
   */
	public function auto_fill_fields($image,$path) {
		$uPath=str_replace($this->_CI->config->item('ASSETS'),"",$path);
		$cfg=$this->_CI->cfg->get('CFG_media_info',$uPath,'fields_autofill_fields');
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
							$this->_CI->db->set( 'user', $this->_CI->flexy_auth->get_user()['id']);
							break;
						case 'media':
						case 'medias':
							$this->_CI->db->set($field,$image);
							break;
						case 'dat':
							$this->_CI->db->set($field,date("Y-m-d"));
							break;
						case 'str':
							$this->_CI->db->set($field,$cleanName);
							break;
					}
				}
        $this->_CI->db->insert($table);
			}
		}
		return TRUE;
	}
	
  /**
   * Resized een afbeelding aan de hand van instellingen in **Img Info**
   *
   * @param string $image Afbeelding
   * @param string $path Pad naar afbeelding
   * @return bool
   * @author Jan den Besten
   */
	public function resize_image($image,$path) {
		$this->file_name=$image;
		$goodluck=TRUE;
		$uPath=remove_assets($path);
		$cfg=$this->_CI->cfg->get('CFG_img_info',$uPath);
    
		$currentSizes=getimagesize($path."/".$this->file_name);

    // strace_($currentSizes);
    // strace_($uPath);
    // strace_($cfg);
    // trace_($this->_CI->cfg->data);

		// first resize copies
		$nr=1;
    $this->extraFiles=array();
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
				$this->_CI->image_lib->initialize($configResize);
				if ( !$this->_CI->image_lib->resize() ) {
					$this->error=$this->_CI->image_lib->display_errors().' -- '.$nr;
          // strace_($this->error);
          // strace_($configResize);
					$goodluck=FALSE;
				}
        else {
          // add extra files and set if they are visible or not
          $this->extraFiles[$copyName]=array('file'=>$copyName,'path'=>$path,'hidden'=>substr($pre,0,1)=='_');
        }
				$this->_CI->image_lib->clear();
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
				$this->_CI->image_lib->initialize($configResize);
				if ( !$this->_CI->image_lib->resize() ) {
					$this->error=$this->_CI->image_lib->display_errors().' -- SELF';
          // trace_($this->error);
					$goodluck=FALSE;
				}
				$this->_CI->image_lib->clear();
				// trace_('Resized original');
			}
		}

		// create cached thumb for flexyadmin if cache map exists
		if (file_exists($this->_CI->config->item('THUMBCACHE')) ) {
			$thumbSize=$this->_CI->config->item('THUMBSIZE');
			if ($currentSizes[0]>$thumbSize[0] or $currentSizes[1]>$thumbSize[1]) { 
				$configResize['source_image'] 	= $path."/".$this->file_name;
				$configResize['maintain_ratio'] = TRUE;
				$configResize['width'] 					= $thumbSize[0];
				$configResize['height'] 				= $thumbSize[1];
				$configResize['new_image']			= $this->_CI->config->item('THUMBCACHE').pathencode($uPath."/".$this->file_name,FALSE);
				$configResize['master_dim']			= 'auto';
				// set mem higher if needed
				$this->_setMemory($currentSizes);
				// trace_($configResize);
				$this->_CI->image_lib->initialize($configResize);
				if (!$this->_CI->image_lib->resize()) {
					$this->error=$this->_CI->image_lib->display_errors().' -- thumb';
					// trace_($this->error);
					$goodluck=FALSE;
				}
				$this->_CI->image_lib->clear();
				// trace_('Resized thumb in cache '.$configResize['new_image']);
			}
		}
		return $goodluck;
	}
  
  
  /**
   * Controleert of de orientatie van de afbeelding klopt met de meta-data, zo niet corrigeer dat (komt vooral voor bij mobiele apparaten)
   *
   * @param string $file 
   * @param string $path 
   * @return bool $success
   * @author Jan den Besten
   */
  public function restore_orientation($file,$path) {
    $fileandpath=$path.'/'.$file;

    // Als niet bestaat, stop er dan meteen maar mee
    if(!file_exists($fileandpath)) return false;

    // Of als de exifdata niet op te vragen is, stop dan ook meteen.
    if (!function_exists('read_exif_data')) return FALSE;
    
    // Get all the exif data from the file
    $exif=FALSE;
    $errorreporting=error_reporting(E_ERROR);
    $exif = read_exif_data($fileandpath, 'IFD0');
    error_reporting($errorreporting);

    // If we dont get any exif data at all, then we may as well stop now
    if(!$exif || !is_array($exif))  return false;
    $exif = array_change_key_case($exif, CASE_LOWER);
    
    // If theres no orientation key, then we can give up
    if(!array_key_exists('orientation', $exif)) return false;
    
    // strace_($exif['orientation']);
    
    // Start rotation
    $rotateConfig=array(
      'source_image' => $fileandpath,
      'new_image' => $fileandpath,
      'quality' => '100%',
    );
    
    switch($exif['orientation']) {
      // upside down
      case 3:
        $rotateConfig['rotation_angle']=180;
        break;
      // 90 left
      case 6:
        $rotateConfig['rotation_angle']=270;
        break;
      // 90 right
      case 8:
        $rotateConfig['rotation_angle']=90;
        break;

      // Orientation is ok (maybe flipped), just return true.
      default:
        return true;
        break;
    }
    
    $this->_CI->image_lib->initialize($rotateConfig);
    // strace_($rotateConfig['rotation_angle']);
    if ( ! $this->_CI->image_lib->rotate()) {
      $this->error=$this->_CI->image_lib->display_errors();
      $this->_CI->image_lib->clear();
      return false;
    }

    $this->_CI->image_lib->clear();
    return true;
  }
  


  /**
   * Geeft array met alle afbeeldingen bestanden die door resize_image() extra zijn aangemaakt
   *
   * @param bool $include_hidden default=FALSE Als TRUE dan worden ook de bestanden gegeven die 'hidden' zijn (beginnen met '_')
   * @return array array('file'=>bestandsnaam, 'path'=>map binnen assets, 'hidden'=>TRUE/FALSE als bestand hidden)
   * @author Jan den Besten
   */
  public function get_created_files($include_hidden=FALSE) {
    $return=$this->extraFiles;
    if (!$include_hidden and !empty($return)) {
      foreach ($return as $key => $file) {
        if ($file['hidden']) unset($return[$key]);
      }
    }
    return $return;
  }


  /**
   * @param string $open 
   * @param string $close 
   * @return void
   * @author Jan den Besten
   */
	public function display_errors($open = '', $close = '') {
		return trim(parent::display_errors($open,$close));
	}
  
  
	/**
	 * Verify that the filetype is allowed, set checking of mime in config
	 *
	 * @return	bool
	 */
  public function is_allowed_filetype($ignore_mime=FALSE) {
    $ignore_mime=$this->_CI->config->item('IGNORE_MIME',$ignore_mime);
    return parent::is_allowed_filetype($ignore_mime);
  }
  


}
?>
