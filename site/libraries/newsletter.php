<?

class Newsletter extends Module {


	public function index($page) {
    $unsubmit=$this->CI->input->get('unsubmit');
    if ($unsubmit!==FALSE) return $this->unsubmit($page);
		return $this->submit($page);
	}

  public function submit($page) {
    $formOut='';
    $errors='';
    $message='';
		$this->CI->load->library('form');
    $form=new Form();
    $formFields = array('str_name'    => array( 'label'=>lang('str_name'), 'validation'=>'required' ),
                        'email_email' => array( 'label'=>lang('email_email'), 'validation'=>'required|valid_email' ),
                        'body'        => array( 'type'=>'hidden')
                       );
    $formButtons = array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) );
    $form->set_data($formFields,lang('submit_to_newsletter'));
		$form->set_buttons($formButtons);
    if ($form->validation()) {
      $data=$form->get_data();
      $data['tme_added']=standard_date('DATE_W3C',now());
			// Check for spam
			$spam=FALSE;
			if (!$spam and $this->_check_if_robot($data)) $spam=TRUE;
			if (!$spam and $this->_check_if_double($data,$this->config('table')))	$spam=TRUE;
      if (!$spam) {
        $this->CI->db->set('str_name',$data['str_name']);
        $this->CI->db->set('email_email',$data['email_email']);
        $this->CI->db->set('tme_added',$data['tme_added']);
        $this->CI->db->insert('tbl_newsletter_addresses');
        // Thank you
        $message=langp('thanks_for_submit_long',$this->CI->db->get_field('tbl_site','str_title'));
        $this->CI->load->library('email');
        $this->CI->email->to($data['email_email']);
        $this->CI->email->from($this->CI->db->get_field('tbl_site','email_email'));
        $this->CI->email->subject(lang('thanks_for_submit'));
        $this->CI->email->message($message);
    		$this->CI->email->send();
      }
    }
    else {
      $errors=validation_errors('<p class="error">', '</p>');
      $formOut=$form->render();
    }
    return $this->CI->view('newsletter/newsletter_submit.php',array('title'=>lang('submit_to_newsletter'), 'form'=>$formOut,'errors'=>$errors,'message'=>$message),true);
  }
  
  public function unsubmit($page) {
    $action=uri_string().'?unsubmit';
    $formOut='';
    $errors='';
    $message='';
    $id=$this->CI->input->get('id');
    $date=$this->CI->input->get('c');
    if ($id!==FALSE) {
      $this->CI->db->where('id',$id);
      $this->CI->db->where('tme_added',urldecode($date));
      $this->CI->db->delete('tbl_newsletter_addresses');
      $message=lang('unsubmit_succes');
    }
    else {
  		$this->CI->load->library('form');
      $form=new Form($action);
      $formFields = array(
                          'email_email' => array( 'label'=>lang('email_email'), 'validation'=>'required|valid_email' ),
                          'body'        => array( 'type'=>'hidden')
                         );
      $formButtons = array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) );
      $form->set_data($formFields,lang('unsubmit_to_newsletter'));
  		$form->set_buttons($formButtons);
      if ($form->validation()) {
        $data=$form->get_data();
  			// Check for spam
  			$spam=FALSE;
  			if (!$spam and $this->_check_if_robot($data)) $spam=TRUE;
        if (!$spam) {
          $this->CI->db->where('email_email',$data['email_email']);
          $this->CI->db->delete('tbl_newsletter_addresses');
          $message=lang('unsubmit_succes');
        }
      }
      else {
        $errors=validation_errors('<p class="error">', '</p>');
        $formOut=$form->render();
      }
    }
    return $this->CI->view('newsletter/newsletter_submit.php',array('title'=>lang('unsubmit_to_newsletter'),'form'=>$formOut,'errors'=>$errors,'message'=>$message),true);
  }

	private function _check_if_robot($data) {
		$robot=false;
		if (!empty($data['body'])) $robot=true;
		return $robot;
	}
	private function _check_if_double($data) {
		unset($data['body']);
		unset($data['tme_added']);
		foreach ($data as $field => $value) $this->CI->db->where($field,$value);
		$double=$this->CI->db->get_row('tbl_newsletter_addresses');
		if ($double) return TRUE;
		return FALSE;
	}



}

?>