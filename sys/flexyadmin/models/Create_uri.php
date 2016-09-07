<?php 

/** \ingroup models
 * Hiermee kunnen uri's worden gecreeërd uit andere velden van een database rij
 *
 * @author Jan den Besten
 */

class Create_uri extends CI_Model {

  /**
   * Tabel
   */
  private $table;
  
  /**
   * Velden waar de uri mogelijk gecreerd van kan worden
   */
  private $source_field='';
  
  /**
   * Een prefix die voor elke uri wordt geplakt
   */
  private $prefix='';

  /**
   * Een prefix kan dynamisch gegenereerd worden door een extern object.method
   */
  private $prefix_callback=false;
  
  
  /**
   * data uit tabel
   */
  private $table_data;
  
  /**
   * veldnamen an tabel
   */
  private $fields;

  /**
   * Zet de database tabel waar de uri('s) gecreerd moeten worden
   *
   * @param string $table 
   * @return object $this
   * @author Jan den Besten
   */
  public function set_table($table) {
    $this->table=$table;
    return $this;
  }
  
  /**
   * Zet het veld waar de uri vanuit gecreerd wordt, meestal een *str_* veld zoals *str_title*
   * 
   * Als dit niet wordt ingesteld zal automatisch een geschikt bron-veld worden gezocht
   *
   * @param string $source_field 
   * @return object $this
   * @author Jan den Besten
   */
  public function set_source_field($source_field) {
    $this->source_field=$source_field;
    return $this;
  }
  
  
  /**
   * Stelt een prefix in die voor elke uri wordt geplakt
   *
   * @param string $prefix 
   * @return object $this
   * @author Jan den Besten
   */
  public function set_prefix($prefix='') {
    $this->prefix=$prefix;
    return $this;
  }
  
  /**
   * Geeft een object & method om de prefix dynamisch te genereren. De method krijgt de data mee.
   *
   * @param array $callback 
   * @return this
   * @author Jan den Besten
   */
  public function set_prefix_callback($callback) {
    $this->prefix_callback = $callback;
    return $this;
  }
  

 	/**
 	 * Maak uri vanuit meegegeven data (rij uit een tabel, of string)
 	 *
 	 * @param array $data 
 	 * @param bool $overrule default=FALSE als TRUE dan wordt altijd een nieuwe uri gecreeerd
 	 * @return string
 	 * @author Jan den Besten
 	 */
  public function create($data,$overrule=FALSE) {
    // init
    $this->table_data=$data;
    $this->fields=array_keys($data);
    if (empty($this->source_field)) $this->set_source_field( $this->_find_source_field($data) );
 		$replaceSpace=$this->config->item('PLUGIN_URI_REPLACE_CHAR');
    
    // Need to create an uri?
 		$uri=el('uri',$this->table_data,'');
 		if (isset($this->table_data[$this->source_field]))
 			$uri_source=$this->table_data[$this->source_field];
 		else
 			$uri_source=$this->table_data['id'];
 		$createUri=true;

    if ($this->cfg->get('CFG_table',$this->table,'b_freeze_uris')) $createUri=false;
    if (isset($this->table_data['b_freeze_uri']) and $this->table_data['b_freeze_uri']) $createUri=false;
    if (empty($uri)) $createUri=true;
    if ($overrule) $createUri=true;
    
    // If needs to create an uri
 		if ($createUri) {
      // Prefix
      $prefix=$this->prefix;
      if ($this->prefix_callback) {
        $model=$this->prefix_callback['model'];
        $method=$this->prefix_callback['method'];
        $this->load->model($model,'prefix_model');
        if (method_exists($this->prefix_model,$method)) {
          $prefix=clean_string($this->prefix_model->$method($data));
        }
      }
      // Uri
      $uri=$prefix.$this->cleanup($uri_source);
      // Exists? add a number
 			$postSpace=$replaceSpace.$replaceSpace;
 			while ($this->_is_existing_uri($uri,$data) or $this->is_forbidden($uri)) {
 				$currUri=remove_suffix($uri,$postSpace);
 				$countUri=(int) get_suffix($uri,$postSpace);
 				$uri=$currUri.$postSpace.($countUri+1);
 			}
 		}
 		return $uri;
 	}
  
