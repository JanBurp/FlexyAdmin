<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verwijderd een plugin
 *  
 * @package default
 * @author Jan den Besten
 */
 
class Plugin_remove_plugin extends Plugin {

  var $wizard;

	public function __construct() {
		parent::__construct();
    $this->CI->load->library('wizard');
    $this->CI->load->library('form');
	}

	
  /**
   * @ignore
   */
   function _admin_api($args=false) {
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
      
      $wizard = array(
                      'title'         => 'Delete module/plugin',
                      'object'        => $this,
                      'uri_segment'   => 4,
                      'steps' => array(
                         'choose' => array(
                            'label' =>  'Choose module/plugin',
                            'method' => 'choose_addon'
                          ),
                        'collect_files' => array(
                           'label' =>  'Collect Files',
                           'method' => 'collect_files'
                         ),
                         'delete_addon' => array(
                            'label' =>  'Delete',
                            'method' => 'delete_addon'
                          ),

                      )
                    );
      $this->wizard = new Wizard($wizard);
      array_shift($args);
      $this->add_content( $this->wizard->render().$this->wizard->call_step($args) );
      
      return $this->content;
		}
	}
  
  public function choose_addon() {
    $out='';
    $addons = read_map('site/libraries','php',TRUE,FALSE);
    $addons = array_unset_keys($addons,$this->config('exclude'));
    $addons = array_keys($addons);
    $addons = array_combine($addons,$addons);
    
    foreach ($addons as $key => $value) {
      $addons[$key]=str_replace('.php','',$value);
    }
    
    $form = new Form();
    $formdata=array('addon'=>array('label'=>'module/plugin','type'=>'dropdown','options'=>$addons));
    $form->set_data($formdata);
    
    if ($form->validation()) {
      $data=$form->get_data();
      $addon = $data['addon'];
      $addon=str_replace('.php','',$addon);
      $redirect=site_url($this->wizard->get_next_step_uri($addon));
      // trace_($redirect);
      redirect($redirect);
    }
    else {
      $out.=$form->render();
    }
    return $out;
  }
  
  public function collect_files($args) {
    $out='';
    
    $addon=$args[0];
    $addon_file=$addon.'.php';
    $is_plugin=(substr($addon,0,6)=='plugin');
    
    // Collect files with same name
    $files=array();
    $files[]='site/libraries/'.$addon_file;
    if (file_exists('site/config/'.$addon_file)) $files[]='site/config/'.$addon_file;
    if (file_exists('site/views/'.$addon_file)) $files[]='site/views/'.$addon_file;
    // lang files
    $langs = read_map('site/language','dir',FALSE,FALSE);
    $langs = array_keys($langs);
    $langfile=$addon.'_lang.php';
    foreach ($langs as $lang) {
      if (file_exists('site/language/'.$lang.'/'.$langfile)) $files[]='site/language/'.$lang.'/'.$langfile;
    }
    
    // Files mentioned in config
    if (file_exists('site/config/'.$addon_file)) {
      $this->CI->config->load($addon_file,true);
      $config=$this->CI->config->item($addon);
      if (isset($config['_files'])) {
        $extra_files=$config['_files'];
        foreach ($extra_files as $key=>$extra_file) {
          // Wildcard at end?
          if (substr($extra_file,-2)=='/*') {
            unset($extra_files[$key]);
            $map=substr($extra_file,0,strlen($extra_file)-2);
            $sub_files=read_map($map,'',TRUE,FALSE,FALSE,FALSE);
            if ($sub_files) $extra_files=array_merge($extra_files,array_keys($sub_files));
          }
        }
        $files=array_merge($files,$extra_files);
      }
    }
    
    sort($files);
    $files=array_combine($files,$files);
    
    // Choose files & name
    $form = new Form();
    $formdata=array(
      'files'         =>array('type'=>'dropdown','options'=>$files,'multiple'=>'multiple','value'=>$files)
    );
    $form->set_data($formdata);
    
    if ($form->validation()) {
      $data=$form->get_data();
      $files=explode('|',$data['files']);
      $files=array_unique($files);
      $this->CI->session->set_userdata('addon_files',$files);
      $redirect=site_url($this->wizard->get_next_step_uri($addon));
      // trace_($redirect);
      redirect($redirect);
    }
    else {
      $out.=$form->render();
    }
    
    return $out;
  }
  
  public function delete_addon($args) {
    $out='';
    $addon=$args[0];
    $files = $this->CI->session->userdata('addon_files');
    $this->CI->session->unset_userdata('addon_files');

    if ($files) {
      $maps=array();
      foreach ($files as $file) {
        $maps[]=remove_suffix($file,'/');
        if (!is_dir($file)) {
          if (unlink($file))
            $out.=$file.' is removed<br/>';
          else
            $out.='<b>Error while removing '.$file.'!</b><br/>';
        }
      }

      $maps=array_unique($maps);

      // Long maps first
      usort($maps, function($a, $b) {
        return strlen($b) - strlen($a);
      });
      
      foreach ($maps as $map) {
        if (file_exists($map) and is_dir($map)) {
          if (count_files($map)==0) {
            empty_map($map,$remove=true,$remove_hidden=true);
            $out.=strtoupper($map).' is removed<br/>';
          }
        }
      }
    }
    
    $out.=h($addon.' is removed',2);
    return $out;
  }
  
	
}

?>