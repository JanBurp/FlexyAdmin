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
    if ( !$this->CI->flexy_auth->is_super_admin()) return false;

    if (isset($args[0])) {
      $maps=array($args[0]);
    }
    else {
      $maps=$this->CI->assets->get_assets_folders(FALSE);
    }

    $actiondata=array();
    
    foreach ($maps as $map) {
      $this->add_message(h('Resize images in `'.$map.'`'));
      $files=read_map(assets().$map,FALSE,FALSE,FALSE);
      
      foreach ($files as $key => $file) {
        if (substr($key,0,1)!='_') $actiondata[]=array('action_url'=> $this->CI->config->item('API_home').'ajax/plugin/resize_images/'.$map.'/'.$file['name'], 'title'=>$map.'/'.$file['name']);
      }
    }
    
    if ($actiondata) {
      $this->CI->actiongrid->add_actions($actiondata);
      $this->add_content( $this->CI->actiongrid->view() );
    }
    
    return $this->view();
	}
  
  
  
  public function _ajax_api( $args ) {
    if ( !$this->CI->flexy_auth->is_super_admin()) return false;
    
		$args = func_get_args();
    $path = $args[0];
    $file = $args[1];
    $result=array('method'=>__METHOD__,'path'=>$path,'file'=>$file,'_message'=>'-');

    $this->CI->load->library('upload');
    if ($this->CI->upload->resize_image($path,$file)) {
      $result['_message']='resized';
    }
    else {
      $result['_message']=span('error').$this->CI->upload->get_error().'</span><br>'.assets().$path.'/'.$file;//'ERROR while resizing';
      $result['success']=false;
    }
    return $result;
  }


	function get_show_type() {
		return 'grid actiongrid';
	}
	
}

?>