  /**
   * Maakt van een gegeven string een uri veilige string
   *
   * @param string $uri 
   * @return string
   * @author Jan den Besten
   */
  public function cleanup($uri) {
    $replaceSpace=$this->config->item('PLUGIN_URI_REPLACE_CHAR');
    $uri=trim(strip_tags($uri),' -_');
    $uri=str_replace(" ",$replaceSpace,$uri);
		$uri=clean_string($uri);
    $uri=strtolower($uri);
    return $uri;
  }

  /**
   * Zoek mooi veld waar uri van gemaakt kan worden
   *
   * @return void
   * @author Jan den Besten
   * @internal
   */
 	private function _find_source_field($data=false) {
		$fields=$this->fields;
    
 		$uriField="";
 		/**
 		 * Auto uri field according to prefixes
 		 */
		$preTypes=$this->config->item('URI_field_pre_types');
		$loop=true;
		while ($loop) {
			$field=current($fields);
			$pre=get_prefix($field);
			if (in_array($pre,$preTypes)) {
				$uriField=$field;
        if (isset($data[$uriField]) and empty($data[$uriField])) $uriField='';
			}
			$field=next($fields);
			$loop=(empty($uriField) and $field!==FALSE);
		}
 		/**
 		 * If still nothing set... just get the first field (after id,order and uri)
 		 */
 		if (empty($uriField)) {
 			unset($fields["id"]);
 			unset($fields["uri"]);
 			unset($fields["order"]);
 			unset($fields["self_parent"]);
 			reset($fields);
 			$uriField=current($fields);
 		}
 		return $uriField;
 	}
	
  
  /**
   * Test of een gegeven uri een verboden uri is
   *
   * @param string $uri 
   * @return bool
   * @author Jan den Besten
   */
  public function is_forbidden($uri) {
    if (substr($uri,0,1)=='_') return true;
    $forbidden=$this->config->item('FORBIDDEN_URIS');
    if (!$forbidden) $forbidden=array("site","sys","admin","file",'offset');
    $forbidden[]=$this->config->item('URI_HASH');
  	$forbidden=array_merge($forbidden,$this->config->item('LANGUAGES'));
    return in_array($uri,$forbidden);
  }
  
  
  /**
   * Geef een alternatieve class die de functie _is_existing_uri zelf heeft
   *
   * @param string $class
   * @return this
   * @author Jan den Besten
   */
  public function set_existing_class($class) {
    $this->existing_class=$class;
    return $this;
  }
  
  
  /**
   * Checkt of de uri al bestaat
   *
   * @param string $uri
   * @return mixed
   * @author Jan den Besten
   * @internal
   */
 	private function _is_existing_uri($uri,$data) {
    if (isset($this->existing_class)) {
      // Alternative function to test
      $class=$this->existing_class;
      return call_user_func(array($class, '_is_existing_uri'),$uri,$data);
    }
    else {
      $this->data->table( $this->table );
      // Normal function to test
   		if ($this->data->field_exists('self_parent') and isset($this->table_data['self_parent'])) {
   			$this->data->select('self_parent');
   			$this->data->where('self_parent',$this->table_data['self_parent']);
   		}
   		$this->data->select("uri");
   		$this->data->where( $this->table.".uri",$uri);
   		if (isset($this->table_data['id'])) $this->data->where( $this->table.".id !=",$this->table_data['id']);
   		$uris = $this->data->get_result();
      
   		if (empty($uris))
   			return FALSE;
   		return current($uris);
    }
 	}
	

 }
?>
