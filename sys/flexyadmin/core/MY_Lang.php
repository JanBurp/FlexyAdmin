<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FlexyAdmin V1
 *
 * @author Jan den Besten
 */


/**
 * Language Class extension
 */

class MY_Lang extends CI_Lang {

	var $setLanguage;

	function __construct() {
		parent::__construct();
		$this->set();
	}
	
	function set($lang="") {
		$this->setLanguage=$lang;
	}

	/**
	 * Load a language file
	 *
	 * @access	public
	 * @param	mixed	the name of the language file to be loaded. Can be an array
	 * @param	string	the language (english, etc.)
	 * @return	mixed
	 */
	function load($langfile = '', $idiom = '', $return = FALSE)
	{
		$langfile = str_replace('.php', '', str_replace('_lang.', '', $langfile)).'_lang'.'.php';

		if (in_array($langfile, $this->is_loaded, TRUE))
		{
			return;
		}

		// Changed by JdB
		if ($idiom == '')
		{
			if (!empty($this->setLanguage)) {
				$deft_lang=$this->setLanguage;
			}
			else {
				$CI =& get_instance();
				if (isset($CI->session))	$deft_lang=$CI->session->userdata("language");
				if (!empty($deft_lang))
					$this->set($deft_lang);
				else	
					$deft_lang = $CI->config->item('language');
			}
			$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
		}
		// Changes end here. JdB


		// Determine where the language file is and load it
		if (file_exists(APPPATH.'language/'.$idiom.'/'.$langfile))
		{
			include(APPPATH.'language/'.$idiom.'/'.$langfile);
		}
		else
		{
			if (file_exists(BASEPATH.'language/'.$idiom.'/'.$langfile))
			{
				include(BASEPATH.'language/'.$idiom.'/'.$langfile);
			}
			else
			{
				show_error('Unable to load the requested language file: language/'.$langfile);
			}
		}

		if ( ! isset($lang))
		{
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
			return;
		}

		if ($return == TRUE)
		{
			return $lang;
		}

		$this->is_loaded[] = $langfile;
		$this->language = array_merge($this->language, $lang);
		unset($lang);

		log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
	}
	
	function get_all() {
		return $this->language;
	}


}

?>
