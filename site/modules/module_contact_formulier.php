<?

/*

This module uses the flexy forms tables.
If they don't exists, add them by running the sql file: add_flexy_forms.sql

*/


function _module_contact_formulier($item) {
	$CI=&get_instance();
	
	// Load form library
	$CI->load->model('form');

	// Form Fields
	
	$formData=$CI->getFormByModule('contact_formulier');
	
	if ($formData) {
		
		// trace_($formData);
		$formFields=$formData['fields'];
		$formButtons=$formData['buttons'];

		// Create form object and set fields and buttons
		$form=new form($CI->get_uri());
		$form->set_data($formFields,"Contact");
		$form->set_buttons($formButtons);

		// Is form validation ok?
		if ($form->validation()) {
			// Yes, form is validated: Send mail

			// Get formdata and site email
			$formValues=$form->get_data();
			$siteMail=$CI->site['email_email'];
			$siteAuthor=$CI->site['str_author'];
			$siteUrl=str_replace('http://','',$CI->site['url_url']);

			// Setup mail
			$CI->load->library('email');
			$CI->email->to($siteMail,$siteAuthor);

			// Is there a from email?
			$from=find_row_by_value($formFields,'email','validation',true);
			if ($from) {
				$from=current($from);
				$CI->email->from($from['value']);
			}
			$CI->email->subject("Email van site: $siteUrl");
			// body
			$body='';
			foreach ($formValues as $key => $value) {
				if (substr($key,0,1)!='_') {
					$showKey=remove_postfix($key);
					if (isset($formFields[$key]['options'][$value])) {
						$value=strip_tags($formFields[$key]['options'][$value]);
					}
					$body.="$showKey:\n$value\n\n";
				}
			}
			$CI->email->message($body);
			$CI->email->send();

			// Show Send message
			$CI->add_content($formData['form']['txt_text']);
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
}

?>