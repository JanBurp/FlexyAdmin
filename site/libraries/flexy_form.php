<?

/*
		This Plugin uses the flexy forms tables.
		If they don't exists, add them by running the sql file: add_flexy_forms.sql
*/

class Contact_formulier extends Module {

	public function __construct() {
		parent::__construct();
		$this->CI->load->library('form');
		$this->CI->load->model('getform');
		$this->CI->load->library('email');
	}

	public function index($page) {
		
		$content='';
		
		// Form Fields
		$formData=$this->CI->getform->by_module('contact_formulier');
	
		if ($formData) {
		
			// trace_($formData);
		
			$formFieldSets=$formData['fieldsets'];
			$formFields=$formData['fields'];
			$formButtons=$formData['buttons'];

			// Create form object and set fields and buttons
			$form=new form($this->CI->uri->get());
			$form->set_fieldsets($formFieldSets);
			$form->set_data($formFields,"Contact");
			$form->set_buttons($formButtons);

			// Is form validation ok?
			if ($form->validation()) {
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
					$content.=$formData['form']['txt_error'];
				}
				else {
					// Show Send message
					// $this->CI->add_content($formData['form']['txt_text'].'<p>&nbsp;</p>'.trace_($error,false).'<p>&nbsp;</p>'.$body.'<p>&nbsp;</p>'.trace_($formValues,false));
					$content.=$formData['form']['txt_text'];
				}

			}
			else {
				// Form isn't filled or validated: show form

				// Show validation errors if any
				$validationErrors=validation_errors('<p class="error">', '</p>');
				if (!empty($validationErrors)) $content.=$validationErrors;

				// Show form
				$content.=$form->render();
			}
		
		}
		
		return $content;
	}

}

?>