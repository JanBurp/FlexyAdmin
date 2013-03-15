<?

/**
	* Standaard contact formulier
	*
	* Bestanden
	* ----------------
	*
	* - site/config/contact_form.php - Hier kun je een een aantal dingen instellen (zie hieronder)
	* - site/views/contact_form.php - De view waarin de comments en het formulier geplaatst worden
	* - site/language/##/contact_form_lang.php - Taalbestanden
	*
	* Installatie
	* ----------------
	*
	* - Pas de configuratie aan indien nodig (zie: site/config/contact_form.php)
	* - Pas de view (en styling) aan indien nodig
	* - Maak je eigen taalbestand en/of wijzig de bestaande
	*
	* @author Jan den Besten
	* @package FlexyAdmin_comments
	*
	*/
class Contact_form extends Module {

  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
		$this->CI->load->library('form');
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
		
		// Form
    $formData=$this->config('form_fields');
    $formButtons=$this->config('form_buttons');

    $form_id='contact_form';
		$form=new form($this->CI->uri->get(),$form_id);
		$form->set_data($formData, lang('form_name') );
    $form->prepare_for_clearinput();
		$form->set_buttons($formButtons);

		// Is form validation ok?
		if ($form->validation($form_id)) {
      $data=$form->get_data();

      // Action
      $this->CI->load->model($this->config('formaction'),'action');
      $this->CI->action->initialize($this->config)->fields($formData);
			if (!$this->CI->action->go( $data )) {
		    $errors=$this->CI->action->get_errors();
        $viewForm=div('message').$errors._div();
			}
      else {
  			// Show Send message
  			$viewForm=div('message').lang('contact_send_text')._div();
      }

		}
	
		else {
			// Form isn't filled or validated: show form and validation errors
			$viewErrors=validation_errors('<p class="error">', '</p>');
			$viewForm.=$form->render();
		}
		
    return $this->CI->view('contact_form',array('form'=>$viewForm,'errors'=>$viewErrors),true);
	}

}

?>