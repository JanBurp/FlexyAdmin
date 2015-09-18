<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup tables
 * 
 * - Alle instellingen van een tabel en zijn velden tabel zijn te vinden in config/tables/... (zodat geen cfg_table_info en cfg_field_info meer nodig is)
 * - Daardoor: veel sneller omdat de gegevens al beschikbaar zijn en niet te hoeven worden opgezocht.
 * - Standaard get/crud zit al in het model, voor elke tabel hetzelfde.
 * - Iedere tabel kan deze overerven en aanpassen naar wens, de aanroepen blijven hetzelfde voor ieder tabel.
 * - Alle query-builder methods (en db methods) kunnen worden gebruikt met het model. Dus dezelfde naamgeving voor de methods.
 * - Naast ->get() die een query object teruggeeft ook ->get_result() en get_row() die een aangepaste result array teruggeeft met eventuele many_to_many data als subarray.
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

Class Table_Model extends CI_Model {
  
  /**
   * De instellingen voor deze tabel en velden.
   * Deze worden opgehaald uit het config bestand met dezelfde naam als die model. (config/tables/table_model.php in dit geval)
   */
  protected $settings = array();
  
  /**
   * Enkele noodzakelijk instellingen die desnoods automatisch worden ingesteld als ze niet bekend zijn.
   */
  protected $autoset = array(
    'table'           => '',
    'fields'          => array(),
    'field_info'      => array(),
    'primary_key'     => PRIMARY_KEY,
    'result_key'      => PRIMARY_KEY,
    'order_by'        => '',
    'max_rows'        => 0,
    'update_uris'     => '',
    'abstract_fields' => '',
    'abstract_filter' => '',
  );
  
  
  /**
   * Welke relaties mee moeten worden genomen
   */
  private $with       = array();
  

  /* --- CONSTRUCT & AUTOSET --- */

	public function __construct() {
		parent::__construct();
    $this->_config();
	}
  
  /**
   * Laad de bijbehorende config.
   * Merge die met de defaults.
   * Als dan nog niet alle belangrijke zaken zijn ingesteld, doe dat dan met autoset
   *
   * @param string $table [''] Stel eventueel de naam van de table in. Default wordt de naam van het huidige model gebruikt.
   * @return $this;
   * @author Jan den Besten
   */
  protected function _config( $table='' ) {
    // Haal de default settings op
    $this->config->load( 'tables/table_model', true);
    $default = $this->config->item( 'tables/table_model' );
    $this->settings = $default;
    if ($table) $this->settings['table'] = $table; // Voor het los instellen van een table zonder eigen model
    // Haal de settings van huidige model op
    if ( empty($table) ) $table=get_class($this);
    if ( get_class()!=$table ) {
      $this->config->load( 'tables/'.$table, true);
      $settings = $this->config->item( 'tables/'.$table );
      // Merge samen tot settings
      if ( $settings ) {
        $this->settings = array_merge( $default, $settings );
      }
      // Test of de noodzakelijke settings zijn ingesteld, zo niet doe dat automatisch
      $this->_autoset( $table );
    }
    return $this;
  }
  
  
  /**
   * Test of belangrijke settings zijn ingesteld. Zo niet doet dat dan automatisch.
   * Dit maakt plug'n play mogelijk, maar gebruikt meer resources.
   *
   * @return $this
   * @author Jan den Besten
   */
  protected function _autoset() {
    foreach ($this->autoset as $key => $value) {
      if ( empty($this->settings[$key]) ) {
        // Moet worden ingesteld, dus automatisch, met bijbehorden waarde of met een method.
        if (method_exists($this,'_autoset_'.$key)) {
          $method = '_autoset_'.$key;
          $this->settings[$key] = $this->$method();
        }
        else {
          $this->settings[$key] = $this->autoset[$key];
        }
        var_dump(['_autoset_'.$key, $this->autoset[$key] ]);
      }
    }
    return $this;
  }
  
  
  /**
   * Autoset table
   *
   * @param object $object [], Standaard wordt de tablenaam gegenereerd aan de hand van het model waarin dit wordt aangeroepen. Geef hier eventueel een andere model mee.
   * @return string
   * @author Jan den Besten
   */
  protected function _autoset_table( $object=null ) {
    if ( $object===null) $object = $this;
    return get_class( $object );
  }

  /**
   * Autoset fields
   *
   * @param string $table [''], Standaard wordt de tabelnaam gebruikt die in het huidige model is ingesteld. Geef hier eventueel een afwijkende table naam.
   * @return array
   * @author Jan den Besten
   */
  protected function _autoset_fields( $table='' ) {
    if (empty($table)) $table = $this->settings['table'];
    return $this->db->list_fields( $table );
  }
  
  /**
   * Autoset field_info
   *
   * @param string $table [''], Standaard wordt de tabelnaam gebruikt die in het huidige model is ingesteld. Geef hier eventueel een afwijkende table naam.
   * @param array $fields [array()], Standaard worden de velden gebruikt die in het huidige model zijn ingesteld. Geef hier eventueel een afwijkende velden lijst. 
   * @return void
   * @author Jan den Besten
   */
  protected function _autoset_field_info( $table='', $fields=array() ) {
    $this->load->model('cfg');
    $this->load->library('form_validation');
    if (empty($table)) $table = $this->settings['table'];
    if (empty($fields)) $fields = $this->settings['fields'];
    $fields_info = array();
    $settings_fields_info = array();
    foreach ($fields as $field) {
      $field_info = $this->cfg->get( 'cfg_field_info', $table.'.'.$field);
      // Haal uit (depricated) cfg_field_info
      $field_info_db = $this->db->where('field_field',$table.'.'.$field)->get_row('cfg_field_info');
      if (is_array($field_info) and is_array($field_info_db)) {
        $field_info = array_merge($field_info,$field_info_db);
      }
      else {
        if (!is_array($field_info)) $field_info=$field_info_db;
        if (!is_array($field_info)) $field_info=null;
      }
      $fields_info[$field] = $field_info;
      $settings_fields_info[$field] = array();
      $settings_fields_info[$field]['validation'] = $this->form_validation->get_validations( $table, $field );
      if (!empty($field_info['str_options'])) {
        $settings_fields_info[$field]['options'] = $field_info['str_options'];
        $settings_fields_info[$field]['multiple_options'] = $field_info['b_multi_options']?true:false;
      }
    }
    return $settings_fields_info;
  }
  
  
  /**
   * Autoset order_by
   * 
   * @param array $fields [array()], Standaard worden de velden gebruikt die in het huidige model zijn ingesteld. Geef hier eventueel een afwijkende velden lijst. 
   * @return string
   * @author Jan den Besten
   */
  protected function _autoset_order_by( $fields=array() ) {
    $this->load->model('cfg');
    if (empty($fields)) $fields = $this->settings['fields'];
    $order_by = '';
    // Haal eerst indien mogelijk uit (depricated) cfg_table_info
    $order_by = $this->cfg->get( 'cfg_table_info', $this->settings['table'], 'str_order_by');
    // Als leeg: Zoek mogelijke standaard order fields
    if (empty($order_by)) {
  		$order_fields = $this->config->item( 'ORDER_default_fields' );
  		do {
  			$possible_order_field = each( $order_fields );
        if ($possible_order_field) {
          $possible_order_field = explode( ' ', $possible_order_field['value'] ); // split DESC/ASC
          $possible_field = $possible_order_field[0];
          if ( $key=in_array_like($possible_field, $fields) ) {
            $order_by = $fields[$key];
            if ( isset($possible_order_field[1]) ) $order_by .= ' ' . $possible_order_field[1]; // add DESC/ASC
          }
        }
      } while (empty($order_by) and $possible_order_field);
      
    }
    // Als leeg: Pak dat het laatste standaard order veld ('id')
    if (empty($order_by)) $order_by = $order_fields[count($order_fields)-1];
    return $order_by;
  }
  
  /**
   * Autoset max_rows
   *
   * @return integer
   * @author Jan den Besten
   */
  protected function _autoset_max_rows() {
    $this->load->model('cfg');
    // Haal eerst indien mogelijk uit (depricated) cfg_table_info
    $max_rows = $this->cfg->get( 'cfg_table_info', $this->settings['table'], 'int_max_rows');
    // Anders is het gewoon standaard 0
    if (is_null($max_rows)) $max_rows = 0;
    return $max_rows;
  }
  
  /**
   * Autoset update_uris
   *
   * @return boolean
   * @author Jan den Besten
   */
  protected function _autoset_update_uris() {
    $this->load->model('cfg');
    // Haal eerst indien mogelijk uit (depricated) cfg_table_info
    $update_uris = $this->cfg->get( 'cfg_table_info', $this->settings['table'], 'b_freeze_uris');
    // Anders is het gewoon standaard true
    if (is_null($update_uris)) $update_uris = true;
    return $update_uris;
  }
  
  

  /**
   * Autoset abstract fields
   *
   * @param array $fields [array()], Standaard worden de velden gebruikt die in het huidige model zijn ingesteld. Geef hier eventueel een afwijkende velden lijst. 
   * @return array
   * @author Jan den Besten
   */
  protected function _autoset_abstract_fields( $fields = array() ) {
    if (empty($fields)) $fields = $this->settings['fields'];
    
    $abstract_fields = array();
    if ( !is_array($fields) ) $fields = explode( ',', $fields );
    // Zoek op type velden
		$abstract_field_types = $this->config->item('ABSTRACT_field_pre_types');
    $max_abstract_fields  = $this->config->item('ABSTRACT_field_max');
		while ( list($key,$field) = each( $fields ) and $max_abstract_fields>0) {
			$pre = get_prefix($field);
			if ( in_array( $pre, $abstract_field_types ) ) {
				array_push( $abstract_fields, $field );
				$max_abstract_fields--;
			}
		}
    // Als leeg, zoek dan de eerste velden
		if (empty($abstract_fields)) {
      $max_abstract_fields  = $this->config->item('ABSTRACT_field_max');
			for ( $n=0; $n<$max_abstract_fields; $n++) {
				array_push( $abstract_fields, each($fields) );
			}
		}
    return $abstract_fields;
  }
  
  /**
   * Autoset abstract_filter
   *
   * @return string
   * @author Jan den Besten
   */
  protected function _autoset_abstract_filter() {
    $this->load->model('cfg');
    // Haal eerst indien mogelijk uit (depricated) cfg_table_info
    $abstract_filter = $this->cfg->get( 'cfg_table_info', $this->settings['table'], 'str_options_where');
    // Anders is het gewoon standaard ''
    if (is_null($abstract_filter)) $abstract_filter = '';
    return $abstract_filter;
  }
  
  
  
  /* --- DB methods --- */

  
  /**
   * Alle Query Builder methods zijn beschikbaar -> TODO beter is ze uit te schrijven hier...
   *
   * @return this
   * @author Jan den Besten
   */
  public function __call($method,$arguments) {
    if (method_exists($this->db,$method)) {
      $result = call_user_func_array( array($this->db,$method), $arguments );
      if ($result!==$this->db) {
        return $result;
      }
      return $this;
    }
    throw new Exception( $method . ' does not exist in '.__CLASS__);
  }
  
  
  
  /* -- Methods voor het klaarmaken van een query --- */
  
  /**
   * Stel hier eventueel een table in die gebruikt moet worden in table_model.
   * 
   * Je kunt table_model ook los gebruiken, zonder een eigen model voor een table.
   * Stel dan hier de table in die gebruikt moet worden.
   * Als de bijbehorede config bestaat (bijvoorbeeld config/tables/tbl_menu.php) dan wordt die geladen.
   * Als de bijbehorede config NIET bestaat, dat wordt zover het kan alles automatisch ingesteld met autoset.
   *
   * @param string $table 
   * @return void
   * @author Jan den Besten
   */
  public function table( $table ) {
    if (empty($table)) {
      $table = $this->autoset_table();
    }
    $this->_config( $table );
    $this->settings['table'] = $table;
    return $this;
  }
  
  
  /**
   * Geeft abstract_fields
   *
   * @param mixed $fields [''] als je hier een array van strings, of een komma gescheiden string met velden meegeeft wordt dat gebruikt.
   * @return array
   * @author Jan den Besten
   */
  public function get_abstract_fields( $fields='' ) {
    return $this->settings['abstract_fields'];
  }
  
  
	/**
	 * Geeft (select) SQL voor selecteren van abstract
	 *
	 * @param string $asPre['']
	 * @return string
	 * @author Jan den Besten
	 */
  public function get_compiled_abstract_select() {
		$abstract_fields = $this->get_abstract_fields();
    $deep_foreigns = $this->config->item('DEEP_FOREIGNS');
    if ($deep_foreigns )  {
      foreach ( $deep_foreigns as $deep_key => $deep_info ) {
        if ( $nr = in_array_like( $deep_key,$abstract_fields ) ) {
          $deep_field = $abstract_fields[$nr];
          $abstract_fields[$nr] = "(SELECT `".$deep_info['abstract']."` FROM `".$deep_info['table']."` WHERE ".$deep_info['table'].".id=".$deep_field.")";
        }
      }
    }
    // Maak de SQL
		$sql = '`'.$this->settings['table'].'`.`'.$this->settings['primary_key']."`, CONCAT_WS('|',`".$this->settings['table'].'`.`' . implode( "`,`".$this->settings['table'].'`.`' ,$abstract_fields ) . "`) AS `" . $this->config->item('ABSTRACT_field_name') . "`";
    return $sql;
	}
  
  
  /* --- Getters & Setters --- */
  
  /**
   * Stelt een setting in.
   * Is alleen nodig als je tijdelijk een afwijkende instelling wilt, want standaard kun je alles al instellen in de het config bestand dat bij het table_model hoort.
   *
   * @param string $key 
   * @param mixed $value 
   * @return $this
   * @author Jan den Besten
   */
  public function set_setting( $key, $value ) {
    $this->settings[$key] = $value;
    return $this;
  }
  
  
  /**
   * Stel result key in voor gebruik bij $this->get_result()
   * Staat standaard al ingesteld (default PRIMARY_KEY), maar kan hier tijdelijk een andere waarder krijgen.
   *
   * @param string $key [''] Als leeg dan wordt primary_key gebruikt
   * @return $this
   * @author Jan den Besten
   */
  public function set_result_key( $key='' ) {
    if (empty($key)) $key = $this->settings['primary_key'];
    $this->set_setting( 'result_key', $key );
    return $this;
  }
  
  
  /**
   * Geeft een setting. Als die niet bestaat dan wordt de standaard autoset waarde gegeven of anders null
   *
   * @param string $key 
   * @return mixed
   * @author Jan den Besten
   */
  public function get_setting( $key ) {
    return el( $key, $settings, el( $key, $this->autoset ) );
  }
  
  /**
   * Geeft alle settings
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_settings() {
    return $this->settings;
  }

  /**
   * Geeft instellingen voor admin_grid
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_admin_grid() {
    return $this->get_setting( 'admin_grid' );
  }

  /**
   * Geeft instellingen voor admin_form
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_admin_form() {
    return $this->get_setting( 'admin_form' );
  }
  
  
  /* --- Methods die query data teruggeven --- */
  
  
  /**
   * Geeft resultaat als query object. Eventueel beperkt door limit en offset
   *
   * @param int $limit [0]
   * @param int $offset [0] 
   * @return object $query
   * @author Jan den Besten
   */
  public function get( $limit=0, $offset=0 ) {
    if ( ! empty($this->settings['order_by']) ) $this->db->order_by( $this->settings['order_by'] );
    return $this->db->get( $this->settings['table'], $limit, $offset );
  }

  
  /**
   * Geeft één rij uit de database tabel, als query_row object, met het meegegeven id
   *
   * @param int $id 
   * @return object $query->row
   * @author Jan den Besten
   */
  public function get_one($id) {
    return $this->get_one_by( $this->settings['primary_key'], $id);
  }
  
  
  /**
   * Geeft één (of de eerste) rij uit de database tabel, als query_row object, waarvoor geld dat 'field' = 'value'
   *
   * @param string $field 
   * @param mixed $value 
   * @return object $query->row
   * @author Jan den Besten
   */
  public function get_one_by( $field,$value ) {
    $this->db->where( $field, $value );
    return $this->get();
  }
  
  
  /**
   * Maakt een mooie result_array van een $query
   * - Met keys die standaard primary_key zijn, of ingesteld kunnen worden met set_result_key()
   * - Met relaties gekoppeld als subarrays
   *
   * @param object $query 
   * @return array
   * @author Jan den Besten
   */
  private function _make_result_array( $query ) {
    $result = array();
    foreach ( $query->result_array() as $row ) {
      // TODO has_many en many_to_many als subarrays toevoegen aan row
      // ...
      // result_key
      if ( isset( $row[$this->settings['result_key']] ) ) {
        $key = $row[ $this->settings['result_key'] ];
      }
      $result[$key] = $row;
    }
    return $result;
  }
  
  
  /**
   * Geeft resultaat terug als result array
   * - array key is standaard de PRIMARY KEY maar kan ingesteld worden met $this->set_result_key()
   * - voor many_to_many en has_many wordt resultaat sub arrays in het resultaat.
   * 
   * NB Gebruikt relatief veel resources.
   * Bij voorkeur niet gebruiken als resources belangrijk zijn.
   * Of alleen bij kleine resultaten en/of in combinatie met limit / pagination.
   *
   * @param int $limit [0]
   * @param int $offset [0] 
   * @return array
   * @author Jan den Besten
   */
  public function get_result( $limit=0, $offset=0 ) {
    $query = $this->get( $limit, $offset );
    return $this->_make_result_array( $query );
  }
  
  
  /**
   * Zelfde als get_result(), maar geeft nu alleen maar de eerstgevonden rij.
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_row() {
    $result = $this->get_result( 1 );
    return current($result);
  }


  
  /* --- Methods om de query te vormen --- */


  /**
   * Selecteert abstract fields
   *
   * @return $this
   * @author Jan den Besten
   */
  public function select_abstract() {
    $this->db->select( $this->get_compiled_abstract_select(), FALSE );
    return $this;
  }
  
  
  /**
   * Geef aan welke relaties meegenomen moeten worden in het resultaat.
   * 
   * Bijvoorbeeld: array(
   *    'belongs_to' => array( 'tbl_items', 'tbl_posts' => array( ... fields ... ) )
   * )
   * 
   *
   * @param array $with
   * @return $this
   * @author Jan den Besten
   */
  public function with( $with ) {
    $this->with = $with;
    return $this;
  }
  
  

  /* --- Informatieve methods --- */
  
  
  /**
   * Geeft velden van tabel terug
   *
   * @return array
   * @author Jan den Besten
   */
  public function list_fields() {
    return $this->settings['fields'];
  }
  
  
  /**
   * Test of een veld bestaat
   *
   * @param string $field 
   * @return boolean
   * @author Jan den Besten
   */
  public function field_exists( $field ) {
    $fields = $this->list_fields();
    return in_array( $field, $fields );
  }


}
