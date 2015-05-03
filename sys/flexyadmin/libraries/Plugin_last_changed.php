<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Deze plugin wordt actief als het veld 'user_changed' bestaat in een tabel en past dit veld aan na elke update van een rij in een tabel.
 *
 * @author Jan den Besten
 * @ignore
 * @internal
 */

class Plugin_last_changed extends Plugin {
  
  public function __construct() {
    parent::__construct();
  }
  
	function _after_update() {
    $this->newData['user_changed']=$this->CI->user->user_id;
		return $this->newData;
	}
	
}

?>