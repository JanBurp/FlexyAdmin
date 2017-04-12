<?php 

/** \ingroup models
 * Hiermee kunnen uri's worden gecreeërd uit andere velden van een database rij
 *
 * @author Jan den Besten
 */

class Create_uri extends CI_Model {

  private $replaceSpace = '_';

  /**
   * Tabel
   */
  private $table;
  private $table_settings = array();
  
  /**
   * Velden waar de uri mogelijk gecreerd van kan worden
   */
  private $source_field='';
  
  /**
   * Of bepaalde uri's van een bepaald item niet aangepast mag worden
   */
  private $freeze = FALSE;

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


  public function __construct() {
    parent::__construct();
    $this->replaceSpace = $this->config->item('PLUGIN_URI_REPLACE_CHAR');
  }



  /**
   * Zet de database tabel waar de uri('s) gecreerd moeten worden
   *
   * @param string $table 
   * @return object $this
   * @author Jan den Besten
   */
  public function set_table($table) {
    $this->table = $table;
    $this->table_settings = $this->data->table($table)->get_settings();
    $update_uris = el( 'update_uris', $this->table_settings, FALSE );
    if (is_array($update_uris)) {
      $this->source_field = el( 'source', $update_uris, '' );
      $this->prefix = el( 'prefix', $update_uris, '' );
      $this->prefix_callback = el( 'prefix_callback', $update_uris, false );
      $this->freeze = el( 'freeze', $update_uris, '' );
    }
    return $this;
  }
  
  
  /**
 	 * Maak uri vanuit meegegeven data (rij uit een tabel, of string)
 	 *
 	 * @param array $data 
 	 * @return string
 	 * @author Jan den Besten
 	 */
  public function create($data) {
    // init
    $this->table_data = $data;
    $this->fields = array_keys($data);

    if (empty($this->source_field)) $this->source_field = $this->_find_source_field($data);
    if (empty($this->source_field)) return FALSE;

    // Uri source
    $uri = el('uri',$this->table_data,'');
    if ($this->_freeze_uri($data)) return $uri;

 		if (isset($this->table_data[$this->source_field]))
 			$uri_source = $this->table_data[$this->source_field];
 		else
 			$uri_source = $this->table_data['id'];

    // Prefix
    if ($this->prefix_callback) {
      $model = $this->prefix_callback['model'];
      $method = $this->prefix_callback['method'];
      if (!empty($model)) {
        $this->load->model($model,'prefix_model');
        if (method_exists($this->prefix_model,$method)) {
          $this->prefix = clean_string($this->prefix_model->$method($data));
        }
      }
    }

    // Uri
    $uri = $this->prefix.$this->cleanup($uri_source);
    $uri = ltrim($uri,'_');

    // Exists? Add prefix(uri) or a number
		$postSpace = $this->replaceSpace.$this->replaceSpace;
		while ($this->_is_existing_uri($uri,$data) or $this->is_forbidden($uri)) {
      $parentUri = $this->_get_parent_uri($data);
      if ($parentUri) {
        $uri = $parentUri.$postSpace.$uri;
      }
      else {
        $currUri = remove_suffix($uri,$postSpace);
        $countUri = (int) get_suffix($uri,$postSpace);
        $uri = $currUri.$postSpace.($countUri+1);
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
    $uri=trim(strip_tags($uri),' -_');
    $uri=str_replace(" ",$this->replaceSpace,$uri);
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
		$fields = $this->fields;
 		$uriField = "";

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
    if (!$forbidden) $forbidden=array('site','sys','offset');
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
      $sql = 'SELECT `uri` FROM `'.$this->table.'` WHERE `uri`="'.$uri.'"';
      if ( isset($this->table_data['id']))          $sql .= ' AND `id` != "'.$this->table_data['id'].'"';
      $query = $this->db->query($sql);
      if (!$query) return FALSE;
      $uris = $query->result_array();
      $existing = current($uris);
   		return $existing;
    }
 	}

  private function _get_parent_uri($data) {
    if (!isset($data['self_parent']) or $data['self_parent']==0) return false;
    $sql = 'SELECT `uri` FROM `'.$this->table.'` WHERE `id`="'.$data['self_parent'].'"';
    $query = $this->db->query($sql);
    if (!$query) return FALSE;
    $uris = $query->result_array();
    $uri = current($uris);
    // trace_(['_get_parent_uri',$uri['uri'],$sql]);
    return $uri['uri'];
  }


  /**
   * Checkt of uri wel aangepast mag worden
   *
   * @param      <type>   $data         The data
   *
   * @return     boolean  ( description_of_the_return_value )
   */
  private function _freeze_uri($data) {
    $freeze = false;
    if (is_array($this->freeze)) {
      foreach ($this->freeze as $field => $value) {
        if (isset($data[$field])) {
          if (is_array($value)) {
            if (in_array($data[$field], $value)) $freeze = true;
          }
          else {
            if ($data[$field]===$value) $freeze = true;
          }
        }
      }
    }
    // trace_([$data,$freeze,$this->freeze]);
    return $freeze;
  }
	

 }
?>
