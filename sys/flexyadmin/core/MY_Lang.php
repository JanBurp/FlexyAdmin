<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup core
 * Uitbreiding op [CI_Lang](http://codeigniter.com/user_guide/libraries/language.html)
 * 
 * @author Jan den Besten
 */
class MY_Lang extends CI_Lang {

  /**
   * Ingestelde taal
   */
	private $idiom='';
  private $setLanguage='';
  
  /**
   * Als ingesteld, dan wordt in de frontend taal eerst geprobeerd te laden van lang_table
   */
  private $lang_table='';
  

  /**
   */
	public function __construct() {
		parent::__construct();
    // check if a language table is set
    $config = &get_config();
    if ( isset($config['language_table']) and !empty($config['language_table']) ) {
      $this->lang_table= $config['language_table'];
      $this->lang_data = $this->data->table($this->lang_table)->set_result_key('key')->get_result();
    }
    $this->set();
    log_message('debug', 'MY Language Class Initialized');
	}


  /**
   * Voor LangTester
   *
   * @return void
   */
  public function reset_lang() {
    $this->language = array();
    $this->is_loaded = array();
  }

	
  /**
   * Zet standaard taal
   *
   * @param string $lang[''] 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set($lang='') {
		$this->setLanguage=$lang;
    return $this;
	}


	/**
	 * Laad een taalbestand
	 *
	 * @param mixed	$langfile Naam van language bestand
	 * @param string $idiom	de taal (nl,en etc.)
	 * @param bool $return default=FALSE
	 * @param bool $add_suffix default=TRUE
	 * @param string $alt_path default=SITE_PATH
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
      // trace_([$idiom,$this->idiom,$this->setLanguage]);
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
	 * @param	  string	$line	the language line
	 * @param   bool $logging default=TRUE
	 * @param   bool $give_key_on_false_result default=TRUE
	 * @return	string Leeg als $line=''
	 */
	public function line($line='', $logging=TRUE, $give_key_on_false_result=TRUE) {
    if ($line==='') return '';

    $CI=&get_instance();
    $value=FALSE;
    if (empty($this->idiom) and isset($CI->site['language'])) {
      $this->idiom=$CI->site['language'];
    }
    
    // only if not a db_error look for cfg_lang
    if ( substr($line,0,3)!=='db_' AND !empty($this->lang_table) AND !empty($this->idiom) ) {
      // Only when db is ready
      if (isset($this->lang_data)) {
        $value = el( array($line,'lang_'.$this->idiom), $this->lang_data, FALSE );
      }
    }
    
    if ($value===FALSE) {
  		$value = ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];
    }

		// Because killer robots like unicorns!
		if ($value===FALSE or empty($value)) {
      if ($give_key_on_false_result) $value='['.$line.'.'.$this->idiom.']';
      if ($logging) log_message('info', 'Could not find the language line "'.$line.'"');
		}

		return $value;
	}
  
  /**
   * Haalt een language key op voor bepaalde taal
   *
   * @param string $key 
   * @param string $lang 
   * @return string
   * @author Jan den Besten
   */
  public function key($key='',$lang='') {
    $cur_lang=$this->idiom;
    $this->idiom=$lang;
    $value=$this->line($key);
    $this->idiom=$cur_lang;
    return $value;
  }

}

?>
