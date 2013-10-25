<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Maakt een zipbestand van de gekozen module of plugin.
 * Dit zipbestand kan dan gebruikt worden met de plugin_add_plugin
 *  
 * @package default
 * @author Jan den Besten
 */
 
class Plugin_create_addon extends Plugin {

  var $wizard;

	public function __construct() {
		parent::__construct();
    $this->CI->load->library('wizard');
    $this->CI->load->library('form');
    $this->CI->load->library('zip');
	}

	
  /**
   * @ignore
   */
   function _admin_api($args=false) {
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
      
      $wizard = array(
                      'title'         => 'Create addon',
                      'object'        => $this,
                      'uri_segment'   => 4,
                      'steps' => array(
                         'choose' => array(
                            'label' =>  'Choose addon',
                            'method' => 'choose_addon'
                          ),
                        'collect_files' => array(
                           'label' =>  'Collect Files',
                           'method' => 'collect_files'
                         ),
                         'create_zip' => array(
                            'label' =>  'Create zipfile',
                            'method' => 'create_zip'
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
    $formdata=array('addon'=>array('type'=>'dropdown','options'=>$addons));
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
    
    // Collect files
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
    sort($files);
    $files=array_combine($files,$files);

    // Extra files?
    $form = new Form();
    $formdata=array(
      'files'         =>array('type'=>'dropdown','options'=>$files,'multiple'=>'multiple','value'=>$files),
      'extra_files'   =>array('label'=>'Extra files'),
      'html'          =>array('label'=>'(help)','type'=>'html','value'=>'<p>Extra files with full path (from your site) and split with a pipe. Example: <i>db/example.txt|site/assets/js/example.js</i>'),
      'zipname'       =>array('label'=>'Name of the zipfile','value'=>'flexyadmin.'.$addon.'.zip')
    );
    $form->set_data($formdata);
    
    if ($form->validation()) {
      $data=$form->get_data();
      $files = explode('|',$data['files']);
      $extra_files = explode('|',$data['extra_files']);
      $files=array_merge($files,$extra_files);
      $files=array_unique($files);

      $this->CI->session->set_userdata('addon_files',$files);
      $this->CI->session->set_userdata('zipname',$data['zipname']);
      $redirect=site_url($this->wizard->get_next_step_uri($addon));
      // trace_($redirect);
      redirect($redirect);
    }
    else {
      $out.=$form->render();
    }
    
    return $out;
  }
  
  public function create_zip($args) {
    $out='';
    $addon=$args[0];
    $files = $this->CI->session->userdata('addon_files');
    $zipname = $this->CI->session->userdata('zipname');
    $this->CI->session->unset_userdata('addon_files');
    $this->CI->session->unset_userdata('zipname');
    
    foreach ($files as $file) {
      $this->CI->zip->read_file($file,true); 
    }
    $this->CI->zip->download($zipname);
    
    $out.=p().'Download will start...'._p();
    return $out;
  }
  
	
}

?>