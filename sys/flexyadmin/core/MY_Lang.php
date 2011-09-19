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
	var $idiom;

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
	function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = SITEPATH )
	{
		$langfile = str_replace('.php', '', $langfile);

		if ($add_suffix == TRUE)
		{
			$langfile = str_replace('_lang.', '', $langfile).'_lang';
		}

		$langfile .= '.php';

		if (in_array($langfile, $this->is_loaded, TRUE))
		{
			return;
		}

		$config =& get_config();

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
			$idiom = ($deft_lang == '') ? 'en' : $deft_lang;
			$this->idiom=$idiom;
		}
		// Changes end here. JdB

		// Determine where the language file is and load it
		if ($alt_path != '' && file_exists($alt_path.'language/'.$idiom.'/'.$langfile))
		{
			include($alt_path.'language/'.$idiom.'/'.$langfile);
		}
		else
		{
			$found = FALSE;

			foreach (get_instance()->load->get_package_paths(TRUE) as $package_path)
			{
				if (file_exists($package_path.'language/'.$idiom.'/'.$langfile))
				{
					include($package_path.'language/'.$idiom.'/'.$langfile);
					$found = TRUE;
					break;
				}
			}

			if ($found !== TRUE)
			{
				show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
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

	function overrule_with_config() {
		$CI=&get_instance();
		if (isset($CI->config->config['lang'][$this->idiom])) {
			$this->language=array_merge($this->language,$CI->config->config['lang'][$this->idiom]);
		}
	}

	
	function get_all() {
		return $this->language;
	}


}

?>
