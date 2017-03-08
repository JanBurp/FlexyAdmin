<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Session extends CI_Session {

	/**
	 * Session destroy
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @return	void
	 */
	public function sess_destroy()
	{
    if (function_exists('session_status') AND session_status() === PHP_SESSION_ACTIVE) parent::sess_destroy();
	}


}
