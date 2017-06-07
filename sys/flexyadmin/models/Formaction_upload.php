<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dit is een Formaction voor een eenvoudig upload formulier
 * 
 * Je kunt de standaard config velden gebruiken van de CodeIgniter File Uploading Class.
 * Als 'allowed_types' leeg is wordt gekeken naar de instellingen van 'Media Info' van het pad. ('upload_path').
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

 class Formaction_upload extends Formaction {
   
   var $settings = array(
     'upload_path'       => 'downloads',
     'allowed_types'     => ''
   );

   public function __construct() {
     parent::__construct();
     $this->load->model('assets');
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
      if (get_prefix($key)=='file' or get_prefix($key)=='media') {
        // En is de naam van het bestand bekend?
				if (isset($_FILES[$key]['name']) and !empty($_FILES[$key]['name']) ) {
          $file = $this->assets->upload_file('pictures',$key);
          if (!$file) {
            $this->errors .= '<span class="error">'.$this->assets->get_error().'</span>';
            return false;
          }
          else {
            $data[$key] = $file;
            unset($_FILES[$key]);
          }
				}
      }
    }
    $this->return_data = $data;
    return $return;
  }
  
  
  /**
   * Data is changed, return it
   *
   * @return array
   * @author Jan den Besten
   */
  public function return_data() {
    return $this->return_data;
  }
  
}
