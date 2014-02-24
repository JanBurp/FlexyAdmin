<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Uitbreiding op [CI_Lang](http://codeigniter.com/user_guide/libraries/language.html)
 * 
 * @package default
 * @author Jan den Besten
 */
class MY_Lang extends CI_Lang {

  /**
   * Ingestelde taal
   *
   * @var string
   */
	private $idiom;
  private $setLanguage;
  
  /**
   * Als ingesteld, dan wordt in de frontend taal eerst geprobeerd te laden van lang_table
   *
   * @var string
   */
  private $lang_table='';
  

  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
		$this->set();
    // check if a language table is set
    $config =& get_config();
    if (isset($config['language_table']) and !empty($config['language_table'])) {
      $this->lang_table=$config['language_table'];
    }
	}
	
  /**
   * Zet standaard taal
   *
   * @param string $lang[''] 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set($lang="") {
		$this->setLanguage=$lang;
    return $this;
	}


	/**
	 * Laad een taalbestand
	 *
	 * @param mixed	$langfile Naam van language bestand
	 * @param string $idiom	de taal (nl,en etc.)
	 * @return mixed
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
        if (isset($CI->session)) $deft_lang=$CI->session->userdata("language");
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
        log_message('error', 'Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
        // show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
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


  /**
   * Kan weg?
   *
   * @return void
   * @author Jan den Besten
   * @ignore
   * @depricated
   */
	public function overrule_with_config() {
		$CI=&get_instance();
		if (isset($CI->config->config['lang'][$this->idiom])) {
			$this->language=array_merge($this->language,$CI->config->config['lang'][$this->idiom]);
		}
	}

	
  /**
   * Geeft alle taal instellingen terug
   *
   * @return array
   * @author Jan den Besten
   */
	public function get_all() {
		return $this->language;
	}


	/**
	 * Fetch a single line of text from the language array
	 *
	 * @access	public
	 * @param	  string	$line	the language line
	 * @param   bool $logging[TRUE]
	 * @return	string
	 */
	function line($line='', $logging=TRUE) {
    $value=FALSE;
    if (!empty($this->lang_table) and !empty($this->idiom)) {
      $CI=&get_instance();
      // Only frontend
      if ($CI->uri->segment(1)!='admin') {
        if ($CI->db->field_exists('lang_'.$this->idiom,$this->lang_table)) $value=$CI->db->get_field_where($this->lang_table,'lang_'.$this->idiom,'key',$line);
      }
    }
    if ($value===FALSE) {
  		$value = ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];
    }

		// Because killer robots like unicorns!
		if ($value===FALSE) {
      $value='['.$line.'.'.$this->idiom.']';
      if ($logging) log_message('error', 'Could not find the language line "'.$line.'"');
		}

		return $value;
	}

}

?>
