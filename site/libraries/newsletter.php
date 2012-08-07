<?

/**
 * Nieuwsbrief
 *
 * Samen met de bijbehorende plugin kan hiermee een nieuwsbrief functie aan de site worden gekoppeld.
 * Deze module verzorgt het aan en afmelden van bezoekers aan de nieuwsbrief
 *
 * <h2>Bestanden</h2>
 * - site/config/plugin_newsletter.php - Plugin voor het admin deel
 * - db/add_newsletter.sql - database bestand met de benodigde tabel
 * - site/views/newsletter - De views en email templates
 * - site/language/##/newsletter_lang.php - Taalbestanden
 *
 * <h2>Installatie</h2>
 * - Laad het database bestand db/add_newsletter.sql
 * - Pas de view (en styling) aan indien nodig
 * - Maak je eigen taalbestand en/of wijzig de bestaande
 *
 * @author Jan den Besten
 * @package FlexyAdmin_newsletter
 *
 */
class Newsletter extends Module {


  /**
   * Roept newsletter.submit aan, maar als er een unsubmit waarde bekend is dan wordt newsletter.unsubmit aangeroepen
   *
   * @param string $page
   * @return string 
   * @author Jan den Besten
   */
   public function index($page) {
    $unsubmit=$this->CI->input->get('unsubmit');
    if ($unsubmit!==FALSE) return $this->unsubmit($page);
		return $this->submit($page);
	}

  /**
   * Verzorgt de aanmelding aan de nieuwsbrief. Toont het aanmeldformulier.
   *
   * @param string $page 
   * @return void
   * @author Jan den Besten
   */
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
  
  /**
   * Verzorgd het afmelden van de nieuwsbrief.
   *
   * @param string $page 
   * @return void
   * @author Jan den Besten
   */
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

  /**
   * Test of een robot zich aanmeld
   *
   * @param string $data 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	private function _check_if_robot($data) {
		$robot=false;
		if (!empty($data['body'])) $robot=true;
		return $robot;
	}
  
  /**
   * Test of het een dubbele aanmelding is
   *
   * @param string $data 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
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