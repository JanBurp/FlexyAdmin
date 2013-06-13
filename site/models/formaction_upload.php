<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dit is een Formaction voor een eenvoudig upload formulier (forms.upload_demo)
 * 
 * Je kunt de standaard config velden gebruiken van de CodeIgniter File Uploading Class.
 * Als 'allowed_types' leeg is wordt gekeken naar de instellingen van 'Media Info' van het pad. ('upload_path').
 * 
 * @package default
 * @author Jan den Besten
 */
 class Formaction_upload extends Formaction {
   
   var $settings = array(
     'upload_path'       => 'downloads',
     'allowed_types'     => ''
   );

   public function __construct() {
     parent::__construct();
 	   $this->load->library('upload');
 	   $this->load->model('file_manager');
   }
   
   /**
    * Voer de actie uit, in dit geval: upload het bestand
    *
    * @param string $data data teruggekomen van het formulier
    * @return int id van toegevoegde data in de database
    * @author Jan den Besten
    */
  public function go($data) {
    parent::go($data);
    $return=TRUE;

    foreach ($data as $key => $value) {
      // Is er een file veld?
      if (get_prefix($key)=='file') {
        // En is de naam van het bestand bekend?
				if (isset($_FILES[$key]['name']) and !empty($_FILES[$key]['name']) ) {
          // Upload het bestand
          if (empty($this->settings['allowed_types'])) {
    				$mediaCfg=$this->cfg->get('CFG_media_info',$this->settings['upload_path']);
    				$this->settings['allowed_types']=$mediaCfg['str_types'];
          }
					$this->file_manager->initialize( $this->settings );
					$result=$this->file_manager->upload_file($key);
          // Gelukt?
					if (!empty($result['file'])) {
            // Zo ja pas formdata aan, voeg bestand toe aan mediatable, en geef bericht
            $path=SITEPATH.'assets/'.$this->settings['upload_path'];
            $file=$path.'/'.$result['file'];
						$formData[$key]=$result['file'];
            $value=$result['file'];
            $this->mediatable->add($value,$path);
					}
          else {
            // Foutmelding
            $this->errors.=$result['error'];
            $return=FALSE;
          }
				}
      }
    }
    return $return;
  }
}
