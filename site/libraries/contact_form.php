<?

class Contact_form extends Module {

	public function __construct() {
		parent::__construct();
		$this->CI->load->library('form');
		$this->CI->load->library('email');
	}

	public function index($item, $submit='Verstuur', $sendText='<p>Bedankt voor uw mail.</p>') {
		
		$content='';
		
		// Form Fields
		$formData=array("str_name"		=>array("label"=>"Naam","validation"=>"required"),
										"email_email"	=>array("label"=>"Email","validation"	=>  "required|valid_email"),
										"str_subject"	=>array("label"=>"Onderwerp","validation"	=>  "required"),																				
										"txt_text"		=>array("type"=>"textarea","label"=>"Vraag","validation"=>"required"));
		// Form Buttons
		$formButtons=array('submit'=>array("submit"=>"submit","value"=>$submit));

		// Create form object and set fields and buttons
		$form=new form($this->CI->uri->get());
		$form->set_data($formData,"Contact");
		$form->set_buttons($formButtons);

		// Is form validation ok?
		if ($form->validation()) {
			// Yes, form is validated: Send mail
		
			// Get formdata and site email
			$formData=$form->get_data();
			$siteMail=$this->CI->site['email_email'];
			$siteAuthor=$this->CI->site['str_author'];

			// Setup mail
			$this->CI->email->to($siteMail,$siteAuthor);
			$this->CI->email->from($formData["email_email"]);
			$this->CI->email->subject("Email van site: '".$formData["str_subject"]."'");
			$this->CI->email->message($formData["txt_text"]);
			$this->CI->email->send();
		
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