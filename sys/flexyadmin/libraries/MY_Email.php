<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Email extends CI_Email {

  var $total_send_addresses=0;

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
  
  private function _count_addresses($addresses) {
    $addresses=explode(',',$addresses);
    return count($addresses);
  }
  
  public function get_total_send_addresses() {
    return $this->total_send_addresses;
  }
  
	
}
