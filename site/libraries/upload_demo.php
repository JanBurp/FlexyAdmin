<?

/**
	* Voorbeeld van een eenvoudig Upload formulier
	*
	* Bestanden
	* ----------------
	*
	* - site/config/upload_demo.php - Hier kun je een een aantal dingen instellen (zie hieronder)
	* - site/views/upload_demo.php - De view waarin het formulier en eventuele meldingen komen te staan
	*
	* Installatie
	* ----------------
	*
	* - Pas de configuratie aan indien nodig (zie: site/config/upload_demo.php)
	* - Pas de view (en styling) aan indien nodig
	*
	* @author Jan den Besten
	* @package FlexyAdmin_comments
	*
	*/
class Upload_demo extends Module {

  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
		$this->CI->load->library('form');
		$this->CI->load->library('upload');
		$this->CI->load->model('file_manager');
	}

  /**
  	* Hier wordt het formulier toegevoegd aan de huidige pagina
  	*
  	* @param string $page 
  	* @return void
  	* @author Jan den Besten
  	* @ignore
  	*/
	public function index($page) {
		$viewForm='';
    $viewErrors='';
    $message='';
		
		// Form
    $formData=array(
      'file_upload'		  => array( 'type'=>'file')
    );

		$form=new form($this->CI->uri->get());
		$form->set_data($formData );

		// Is form validation ok?
		if ($form->validation()) {
			// Yes, form is validated: Upload file
      
			$formData=$form->get_data();
      
      // Process each field
			foreach ($formData as $key => $value) {
        
        // Hier gebeurd het uploaden....
        
        // Is er een file veld?
        if (get_prefix($key)=='file') {
          
          // En is de naam van het bestand bekend?
					if (isset($_FILES[$key]['name']) and !empty($_FILES[$key]['name']) ) {
            
            // Upload het bestand
						$this->CI->file_manager->init( $this->config('upload_folder'), $this->config('upload_types') );
						$result=$this->CI->file_manager->upload_file($key);
            
            // Gelukt?
						if (!empty($result['file'])) {
              // Zo ja pas formdata aan, voeg bestand toe aan mediatable, en geef bericht
              $path=SITEPATH.'assets/'.$this->config('upload_folder');
              $file=$path.'/'.$result['file'];
							$formData[$key]=$result['file'];
              $value=$result['file'];
              $this->CI->mediatable->add($value,$path);
              $message='Uploaden van <i>"'.$value.'"</i> is gelukt';
						}
            else {
              // Foutmelding
              $message=$result['error'];
            }
              
					}
        }
        
			}

		}
	
		else {
			// Form isn't filled or validated: show form and validation errors
			$viewErrors=validation_errors('<p class="error">', '</p>');
			$viewForm.=$form->render();
		}
		
		return $this->CI->view('upload_demo',array('form'=>$viewForm,'errors'=>$viewErrors,'message'=>$message),true);
	}

}

?>