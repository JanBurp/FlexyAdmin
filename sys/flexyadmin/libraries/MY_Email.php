<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Uitbreiding op [CI_Email](http://codeigniter.com/user_guide/libraries/email.html)
 *
 * @package default
 * @author Jan den Besten
 */

class MY_Email extends CI_Email {

  /**
   * Houdt bij naar hoeveel adressen 
   *
   * @var string
   */
  private $total_send_addresses=0;

  /**
   * Taal voor het verzenden van emails uit cfg_email
   *
   * @var string
   */
  private $lang='';
  
  private $default_data = array();

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
    if (isset($mail['from'])) {
      if (isset($mail['name']))
        $this->from($mail['from'], $mail['name']);
      else
        $this->from($mail['from']);
    }
    if (isset($mail['to']))	{
      $this->to($mail['to']);
      $this->total_send_addresses += $this->_count_addresses($mail['to']);
    }
    if (isset($mail['cc'])) {
      $this->cc($mail['cc']);
      $this->total_send_addresses += $this->_count_addresses($mail['cc']);
    }
    if (isset($mail['bcc'])) {
      $this->bcc($mail['bcc']);
      $this->total_send_addresses += $this->_count_addresses($mail['bcc']);
    }
		$this->subject($mail['subject']);
		$this->message($mail['body']);
    return $this;
	}
  
  /**
   * Telt aantal adressen
   *
   * @param string $addresses 
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
  private function _count_addresses($addresses) {
    $addresses=explode(',',$addresses);
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
   * Send a mail from cfg_emails
   * 
   * Standaard parse data:
   * site_url        => url zoals ingesteld in tbl_site
   * site_title      => title zoals ingesteld in tbl_site
   *
   * @param string $key The key in cfg_email to find the subject and body 
   * @param array $data Array of values that will be parsed in the subject and body, example: {name} $data=array('name'=>'My Name')
   * @return bool
   * @author Jan den Besten
   */
  public function send_lang($key,$data=array()) {
    if (empty($this->lang)) $this->set_language();
    
    // Get subject & body
    $CI = &get_instance();
    $CI->db->where('key',$key);
    $mail=$CI->db->get_row('cfg_email');
    if (!$mail) {
      $this->_set_error_message('email_key_not_found', $key);
      return false;
    }
    
    // Get mail info
    $subject=el('str_subject_'.$this->lang,$mail,'');
    $body=el('txt_email_'.$this->lang,$mail,'');
    if (empty($subject) or empty($body)) {
      $this->_set_error_message('email_subject_text_empty', $key);
      return false;
    }
    
    // Parse values in subject or body
    $this->_set_default_data();
    $data=array_merge($this->default_data,$data);
    $CI->load->library('parser');
    $subject = $CI->parser->parse_string($subject,$data,true);
    $body = $CI->parser->parse_string($body,$data,true);

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
    if (!isset($this->default_data['site_url']))    $this->default_data['site_url'] = $CI->db->get_field('tbl_site','url_url');
    if (!isset($this->default_data['site_title']))  $this->default_data['site_title'] = $CI->db->get_field('tbl_site','str_title');
    return $this;
  }
  
  
  
  /**
   * Zorgt voor juiste verwijzingingen in de tekst van een mailbody en de juiste styling
   *
   * @param string $body 
   * @return string
   * @author Jan den Besten
   * @ignore
   */
  public function prepare_body($body) {
    // good paths to local images
		$body=str_replace('src="','src="'.base_url(),$body);
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
   * @param string $body
   * @param array $styles 
   * @return string
   * @author Jan den Besten
   */
  public function add_styles($body,$styles) {
    if ($styles) {
      foreach ($styles as $tag => $style) {
        $body = preg_replace("/<".$tag."(|\s[^>]*)>/uiU", "<".$tag." style=\"".$style."\"$1>", $body);
      }
    }
    return $body;
  }
  
  

	
}
