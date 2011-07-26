<?

class Contact_form extends CI_Model {

	function Contact_form() {
		parent::__construct();
		$this->load->model('form');
	}

	function main($item, $submit='Verstuur', $sendText='<p>Bedankt voor uw mail.</p>') {
		
		$content='';
		
		// Form Fields
		$formData=array("str_name"		=>array("label"=>"Naam","validation"=>"required"),
										"email_email"	=>array("label"=>"Email","validation"	=>  "required|valid_email"),
										"str_subject"	=>array("label"=>"Onderwerp","validation"	=>  "required"),																				
										"txt_text"		=>array("type"=>"textarea","label"=>"Vraag","validation"=>"required"));
		// Form Buttons
		$formButtons=array('submit'=>array("submit"=>"submit","value"=>$submit));

		// Create form object and set fields and buttons
		$form=new form($this->uri->get());
		$form->set_data($formData,"Contact");
		$form->set_buttons($formButtons);

		// Is form validation ok?
		if ($form->validation()) {
			// Yes, form is validated: Send mail
		
			// Get formdata and site email
			$formData=$form->get_data();
			$siteMail=$this->site['email_email'];
			$siteAuthor=$this->site['str_author'];

			// Setup mail
			$this->load->library('email');
			$this->email->to($siteMail,$siteAuthor);
			$this->email->from($formData["email_email"]);
			$this->email->subject("Email van site: '".$formData["str_subject"]."'");
			$this->email->message($formData["txt_text"]);
			$this->email->send();
		
			// Show Send message
			$content=$sendText;
		}
	
		else {
			// Form isn't filled or validated: show form
		
			// Show validation errors if any
			$validationErrors=validation_errors('<p class="error">', '</p>');
			if (!empty($validationErrors)) $content.=($validationErrors);
		
			// Show form
			$content.=$form->render();
		}
		
		return $content;
		
	}

}

?>