<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup core
 * Uitbreiding op CI Input
 *
 * @author Jan den Besten
 */

class MY_Input extends CI_Input {
  
  public function __construct() {
    parent::__construct();
  }
  
	/**
	 * Clean Keys
	 *
	 * Internal method that helps to prevent malicious users
	 * from trying to exploit keys we make sure that keys are
	 * only named with alpha-numeric text and a few other items.
	 * 
	 * Jdb:Added fieldname in error 2015-05-02
	 *
	 * @param	string	$str	Input string
	 * @param	bool	$fatal	Whether to terminate script exection
	 *				or to return FALSE if an invalid
	 *				key is encountered
	 * @return	string|bool
	 */
	protected function _clean_input_keys($str, $fatal = TRUE)
	{
		if ( ! preg_match('/^[a-z0-9:_\/|-]+$/i', $str))
		{
			if ($fatal === TRUE)
			{
				return FALSE;
			}
			else
			{
				set_status_header(503);
				echo 'Disallowed Key Characters. <b>'.$str.'</b>'; // JdB 2015-05-02
				exit(7); // EXIT_USER_INPUT
			}
		}

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			return $this->uni->clean_string($str);
		}

		return $str;
	}
  

}

/* End of file MY_Input.php */
