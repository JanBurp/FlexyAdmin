<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Installeert een module/plugin (.zip bestand)
 *  
 * @author Jan den Besten
 */
 
class Plugin_install_plugin extends Plugin {

	public function __construct() {
		parent::__construct();
    $this->CI->load->library('form');
    $this->CI->load->model('file_manager');
    $this->CI->load->library('zip');
    $this->CI->load->dbutil();
    $this->CI->load->helper('markdown');
    $this->CI->load->library('table');
	}

	
  /**
   */
   function _admin_api($args=false) {
     
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
      
      $form = new Form();
      $formdata=array('file_addon'=>array('label'=>'module/plugin','type'=>'file'));
      $form->set_data($formdata);
      $form->set_caption('Install plugin');
      
			if (isset($_FILES['file_addon']['name']) and !empty($_FILES['file_addon']['name']) ) {
        // Upload het bestand
        $settings['upload_path']='../cache';
				$settings['allowed_types']='zip|sql';
				$this->CI->file_manager->initialize( $settings );
				$result=$this->CI->file_manager->upload_file('file_addon');
				if (!empty($result['file'])) {
          $path=SITEPATH.'assets/'.$settings['upload_path'];
          $file=$path.'/'.$result['file'];
          $ext=get_suffix($file,'.');
          
          // SQL FILE
          if ($ext=='sql') {
            $this->add_content(h("Import '".$result['file']."'",2));
            $readmeName=$result['file'];
            $readme=$this->get_readme($file);
            $sql=file_get_contents($file);
            if ($this->CI->dbutil->is_safe_sql($sql,true,true)) {
              $this->CI->dbutil->import($sql);
              $imported[]=$file;
            }
          }
          
          // ZIP FILE
          else {
            $this->add_content(h("Unpacking '".$result['file']."'",2));
            $readmeName='';
            $readme='';
            $skipped=array();
            $installed=array();
            $errors=array();
            $zip = new ZipArchive;
            if ($zip->open($file) === true) {
              for($i = 0; $i < $zip->numFiles; $i++) {
                $name=$zip->getNameIndex($i);
                // If not hidden or dir
                if (!(substr($name,0,1)=='_' or !substr($name,0,1)=='.' or !substr($name,-1)=='/')) {
                  if (has_string('readme',$name)) {
                    $readme=$zip->getFromName($name);
                    $readmeName=get_suffix($name,'/');
                  }
                  // Safe to install?
                  if (!has_string($this->config['safe_paths'],$name)) {
                    $skipped[]=$name;
                  }
                  else {
                    // sql?
                    $ext=get_suffix($name,'.');
                    if ($ext=='sql') {
                      $sql=$zip->getFromName($name);
                      if ($this->CI->dbutil->is_safe_sql($sql,true,true)) {
                        $result=$this->CI->dbutil->import($sql);
                        if (!empty($result['errors'])) {
                          $errors=array_merge($errors,$result['errors']);
                        }
                        $imported[]=$name;
                      }
                    }
                    // Only install files (directories are automatically installed)
                    if (has_string('.',$name)) {
                      $zip->extractTo('./', array($name));
                      $installed[]=$name;
                    }
                  }
                }
              }
              $zip->close();
            } else {
              $this->add_content('Error installing Module/Plugin.');
            }
          }
          
          if (!empty($readme)) {
            $readme=Markdown($readme);
            $this->add_content($readme);
            $this->add_content('<hr>');
          }
          // $this->add_content(h($readmeName.':',2).'<textarea rows="15" style="width:100%;">'.$readme.'</textarea>');
          if (isset($installed)) $this->add_content(h('Installed files:',2).ul($installed));
          if (isset($imported))  $this->add_content(h('Imported files:',2).ul($imported));
          if (isset($skipped))   $this->add_content(h('Skipped files:',2).ul($skipped));
          if (!empty($errors))   {
            $this->add_content(h('Errors:',2,'error'));
            foreach ($errors as $error) {
              $this->add_content(p('error').$error['message']._p());
            }
          }
          
          
          unlink($file);
          $this->add_content('<p><br/><a href="'.$this->CI->uri->get().'">Install another...</a></p>');
          
				}
        else {
          // Foutmelding
          $this->add_content($result['error']);
        }
			}

      $this->add_content(br().br().p().'WARNING: Will overwrite existing files in module/plugin with same name!'._p());
      $this->add_content($form->render());
      /**
       * Show list and docs of all plugins
       */
      $files=read_map('plugins','zip,sql', FALSE,FALSE,FALSE);
      foreach ($files as $file => $info) {
        $readme=$this->get_readme('plugins/'.$file);
        $files[$file]['readme']=$readme;
      }
    
      $this->add_content(h('Plugins:',2));
      $this->CI->table->set_heading(array('Plugin', 'Readme'));
      $this->CI->table->set_template(array('table_open'  => '<table class="list_of_plugins table-class">')); 
      foreach ($files as $file => $info) {
        $this->CI->table->add_row(array($file, $info['readme']));
      }
      $this->add_content( $this->CI->table->generate() );
      
      return $this->content;
		}
	}
  

  /**
   * Geeft readme documentation van plugin
   *
   * @param string $file 
   * @return string
   * @author Jan den Besten
   */
  private function get_readme($file) {
    $readme='';
    $ext=get_suffix($file,'.');
    if ($ext=='sql') {
      $sql=file_get_contents($file);
      $readme=$sql;
      $readme = preg_replace("/^[^#].*\n/uUm", "", $readme);
      $readme = preg_replace("/^#(.*\n)/uUm", "$1", $readme);
    }
    else {
      $zip = new ZipArchive;
      if ($zip->open($file) === true) {
        $plugin=remove_suffix($file,'.');
        $plugin=str_replace('flexyadmin_','',$plugin);
        $readmefile=$plugin.'_readme.md';
        $readmefile=get_suffix($readmefile,'/');
        $readme=$zip->getFromName($readmefile);
      }
    }
    $readme=Markdown($readme);
    return $readme;
  }
  
  
	
}

?>