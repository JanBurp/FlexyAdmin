<?php 


/**
 * Dit is een cronjob voorbeeld. Het stuurt een mail om de zoveel tijd.
 * 
 * Cronjobs kun je instellen in site/config.php, zie daar voor meer info.
 *
 * @package default
 * @author Jan den Besten
 */


class Cronjob_example extends Module {

  public function __construct() {
    parent::__construct();
    $this->CI->load->library('email');
  }

	public function index() {
    $this->CI->email->to( $this->CI->site['email_email'] );
    $this->CI->email->from( 'info@flexyadmin.com' );
    $this->CI->email->subject( 'cronjob example from '.$this->CI->site['url_url'] );
    if ($this->CI->email->send())
      return TRUE;
    else
      return $this->CI->email->print_debugger();
	}

}

?>