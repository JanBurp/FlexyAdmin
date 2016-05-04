<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Resize alle afbeeldingen (opnieuw). Geef eventueel het pad mee, als dat niet is meegegeven dan worden alle paden uit cfg_img_info gedaan. NB Kan even duren...
 * 
 * @author Jan den Besten
 */
class Plugin_resize_images extends Plugin {

  public function __construct() {
		parent::__construct();
    $this->CI->load->model('actiongrid');
	}

	public function _admin_api($args=NULL) {
    if (isset($args[0])) {
      $maps=array($args[0]);
    }
    else {
      $imgInfo=$this->CI->cfg->get('cfg_img_info');
      $maps=array_keys($imgInfo);
    }

    $actiondata=array();
    
    foreach ($maps as $map) {
      $this->add_message(h('Resize images in `'.$map.'`'));
      $files=read_map(assets().$map,FALSE,FALSE,FALSE);
      
      foreach ($files as $key => $file) {
        if (substr($key,0,1)!='_') $actiondata[]=array('action_url'=>'admin/ajax/plugin/resize_images/'.$map.'/'.$file['name'], 'title'=>$map.'/'.$file['name']);
      }
    }
    
    if ($actiondata) {
      $this->CI->actiongrid->add_actions($actiondata);
      $this->add_content( $this->CI->actiongrid->view() );
    }
    
    return $this->view();
	}
  
  
  public function _after_update($data) {
    $nr=0;
    do {
      $wfield='int_width_'.$nr;
      $hfield='int_height_'.$nr;
      if ($nr==0) {
        $wfield='int_img_width';
        $hfield='int_img_height';
      }
      $width=el($wfield,$data,0);
      $height=el($hfield,$data,0);
      if (($width==0 and $height>0) or ($width>0 and $height==0)) {
        return 'Both Width/Height must be larger than 0 for resize sizes.';
      }
      $nr++;
    } while (isset($data['int_width_'.$nr]));
    return $data;
  }
  
  
  public function _ajax_api( $args ) {
		$args = func_get_args();
    $path = $args[0];
    $file = $args[1];
    $result=array('method'=>__METHOD__,'path'=>$path,'file'=>$file,'_message'=>'-');

    $this->CI->load->library('upload');
    if ($this->CI->upload->resize_image($file,assets().$path)) {
      $result['_message']='resized';
    }
    else {
      $result['_message']='ERROR while resizing';
      $result['success']=false;
    }
    return $result;
  }


	function get_show_type() {
		return 'grid actiongrid';
	}
	
}

?>