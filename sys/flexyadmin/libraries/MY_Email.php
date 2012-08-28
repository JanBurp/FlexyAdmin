<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Je kunt hiermee in één keer de email instellen
 * 
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
   * Stel de email class in één keer in met een array ipv losse methods aanroepen, bijvoorbeeld:
   * 
   *      $this->email->set_mail(
   *        'from'    => 'your@sender.com',
   *        'name'    => 'Name of Sender',
   *        'to'      => 'the@receiver.com',
   *        'cc'      => '',
   *        'bcc'     => '',
   *        'subject  => 'Subject of the email',
   *        'body'    => 'Hi, I think you get the picture now, are you?'
   *      );
   *
   * @param array $mail bijvoorbeeld: `array('name'=>'Naam van afzender', 'from'=>'info@flexyadmin.com', 'to'=>'info@flexyadmin.com', )`
   * @return array $this
   * @author Jan den Besten
   */
	public function set_mail($mail) {
    $this->total_send_addresses=0;
    if (isset($mail['name']))
      $this->from($mail['from'], $from['name']);
    else
      $this->from($mail['from']);
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
  
	
}
