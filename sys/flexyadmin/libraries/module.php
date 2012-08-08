<?

/**
 * Basis class voor alle frontend modules. Zo begint je eigen module dus:
 * <code>class Mijn_module extends Module</code>
 *
 * @package default
 * @author Jan den Besten
 */

class Module extends Parent_module_plugin {

	function __construct($name='') {
		parent::__construct($name);
	}

  /**
   * if method of module can't be found, print a simple warning
   *
   * @param string $function 
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @internal
   */
	public function __call($function, $args) {
		echo '<div class="warning">Method: `'.ucfirst($function)."()` doesn't exists.<div>";
	}


	/**
	 * Deze method wordt standaard aangeroepen
	 *
	 * @param string $page 
	 * @return void
	 * @author Jan den Besten
	 */
	public function index($page) {
		return '<h1>'.__CLASS__.'</h1>';
	}


  /**
   * Als je dit aanroept ergens in je module dan laad de controller daarna geen andere modules meer.
   *
   * @return void
   * @author Jan den Besten
   */
	protected function break_content() {
		$this->CI->site['content']='';
		$this->CI->site['break']=true;
	}
  
  
  /**
   * Stelt de uri in van de pagina waar de module is te vinden
   *
   * @return void
   * @author Jan den Besten
   */
  protected function set_module_uri() {
    if (!isset($this->config['module_uri'])) $this->config['module_uri']=$this->CI->find_module_uri($this->name).'/'.$this->CI->config->item('PLUGIN_URI_ARGS_CHAR');
    return $this->config['module_uri'];
  }
  
  /**
   * Haalt eventuele argumenten voor deze specifieke module op uit de URI
   *
   * @return void
   * @author Jan den Besten
   */
  protected function get_uri_args() {
    if (!isset($this->config['uri_args'])) $this->config['uri_args']=$this->CI->uri->get_from_part($this->CI->config->item('PLUGIN_URI_ARGS_CHAR'));
    return $this->config['uri_args'];
  }
  

}

?>