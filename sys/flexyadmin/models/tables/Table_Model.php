<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup tables
 * 
 * - Alle instellingen van een tabel en zijn velden tabel zijn te vinden in config/tables/...
 * - Standaard get/crud zit in het model, voor elke tabel hetzelfde.
 * - Iedere tabel kan deze overerven en aanpassen naar wens, de aanroepen blijven hetzelfde voor iedere tabel.
 * - Alle query-builder methods (en db methods) kunnen worden gebruikt met het model. Dus dezelfde naamgeving voor de methods.
 * - Naast ->get() die een query object teruggeeft ook ->get_result() en get_row() die een aangepaste result array teruggeeft met eventuele many_to_many data als subarray.
 * - Relaties worden helemaal met SQL gegenereerd, ook de WHERE statements:
 * 
 * many_to_one
 * -----------
 * ->where( 'tbl_links.str_title', 'test );           // Filtert het resultaat op het veld 'str_title' uit de many_to_one relatie met tbl_links.
 * 
 * many_to_many
 * ------------
 * ->where( 'tbl_links.str_title', 'text' );          // Filtert het resultaat op het veld 'str_title' uit de many_to_many relatie met tbl_links. Alleen het gezochte subitem komt mee in het resultaat.
 * ->where_exists( 'tbl_links.str_title', 'text' );   // Idem maar in resultaat worden alle subitems meegenomen
 * 
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
   * Enkele noodzakelijk instellingen die automatisch worden ingesteld als ze niet bekend zijn.
   */
  protected $autoset = array(
    'table'           => '',
    'fields'          => array(),
    'field_info'      => array(),
    'order_by'        => '',
    'abstract_fields' => '',
    'admin_grid'      => array(),
    'admin_form'      => array(),
  );
  
  
  /**
   * Hou SELECT bij om ervoor te zorgen dat SELECT in orde is
   */
  private $tm_select  = false;


  /**
   * Welke relaties mee moeten worden genomen
   */
  private $tm_with    = array();



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
        // var_dump(['_autoset_'.$key => $this->settings[$key] ]);
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
      if (!empty($field_info['str_options'])) {
        $settings_fields_info[$field]['options'] = $field_info['str_options'];
        $settings_fields_info[$field]['multiple_options'] = el('b_multi_options', $field_info, false)?true:false;
      }
      $settings_fields_info[$field]['validation'] = $this->form_validation->get_validations( $table, $field );
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
  
  /**
   * Autoset admin_grid
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _autoset_admin_grid() {
    $this->load->model('cfg');
    $table_info = $this->cfg->get( 'cfg_table_info',$this->settings['table'] );
    $show_always = $this->config->item('ALWAYS_SHOW_FIELDS');

    $admin_grid['fields'] = $this->settings['fields'];
    foreach ($admin_grid['fields'] as $key => $field) {
      $field_info = $this->cfg->get('cfg_field_info', $this->settings['table'].'.'.$field );
      if ( !in_array($field,$show_always) and !el('b_show_in_grid',$field_info,TRUE) ) unset($admin_grid['fields'][$key]);
    }
    $admin_grid['order_by']      = $this->settings['order_by'];
    $admin_grid['jump_to_today'] = el('b_jump_to_today',$table_info,TRUE);
    $admin_grid['pagination']    = el('b_pagination',$table_info,TRUE);
    $admin_grid['with']          = array();
    return $admin_grid;
  }

  /**
   * Autoset admin_form
   *
   * @return void
   * @author Jan den Besten
   */
  protected function _autoset_admin_form() {
    $this->load->model('cfg');
    $table_info = $this->cfg->get( 'cfg_table_info',$this->settings['table'] );
    $show_always = $this->config->item('ALWAYS_SHOW_FIELDS');
    $main_fieldset = $this->settings['table'];
    $fieldsets = array($main_fieldset=>array());

    $admin_form['fields'] = $this->settings['fields'];
    foreach ($admin_form['fields'] as $key => $field) {
      $field_info = $this->cfg->get('cfg_field_info', $this->settings['table'].'.'.$field );
      // Show?
      if ( !in_array($field,$show_always) and !el('b_show_in_form',$field_info,TRUE) ) {
        unset($admin_form['fields'][$key]);
      }
      // in which fieldset?
      else {
        $fieldset = el('str_fieldset',$field_info, $main_fieldset );
        if (!isset($fieldsets[$fieldset])) $fieldsets[$fieldset]=array();
        array_push( $fieldsets[$fieldset], $field );
      }
    }
    $admin_form['fieldsets'] = $fieldsets;
    $admin_form['with']      = array();
    return $admin_form;
  }
  
  
  /* --- Informatie uit andere tabellen/models --- */

  /**
   * Haalt een setting op van een andere table (model) 
   *
   * @param string $table 
   * @param string $key 
   * @return mixed NULL als niet gevonden
   * @author Jan den Besten
   */
  protected function get_other_table_setting( $table, $key ) {
    $setting = null;
    // Probeer eerst of het table model bestaat
    if ( method_exists( $table, 'get_setting' ) ) {
      $setting = $this->$table->get_setting( $key );
    }
    // Laad anders de config van die tabel/model
    else {
      $this->config->load( 'tables/'.$table, true);
      $settings = $this->config->item( 'tables/'.$table );
      $setting = el( $key, $settings );
    }
    return $setting;
  }
  
  /**
   * Haalt de velden van een andere table model op.
   * Als die niet gevonden worden, of niet zijn ingesteld, dan worden de velden uit de database gehaald.
   *
   * @param string $table 
   * @return array()
   * @author Jan den Besten
   */
  protected function get_other_table_fields( $table ) {
    $fields = $this->get_other_table_setting( $table, 'fields' );
    if ( is_null($fields) or empty($fields)) {
      $fields = $this->db->list_fields( $table );
    }
    return $fields;
  }
  
  /**
   * Haalt de abstract fields van een andere table model op.
   * Als die niet gevonden worden, of niet zijn ingesteld, dan worden de velden gegenereerd.
   *
   * @param string $table 
   * @return array()
   * @author Jan den Besten
   */
  protected function get_other_table_abstract_fields( $table ) {
    $abstract_fields = $this->get_other_table_setting( $table, 'abstract_fields' );
    if ( is_null($abstract_fields) or empty($abstract_fields)) {
      $fields = $this->get_other_table_fields( $table );
      $abstract_fields = $this->_autoset_abstract_fields( $table, $fields );
    }
    return $abstract_fields;
  }
  
  /**
   * Haal abstract select op van andere tabel
   *
   * @param string $table 
   * @return string
   * @author Jan den Besten
   */
  protected function get_other_table_compiled_abstract_select( $table ) {
    $abstract_fields = $this->get_other_table_abstract_fields( $table );
    return $this->get_compiled_abstract_select( $table, $abstract_fields, $table.'__' );
  }
  
  
  
  /* --- DB methods --- */

  
  /**
   * Alle Query Builder en andere database methods zijn beschikbaar
   *
   * @return mixed
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
   * Reset alle instellingen voor het opbouwen van een query
   *
   * @return void
   * @author Jan den Besten
   */
  public function reset() {
    $this->tm_select = false;
    $this->with();
    return $this;
  }
  
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
    $this->reset();
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
	 * @param string $table [''] als leeg dan wordt de table uit de settings gehaald
	 * @param array  $abstract_fields [''] als leeg dan worden de abstract_fields uit de 'settings' gehaald
	 * @param string $abstract_prefix [''] een eventuele prefix string die voor de veldnaam 'abstract' wordt geplakt
	 * @return string
	 * @author Jan den Besten
	 */
  public function get_compiled_abstract_select( $table='', $abstract_fields='', $abstract_prefix = '' ) {
    $abstract_field_name = $abstract_prefix . $this->config->item('ABSTRACT_field_name');
		if (empty($table)) $table = $this->settings['table'];
		if (empty($abstract_fields)) $abstract_fields = $this->get_abstract_fields();
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
		$sql = "CONCAT_WS('|',`".$table.'`.`' . implode( "`,`".$table.'`.`' ,$abstract_fields ) . "`) AS `" . $abstract_field_name . "`";
    return $sql;
	}
  
  /**
   * Find relation tables of a given type
   *
   * @param string $type 
   * @return array
   * @author Jan den Besten
   */
  public function get_relation_tables( $type ) {
    $method = '_get_'.$type.'_tables';
    if ( method_exists($this,$method) ) {
      return $this->$method();
    }
    else {
      throw new ErrorException( 'FA: Method "'.$method.'" does not exists. So the relation tables cannot be found.' );
    }
    return $tables;
  }

  /**
   * Find many_to_one tables
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _get_many_to_one_tables() {
    $tables = array();
    $foreign_keys = filter_by( $this->settings['fields'], $this->settings['primary_key'].'_' );
    foreach ( $foreign_keys as $foreign_key ) {
      $tables[] = 'tbl_'.remove_prefix($foreign_key);
    }
    return $tables;
  }

  /**
   * Find many_to_many tables
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _get_many_to_many_tables() {
    $tables = $this->db->list_tables();
    $tables = filter_by( $tables, 'rel_'.remove_prefix($this->settings['table']) );
    return $tables;
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
  
  /**
   * Geeft relatie instellingen terug
   * 
   * @param string $type [''] geef hier eventueel het type relatie dat je wilt terugkrijgen
   * @return array
   * @author Jan den Besten
   */
  public function get_with( $type='' ) {
    if (!empty($type)) {
      return el( $type, $this->tm_with );
    }
    return $this->tm_with;
  }
  
  
  /* --- Methods die query data teruggeven --- */
  
  
  /**
   * Geeft resultaat als query object. Eventueel beperkt door limit en offset
   *
   * @param int $limit [0]
   * @param int $offset [0]
   * @param bool $reset [true] als true dan wordt aan het eind alle instellingen gereset (with,)
   * @return object $query
   * @author Jan den Besten
   */
  public function get( $limit=0, $offset=0, $reset = true ) {
    // select, zorg ervoor dat er iig iets in de select komt
    if ( !$this->tm_select ) $this->select();

    // with, maak de relatie queries
    if ( !empty( $this->tm_with ) ) $this->_with( $this->tm_with );

    // order_by
    if ( ! empty($this->settings['order_by']) ) $this->db->order_by( $this->settings['order_by'] );
    
    // limit & offset
    $query = $this->db->get( $this->settings['table'], $limit, $offset );
    if ( $reset ) $this->reset();
    return $query;
  }
  
  /**
   * Geeft één (of de eerste) rij uit de database tabel, als query_row object, waarvoor geld dat 'field' = 'value'
   *
   * @param string $field 
   * @param mixed $value
   * @param bool $reset [true] als true dan wordt aan het eind alle instellingen gereset (with,)
   * @return object $query->row
   * @author Jan den Besten
   */
  public function get_one_by( $field, $value, $reset = true ) {
    $this->db->where( $field, $value );
    return $this->get( 0,0, $reset );
  }
  
  /**
   * Geeft één rij uit de database tabel, als query_row object, met het meegegeven id
   *
   * @param int $id
   * @param bool $reset [true] als true dan wordt aan het eind alle instellingen gereset (with,)
   * @return object $query->row
   * @author Jan den Besten
   */
  public function get_one( $id, $reset = true ) {
    return $this->get_one_by( $this->settings['primary_key'], $id, $reset );
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
    $key = el( 'result_key', $this->settings, el( 'primary_key',$this->settings ) );
    $has_many_to_many = !empty($this->tm_with);
    $many_data = array();
    
    foreach ( $query->result_array() as $row ) {
      $result_key = $row[$key];
      
      // many_to_many als subarrays toevoegen aan row
      if ($has_many_to_many) {
        foreach ($this->tm_with['many_to_many'] as $table => $fields) {
          $other_table = 'tbl_'.get_suffix($table,'__');
          // split row and many data
          $row_many_data = filter_by_key( $row, $other_table );
          $row = array_diff( $row, $row_many_data );
          // process many data
          foreach ($row_many_data as $oldkey => $values) {
            $newkey = remove_prefix( $oldkey, '__');
            $row_many_data[$newkey] = $values;
            unset($row_many_data[$oldkey]);
          }
          // remember many data
          if ( isset($row_many_data[ $this->settings['primary_key'] ]) )
            $many_data[$result_key][$row_many_data[$this->settings['primary_key']]] = $row_many_data;
          else
            $many_data[$result_key][]=$row_many_data;
        }
        if (!isset($row[$table])) $row[$table] = array();
        $row[$table] = array_unique_multi($many_data[$result_key]);
      }
      
      // result_key
      $result[ $result_key ] = $row;
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
    $query = $this->get( $limit, $offset, false );
    $result = $this->_make_result_array( $query );
    $this->reset();
    return $result;
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
   * Zelfde als 'select' van Query Builder, maar met enkele checks:
   * - zorgt ervoor dat altijd de primary_key in de select voorkomt
   * - zorgt ervoor dat de veldnamen altijd met de tabel naam ervoor wordt geselecteerd
   *
   * @param mixed $select ['*']
   * @param mixed $escape [NULL]
   * @return $this
   * @author Jan den Besten
   */
	public function select( $select = '*', $escape = NULL ) {
    // Niet '*' maar alle velden expliciet maken
    if ( $select=='*' ) $select = $this->settings['fields'];

    // Alle selects als array
    if (is_string($select)) $select = explode(',', $select);

		// Zorgt ervoor dat iig primary_key wordt geselecteerd (bij de eerste keer dat select wordt aangeroepen)
    if ( !$this->tm_select and !in_array( $this->settings['primary_key'], $select ) ) {
      array_unshift( $select, $this->settings['primary_key'] );
    }

    foreach ($select as $key => $field) {
      // Zorg ervoor dat alle velden geprefixed worden door de eigen tabelnaam om dubbelingen te voorkomen
      if (in_array($field,$this->settings['fields'])) {
        $field = $this->settings['table'].'.'.$field;
      }
      // Bewaar
      $this->tm_select[] = $field;
      // Normale select
      $this->db->select( $field, $escape );
    }

		return $this;
	}
  
  /**
   * Selecteert abstract fields
   *
   * @return $this
   * @author Jan den Besten
   */
  public function select_abstract() {
    $this->select( $this->get_compiled_abstract_select(), FALSE );
    return $this;
  }
  
  /**
   * Zelfde als 'where' van Query Builder, met deze uitbreidingen:
   * - Als $value een array is wordt 'where_in' aangeroepen.
   * - Je kunt ook where statements voor relaties aangeven, zie hieronder.
   * 
   * many_to_one
   * -----------
   * ->where( 'tbl_links.str_title', 'test );     // Zoekt het resultaat op het veld 'str_title' uit de many_to_one relatie met tbl_links.
   * 
   * many_to_many
   * ------------
   * ->where( 'tbl_links.str_title', 'text' );    // Zoekt het resultaat op het veld 'str_title' uit de many_to_many relatie met tbl_links.
   * ->where( 'tbl_links.id', 3 );                // idem op 'id'
   * LET OP: deze voorbeelden zoeken snel maar geven geen complete many_to_many resultaten. De resulataten zijn ook alleen maar met de where voorwaarden.
   * Als je wilt zoeken, maar wel de complete waarden wilt, gebruik dan ->where_exists()
   *
   * @param string $key 
   * @param mixed $value [NULL]
   * @param mixed $escape [NULL]
   * @return $this
   * @author Jan den Besten
   */
	public function where($key, $value = NULL, $escape = NULL) {
    if (isset($value) and is_array($value)) {
      $this->db->where_in($key,$value,$escape);
      return $this;
    }
    $this->db->where($key,$value,$escape);
    return $this;
  }
  
  /**
   * where_exists zoekt in many_to_many data
   * - Eerst wordt gezocht in de andere tabel voor de key/value combinatie
   * - Alle items in de eigen tabel worden getoond waarvan een relatie bestaat met het zoekresultaat
   * - Alle subitems van de eigen tabel worden getoond
   * 
   * many_to_many
   * ------------
   * ->where_exists( 'tbl_links.str_title', 'text' );    // Zoekt het resultaat op het veld 'str_title' uit de many_to_many relatie met tbl_links.
   * ->where_exists( 'tbl_links.id', 3 );                // idem op 'id'
   *
   * @param string $key Moet in het formaat table.field zijn.
   * @param string $value De gezocht waarde
   * @return $this
   * @author Jan den Besten
   */
  public function where_exists( $key, $value ) {
    return $this->_where_exists( $key, $value, 'AND');
  }
  
  /**
   * Zelfde als where_exists maar dan een OR
   *
   * @param string $key Moet in het formaat table.field zijn.
   * @param string $value 
   * @return void
   * @author Jan den Besten
   */
  public function or_where_exists( $key, $value ) {
    return $this->_where_exists( $key, $value, 'OR');
  }
  
  /**
   * where_exists zoekt in many_to_many data, met als sql resultaat (zie voorbeeld)
   * WHERE `tbl_menu`.`id` IN (
   * 	SELECT `rel_menu__links`.`id_menu`
   * 	FROM `rel_menu__links`
   * 	WHERE `rel_menu__links`.`id_links` IN (
   * 		SELECT `tbl_links`.`id`
   * 		FROM `tbl_links`
   * 		WHERE `str_title` = "text"
   * 	)	
   * )
   *
   * @param string $key 
   * @param string $value 
   * @param string $type ['AND'|'OR']
   * @return $this
   * @author Jan den Besten
   */
  protected function _where_exists( $key, $value, $type = 'AND' ) {
    $other_table = get_prefix($key,'.');
    $key         = get_suffix($key,'.');
    if (empty($other_table) or empty($key) or $key==$other_table) {
      throw new ErrorException( 'FA: First argument of `where_exists` needs to be of this format: `table.field`.' );
    }
    $id                = $this->settings['primary_key'];
    $this_table        = $this->settings['table'];
    $join_table        = 'rel_'.remove_prefix($this_table).'__'.remove_prefix($other_table);
    $this_foreign_key  = $id.'_'.remove_prefix($this_table);
    $other_foreign_key = $id.'_'.remove_prefix($other_table);
    
    $sql = ' `'.$this_table.'`.`'.$id.'` IN (
    	SELECT `'.$join_table.'`.`'.$this_foreign_key.'`
    	FROM `'.$join_table.'`
    	WHERE `'.$join_table.'`.`'.$other_foreign_key.'` IN (
    		SELECT `'.$other_table.'`.`'.$id.'`
    		FROM `'.$other_table.'`
    		WHERE `'.$key.'` = "'.$value.'"
    	)	
    )';
    if ($type=='OR')
      $this->db->or_where( $sql, NULL, false );
    else
      $this->db->where( $sql, NULL, false );
    return $this;
  }

  /**
   * Geef aan welke relaties meegenomen moeten worden in het resultaat.
   * Deze method kan vaker aangeroepen worden met nieuwe relaties.
   * Als de relatie eenzelfde tabel bevat, worden van die tabel de extra meegegeven waarden vervangen
   * 
   * Voorbeelden: [] = array()
   * 
   * - with();                                                                // Reset alle relaties. Wordt ook aangeroepen na elke get() (of variant)
   * 
   * - with( 'many_to_one' );                                                 // Voegt alle many_to_one relaties toe aan resultaat. Hiermee wordt automatisch naar de foreign keys gezocht.
   * - with( 'many_to_one', [] );                                             // idem
   * - with( 'many_to_one' => [ 'tbl_posts' ] );                              // Voegt alleen de many_to_one relatie met 'tbl_posts' toe
   * - with( 'many_to_one' => [ 'tbl_posts', 'tbl_links' ] );                 // Voegt alleen de many_to_one relatie met 'tbl_posts' en 'tbl_links' toe
   * - with( 'many_to_one' => [ 'tbl_posts' => 'str_title,txt_text' ] );      // Voegt alleen de many_to_one relatie met 'tbl_posts' en daarvan alleen de velden 'str_title' en 'txt_text'
   * - with( 'many_to_one' => [ 'tbl_posts' => ['str_title','txt_text'] ] );  // idem
   * - with( 'many_to_one' => [ 'tbl_posts' => 'abstract ] );                 // Voegt alleen de many_to_one relatie met 'tbl_posts' en daarvan alleen een abstract
   * 
   * - with( 'many_to_many' );                                                 // Voegt alle many_to_many relaties toe aan resultaat
   * - with( 'many_to_many' => [ 'tbl_posts' ] );                              // Voegt alleen de many_to_many relatie met 'tbl_posts' toe
   * - with( 'many_to_many' => [ 'tbl_posts' => ['str_title','txt_text'] ] );  // idem
   * - with( 'many_to_many' => [ 'tbl_posts' => 'abstract ] );                 // Voegt alleen de many_to_many relatie met 'tbl_posts' en daarvan alleen een abstract
   * 
   * @param string $type
   * @param array $tables [array()]
   * @return $this
   * @author Jan den Besten
   */
  public function with( $type='', $tables=array() ) {
    // Reset?
    if ( empty($type) ) {
      $this->tm_with = array();
      return $this;
    }
    
    // Als geen tables zijn meegegeven, zoek ze automatisch
    if ( empty($tables) ) $tables = $this->get_relation_tables( $type );
    // Zorg ervoor dat $tables in dit formaat komt: 'table' => fields
    $tables_new = array();
    foreach ($tables as $key => $value) {
      // Juist key
      $table  = $key;
      $fields = $value;
      if ( is_integer($key) ) {
        $table  = $value;
        $fields = array();
      }
      // Velden moeten een array of 'abstract' zijn.
      if (is_string($fields) and $fields!==$this->config->item('ABSTRACT_field_name')) {
        $fields = explode( ',',$fields );
      }
      // Bewaar
      $tables_new[$table] = $fields;
    }
    // Merge met bestaande
    $tables_before = el( $type, $this->tm_with, array() );
    $tables_new = array_merge( $tables_before, $tables_new );
    // Bewaar deze relatie instelling
    $this->tm_with[$type] = $tables_new;
    return $this;
  }
  
  /**
   * Bouwt de query op voor relaties, roept voor elke soort relatie een eigen method aan.
   *
   * @param string $with 
   * @return $this
   * @author Jan den Besten
   */
  protected function _with( $with ) {
    foreach ( $with as $type => $tables ) {
      $method = '_with_'.$type;
      if ( method_exists( $this, $method ) ) {
        $this->$method( $tables );
      }
      else {
        throw new ErrorException( 'FA: Method "'.$method.'" does not exists. So the relation cannot be included in the result.' );
      }
    }
    return $this;
  }
  
  /**
   * Selecteerd de velden die bij SELECT moeten komen bij relaties
   *
   * @param string $table de gerelateerde tabel
   * @param array $fields velden van de gerelateerde tabel
   * @return $this
   * @author Jan den Besten
   */
  protected function _select_with_fields( $table, $fields ) {
    $select = false;
    // Welke velden van de gerelateerde tabel?
    if ( empty($fields) )
      $fields = $this->get_other_table_fields( $table );
    elseif ( $fields === 'abstract' )
      $select = $this->get_other_table_compiled_abstract_select( $table );

    // Select de velden van de gerelateerde tabel
    if ($select) {
      $this->db->select( $select );
    }
    else {
      // primary_key hoeft er niet in
      if ( $key=array_search( $this->settings['primary_key'], $fields ) ) {
        unset($fields[$key]);
      }
      foreach ($fields as $field) {
        $this->db->select( "$table.$field AS $table"."__"."$field" );
      }
    }
    return $this;
  }

  /**
   * Bouwt many_to_one join query
   *
   * @param array $tables 
   * @return $this
   * @author Jan den Besten
   */
  protected function _with_many_to_one( $tables ) {
    $id = $this->settings['primary_key'];
    foreach ($tables as $table => $fields) {
      // Select fields
      $this->_select_with_fields( $table, $fields );
      // Join
      $foreign_key = $id.'_'.remove_prefix( $table );
      $this->join( $table, $table.'.'.$id.' = '.$this->settings['table'].".".$foreign_key, 'left');
    }
    return $this;
  }
  
  /**
   * Bouwt many_to_many join query
   *
   * @param string $tables 
   * @return $this
   * @author Jan den Besten
   */
  protected function _with_many_to_many( $tables ) {
    $this_table = $this->settings['table'];
    $id = $this->settings['primary_key'];
    foreach ( $tables as $rel_table => $fields ) {
      $other_table = 'tbl_'.get_suffix( $rel_table, '__' );
      $this_foreign_key  = $id.'_'.remove_prefix( $this_table );
      $other_foreign_key = $id.'_'.remove_prefix( $other_table );
      // Select fields
      $this->_select_with_fields( $other_table, $fields, $other_table.'__' );
      // Joins
      $this->join( $rel_table,    $this_table.'.'.$id.' = '.$rel_table.".".$this_foreign_key,     'left');
      $this->join( $other_table,  $rel_table. '.'.$other_foreign_key.' = '.$other_table.".".$id,  'left');
    }
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
