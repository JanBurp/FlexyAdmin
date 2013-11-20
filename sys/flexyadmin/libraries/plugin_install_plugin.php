<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Installeert een module/plugin (.zip bestand)
 *  
 * @package default
 * @author Jan den Besten
 */
 
class Plugin_install_plugin extends Plugin {

	public function __construct() {
		parent::__construct();
    $this->CI->load->library('form');
    $this->CI->load->model('file_manager');
    $this->CI->load->library('zip');
	}

	
  /**
   * @ignore
   */
   function _admin_api($args=false) {
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
  
      $form = new Form();
      $formdata=array('file_addon'=>array('label'=>'module/plugin','type'=>'file'));
      $form->set_data($formdata);
    
      if ($form->validation()) {
        
				if (isset($_FILES['file_addon']['name']) and !empty($_FILES['file_addon']['name']) ) {
          // Upload het bestand
          $settings['upload_path']='lists';
  				$settings['allowed_types']='zip';
					$this->CI->file_manager->initialize( $settings );
					$result=$this->CI->file_manager->upload_file('file_addon');
					if (!empty($result['file'])) {
            $path=SITEPATH.'assets/'.$settings['upload_path'];
            $file=$path.'/'.$result['file'];

            $this->add_content(h("Unpacking '".$result['file']."'",2));
            $readmeName='';
            $readme='';
            $skipped=array();
            $installed=array();
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
                    $zip->extractTo('./', array($name));
                    $installed[]=$name;
                  }
                }
              }
              $zip->close();
            } else {
              $this->add_content('Error installing Module/Plugin.');
            }
            
            $this->add_content(h($readmeName.':',2).'<textarea rows="15" style="width:100%;">'.$readme.'</textarea>');
            $this->add_content(h('Installed files:',2).ul($installed));
            $this->add_content(h('Skipped files:',2).ul($skipped));
            
            unlink($file);
            $this->add_content('<p><br/><a href="'.$this->CI->uri->get().'">Install another...</a></p>');
            
					}
          else {
            // Foutmelding
            $this->add_content($result['error']);
          }
				}
        else {
          trace_($_FILES);
        }
        
      }
      else {
        $this->add_content(p().'WARNING: Will overwrite existing files in module/plugin with same name!'._p());
        $this->add_content($form->render());
      }
      
      return $this->content;
		}
	}
  
  
	
}

?>