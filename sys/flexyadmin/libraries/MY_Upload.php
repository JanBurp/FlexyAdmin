<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/** \ingroup libraries
 * Uitbreiding op [CI_Upload](http://codeigniter.com/user_guide/libraries/file_uploading.html)
 *
 * @author Jan den Besten
 */
class MY_Upload extends CI_Upload {

	/**
	 * Maximum duplicate filename increment ID
	 */
	public $max_filename_increment = 10000000;
  

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
    if (!is_array($config['allowed_types']))  $config['allowed_types'] = preg_split('/[|,]/u', $config['allowed_types']);
		$this->settings=$config;
		$this->resized=FALSE;
    return $this;
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
   * @param string $prefixfix default='downloaded_' Prefix die aan het bestand moet worden toegevoegd.
   * @return string Nieuwe naam van bestand
   * @author Jan den Besten
   */
  public function download_and_add_file($url,$path,$prefixfix='downloaded_') {
    $name=$prefixfix.get_suffix($url,'/');
    $name=clean_file_name($name);
    $fullpath=$this->_CI->config->item('ASSETSFOLDER').$path;
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
    $this->error = '';
		$config=$this->settings;
		$this->initialize($config);
    
    // Start upload
		if ( !$this->do_upload( $file ) ) {
      $this->error = $this->display_errors();
			log_("info","[UPLOAD] error while uploaded: '$this->file_name' [$this->error]");
      return false;
    }
    
    // Als gelukt, pas naam aan als dat nodig is
			$this->result=$this->data();
			$this->file_name=$this->result['file_name'];
			$cleanName = clean_file_name($this->file_name);
      if (el('prefix',$config)) {
        $cleanName=el('prefix',$config).$cleanName;
      }
			if ($cleanName!=$this->file_name) {
			rename( $config['upload_path'].'/'.$this->file_name, $config['upload_path'].'/'.$cleanName);
				$this->file_name=$cleanName;
			}
			log_("info","[UPLOAD] uploaded: '$this->file_name'");
    return true;
		}
	
  
  /**
   * Controleert of afbeelding groot genoeg is
   *
   * @param string $path pad naar bestand
   * @param string $file het afbeeldingsbestand
   * @return bool TRUE als afbeelding groot genoeg is
   * @author Jan den Besten
   */
   public function check_size($path,$image,$sizes=array()) {
    if (empty($sizes)) $sizes = $this->assets->get_folder_settings($path);
    $size = @getimagesize( $this->_CI->config->item('ASSETSFOLDER').$path.'/'.$image);
    if (isset($size[0]) and isset($size[1])) {
      if ( el('min_width',$sizes)>0 and el('min_height',$sizes)>0) {
        return ( $size[0]>=$sizes['min_width'] and $size[1]>=$sizes['min_height'] );
      }
    }
    return true;
  }
  
  
  /**
   * Vult velden in database automatisch aan de hand van instellingen in **Media Info**
   * TODO: database model maken voor dit soort dingen
   *
   * @param string $image Bestand
   * @param string $path Pad naar bestand
   * @return bool
   * @author Jan den Besten
   */
	public function auto_fill_fields($image,$path) {
    // $uPath=str_replace($this->_CI->config->item('ASSETS'),"",$path);
    // $cfg=$this->_CI->cfg->get('CFG_media_info',$uPath,'fields_autofill_fields');
    // if (!empty($cfg)) {
    //   $fields=explode('|',$cfg);
    //   if (count($fields)>0) {
    //     foreach ($fields as $field) {
    //       $table=get_prefix($field,'.');
    //       $field=remove_prefix($field,'.');
    //       $fieldPre=get_prefix($field);
    //       if (empty($fieldPre)) $fieldPre=$field;
    //       $cleanName=str_replace('_',' ',get_file_without_extension($image));
    //       // TODO: database model maken voor dit soort dingen
    //       switch ($fieldPre) {
    //         case 'user':
    //           $this->_CI->db->set( 'user', $this->_CI->flexy_auth->get_user()['id']);
    //           break;
    //         case 'media':
    //         case 'medias':
    //           $this->_CI->db->set($field,$image);
    //           break;
    //         case 'dat':
    //           $this->_CI->db->set($field,date("Y-m-d"));
    //           break;
    //         case 'str':
    //           $this->_CI->db->set($field,$cleanName);
    //           break;
    //       }
    //     }
    //         $this->_CI->db->insert($table);
    //   }
    // }
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
	public function resize_image( $path, $image, $sizes=array() ) {
		$result=TRUE;
		$this->file_name=$image;
    if (empty($sizes)) $sizes = $this->assets->get_folder_settings($path);
		$current_size = @getimagesize( $this->_CI->config->item('ASSETSFOLDER').$path.'/'.$this->file_name);
    
    
		// 1) resize copies
		$nr=1;
    $this->extraFiles=array();
		while ( isset($sizes["create_$nr"]) ) {
			if ( $sizes["create_$nr"] ) {
        
				// check if resize is not bigger than original
				if ($current_size[0]<$sizes["width_$nr"] and $current_size[1]<$sizes["height_$nr"] ) {
					$sizes["width_$nr"]=$current_size[0];
					$sizes["height_$nr"]=$current_size[1];
				}
        // Name
				$prefix = $sizes["prefix_$nr"];
				$suffix = $sizes["suffix_$nr"];
				$ext=get_file_extension($this->file_name);
				$name=str_replace(".$ext","",$this->file_name);
				$create_name = $prefix.$name.$suffix.".".$ext;
        
        // Resize config
				$resize_config['source_image']   = $this->_CI->config->item('ASSETSFOLDER').$path.'/'.$this->file_name;
				$resize_config['maintain_ratio'] = TRUE;
				$resize_config['width']          = $sizes["width_$nr"];
				$resize_config['height']         = $sizes["height_$nr"];
				$resize_config['new_image']      = $this->_CI->config->item('ASSETSFOLDER').$path.'/'.$create_name;
				$resize_config['master_dim']     = 'auto';
        
        // Zorg voor voldoende geheugen
				$this->_setMemory($current_size);
        
        // Start resize
				$this->_CI->image_lib->initialize($resize_config);
				if ( !$this->_CI->image_lib->resize() ) {
					$this->error=$this->_CI->image_lib->display_errors().' -- '.$nr;
					$result = FALSE;
				}
        else {
          // add extra files and set if they are visible or not
          $this->extraFiles[$create_name]=array('file'=>$create_name,'path'=>$path,'hidden'=>substr($prefix,0,1)=='_');
        }
				$this->_CI->image_lib->clear();
			}
			$nr++;
		}

		// resize original
		if ( $sizes["resize_img"] and ($sizes['img_width']>0 and $sizes['img_height']>0) ) {

			// check if resize is necessary
			if ($current_size[0]>$sizes['img_width'] or $current_size[1]>$sizes['img_height'] ) {
				$resize_config['source_image']   = $this->_CI->config->item('ASSETSFOLDER').$path.'/'.$this->file_name;
				$resize_config['maintain_ratio'] = TRUE;
				$resize_config['width']          = $sizes['img_width'];
				$resize_config['height']         = $sizes['img_height'];
				$resize_config['new_image']      = "";
				$resize_config['master_dim']     = 'auto';

				// set mem higher if needed
				$this->_setMemory($current_size);
        
        // Rezize
				$this->_CI->image_lib->initialize($resize_config);
				if ( !$this->_CI->image_lib->resize() ) {
					$this->error=$this->_CI->image_lib->display_errors().' -- SELF';
					$result = FALSE;
				}
				$this->_CI->image_lib->clear();
			}
		}

		// Create cached thumb
		if (file_exists($this->_CI->config->item('THUMBCACHE')) ) {
			$thumbSize=$this->_CI->config->item('THUMBSIZE');
			$resize_config['source_image']   = $this->_CI->config->item('ASSETSFOLDER').$path.'/'.$this->file_name;
			$resize_config['maintain_ratio'] = TRUE;
			$resize_config['width']          = $thumbSize[0];
			$resize_config['height']         = $thumbSize[1];
			$resize_config['new_image']      = $this->_CI->config->item('THUMBCACHE').pathencode($path.'/'.$this->file_name,FALSE);
			$resize_config['master_dim']     = 'auto';
			$this->_setMemory($current_size);
			$this->_CI->image_lib->initialize($resize_config);
			if (!$this->_CI->image_lib->resize()) {
				$this->error=$this->_CI->image_lib->display_errors().' -- thumb';
				$result = FALSE;
			}
			$this->_CI->image_lib->clear();
		}
		return $result;
	}
  
  
  /**
   * Controleert of de orientatie van de afbeelding klopt met de meta-data, zo niet corrigeer dat (komt vooral voor bij mobiele apparaten)
   *
   * @param string $file 
   * @param string $path 
   * @return bool $success
   * @author Jan den Besten
   */
  public function restore_orientation($path,$file) {
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
      'new_image'    => $fileandpath,
      'quality'      => '100%',
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
