<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Resize alle afbeeldingen (opnieuw). Geef eventueel het pad mee, als dat niet is meegegeven dan worden alle paden uit cfg_img_info gedaan. NB Kan even duren...
 * 
 * @package default
 * @author Jan den Besten
 */
class Plugin_resize_images extends Plugin {

  /**
   * @ignore
   */
  function __construct() {
		parent::__construct();
    $this->CI->load->library('upload');
	}

  /**
   * Plugin wordt met URL worden aangeroepen
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function _admin_api($args=NULL) {
    if (isset($args[0])) {
      $maps=array($args[0]);
    }
    else {
      $imgInfo=$this->CI->cfg->get('cfg_img_info');
      $maps=array_keys($imgInfo);
    }
    
    foreach ($maps as $map) {
      $this->add_message(h('Resize images in `'.$map.'`'));
      $files=read_map(assets().$map);
      foreach ($files as $key => $value) {
        if ($this->CI->upload->resize_image($key,assets().$map)) {
          $this->add_message($key.' resized...');  
        }
        else {
          $this->add_message('ERROR while resizing '.$key);  
        }
      }
    }
    return $this->view();
	}


	
}

?>