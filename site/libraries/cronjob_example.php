<?php 

class Cronjob_example extends Module {

  public function __construct() {
    parent::__construct();
    $this->CI->load->library('email');
  }

	public function index() {
    $this->CI->email->to( $this->CI->site['email_email'] );
    $this->CI->email->from( 'info@flexyadmin.com' );
    $this->CI->email->subject( 'cronjob example from '.$this->CI->site['url_url'] );
    $this->CI->email->send();
    return $this->CI->email->print_debugger();
	}

}

?>