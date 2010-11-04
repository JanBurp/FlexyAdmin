<?

function _module_contact_form($item, $submit='Verstuur', $sendText='<p>Bedankt voor uw mail.</p>') {
	$CI=&get_instance();
	
	// Load form library
	$CI->load->model('form');

	// Form Fields
	$formData=array("str_name"		=>array("label"=>"Naam","validation"=>"required"),
									"email_email"	=>array("label"=>"Email","validation"	=>  "required|valid_email"),
									"str_subject"	=>array("label"=>"Onderwerp","validation"	=>  "required"),																				
									"txt_text"		=>array("type"=>"textarea","label"=>"Vraag","validation"=>"required"));
	// Form Buttons
	$formButtons=array('submit'=>array("submit"=>"submit","value"=>$submit));

	// Create form object and set fields and buttons
	$form=new form($CI->get_uri());
	$form->set_data($formData,"Contact");
	$form->set_buttons($formButtons);

	// Is form validation ok?
	if ($form->validation()) {
		// Yes, form is validated: Send mail
		
		// Get formdata and site email
		$formData=$form->get_data();
		$siteMail=$CI->site['email_email'];
		$siteAuthor=$CI->site['str_author'];

		// Setup mail
		$CI->load->library('email');
		$CI->email->to($siteMail,$siteAuthor);
		$CI->email->from($formData["email_email"]);
		$CI->email->subject("Email van site: '".$formData["str_subject"]."'");
		$CI->email->message($formData["txt_text"]);
		$CI->email->send();
		
		// Show Send message
		$CI->add_content($sendText);
	}
	
	else {
		// Form isn't filled or validated: show form
		
		// Show validation errors if any
		$validationErrors=validation_errors('<p class="error">', '</p>');
		if (!empty($validationErrors)) $CI->add_content($validationErrors);
		
		// Show form
		$CI->add_content($form->render());
	}
}

?>