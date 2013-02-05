<?

/**
 * Hiermee kunnen uri's worden gecreeërd uit andere velden van een database rij
 *
 * @package default
 * @author Jan den Besten
 */

class Create_uri extends CI_Model {

  /**
   * Tabel
   *
   * @var string
   */
  private $table;
  
  /**
   * Velden waar de uri mogelijk gecreerd van kan worden
   *
   * @var string
   */
  private $source_field='';
  
  /**
   * data uit tabel
   *
   * @var string
   */
  private $data;
  
  /**
   * veldnamen an tabel
   *
   * @var string
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
 	 * Maak uri vanuit meegegeven data (rij uit een tabel)
 	 *
 	 * @param array $data 
 	 * @return string
 	 * @author Jan den Besten
 	 */
  public function create($data) {
    // init
    $this->data=$data;
    $this->fields=array_keys($data);
    if (empty($this->source_field)) $this->set_source_field( $this->_find_source_field() );
 		$replaceSpace=$this->config->item('PLUGIN_URI_REPLACE_CHAR');
    
    // Need to create an uri?
 		$uri=el('uri',$this->data,'');
 		if (isset($this->data[$this->source_field]))
 			$uri_source=$this->data[$this->source_field];
 		else
 			$uri_source=$this->data['id'];
 		$createUri=true;
 		if ($this->cfg->get('CFG_table',$this->table,'b_freeze_uris')) $createUri=false;
 		if (isset($this->data['b_freeze_uri']) and $this->data['b_freeze_uri']) $createUri=false;
 		if (empty($uri)) $createUri=true;
    // If needs to create an uri
 		if ($createUri) {
 			$uri=trim(strip_tags(strtolower($uri_source)),' -_');
 			$uri=str_replace(" ",$replaceSpace,$uri);
 			$uri=clean_string($uri);
      $forbidden=$this->config->item('FORBIDDEN_URIS');
      if (!$forbidden) $forbidden=array("site","sys","admin","rss","file",'offset');
      $forbidden[]=$this->config->item('URI_HASH');
 			$forbidden=array_merge($forbidden,$this->config->item('LANGUAGES'));
 			$postSpace=$replaceSpace.$replaceSpace;
 			while ($this->_is_existing_uri($uri) or in_array($uri,$forbidden)) {
 				$currUri=remove_suffix($uri,$postSpace);
 				$countUri=(int) get_suffix($uri,$postSpace);
 				$uri=$currUri.$postSpace.($countUri+1);
 			}
 		}
 		return $uri;
 	}

  /**
   * Zoek mooi veld waar uri van gemaakt kan worden
   *
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
 	private function _find_source_field() {
		$fields=$this->fields;
    
 		$uriField="";
 		/**
 		 * Auto uri field according to prefixes
 		 */
 		if (empty($uriField)) {
 			$preTypes=$this->config->item('URI_field_pre_types');
 			$loop=true;
 			while ($loop) {
 				$field=current($fields);
 				$pre=get_prefix($field);
 				if (in_array($pre,$preTypes)) {
 					$uriField=$field;
 				}
 				$field=next($fields);
 				$loop=(empty($uriField) and $field!==FALSE);
 			}
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
   * Checkt of de uri al bestaat
   *
   * @param string $uri
   * @return mixed
   * @author Jan den Besten
   * @internal
   * @ignore
   */
 	private function _is_existing_uri($uri) {
 		if ($this->db->field_exists('self_parent',$this->table) and isset($this->data['self_parent'])) {
 			$this->db->select('self_parent');
 			$this->db->where('self_parent',$this->data['self_parent']);
 		}
 		$this->db->select("uri");
 		$this->db->where("uri",$uri);
 		if (isset($this->data['id'])) $this->db->where("id !=",$this->data['id']);
 		$uris=$this->db->get_result($this->table);
 		if (empty($uris))
 			return FALSE;
 		return current($uris);
 	}
	

 }
?>
