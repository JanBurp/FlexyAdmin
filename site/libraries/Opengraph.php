<?php
class Opengraph extends Module {

  public function __construct() {
    parent::__construct();
  }

  /**
    * Kies afbeelding voor open-graph
  	*/
  public function index($page) {
    $image = '';
    if (isset($page['media_foto']) and !empty($page['media_foto'])) {
      $image = $this->CI->config->item('ASSETS').'pictures/'.$page['media_foto'];
    }
    elseif (isset($page['medias_fotos']) and !empty($page['medias_fotos'])) {
      $images = explode('|',$page['medias_fotos']);
      $image = $this->CI->config->item('ASSETS').'pictures/'.current($images);
    }
    else {
      if (isset($page['txt_text']) && preg_match('/<img.*src="(.*)"/uiU', $page['txt_text'],$match)) {
        $image = $match[1];
      }
    }

    $this->CI->site['image'] = $image;
    return $page;
  }

}

?>
