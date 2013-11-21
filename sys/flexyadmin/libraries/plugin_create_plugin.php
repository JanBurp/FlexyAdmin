<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Maakt een zipbestand van de gekozen module of plugin.
 * Met dit zipbestand kan de module/plugin geïnstalleerd worden met de plugin_install_plugin
 *  
 * @package default
 * @author Jan den Besten
 */
 
class Plugin_create_plugin extends Plugin {

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
                      'title'         => 'Create zipfile from module/plugin',
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
                         'create_zip' => array(
                            'label' =>  'Create zipfile',
                            'method' => 'create_zip'
                          ),

                      )
                    );
      $this->wizard = new Wizard($wizard);
      array_shift($args);
      $this->add_content("<h3>These files are added:</h3><ul>
        <li>The config file with the same name in 'site/config'</li>
        <li>All language files with the name and the suffix '_lang' in 'site/language/xx/'</li>
        <li>The module/plugin file in 'site/libraries'</li>
        <li>The view file with the same name in 'site/views'</li>
        </ul>
        <h3>readme.md</h3>
        <p>A 'readme.md' file is also generated and added. It contains:<ul>
        <li>The documentation that exists before the class definition in the core plugin/module file</li>
        <li>A list of all added files</li>
        <li>Before the zipfile will be created you can add more text if you like</li>
        <li>NB You may use <a href=\"https://en.wikipedia.org/wiki/Markdown\" target=\"_blank\">Markdown</a> in the documentation</li>
        </uL>
        <h3>If you need more files:</h3><ul>
        <li>Make sure a config file for the plugin/module file exists</li>
        <li>Add these lines (with the filenames you like to add):</li>
        </ul>
        <code>\$config['_files']=array(<br/>&nbsp;&nbsp;'db/example.sql'<br/>);</code><br/><br/>"
      );
      
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
    
    // Readme
    $code=read_file('site/libraries/'.$addon_file);
    $readme=strtoupper($addon)."\n".repeater('=',strlen($addon))."\n\n";
    if (preg_match("/\*\*(.*?)\*\//uis", $code,$matches)) {
      $help=$matches[1];
      $help=preg_replace("/^\s*\* ?/uism", "", $help);
      $help=preg_replace("/^@/uism", "\n@", $help);
      $readme.=$help;
    }

    sort($files);
    $files=array_combine($files,$files);
    
    $readme.="\n\nfiles\n------\n\n- ".implode("\n- ",$files);
    $readme.="\n\n".strtoupper($addon)." is a flexyadmin ".($is_plugin?'plugin':'module')." - packed at ".strftime('%d %b %Y %R')."\nwww.flexyadmin.com";

    // Choose files & name
    $form = new Form();
    $formdata=array(
      'readme'        =>array('label'=>'readme.md','type'=>'textarea','value'=>$readme),
      'files'         =>array('type'=>'dropdown','options'=>$files,'multiple'=>'multiple','value'=>$files),
      'zipname'       =>array('label'=>'Name of the zipfile','value'=>'flexyadmin_'.$addon.'.zip')
    );
    $form->set_data($formdata);
    
    if ($form->validation()) {
      $data=$form->get_data();
      $files=explode('|',$data['files']);
      $files=array_unique($files);

      $this->CI->session->set_userdata('readme',$data['readme']);
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
    $readme = $this->CI->session->userdata('readme');
    $files = $this->CI->session->userdata('addon_files');
    $zipname = $this->CI->session->userdata('zipname');
    $this->CI->session->unset_userdata('addon_files');
    $this->CI->session->unset_userdata('zipname');
  
    $this->CI->zip->add_data($addon.'_readme.md',$readme); 
    foreach ($files as $file) {
      $this->CI->zip->read_file($file,true); 
    }
    $this->CI->zip->download($zipname);
    
    $out.=p().'Download will start...'._p();
    return $out;
  }
  
	
}

?>