<?php 
/** \ingroup libraries
 * De basis Class voor Modules en Plugins
 *
 * @author Jan den Besten
 */
class Parent_module_plugin {

  /**
   * Verwijzing naar het CodeIgniter super-object
   */
	protected $CI;
  
  /**
   * Instellingen van deze Module of Plugin
   */
	protected $config=array();
  
  /**
   * Naam van deze Module of Plugin
   */
	protected $name='';
  
  /**
   * Naam van deze Module of Plugin zonder prefix 'plugin_'.
   */
	protected $shortname='';


  /**
   * @param array $args default=array()
   */
	public function __construct($args=array()) {
		$this->CI=&get_instance();
    $name=el('name',$args,strtolower(get_class($this)));
    $file=el('file',$args,$name);
    if (IS_AJAX) $name=str_replace('ajax_','',$name);
		if (!in_array($name, array('parent_module_plugin','module','plugin','plugin_'))) {
			$this->set_name($name);
      $langfile='language/'.$this->CI->config->item('language').'/'.$file.'_lang.php';
      if (file_exists(APPPATH.$langfile) or file_exists(SITEPATH.$langfile)) {
        $this->CI->lang->load($name);
        if (substr($name,0,6)=='plugin') {
          $this->CI->config->unload($name); // Will be reloaded later
        }
      }
      $this->load_config($file);
		}
	}

  /**
   * Stelt de naam van de Module of Plugin in
   *
   * @param string $name 
   * @return void
   * @author Jan den Besten
   */
	public function set_name($name) {
		$this->name=$name;
		$this->shortname=str_replace('plugin_','',$name);
    return $this;
	}

  /**
   * Laad het bijbehorende config bestand van de Module/Plugin (als het bestaat)
   *
   * @param string $file[''] Als leeg, wordt de config van huidige module/plugin geladen, anders de meegegeven config file
   * @return array config
   * @author Jan den Besten
   */
	protected function load_config($file='') {
		if (empty($file)) $file=$this->name;
    if (!is_array($file)) { // Hack, want hoe komt het dat $name soms de $config is???
  		$this->CI->config->load($file,true,false);
  		$this->set_config( $this->CI->config->item($file) );
    }
		return $this->config;
	}
	
  
  /**
   * Stel extra config instellingen in: overruled eventueel bestaande.
   *
   * @param array $config 
   * @param bool $merge default=TRUE
   * @return array config
   * @author Jan den Besten
   */
	protected function set_config($config=array(),$merge=TRUE) {
		if (!empty($config) and is_array($config)) {
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
   * @param string $default default=NULL Stel hier eventueel een andere default waarde in 
   * @return mixed config item of de default waarde
   * @author Jan den Besten
   */
	public function config($item,$default=NULL) {
		return el($item,$this->config,$default);
	}

}

?>