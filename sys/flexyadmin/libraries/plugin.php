<?

/**
 * Basis class voor alle backend plugins. Zo begint je eigen plugin:
 * <code>class Plugin_mijn_plugin extends Plugin</code>
 *
 * @package default
 * @author Jan den Besten
 */
 
class Plugin extends Parent_module_plugin {
	
  /**
   * Eventuele output van de plugin komt hier.
   *
   * @var string
   * @ignore
   */
	protected $content;
  
  /**
   * Oorspronkelijke data van het huidige record. Deze data kan de plugin aanpassen en in newData zetten.
   *
   * @var string
   */
	protected $oldData;

  /**
   * Door de plugin aangepaste data van het huidige record.
   *
   * @var string
   */
	protected $newData;
  
  /**
   * Database tabel van het huidige record
   *
   * @var string
   */
	protected $table;
  
  /**
   * id van huidige record
   *
   * @var string
   */
	protected $id;
  
  /**
   * Trigger instellingen van de plugin (worden in de config ingesteld)
   *
   * @var string
   * @ignore
   */
	protected $trigger=array();


	public function __construct($name='') {
		parent::__construct($name);
	}
	
  
  /**
   * Stel extra config instellingen in: overruled eventueel bestaande.
   *
   * @param array $config 
   * @param bool $merge[TRUE]
   * @return array config
   * @author Jan den Besten
   */
  public function set_config($config=array()) {
    if (!isset($config['config'])) $config=array('config'=>$config);
		if (isset($config['config'])) parent::set_config($config['config']);
		if (isset($config['trigger'])) $this->trigger=$config['trigger'];
	}

	/**
	 * Zorgt dat alle benodigde gegevens bekend zijn in de plugin. Wordt alleen intern gebruikt.
	 *
	 * @param array $data 
	 * @return void
	 * @author Jan den Besten
   * @internal
   * @ignore
	 */
  public function set_data($data) {
		if (isset($data['old'])) 		$this->oldData=$data['old'];
		if (isset($data['new'])) 		$this->newData=$data['new'];
		if (isset($data['table'])) 	$this->table=$data['table'];
		if (isset($data['id'])) 		$this->id=$data['id'];
	}

	
  /**
   * Geeft output van de plugin, wordt alleen intern gebruikt.
   *
   * @return string
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	public function get_content() {
		return $this->content;
	}

  /**
   * Voegt output toe van de plugin
   *
   * @param string $content 
   * @return string $content Huidige output
   * @author Jan den Besten
   */
	protected function add_content($content) {
		$this->content.=$content;
		return $this->content;
	}
	
	/**
	 * Depricated
	 *
	 * @return void
	 * @author Jan den Besten
   * @internal
   * @ignore
	 */
	function get_show_type() {
		return '';
	}


}

/**
 * Just to make sure old plugin extensions work too
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 */
class Plugin_ extends Plugin {
}


?>