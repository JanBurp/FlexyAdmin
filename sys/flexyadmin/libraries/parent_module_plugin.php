<?

/**
 * De basis Class voor Modules en Plugins
 *
 * @package default
 * @author Jan den Besten
 */
class Parent_module_plugin {

  /**
   * Verwijzing naar het CodeIgniter super-object
   *
   * @var object
   */
	protected $CI;
  
  /**
   * Instellingen van deze Module of Plugin
   *
   * @var array
   */
	protected $config=array();
  
  /**
   * Naam van deze Module of Plugin
   *
   * @var string
   */
	protected $name='';
  
  /**
   * Naam van deze Module of Plugin zonder prefix 'plugin_'.
   *
   * @var string
   */
	protected $shortname='';


  /**
   * @param string $name 
   * @author Jan den Besten
   * @ignore
   */
	public function __construct($name='') {
		$this->CI=&get_instance();
		if (empty($name) or is_array($name)) $name=strtolower(get_class($this));
		if (!in_array($name, array('parent_module_plugin','module','plugin','plugin_'))) {
			$this->set_name($name);
      $langfile='language/'.$this->CI->config->item('language').'/'.$name.'_lang.php';
      if (file_exists(APPPATH.$langfile) or file_exists(SITEPATH.$langfile)) {
        $this->CI->lang->load($name);
        if (substr($name,0,6)=='plugin') {
          $this->CI->config->unload($name); // Will be reloaded later
        }
      }
      $this->load_config();
		}
	}

  /**
   * Stelt de naam van de Module of Plugin in
   *
   * @param string $name 
   * @return void
   * @author Jan den Besten
   */
	protected function set_name($name) {
		$this->name=$name;
		$this->shortname=str_replace('plugin_','',$name);
	}

  /**
   * Laad het bijbehorende config bestand van de Module/Plugin (als het bestaat)
   *
   * @param string $name[''] Als leeg, wordt de config van huidige module/plugin geladen
   * @return array config
   * @author Jan den Besten
   */
	protected function load_config($name='') {
		if (empty($name)) $name=$this->name;
    if (!is_array($name)) { // Hack, want hoe komt het dat $name soms de $config is???
  		$this->CI->config->load($name,true,false);
  		$this->set_config( $this->CI->config->item($name) );
    }
		return $this->config;
	}
	
  
  /**
   * Stel extra config instellingen in: overruled eventueel bestaande.
   *
   * @param array $config 
   * @param bool $merge[TRUE]
   * @return array config
   * @author Jan den Besten
   */
	protected function set_config($config=array(),$merge=TRUE) {
		if (!empty($config)) {
			if ($merge)
				$this->config=array_merge($this->config,$config);
			else
				$this->config=$config;
		}
		return $this->config;
	}

  /**
   * Geeft config item terug uit de instellingen van huidige module/plugin. Als het item niet bestaat dan wordt een defaultwaarde teruggegeven
   *
   * @param string $item 
   * @param string $default[NULL] Stel hier eventueel een andere default waarde in 
   * @return mixed config item of de default waarde
   * @author Jan den Besten
   */
	protected function config($item,$default=NULL) {
		return el($item,$this->config,$default);
	}

}

?>