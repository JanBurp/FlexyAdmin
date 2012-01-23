<?

class Contact_form extends Module {

	public function __construct() {
		parent::__construct();
		$this->CI->load->library('form');
		$this->CI->load->library('email');
	}

	public function index($page) {
		$content='';
		
		// Form
    $formData=$this->config('form_fields');
    $formButtons=$this->config('form_buttons');

		$form=new form($this->CI->uri->get());
		$form->set_data($formData, lang('form_name') );
    $form->prepare_for_clearinput();
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
			$this->CI->email->from($formData[$this->config('from_address_field')]);
			$this->CI->email->subject(lang('contact_mail_subject'));
      
			$body='';
			foreach ($formData as $key => $value) {
				if (substr($key,0,1)!='_') {
					$showKey=ucfirst(remove_prefix($key));
					$body.="<b>$showKey:&nbsp;</b>";
					$body.="$value<br/><br/>";
					if (isset($formData[$key]['options'][$value])) {
						$value=strip_tags($formData[$key]['options'][$value]);
					}
				}
			}
			$this->CI->email->message($body);
			$this->CI->email->send();
		
			// Show Send message
			$content=lang('contact_send_text');
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