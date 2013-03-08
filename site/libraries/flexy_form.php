<?

/**
	* Formulier dat de gebruiker zelf kan instellen
	*
	* In de database zijn twee extra tabellen waarin de gebruiker formulieren kan maken en aanpassen.
	*
	* Bestanden
	* ---------
	*
	* - site/config/flexy_form.php - Hier kun je een een aantal dingen instellen (zie hieronder)
	* - db/add_flexy_forms.sql - database bestand met de benodigde tabel
	* - site/views/flexy_form.php - De view waarin de comments en het formulier geplaatst worden
	*
	* Installatie
	* -----------
	*
	* - Laad het database bestand db/add_flexy_forms.sql
	* - Pas de configuratie aan indien nodig (zie: site/config/flexy_form.php)
	* - Pas de view (en styling) aan indien nodig
	*
	* @author Jan den Besten
	* @package FlexyAdmin_contact_formulier
	*/

class Flexy_form extends Module {
  

  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
		$this->CI->load->library('form');
		$this->CI->load->model('getform');
		$this->CI->load->library('email');
	}

  /**
  	* Hier wordt de module aangeroepen
  	*
  	* @param string $page
  	* @param string $form_name['flexy_form'] Naam van formulier.
  	* @return string 
  	* @author Jan den Besten
  	* @ignore
  	*/
	public function index($page,$form_name='flexy_form') {
		$viewForm='';
    $viewErrors='';
		
		// Form Fields
		$formData=$this->CI->getform->by_module($form_name);
	
		if ($formData) {
		
      // trace_($formData);
		
			$formFieldSets=$formData['fieldsets'];
			$formFields=$formData['fields'];
			$formButtons=$formData['buttons'];

			// Create form object and set fields and buttons
      $form_id=$form_name;
  		$form=new form($this->CI->uri->get(),$form_id);
			$form->set_fieldsets($formFieldSets);
			$form->set_data($formFields,"Contact");
			$form->set_buttons($formButtons);

			// Is form validation ok?
			if ($form->validation($form_id)) {
				// Yes, form is validated: Send mail

				// Get formdata and site email
				$formValues=$form->get_data();
				$siteMail=$this->CI->site['email_email'];
				$siteAuthor=$this->CI->site['str_author'];
				$siteUrl=str_replace('http://','',$this->CI->site['url_url']);

				// Setup mail
				$this->CI->email->initialize(array('mailtype'=>'html'));
				$this->CI->email->to($siteMail,$siteAuthor);

				// Is there a from email?
				$from=find_row_by_value($formFields,'email','validation',true);
				if ($from) {
					$from=current($from);
					$this->CI->email->from($from['value']);
				}
				$this->CI->email->subject("Email van site: $siteUrl");
				// body
				$body='';
				foreach ($formValues as $key => $value) {
					if (substr($key,0,1)!='_') {
						if ($formFields[$key]['type']=='checkbox') {if ($value) $value=strip_tags($formFields[$key]['html']); else $value='Nee';}
						$showKey=ucfirst(remove_suffix($key));
						$body.="<b>$showKey:&nbsp;</b>";
						if ($formFields[$key]['type']=='textarea') $body.="<br/>";
						$body.="$value<br/><br/>";
						if (isset($formFields[$key]['options'][$value])) {
							$value=strip_tags($formFields[$key]['options'][$value]);
						}
					}
				}
				$this->CI->email->message($body);
				$succes=$this->CI->email->send();
				if (!$succes) {
					// Show Error message
					$viewForm.=$formData['form']['txt_error'];
				}
				else {
					// Show Send message
					// $this->CI->add_content($formData['form']['txt_text'].'<p>&nbsp;</p>'.trace_($error,false).'<p>&nbsp;</p>'.$body.'<p>&nbsp;</p>'.trace_($formValues,false));
					$viewForm.=$formData['form']['txt_text'];
				}

			}
  		else {
  			// Form isn't filled or validated: show form and validation errors
  			$viewErrors=validation_errors('<p class="error">', '</p>');
  			$viewForm.=$form->render();
  		}
		}
		
		return $this->CI->view('flexy_form',array('form'=>$viewForm,'errors'=>$viewErrors),true);
	}

}

?>