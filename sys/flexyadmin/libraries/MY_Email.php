<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup libraries
 * Uitbreiding op [CI_Email](http://codeigniter.com/user_guide/libraries/email.html)
 *
 * @author Jan den Besten
 */

class MY_Email extends CI_Email {
  
  /**
   * Houdt bij naar hoeveel adressen 
   */
  private $total_send_addresses=0;
  
  /**
   * Remember to for logging
   */
  private $to = array();

  /**
   * Taal voor het verzenden van emails uit cfg_email
   */
  private $lang='';
  
  /**
   * Default replace data
   */
  private $default_data = array();
  
  /**
   * Resulting body
   */
  private $body='';
  
  /**
   * Send with with/as pdf 
   */
  private $send_with_pdf=FALSE;
  
  
	/**
	 * Send Email, met logging
	 *
	 * @param	bool	$auto_clear = TRUE
	 * @return	bool
	 */
  public function send($auto_clear = TRUE) {
    if (empty( $this->to )) $this->to = el('To',$this->_headers, 'unknown' );
    if (!is_array($this->to)) $this->to = array($this->to);
    
    $send = parent::send(FALSE);
    $CI = &get_instance();
    $CI->load->model('log_activity');
    if ($send) {
      $CI->log_activity->email( implode_assoc( PHP_EOL, $this->_headers) ,'to', implode(',',$this->to) );
    }
    else {
      $CI->log_activity->email( $this->print_debugger('headers') ,'error', implode(',',$this->to) );
    }
    $this->to=array();
    if ($send and $auto_clear) $this->clear();
    return $send;
  }
  
  /**
   * to()
   * 
   * Zelfde als origineel, maar onthoud adressen voor logging
   *
   * @param string $to 
   * @return $this
   * @author Jan den Besten
   */
  public function to($to) {
    $this->add_to($to);
    return parent::to($to);
  }
  
  /**
   * cc()
   * 
   * Zelfde als origineel, maar onthoud adressen voor logging
   *
   * @param string $cc 
   * @return $this
   * @author Jan den Besten
   */
  public function cc($cc) {
    $this->add_to($cc);
    return parent::cc($cc);
  }
  
  /**
   * bcc()
   * 
   * Zelfde als origineel, maar onthoud adressen voor logging
   *
   * @param string $bcc 
   * @param string $limit [''] 
   * @return $this
   * @author Jan den Besten
   */
	public function bcc($bcc, $limit = '') {
    $this->add_to($bcc);
    return parent::bcc($bcc,$limit);
  }

  /**
   * Zelfde als CI clear(), met wat extra's
   *
   * @param string $clear_attachments 
   * @return void
   * @author Jan den Besten
   */
	public function clear($clear_attachments = FALSE) {
    $this->to=array();
    return parent::clear($clear_attachments);
	}
  

  /**
   * Test if an email could be send (send to a testmail)
   *
   * @return bool
   * @author Jan den Besten
   */
  public function can_send() {
	  $to="error@flexyadmin.com";
    if (defined("ERROR_EMAIL")) $to=ERROR_EMAIL;
    $this->to($to);
    $this->from($to);
		$this->subject('THIS IS A TEST');
		$this->message('TEST at '.date(DATE_RFC2822));
    return $this->send();
  }
  

  /**
   * Stel de email class in één keer in met een array ipv losse methods aanroepen, bijvoorbeeld:
   * 
   *      $this->email->set_mail(
   *        'from'    => 'your@sender.com',
   *        'name'    => 'Name of Sender',
   *        'to'      => 'the@receiver.com',
   *        'cc'      => '',
   *        'bcc'     => '',
   *        'subject' => 'Subject of the email',
   *        'body'    => 'Hi, I think you get the picture now, are you?'
   *      );
   *
   * @param array $mail bijvoorbeeld: `array('name'=>'Naam van afzender', 'from'=>'info@flexyadmin.com', 'to'=>'info@flexyadmin.com', )`
   * @return array $this
   * @author Jan den Besten
   */
	public function set_mail($mail) {
    $this->total_send_addresses=0;
    $this->body='';
    if (isset($mail['from'])) {
      if (isset($mail['name']))
        $this->from($mail['from'], $mail['name']);
      else
        $this->from($mail['from']);
    }
    if (isset($mail['to']))	{
      $this->add_to($mail['to']);
      $this->to($mail['to']);
      $this->total_send_addresses += $this->_count_addresses($mail['to']);
    }
    if (isset($mail['cc'])) {
      $this->add_to($mail['cc']);
      $this->cc($mail['cc']);
      $this->total_send_addresses += $this->_count_addresses($mail['cc']);
    }
    if (isset($mail['bcc'])) {
      $this->add_to($mail['bcc']);
      $this->bcc($mail['bcc']);
      $this->total_send_addresses += $this->_count_addresses($mail['bcc']);
    }
		$this->subject($mail['subject']);
		$this->message($mail['body']);
    if (isset($mail['attachment'])) {
      $this->attach($mail['attachment']);
    }
    return $this;
	}
  
  /**
   * Voeg adres aan de 'to' lijst voor logging
   *
   * @param mixed $to
   * @return void
   * @author Jan den Besten
   */
  private function add_to( $to ) {
    if (!is_array($to)) $to=explode(',',$to);
    $this->to = array_merge( $this->to,$to );
    $this->to = array_unique( $this->to );
    return $this;
  }
  
  /**
   * Telt aantal adressen
   *
   * @param string $addresses 
   * @return void
   * @author Jan den Besten
   * @internal
   */
  private function _count_addresses($addresses) {
    if (!is_array($addresses)) $addresses=explode(',',$addresses);
    return count($addresses);
  }
  
  /**
   * Geeft het aantal adressen waarnaar de mail is verzonden (To, Cc en Bcc)
   * 
   * Werkt alleen als de mail is ingesteld met set_mail()
   *
   * @return int het aantal adressen 
   * @author Jan den Besten
   */
  public function get_total_send_addresses() {
    return $this->total_send_addresses;
  }
  
  
  
  
  /**
   * Stel taal in (voordat je een mail stuurt met vanuit cfg_email)
   * Standaard wordt de taal ingesteld op de taal in de sessie (als die bestaat), of de taal die in de config is ingesteld
   *
   * @param string $lang 
   * @return $this
   * @author Jan den Besten
   */
  public function set_language($lang='') {
    if (empty($lang)) {
      $CI = &get_instance();
      if (isset($CI->session->userdata['language'])) {
        // is set in session?
        $lang=$CI->session->userdata['language'];
      }
      else {
        // default from config
        $lang=$CI->config->item('language');
      }
    }
    $this->lang=$lang;
    return $this;
  }
  
  
  /**
   * Set to send email with body as pdf
   *
   * @param string $pdf Default=TRUE. Kan ook de naam van de pdf bevatten
   * @return void
   * @author Jan den Besten
   */
  public function send_with_pdf($pdf=true) {
    $this->send_with_pdf=$pdf;
  }


  /**
   * Send a mail from cfg_emails
   * 
   * Standaard parse data:
   * site_url        => url zoals ingesteld in tbl_site
   * site_title      => title zoals ingesteld in tbl_site
   *
   * @param string $key The key in cfg_email to find the subject and body 
   * @param array $data Array of values that will be parsed in the subject and body, example: {name} $data=array('name'=>'My Name')
   * @param bool $prepare_body default=TRUE
   * @param string $body [''] Je kunt een expliciete body meegeven. (overruled de body uit cfg_email)
   * @return bool
   * @author Jan den Besten
   */
  public function send_lang($key,$data=array(),$prepare_body=TRUE, $body='' ) {
    if (empty($this->lang)) $this->set_language();
    $this->body='';
    
    // Get subject & body
    $CI = &get_instance();
    $mail = $CI->data->table('cfg_email')->where('key',$key)->get_row();
    if (!$mail) {
      $this->_set_error_message('email_key_not_found', $key);
      return false;
    }
    
    // Get mail info
    $subject=el('str_subject_'.$this->lang,$mail,'');
    if (empty($body)) $body=el('txt_email_'.$this->lang,$mail,'');
    if (empty($subject) or empty($body)) {
      $this->_set_error_message('email_subject_text_empty', $key);
      return false;
    }
    
    // Parse values in subject and body
    $this->_set_default_data();
    $data=array_merge($this->default_data,$data);
    $CI->load->library('parser');
    $subject = $CI->parser->parse_string($subject,$data,true);
    $body = $CI->parser->parse_string($body,$data,true);
    // Prepare body
    if ($prepare_body) $body = $this->prepare_body($body);
    $this->body=$body;

    // Create PDF?
    if ($this->send_with_pdf) {
      $pdf_name = 'mail_'.date('Y-m-d-G-i').'.pdf';
      if (is_string($this->send_with_pdf)) $pdf_name = $this->send_with_pdf;
      // Create PDF from HTML
      $CI->load->library('html2pdf/html2pdf');
      $html2pdf = new HTML2PDF('P', 'A4', 'en');
      $html2pdf->writeHTML($this->body);
      $file='site/cache/'.$pdf_name;
      $html2pdf->Output($file,'F');
      $this->attach($file);
    }

    // Set email
    $this->subject($subject);
    $this->message($body);
    
    // Send email
    return $this->send();
  }
  
  /**
   * Sets default data for send_lang()
   *
   * @return void
   * @author Jan den Besten
   */
  private function _set_default_data() {
    $CI = &get_instance();
    if (!isset($this->default_data['site_url']))    $this->default_data['site_url'] = site_url();
    if (!isset($this->default_data['site_title']))  $this->default_data['site_title'] = $CI->data->table('tbl_site')->get_field('str_title');
    if (!isset($this->default_data['today']))       $this->default_data['today'] = strftime('%A %e %B %Y');
    return $this;
  }
  
  
  
  /**
   * Zorgt voor juiste verwijzingen in de tekst van een mailbody en de juiste styling
   *
   * @param string $body 
   * @return string
   * @author Jan den Besten
   */
  public function prepare_body($body) {
    // good paths to local images
		$body=preg_replace('/src=\"(?!https?:\/\/).*?/','src="'.base_url(),$body);
    // good internal links
		$body=str_replace('href="mailto:','##MAIL##',$body);
		$body=str_replace('href="undefined/','href="'.base_url(),$body);
		$body=preg_replace('/href=\"(?!https?:\/\/).*?/','href="'.base_url(),$body);
		$body=str_replace('##MAIL##','href="mailto:',$body);
    return $body;
  }
  
  /**
   * Add styling to tags
   * 
   * Met deze functie kun je aan tags en tags met een class in de html van je email styling toevoegen.
   * Je geeft een array met styles mee:
   * - Waarvan de key de tag is of een tag met een class: 'td' of 'td.speciaal' bijvoorbeeld.
   * - De value komt dat bij al die tags ('<td>' of '<td class="speciaal">) in de vorm van een style attribuut: style="..."
   * - Let op dat bij emails overerving en dat soort zaken niet (of niet geheel) worden ondersteund.
   * - Verder is de volgorde van de style array van belang. Zorg ervoor dat een tag zonder class eerder komt dan dezelfde tag met een class.
   * 
   * Voorbeeld:
   * 
   * array(
   *  'td'           => 'color:grey',
   *  'td.speciaal'  => 'font-weight:bold',
   *  'p'            => 'font-family:Arial;font-size:16px;',
   *  'a'            => 'font-family:Arial;font-size:16px;'
   * )
   * 
   *
   * @param string $body
   * @param array $styles 
   * @return string
   * @author Jan den Besten
   */
  public function add_styles($body,$styles ) {
    if ($styles) {
      foreach ($styles as $tag => $style) {
        $class=get_suffix($tag,'.');
        $tag=remove_suffix($tag,'.');
        if ($class==$tag) $class='';
        // trace_([$tag,$class]);
        if ($class)
          $body = preg_replace("/<".$tag."(|\s[^>]*)(class=\"".$class."\")(|\s[^>]*)>/uiU", "<".$tag." class=\"".$class."\" style=\"".$style."\"$1$2>", $body);
        else
          $body = preg_replace("/<".$tag."(|\s[^>]*)>/uiU", "<".$tag." style=\"".$style."\"$1$2>", $body);
        
      }
    }
    return $body;
  }
  
  /**
   * Get resulting body after sending
   *
   * @return string
   * @author Jan den Besten
   */
  public function get_body() {
    return $this->body;
  }
  
  

	
}
