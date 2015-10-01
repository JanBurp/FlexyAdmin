<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup tables
 * 
 * - Alle instellingen van een tabel en zijn velden tabel zijn te vinden in config/tables/...
 * - Standaard get/crud zit in het model, voor elke tabel hetzelfde.
 * - Iedere tabel kan deze overerven en aanpassen naar wens, de aanroepen blijven hetzelfde voor iedere tabel.
 * - Alle query-builder methods (en db methods) kunnen worden gebruikt met het model. Als je CodeIgniter kent, ken je dit model al bijna.
 * - Naast ->get() die een query object teruggeeft ook ->get_result() die een aangepaste result array teruggeeft met relatie data als subarray.
 * 
 * 
 * Enkele belangrijke methods (deels overgeerft van Query Builder):
 * 
 * ->table( $table )                              // Stelt tabel waarvoor het model wordt gebruikt (laad corresponderende settings als die bestaan)
 * 
 * ->get( $limit=0, $offset=0 )                   // Geeft een $query object (zoals in Query Builder)
 * ->get_where( $where=NULL, $limit=0, $offset=0) // Geeft een $query object (zoals in Query Builder)
 * ->get_result( $limit=0, $offset=0 )            // Geeft een aangepaste $query->result_array, met de key ingesteld als result_key en relatie data als subarray per item
 * ->get_row( $where = NULL )                     // Idem, maar dan maar één item (de eerste in het resultaat)
 * ->get_field( $field, $where = NULL )           // Idem, maar dan van één item alleen de waarde van het gevraagde veld
 * ->set_result_key( $key='' )                    // Hiermee kan voor ->get_result() de key van de array ingesteld worden op een ander (uniek) veld. Standaard is dat de primary_key
 * 
 * ->insert( $set = NULL )                        // Als Query Builder, maar met verwijzingen naar bestaande many_to_many data
 * ->update( $set=NULL, $where=NULL, $limit=NULL) // idem
 * ->delete( $where = '', $limit = NULL )         // idem
 * 
 * ->select( $select = '*' )                      // Maak SELECT deel van de query (zoals in Query Builder)
 * ->select_abstract()                            // Maak SELECT deel van de query door alle abstract_fields te gebruiken (en als die niet zijn ingesteld zelf te genereren)
 * ->path( $path_field, $original_field = '' )    // Geeft een veld aan dat een geheel pad aan waarden moet bevatten in een tree table (bijvoorbeeld een menu)
 * 
 * ->with( $type='', $tables=array() )            // Voeg relaties toe (many_to_one, many_to_many) en specificeer eventueel van welke tabellen en hun velden
 * ->with_grouped( $type='', $tables=array() )    // Idem, maar dan met gegroepeerde many_to_many data
 * 
 * ->where($key, $value = NULL)                   // Zoals in Query Builder. Kan ook zoeken in many_to_many data (waar de many_to_many data ook gefilterd is)
 * ->where_exists( $key, $value = NULL )          // Idem met als resultaat dezelfde items maar met complete many_to_many data van dat item (ongefilterd)
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
    'order_by'        => 'id',
    'max_rows'        => 0,
    'update_uris'     => true,
    'abstract_fields' => array(),
    'abstract_filter' => '',
    'relations'       => array(),
    'admin_grid'      => array(),
    'admin_form'      => array(),
  );
  
  /**
   * Onthoud eventueel opgevraagde field_data
   */
  private $field_data = NULL;
  
  /**
   * Onthoud eventueel al opgezochte relatie tabellen
   */
  private $relation_tables = array();


  /**
   * Hou SELECT bij om ervoor te zorgen dat SELECT in orde is
   */
  private $tm_select  = FALSE;
  
  /**
   * Maximale lengte van txt velden, 0 is geen aanpassing.
   */
  private $tm_txt_maxlen = 0;
  
  /**
   * Hou LIMIT en OFFSET bij om eventueel total_rows te kunnen berekenen
   */
  private $tm_limit  = 0;
  private $tm_offset = 0;

  /**
   * Een eventueel veld dat een compleet pad moet bevatten in een tree table
   */
  private $tm_path = FALSE;


  /**
   * Welke relaties mee moeten worden genomen en op welke manier
   * 
   * Kan er bijvoorbeeld zo uit zien:
   * 
   * array(
   *  'many_to_many' => array(
   *    'tbl_links' => array(
   *       'fields'   => 'abstract',
   *       'grouped'  => FALSE
   *     ),
   *    'tbl_posts' => array(
   *       'fields'   => array('id','str_title',')
   *       'grouped'  => true
   *     ),
   *   )
   * )
   */
  private $tm_with    = array();


  /**
   * Set array voor insert/update
   */
  private $tm_set     = NULL;

  
  /**
   * Moet de data voor een insert/update eerste gevalideerd worden?
   */
  private $validation = FALSE;
  
  /**
   * Is nodig om eventueel te kunnen instellen in de database wie iets heeft aangepast
   */
  private $user_id    = NULL;
  

  
  /**
   * Bewaart informatie van bepaalde methods
   * 
   * ->get_result() (en varianten):
   * ------------------------------  
   * - num_rows           - Zelfde als $query->num_rows()
   * - total_rows         - Idem, maar nu zonder limit
   * - num_fields         - Zelfde als $query->num_fields()
   * (- last_query)       - Alleen als nodig is geweest voor het berekenen van total_rows
   * (- last_clean_query) - Alleen als nodig is geweest voor het berekenen van total_rows
   * 
   * ->insert() / ->update():
   * ------------------------
   * - validation         - TRUE/FALSE, alleen als $this->validate() bij
   * - validation_errors  - Als 'validation' = FALSE, dan staan hier foutmeldingen
   */
  private $query_info = array();
  


  /* --- CONSTRUCT & AUTOSET --- */

	public function __construct() {
		parent::__construct();
    $this->_config();
	}


  
  //
  // TODO
  // 
  // van Query Builder:
  // 
  // public function insert_batch()
  // public function update_batch()
  // public function set_insert_batch()
  // public function set_update_batch()
  //





  /**
   * Laad de bijbehorende config.
   * Merge die met de defaults.
   * Als dan nog niet alle belangrijke zaken zijn ingesteld, doe dat dan met autoset
   *
   * @param string $table [''] Stel eventueel de naam van de table in. Default wordt de naam van het huidige model gebruikt.
   * @return $this->settings;
   * @author Jan den Besten
   */
  public function _config( $table='', $load = true ) {
    // Haal de default settings op
    $this->config->load( 'tables/table_model', true);
    $default = $this->config->item( 'tables/table_model' );
    $this->settings = $default;
    if ($table) $this->settings['table'] = $table; // Voor het los instellen van een table zonder eigen model
    // Haal de settings van huidige model op
    if ( empty($table) ) $table=get_class($this);
    if ( get_class()!=$table ) {
      if ($load) {
        $this->config->load( 'tables/'.$table, true);
        $settings = $this->config->item( 'tables/'.$table );
        // Merge samen tot settings
        if ( $settings ) {
          $this->settings = array_merge( $default, $settings );
        }
      }
      // Test of de noodzakelijke settings zijn ingesteld, zo niet doe dat automatisch
      $this->_autoset( );
    }
    return $this->settings;
  }
  


  /**
   * Test of belangrijke settings zijn ingesteld. Zo niet doet dat dan automatisch.
   * Dit maakt plug'n play mogelijk, maar gebruikt meer resources.
   *
   * @return array $autoset
   * @author Jan den Besten
   */
  protected function _autoset() {
    foreach ($this->autoset as $key => $value) {
      if ( !isset($this->settings[$key]) ) {
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
      if ($this->settings[$key]===NULL) unset($this->settings[$key]);
    }
    return $this->settings;
  }
  


  /**
   * Autoset table
   *
   * @param object $object [], Standaard wordt de tablenaam gegenereerd aan de hand van het model waarin dit wordt aangeroepen. Geef hier eventueel een andere model mee.
   * @return string
   * @author Jan den Besten
   */
  protected function _autoset_table( $object=NULL ) {
    if ( $object===NULL) $object = $this;
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
        if (!is_array($field_info)) $field_info=NULL;
      }
      $fields_info[$field] = $field_info;
      $settings_fields_info[$field] = array();
      if (!empty($field_info['str_options'])) {
        $settings_fields_info[$field]['options'] = explode('|',$field_info['str_options']);
        $settings_fields_info[$field]['multiple_options'] = el('b_multi_options', $field_info, FALSE)?true:FALSE;
      }
      $settings_fields_info[$field]['validation'] = explode('|',$this->form_validation->get_validations( $table, $field ));
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
    // Anders is het gewoon standaard NULL (niet nodig)
    if ($max_rows==0) return NULL;
    return intval($max_rows);
  }
  


  /**
   * Autoset update_uris
   *
   * @return boolean
   * @author Jan den Besten
   */
  protected function _autoset_update_uris() {
    // Heeft alleen maar nu als een 'uri' veld bestaat
    if ( ! $this->field_exists('uri') ) return NULL;
    $this->load->model('cfg');
    // Haal eerst indien mogelijk uit (depricated) cfg_table_info
    $update_uris = $this->cfg->get( 'cfg_table_info', $this->settings['table'], 'b_freeze_uris');
    // Anders is het gewoon standaard true
    if (is_NULL($update_uris)) $update_uris = true;
    return boolval($update_uris);
  }



  /**
   * Autoset abstract fields
   *
   * @param string $table [''], Standaard wordt de tabelnaam gebruikt die in het huidige model is ingesteld. Geef hier eventueel een afwijkende table naam.
   * @param array $fields [array()], Standaard worden de velden gebruikt die in het huidige model zijn ingesteld. Geef hier eventueel een afwijkende velden lijst. 
   * @return array
   * @author Jan den Besten
   */
  protected function _autoset_abstract_fields( $table='', $fields = array() ) {
    if (empty($table))  $table = $this->settings['table'];
    if (empty($fields)) $fields = $this->settings['fields'];
    if ( !is_array($fields) ) $fields = explode( ',', $fields );
    
    // Haal eerst indien mogelijk uit (depricated) cfg_table_info
    $this->load->model('cfg');
    $this->cfg->load( 'cfg_table_info' );
    $abstract_fields = $this->cfg->get( 'cfg_table_info', $table, 'str_abstract_fields');
    if ($abstract_fields) {
      $abstract_fields = explode(',',$abstract_fields);
      if (is_string($abstract_fields)) $abstract_fields = explode('|',$abstract_fields);
    }

    // Als leeg zoek op type velden
		if (empty($abstract_fields)) {
      $abstract_fields=array();
  		$abstract_field_types = $this->config->item('ABSTRACT_field_pre_types');
      $max_abstract_fields  = $this->config->item('ABSTRACT_field_max');
  		while ( list($key,$field) = each( $fields ) and $max_abstract_fields>0) {
  			$pre = get_prefix($field);
  			if ( in_array( $pre, $abstract_field_types ) ) {
  				array_push( $abstract_fields, $field );
  				$max_abstract_fields--;
  			}
  		}
    }
    // Als leeg, zoek dan de eerste velden
		if (empty($abstract_fields)) {
      $abstract_fields=array();
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
    // Anders is het gewoon standaard NULL
    if ($abstract_filter=='') $abstract_filter=NULL;
    return $abstract_filter;
  }
  
  
  /**
   * Autoset relations
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _autoset_relations() {
    $relations = array();
    // many_to_one
    $tables = $this->get_relation_tables( 'many_to_one' );
    if ($tables) {
      $relations['many_to_one'] = array();
      foreach ($tables as $table) {
        $foreign_key = $this->settings['primary_key'].'_'.remove_prefix($table);
        $relations['many_to_one'][$table] = array(
          'other_table' => $table,
          'foreign_key' => $foreign_key,
        );
      }
    }
    // many_to_many
    $tables = $this->get_relation_tables( 'many_to_many' );
    if ($tables) {
      $relations['many_to_many'] = array();
      foreach ($tables as $other_table) {
        $rel_table = 'rel_'.remove_prefix($this->settings['table']).'__'.remove_prefix($other_table);
        $this_key  = $this->settings['primary_key'].'_'.remove_prefix($this->settings['table']);
        $other_key = $this->settings['primary_key'].'_'.remove_prefix($other_table);
        $relations['many_to_many'][$other_table] = array(
          'other_table' => $other_table,
          'rel_table'   => $rel_table,
          'this_key'    => $this_key,
          'other_key'   => $other_key,
        );
      }
    }
    return $relations;
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
    $setting = NULL;
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
    if ( is_NULL($fields) or empty($fields)) {
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
    if ( is_NULL($abstract_fields) or empty($abstract_fields)) {
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
    $this->tm_select = FALSE;
    $this->tm_path   = FALSE;
    $this->tm_limit  = 0;
    $this->tm_offset = 0;
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
   * Stel (eventueel automatisch) een user_id in.
   * Dat is nodig voor het bijhouden van wie wat heeft aangepast.
   *
   * @param int $user_id [FALSE]
   * @return $this
   * @author Jan den Besten
   */
  public function set_user_id( $user_id = FALSE ) {
    if ( $user_id === FALSE ) {
      $this->user_id = FALSE; // we hebben het iig gebrobeerd in te stellen
      if (defined('PHPUNIT_TEST')) {
        $this->user_id = 0; // TESTER
      }
      else {
        $this->load->library('user');
        $user_info = $this->user->get_user();
        if ($user_info) {
          $this->user_id = $user_info->id;
        }
      }
    }
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
      $this->reset();
      throw new ErrorException( __CLASS__.'->'.$method.' does not exists. So the relation tables cannot be found.' );
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
    $this->relation_tables['many_to_one'] = $tables;
    return $tables;
  }



  /**
   * Find many_to_many tables
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _get_many_to_many_tables() {
    $rel_tables = $this->db->list_tables();
    $rel_tables = filter_by( $rel_tables, 'rel_'.remove_prefix($this->settings['table']) );
    $this->relation_tables['many_to_many__rel'] = $rel_tables;
    $tables = array();
    foreach ($rel_tables as $rel_table) {
      $other_table = 'tbl_'.get_suffix($rel_table,'__');
      $tables[] = $other_table;
    }
    $this->relation_tables['many_to_many'] = $tables;
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
   * Staat standaard ingesteld op primary_key.
   * 
   * NB Als het meegegeven veld geen unieke waarden bevat dan kan het resulteren in een onverwachte result_array
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
   * Geeft een setting. Als die niet bestaat dan wordt de standaard autoset waarde gegeven of anders NULL
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
  
  
  /**
   * Geeft eventuele opties van een bepaald veld, of van alle velden met opties als geen veld is gegeven.
   * 
   * Resultaat bij één veld:
   * 
   * array(
   *  'options'           => array(...),
   *  'multiple_options'  => [true|FALSE]
   * )
   * 
   * Resultaat bij geen meegegeven veld:
   * 
   * array(
   *    '...veldnaam...' => array( ...zie hierboven... ),
   *    ....
   * )
   *
   * @param string $field ['']
   * @return array
   * @author Jan den Besten
   */
  public function get_options( $field='' ) {
    $one = FALSE;
    if (!empty($field)) {
      $one = true;
      $fields = array($field);
    }
    else {
      $fields = array_keys($this->settings['field_info']);
    }
    // Collect options
    $options=array();
    foreach ($fields as $field) {
      $field_options = el( array($field,'options'), $this->settings['field_info'] );
      if ($field_options) {
        $options[$field] = array_keep_keys( $this->settings['field_info'][$field], array('options','multiple_options') );
      }
    }
    // Return
    if ($one) {
      return current($options);
    }
    return $options;
  }
  
  

  
  /**
   * Geeft default waarden van een row. Wordt uit de database gehaald.
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_defaults() {
    $defaults = array();
		$fields = $this->settings['fields'];
		foreach ($fields as $field) {
      $defaults[$field] = $this->field_data( $field, 'default' );
		}
    $defaults[$this->settings['primary_key']] = -1;
    return $defaults;
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
    // Bewaar limit & offset als ingesteld (overruled eerder ingestelde door ->limit() )
    if ($limit!=0 or $offset!=0) $this->limit( $limit,$offset );
    
    // select, zorg ervoor dat er iig iets in de select komt
    if ( !$this->tm_select ) $this->select();

    // with, maak de relatie queries
    if ( !empty( $this->tm_with ) ) $this->_with( $this->tm_with );

    // order_by
    if ( ! empty($this->settings['order_by']) ) $this->db->order_by( $this->settings['order_by'] );
    
    // limit & offset & GET
    $this->query_info = array();
    $query = $this->db->get( $this->settings['table'], $this->tm_limit, $this->tm_offset );
    
    // Info
    if ($query) {
      $this->query_info['num_rows'] = $query->num_rows();
      $this->query_info['total_rows'] = $query->num_rows();
      $this->query_info['num_fields'] = $query->num_fields();
      $this->query_info['last_query'] = $this->last_query();
      if ($this->tm_limit>0) {
        $this->query_info['total_rows'] = $this->total_rows( true );
      }
    }

    if ( $reset ) $this->reset();
    return $query;
  }
  
  
  /**
   * Zelfde als bij Query Builder
   *
   * @param mixed $where [NULL]
   * @param int $limit [0]
   * @param int $offset [0]
   * @return object $query
   * @author Jan den Besten
   */
	public function get_where( $where = NULL, $limit = NULL, $offset = NULL) {
		if ($where !== NULL) $this->where($where);
    return $this->get( $limit,$offset);
	}
  

  /**
   * Maakt een mooie result_array van een $query
   * - Met keys die standaard primary_key zijn, of ingesteld kunnen worden met set_result_key()
   * - Met relaties gekoppeld als subarrays
   * - Als select_txt_maxlen() is ingesteld dan worden die velden ook nog gestript van HTML tags
   *
   * @param object $query 
   * @return array
   * @author Jan den Besten
   */
  private function _make_result_array( $query ) {
    if ( $query===FALSE) return array();
    
    $key = el( 'result_key', $this->settings, el( 'primary_key',$this->settings ) );
    $result = array();
    $with_data = array();
    
    if ($this->tm_path) {
      $paths=array();
      $needed_path_fields = array_merge(array_keys($this->tm_path),array($this->settings['primary_key'],'self_parent'));
    }
    
    foreach ( $query->result_array() as $row ) {
      $id = $row[$this->settings['primary_key']];
      $result_key = $row[$key];
      
      // many_to_many als subarrays toevoegen aan row
      if ($this->tm_with) {
        foreach ($this->tm_with as $with_type => $this_with) {
          // if ($with_type=='many_to_one') break; // Hier wordt besloten of many_to_one ook in array gaat of niet.
          foreach ($this_with as $other_table => $info) {
            if ( ! el('grouped',$info,FALSE) ) {
              $fields   = $info['fields'];
              // split row and with data
              $row_with_data = filter_by_key( $row, $other_table );
              $row = array_diff( $row, $row_with_data );
              // process with data
              foreach ($row_with_data as $oldkey => $values) {
                $newkey = remove_prefix( $oldkey, '__');
                $row_with_data[$newkey] = $values;
                unset($row_with_data[$oldkey]);
              }
              // remember with data
              if ($with_type=='many_to_one') {
                $with_data[$result_key][$other_table] = $row_with_data;
              }
              else {
                if ( isset($row_with_data[ $this->settings['primary_key'] ]) ) {
                  $with_data[$result_key][$other_table][$row_with_data[$this->settings['primary_key']]] = $row_with_data;
                }
                else {
                  $with_data[$result_key][$other_table][] = $row_with_data;
                }
              }
            }
          }
          // Merge with data met normale data in row
          if (isset($with_data[$result_key])) {
            $row = array_merge($row,$with_data[$result_key]);
          }
        }
      }
      
      // path
      if ($this->tm_path)  {
        // Remember current row with necessary fields
        $paths[$id] = array_keep_keys($row,$needed_path_fields);
        // Recursive create current path field
        foreach ($this->tm_path as $field => $path_info) {
          $row[$path_info['path_field']] = $this->_fill_path( $paths, $id, $path_info );
        }
      }
      
      // tm_txt_maxlen
      if ($this->tm_txt_maxlen>0) {
        $txt_row = filter_by_key($row,'txt_');
        foreach ($txt_row as $key => $value) {
          $row[$key] = strip_tags($value);
        }
      }
      
      // result_key
      $result[ $result_key ] = $row;
    }
    
    // pas query info aan
    $this->query_info['num_rows']   = count($result);
    // $this->query_info['total_rows'] = count($result);
    if (isset($this->tm_with['many_to_many'])) {
      $this->query_info['total_rows'] = $this->total_rows(true,true);
    }
    
    return $result;
  }
  
  /**
   * Vul een path veld recursief
   *
   * @param array $result 
   * @param int $key
   * @param array $path_info 
   * @return string
   * @author Jan den Besten
   */
  protected function _fill_path( &$result, $key, $path_info ) {
    $value = '';
    $parent = $result[$key]['self_parent'];
    if ( $parent>0 ) {
      $value .= $this->_fill_path( $result, $parent, $path_info) . $path_info['split'];
    }
    $value .= $result[$key][$path_info['original_field']];
    return $value;
  }


  /**
   * Geeft resultaat terug als result array
   * - array key is standaard de PRIMARY KEY maar kan ingesteld worden met $this->set_result_key()
   * - relatie data komt als sub arrays in het resultaat per relatietabel
   * 
   * Bij voorkeur niet gebruiken als resources belangrijk zijn.
   * Of alleen bij kleine resultaten en/of in combinatie met limit / pagination.
   *
   * @param int $limit [0]
   * @param int $offset [0] 
   * @return array
   * @author Jan den Besten
   */
  public function get_result( $limit=0, $offset=0 ) {
    $result = array();
    $query = $this->get( $limit, $offset, FALSE );
    if ($query) {
      $result = $this->_make_result_array( $query );
      $query->free_result();
    }
    $this->reset();
    return $result;
  }
  


  /**
   * Zelfde als get_result(), maar geeft nu alleen maar de eerstgevonden rij.
   * 
   * @param mixed $where [NULL]
   * @return array
   * @author Jan den Besten
   */
  public function get_row( $where = NULL ) {
    if ($where) $this->where( $where );
    // Als er many_to_many data is die niet grouped is dan kan het zijn dat er meer resultaten nodig zijn om één row samen te stellen
    if ( isset($this->tm_with['many_to_many']) ) {
      $result = $this->get_result();
    }
    else {
      $result = $this->get_result( 1 );
    }
    return current($result);
  }


  /**
   * Zelfde als ->get_row() maar geeft alleen de waarde van het gevraagde field terug
   *
   * @param string $field 
   * @param mixed $where [NULL]
   * @return mixed
   * @author Jan den Besten
   */
	public function get_field( $field, $where = NULL ) {
    $this->select( $field );
    $row = $this->get_row( $where );
		return $row[$field];
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
      $sql = $field;
      if (in_array($field,$this->settings['fields'])) {
        $sql = $this->settings['table'].'.'.$field;
      }

      // tm_txt_maxlen?
      if ($this->tm_txt_maxlen>0 and get_prefix($field)=='txt') {
        $sql = 'SUBSTRING(`'.$this->settings['table'].'`.`'.$field.'`,1,'.$this->tm_txt_maxlen.') AS `'.$field.'`';
      }
      
      // Bewaar
      $this->tm_select[$field] = $sql;
      // Normale select
      $this->db->select( $sql, $escape );
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
   * Veranderd all txt velden tot een string met een maximale lengte en zonder html tags.
   *
   * @param int $txt_maxlen [0]
   * @return $this
   * @author Jan den Besten
   */
  public function select_txt_maxlen( $txt_maxlen = 0 ) {
    $this->tm_txt_maxlen = $txt_maxlen;
    return $this;
  }
  
  
  /**
   * Selecteert een veld waarvan de waarde een samengevoegde string is van alle waarden in een pad van een tree table.
   * Een tree table is een tabel met rijen die in een boomstructuur aan elkaar gekoppeld zijn, bijvoorbeeld een menu.
   * Een tree table bevat altijd de velden order en self_parent
   * 
   * Voorbeeld:
   * 
   * ->path( 'uri' )
   * 
   * Een andere optie is om het origenele veld te behouden en een extra veld toe te voegen met het hele pad.
   * In het voorbeeld hieronder zal het veld 'path' worden toegevoegd en dezefde waarden hebben als het veld 'uri' in het voorbeeld hierboven.
   * 
   * ->path( 'path', 'uri' );
   * 
   * NB Kan alleen gebruikt worden in combinate met ->get_result() en varianten.
   * NB2 In combinatie met een ->where() statement kan het zijn dat de resultaten niet compleet zijn omdat rijen kunnen ontbreken die een tak in een tree zijn.
   *
   * @param string $path_field 
   * @param string $original_field ['']
   * @param string $split ['/'] Eventueel kan een andere string worden meegegeven die tussen de diverse paden in komt.
   * @return $this
   * @author Jan den Besten
   */
  public function path( $path_field, $original_field = '', $split = '/' ) {
    if ( !$this->field_exists('order') and !$this->field_exists('order') ) {
      $this->reset();
      throw new ErrorException( __CLASS__.'->'.$method.'() table is not a tree table. (tables whith the fields `order` and `self_parent`)' );
      return $this;
    }
    if (empty($original_field)) $original_field = $path_field;
    $this->tm_path[$original_field] = array(
      'path_field'      => $path_field,
      'original_field'  => $original_field,
      'split'           => $split
    );
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
   * 
   * LET OP: Bovenstaand many_to_many voorbeelden zijn snel, maar geven many_to_many data die voldoet aan het where statement.
   * Als je wilt zoeken, maar wel de complete many_to_many data voor een bepaald item gebruik dan ->where_exists()
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
   * where_exists zoekt in many_to_many data en toont data waarbinnen de zoekcriteria voldoet maar met de complete many_to_many subdata.
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
  public function where_exists( $key, $value = NULL ) {
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
  public function or_where_exists( $key, $value = NULL ) {
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
  protected function _where_exists( $key, $value = NULL, $type = 'AND' ) {
    
    if (!isset($this->tm_with['many_to_many'])) {
      $this->reset();
      throw new ErrorException( __CLASS__.'->'.$method.'(): No `many_to_many` relation set. This is needed when using `where_exists`.' );
      return $this;
    }
    $other_table = get_prefix($key,'.');
    $key         = get_suffix($key,'.');
    if (empty($other_table) or empty($key) or $key==$other_table) {
      $this->reset();
      throw new ErrorException( __CLASS__.'->'.$method.'(): First argument of `where_exists` needs to be of this format: `table.field`.' );
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
      $this->db->or_where( $sql, NULL, FALSE );
    else
      $this->db->where( $sql, NULL, FALSE );
    return $this;
  }
  
  /**
   * Zoekt de gevraagde zoekterm(en).
   * Bouwt een uitgebreide zoekquery op.
   * 
   * Voorbeelden van $by:
   * 
   * - zoek                     // Zoekt naar 'zoek'
   * - zoek ook                 // Zoekt naar 'zoek' of 'ook'
   * - "zoek ook"               // Zoekt naar 'zoek ook'      
   * - array( 'zoek ook' )      // Zoekt naar 'zoek' of 'ook'
   * - array( 'zoek', 'ook' )   // Zoekt naar 'zoek' of 'ook'
   * - array( '"zoek ook"' )    // Zoekt naar 'zoek ook'
   * 
   * @param mixed $by 
   * @param array $fields Kan ook in relatietabellen zoeken, bijvoorbeeld 'tbl_links.str_title' als een veld in een gerelateerde tabel
   * @param bool $word_boundaries 
   * @return $this
   * @author Jan den Besten
   */
  public function find( $by, $fields = array(), $word_boundaries = FALSE ) {
    if ( empty($fields) ) $fields = $this->settings['fields'];
    if (is_string($fields)) $fields = array($fields);

    // Maak tot een lange string gescheiden door spaties
    if ( is_array($by) ) $by = implode(' ',$by);
    // Splits de termen (behalve die tussen quotes)
    $by=preg_split('~(?:"[^"]*")?\K[/\s]+~', ' '.$by.' ', -1, PREG_SPLIT_NO_EMPTY );
    
    // Bouw query op voor elke term
    foreach ($by as $term) {
      $term = trim($term,'"');
      foreach ($fields as $field) {
        // Plak tabelnaam voor veld, als dat nog niet zo is, en escape
        if (strpos($field,'.')===FALSE) $field = $this->settings['table'].'.'.$field;
        $field = $this->db->protect_identifiers($field);
        // LIKE of REGEXP (word boundaries)
        if ($word_boundaries)
          $this->db->or_where( $field.' REGEXP \'[[:<:]]'.$term.'[[:>:]]\'', NULL, FALSE);
        else
          $this->db->or_like( $field, $term, 'both', FALSE);
      }
    }
    
    return $this;
  }



  /**
   * Geef aan welke relaties meegenomen moeten worden in het resultaat.
   * Deze method kan vaker aangeroepen worden met nieuwe relaties, bestaande relaties worden dan samengevoegd met de nieuwe.
   * 
   * Reset alle relaties:
   * (wordt automatisch aangeroepen na iedere ->get() variant)
   * 
   * ->with();
   * 
   * 
   * many_to_one
   * -----------
   * 
   * Voegt alle many_to_one relaties met al hun velden toe aan resultaat:
   * 
   * ->with( 'many_to_one' );
   * ->with( 'many_to_one', [] );
   * 
   * Specificeer welke relatietabellen mee moeten worden genomen in het resultaat:
   * 
   * ->with( 'many_to_one' => [ 'tbl_posts' ] );
   * ->with( 'many_to_one' => [ 'tbl_posts', 'tbl_links' ] );
   * 
   * Specificeer per tabel welke velden meegenomen moeten worden in het resultaat:
   * 
   * ->with( 'many_to_one' => [ 'tbl_posts' => 'str_title,txt_text' ] );
   * ->with( 'many_to_one' => [ 'tbl_posts' => ['str_title','txt_text'] ] );
   * ->with( 'many_to_one' => [ 'tbl_posts' => 'str_title,txt_text', 'tbl_links' ] );
   * 
   * Geef aan dat bij een tabel een abstract van de velden moet worden meegenomen in plaats van specifieke velden:
   * 
   * ->with( 'many_to_one' => [ 'tbl_posts' => 'abstract ] );
   * 
   * 
   * many_to_many
   * ------------
   * 
   * Hetzelfde als bij 'many_to_one', vervang alleen het type in 'many_to_many'.
   * Als dit gecombineerd wordt met ->limit() kan het onverwachte resultaten geven.
   * Dit kun je voorkomen door ->with_grouped() te gebruiken, zie hieronder.
   * 
   * grouped
   * -------
   * 
   * Een standaard 'many_to_many' $query resultaat (met ->get() en varianten) kan erg veel rows bevatten: voor elke rij in de relatietabel komt er een extra rij in het query resultaat bij.
   * In een result array (met ->get_result() en varianten) wordt dat standaard opgelost door de 'many_to_many' data samen te voegen in een subarray met de naam van de relatietabel.
   * Nadeel is dat $query->num_rows geen goed resultaat geeft in combinatie met ->limit()
   * 
   * Dit kan op $query niveau opgelost worden door in plaats van ->with() ->with_grouped() te gebruiken.
   * Dan worden de many_to_many data samengevoegd tot één veld onder de naam van de relatietabel.
   * 
   * Voordelen:
   * - $query->num_rows geeft juiste informatie waardoor pagination etc prima werkt.
   * - Het resultaat is klaar om te tonen in een grid doordat de many_to_many data geen eigen rijen krijgt met dubbele informatie
   * Nadelen:
   * - Het groeperen van de data gebeurt door de data tussen [] te zetten en te scheiden met een komma. De velden worden onderling gescheiden door een |
   * - Deze karakters (met name de | ) kunnen dan niet meer voorkomen in de data zelf.
   * 
   * @param string $type De soort relatie ['many_to_one'|'many_to_many']
   * @param array $tables Een array van tabellen die meegenomen moeten worden bij deze relatie.
   *                      - Als deze paramater niet wordt meegegeven worden automatisch alle relatie tabellen gezocht en meegenomen met al hun velden
   *                      - Je kunt een array van tabellen specificeren.
   *                      - En eventueel per tabel de velden of 'abstract'
   * @param bool $grouped [FALSE] bepaalt of een 'many_to_many' resultaat gegroupeerd moet worden op rij niveau. (zie ->with_grouped())
   * @return $this
   * @author Jan den Besten
   */
  public function with( $type='', $tables=array(), $grouped=FALSE ) {
    
    // Reset?
    if ( empty($type) ) {
      $this->tm_with = array();
      return $this;
    }
    
    // Als geen tables zijn meegegeven, haal ze uit de settings
    if ( empty($tables) ) {
      $tables = el( array('relations',$type), $this->settings, array() );
      if ($tables) $tables = array_keys($tables);
    }
    
    // Zorg ervoor dat $tables in dit formaat komt: 'table' => fields
    $tables_new = array();
    foreach ($tables as $key => $value) {
      // table als key en fields als value
      $table  = $key;
      $fields = $value;
      if ( is_integer($key) ) {
        $table  = $value;
        $fields = array();
      }
      // fields moet een (lege) array of een string ('abstract') zijn.
      if (is_string($fields) and $fields!==$this->config->item('ABSTRACT_field_name')) {
        $fields = explode( ',',$fields );
      }
      // Bewaar
      $tables_new[$table] = $fields;
    }
    // Merge met bestaande
    $tm_with_before = el( $type, $this->tm_with, array() );
    $tm_with_new    = array();
    foreach ($tables_new as $table => $fields) {
      $tm_with_new[$table] = array(
        'fields'  => $fields,
        'grouped' => $grouped
      );
    }
    $tm_with_new = array_replace_recursive( $tm_with_before, $tm_with_new );
    // Bewaar deze relatie instelling
    $this->tm_with[$type] = $tm_with_new;
    return $this;
  }
  
  /**
   * Geef aan welke relaties meegenomen moeten worden in het resultaat.
   * De data uit de relatietabellen worden gegroupeerd in een veld met de naam van de relatietabel.
   * Zie ook bij ->with()
   *
   * @param string $type ['many_to_many'] 
   * @param array $tables [array()]
   * @return $this
   * @author Jan den Besten
   */
  public function with_grouped( $type='many_to_many', $tables=array() ) {
    return $this->with( $type, $tables, TRUE);
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
        $this->reset();
        throw new ErrorException( __CLASS__.'->'.$method.'() does not exists. The `'.$type.'` relation could not be included in the result.' );
      }
    }
    return $this;
  }
  


  /**
   * Selecteerd de velden die bij SELECT moeten komen bij relaties
   *
   * @param string $other_table de gerelateerde tabel
   * @param array $fields velden van de gerelateerde tabel
   * @param bool $grouped of de many_to_many data gegroupeerd worden in één veld met de naam van de relatie tabel
   * @return $this
   * @author Jan den Besten
   */
  protected function _select_with_fields( $other_table, $fields, $grouped = FALSE ) {
    $abstract = FALSE;

    // Welke velden van de gerelateerde tabel?
    if ( empty($fields) ) {
      $fields = $this->get_other_table_fields( $other_table );
    }
    elseif ( $fields === 'abstract' ) {
      $abstract = $this->get_other_table_compiled_abstract_select( $other_table );
    }
    
    // Select de velden van de gerelateerde tabel voor een abstract
    if ($abstract) {
      $select = $abstract;
      if ($grouped) {
        $abstract = remove_suffix($abstract,' AS ');
        $select = 'GROUP_CONCAT( "[",'.$abstract.',"]" SEPARATOR ",") `'.$other_table.'`';
      }
    }
    // Select de velden van de gerelateerde tabel voor een lijst met velden
    else {
      // primary_key hoeft er niet in
      if ( $key=array_search( $this->settings['primary_key'], $fields ) ) {
        unset($fields[$key]);
      }
      // maak velden sql
      $select = '';
      foreach ($fields as $field) {
        $select .= '`'.$other_table.'`.`'.$field.'`';
        if ($grouped)
          $select.= ',"|",';
        else
          $select.= ' AS `'.$other_table.'__'.$field.'`, ';
      }
      $select = trim($select,',');
      if ($grouped) {
        $select = substr($select,0,strlen($select)-4); // remove last ,"|"
        $select = 'GROUP_CONCAT( "[",' . $select . ',"]" SEPARATOR ",") `'.$other_table.'`';
      }
    }
    
    // Stop select in query
    $this->db->select( $select );
    if ($grouped) $this->db->group_by( $this->settings['table'].'.'.$this->settings['primary_key'] );
    
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
    foreach ($tables as $other_table => $info) {
      $fields = $info['fields'];
      // Select fields
      $this->_select_with_fields( $other_table, $fields );
      // Join
      $foreign_key = $this->settings['relations']['many_to_one'][$other_table]['foreign_key'];
      $this->join( $other_table, $other_table.'.'.$id.' = '.$this->settings['table'].".".$foreign_key, 'left');
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
    foreach ( $tables as $other_table => $info ) {
      $fields   = $info['fields'];
      $grouped  = $info['grouped'];
      $rel_table         = $this->settings['relations']['many_to_many'][$other_table]['rel_table'];
      $this_foreign_key  = $this->settings['relations']['many_to_many'][$other_table]['this_key'];
      $other_foreign_key = $this->settings['relations']['many_to_many'][$other_table]['other_key'];
      // Select fields
      $this->_select_with_fields( $other_table, $fields, $grouped );
      // Joins
      $this->join( $rel_table,    $this_table.'.'.$id.' = '.$rel_table.".".$this_foreign_key,     'left');
      $this->join( $other_table,  $rel_table. '.'.$other_foreign_key.' = '.$other_table.".".$id,  'left');
    }
    return $this;
  }
  
  
  /**
   * Zelfde als Query Builder
   * 
   * many_to_one
   * -----------
   * 
   * ->order_by( 'tbl_posts__str_title' );
   * 
   * many_to_many
   * ------------
   * 
   * ->order_by( 'tbl_posts.str_title' );
   *
   * @param string $orderby 
   * @param string $direction [''] 
   * @param string $escape [NULL]
   * @return $this
   * @author Jan den Besten
   */
  public function order_by( $orderby, $direction = '', $escape = NULL ) {
    $this->db->order_by($orderby,$direction,$escape);
    return $this;
  }
  
  
  /**
   * Zelfde als bij Query Builder, met als extra dat de limit instelling wordt bewaard voor intern gebruik.
   *
   * @param int $value 
   * @param int $offset [0]
   * @return $this
   * @author Jan den Besten
   */
	public function limit($value, $offset = 0) {
    $this->tm_limit = $value;
    $this->tm_offset = $offset;
		return $this;
	}
  
  
  
  /* --- CRUD methods --- */
  
  
  /**
   * Insert & Update data moet eerst worden gevalideerd (als true).
   *
   * @param bool $validation [true]
   * @return $this
   * @author Jan den Besten
   */
  public function validate( $validation = true ) {
    $this->validation = $validation;
    if ( $this->validation ) $this->load->library('form_validation');
    return $this;
  }
  
  
  
  /**
   * Zelfde als Query Builder, behalve:
   * - relatie kan als subarray mee met de set
   * - $key kan geen object zijn
   *
   * @param mixed $key 
   * @param mixed $value 
   * @param mixed $escape 
   * @return $this
   * @author Jan den Besten
   */
	public function set($key, $value = '', $escape = NULL) {
		if ( ! is_array($key)) $key = array($key => $value);
    $this->tm_set = $key;
		return $this;
	}

  
  
	/**
	 * Zelfde als in Query Builder, maar ook met verwijzingen naar bestaande many_to_many data
	 *
	 * @param array $set [NULL]
	 * @param mixed $escape [NULL]
	 * @return $this
	 * @author Jan den Besten
	 */
  public function insert( $set = NULL, $escape = NULL ) {
    return $this->_update_insert( 'INSERT', $set );
	}
  

  /**
   * Zelfde als in Query Builder, maar ook met verwijzingen naar bestaande many_to_many data
   *
   * @param array $set [NULL]
   * @param string $where [NULL]
   * @param int $limit [NULL]
   * @return $this
   * @author Jan den Besten
   */
	public function update( $set = NULL, $where = NULL, $limit = NULL) {
    return $this->_update_insert( 'UPDATE', $set, $where, $limit);
	}

  /**
   * Voert insert/update uit
   *
   * @param string $type [INSERT|UPDATE]
   * @param array $set = NULL
   * @param mixed $where = NULL
   * @param int $limit = NULL
   * @return int $id
   * @author Jan den Besten
   */
	protected function _update_insert( $type, $set = NULL, $where = NULL, $limit = NULL ) {

    // Is een type meegegeven?
    $types = array('INSERT','UPDATE');
    if ( ! in_array($type,$types) ) {
      throw new ErrorException( __CLASS__.'->'.$method.'(): no type set, should be one of `'.implode(',',$types).'`' );
    }
    
    // Is er een data set?
    if ($set) $this->set( $set );
    if (empty( $this->tm_set )) {
      throw new ErrorException( __CLASS__.'->'.$method.'(): no data set for `'.$type.'`. Use ->set()' );
    }
    
    /**
     * Ok we kunnen! Stel nog even alles in...
     */
    if ($where) $this->where( $where );
    if ($limit) $this->limit( $limit );
		$set = $this->tm_set;
    $id = NULL;
    
		/**
		 * Stel nieuwe volgorde van een item in, indien nodig
		 */
    if ( $type=='INSERT' and isset( $set["order"]) ) {
      $this->load->model('order','_order');
      if ( isset( $set["self_parent"]) ) { 
        $set["order"] = $this->_order->get_next_order( $this->settings['table'], $set["self_parent"]);
      }
      else {
        $set["order"] = $this->_order->get_next_order( $this->settings['table'] );
      }
    }
      
    /**
     * Valideer eventueel eerst de set
     */
    if ( $this->validation ) {
      if ( ! $this->form_validation->validate_data( $set, $this->settings['table'] ) ) {
        $this->query_info = array(
          'validation'        => FALSE,
          'validation_errors' => $this->form_validation->get_error_messages()
        );
        return FALSE; // CHECK Heeft dit zin? Hij moet iig hier afbreken.
      }
    }
      

    /**
     * Split eventuele many_to_many data
     */
    $many_to_many = filter_by_key( $set, 'tbl_'); // CHECK, is dit de manier? of nu juist met 'tbl_'
    if ( !empty($many_to_many) ) {
      $many_to_many_keys = array_keys($many_to_many);
      $set = array_unset_keys( $set, $many_to_many_keys );
    }
      
    /**
     * Bij INSERT is primary_key niet nodig, haal die weg mocht die er zijn.
     */
		if ( $type=='INSERT' ) unset( $set[ $this->settings['primary_key']] );

    /**
     * Verwijder lege wachtwoorden, zodat die niet overschreven worden in de db
     * TODO/CHECK: Verhuis dit naar cfg_users
     */
    foreach ( $set as $key => $value ) {
      if ( empty($value) and in_array(get_prefix($key), $this->config->item('PASSWORD_field_types') ) ) {
        unset( $set[$key] );
      }
    }

        
    /**
     * Verwijder data die NULL is of waarvan het veld niet in de table bestaat.
     */
    foreach ( $set as $key => $value ) {
      if ( !isset($value) or !$this->field_exists( $key) ) unset( $set[$key] );
    }
    
    /**
     * Ga door als de set niet leeg is
     */
    if (!empty($set)) {
      
      /**
       * Voeg user_changed data toe als bekend is en veld bestaat
       */
      if ( $this->field_exists('user_changed')) {
        if ( !isset( $this->user_id )) {
          $this->set_user_id();
        }
        if ( $this->user_id!==FALSE ) $set['user_changed'] = $this->user_id;
      }
          
      /**
       * Eindelijk, we kunnen de set instellen...
       */
      $this->db->set($set);
      
      /**
       * En de INSERT of UPDATE doen
       */
      if ($type=='INSERT') {
				$this->db->insert( $this->settings['table'] );
				$id = $this->db->insert_id();
        $this->query_info = array(
          'insert_id' => $id
        );
			}
    	else {
        $sql = $this->db->get_compiled_update( $this->settings['table'], FALSE );
				$this->db->update( $this->settings['table'], NULL,NULL, $this->tm_limit );
        $ids = $this->_get_ids( $sql );
        $id = current( $ids );
        $this->query_info = array(
          'affected_rows' => $this->db->affected_rows(),
          'affected_ids'  => $ids,
        );
			}
      
			/**
			 * Als er many_to_many data is, update/insert die ook
			 */
			if ( ! empty($many_to_many) ) {
        $affected = 0;
				foreach( $many_to_many as $other_table => $other_ids ) {
          $rel_table         = $this->settings['relations']['many_to_many'][$other_table]['rel_table'];
					$this_foreign_key  = $this->settings['relations']['many_to_many'][$other_table]['this_key'];
          $other_foreign_key = $this->settings['relations']['many_to_many'][$other_table]['other_key'];
            // if ( $this_foreign_key==$other_foreign_key ) $other_foreign_key.="_"; // TODO : self relaties?

					// DELETE eerst huidige items
					$this->db->where( $this_foreign_key, $id );
					$this->db->delete( $rel_table );

					// INSERT dan nieuwe many_to_many ids
					foreach ( $other_ids as $other_id ) {
						$this->db->set( $this_foreign_key,  $id );
						$this->db->set( $other_foreign_key, $other_id );
						$this->db->insert( $rel_table );
            $affected++;
            // $rel_id=$this->db->insert_id();
					}
				}
        $this->query_info['affected_rel_rows'] = $affected;
			}
		}
    
    $this->reset();
		return intval($id);
	}
  
  
  /**
   * Net als Query Builder, en met verwijderen van bijbehorende many_to_many verwijzingen
   *
   * @param mixed $where ['']
   * @param int $limit [NULL]
   * @param bool $reset_data 
   * @return void
   * @author Jan den Besten
   */
	public function delete( $where = '', $limit = NULL, $reset_data = TRUE) {
    if ($where) $this->where( $where );
    if ($limit) $this->limit( $limit );

    $is_ordered_table = $this->field_exists( 'order' );
    if ($is_ordered_table) $this->load->model('order','_order');
      
		/**
		 * Wat zijn de id's van de te verwijderen items?
		 * Zijn nodig om eventuele many_to_many data te verwijderen
		 */
    $ids = $this->_get_ids( $this->db->get_compiled_delete( $this->settings['table'], FALSE) );
    
    /**
     * Start DELETE
     */
    $this->db->trans_start();
		
    $is_deleted = $this->db->delete( $this->settings['table'], '', $this->tm_limit, $reset_data );
    
    $this->query_info = array(
      'affected_rows' => $this->db->affected_rows(),
      'affected_ids'  => $ids,
    );

		if ($is_deleted) {

  		/**
  		 * Reset volgorde
  		 */
  		if ( $is_ordered_table ) {
  		  $this->query_info['moved_rows'] = $this->_order->reset( $this->settings['table'] );
  		}

			/**
			 * Als er many_to_many is, verwijder die ook
			 */
			$other_tables = $this->settings['relations']['many_to_many'];
      if ( ! empty($other_tables) ) {
        $other_tables = array_keys($other_tables);
        // $this_foreign_key = $this->settings['relations']['many_to_many'][$other_table]['this_key'];
        // $rel_tables = $this->relation_tables['many_to_many__rel'];
        $affected = 0;
        foreach ( $other_tables as $other_table ) {
          $rel_table        = $this->settings['relations']['many_to_many'][$other_table]['rel_table'];
          $this_foreign_key = $this->settings['relations']['many_to_many'][$other_table]['this_key'];
          $this->db->where_in( $this_foreign_key, $ids );
          $this->db->delete( $rel_table );
          $affected = $affected + $this->db->affected_rows();
        }
        $this->query_info['affected_rel_rows'] = $affected;
      }
      
    }
    
    $this->db->trans_complete();
    $this->reset();
		return $is_deleted;
	}
  
  
  /**
   * Geeft ids terug van een update of delete sql query die vereenvoudigd een resultaat teruggeeft
   *
   * @param string $sql 
   * @return array
   * @author Jan den Besten
   */
  protected function _get_ids( $sql ) {
    $ids = array();
    $sql = preg_replace("/DELETE\sFROM/u", "SELECT `".$this->settings['table'].'`.`'.$this->settings['primary_key']."` FROM", $sql);
    $sql = preg_replace("/UPDATE(.*)SET(.*)WHERE/uUs", "SELECT `".$this->settings['table'].'`.`'.$this->settings['primary_key']."` FROM $1 WHERE", $sql);
    if ($this->tm_limit>0 and strpos($sql,'LIMIT')===FALSE) {
      $sql.=' LIMIT '.$this->tm_limit;
    }
    $query = $this->db->query( $sql );
    if ( is_object($query) ) {
      $result = $query->result_array();
      foreach ($result as $row) {
        $ids[] = $row[$this->settings['primary_key']];
      }
    }
    return $ids;
  }
  
  
  

  /* --- Informatieve methods --- */
  
  /**
   * Geeft informatie van laatste query
   *
   * @param string $what ['']
   * @return mixed
   * @author Jan den Besten
   */
  public function get_query_info( $what='' ) {
    if (!empty($what)) return el($what,$this->query_info);
    return $this->query_info;
  }
  
  
  /**
   * Geeft insert_id
   *
   * @return int
   * @author Jan den Besten
   */
  public function insert_id() {
    return $this->get_query_info('insert_id');
  }
  
  
  /**
   * Geeft affected_rows
   *
   * @return int
   * @author Jan den Besten
   */
  public function affected_rows() {
    return $this->get_query_info('affected_rows');
  }
  
  
  /**
   * Geeft aantal rijen in laatste resultaat
   *
   * @return int
   * @author Jan den Besten
   */
  public function num_rows() {
    return $this->get_query_info('num_rows');
  }
  
  /**
   * Geeft aantal rijen in laatste resultaat zonder limit
   *
   * @param bool $calculate [FALSE] als TRUE dan moet het uitgerekend worden, anders zit het in query_info
   * @return int
   * @author Jan den Besten
   */
  public function total_rows( $calculate=FALSE, $grouped=FALSE ) {
    if ($calculate) {
      // perform simple query count
      $query = $this->db->query( $this->last_clean_query( $grouped ) );
      $total_rows = $query->num_rows();
      return $total_rows;
    }
    return $this->get_query_info('total_rows');
  }

  /**
   * Geeft aantal velden in laatste resultaat
   *
   * @return int
   * @author Jan den Besten
   */
  public function num_fields() {
    return $this->get_query_info('num_fields');
  }


  /**
   * Geeft laatst gebruikte query
   *
   * @return return
   * @author Jan den Besten
   */
  public function last_query() {
    if (!isset($this->query_info['last_query'])) {
      $this->query_info['last_query'] = $this->db->last_query();
    }
    return $this->query_info['last_query'];
  }

  
  /**
   * Geeft opgeschoonde last_query()
   * - Eenvoudiger SELECT met alleen primary_key
   * - Verwijder LIMIT
   * - Verwijder ORDER BY
   *
   * @return string
   * @author Jan den Besten
   */
  protected function last_clean_query( $grouped=FALSE, $query='' ) {
    if (empty($query)) $query = $this->last_query();
    // $query = preg_replace("/(WHERE.*)GROUP/uUs", " GROUP", $query);
    // $query = preg_replace("/(WHERE.*)ORDER/uUs", " ORDER", $query);
    // $query = preg_replace("/(WHERE.*)LIMIT/uUs", " LIMIT", $query);
    $query = preg_replace("/SELECT.*FROM/uUs", 'SELECT `'.$this->settings['table'].'`.`'.$this->settings['primary_key'].'` FROM', $query, 1);
    $query = preg_replace("/LIMIT\s+\d*/us", " ", $query);
    $query = preg_replace("/ORDER.*/us", "", $query);
    if ($grouped and strpos($query,'GROUP BY')===FALSE) {
      $query.=' GROUP BY `'.$this->settings['table'].'`.`'.$this->settings['primary_key'].'`';
    }
    $this->query_info['last_clean_query'] = $query;
    return $this->query_info['last_clean_query'];
  }



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
  
  
  /**
   * Een uitgebreidere versie van field_data() bij ->db.
   * En het resultaat is een array waarvan de keys de veldnamen zijn zodat het eenvoudiger kan worden opgezocht.
   *
   * @param string $asked_field ['']
   * @param string $asked_key ['']
   * @return array
   * @author Jan den Besten
   */
	public function field_data( $asked_field='', $asked_key='' ) {
    if (!isset($this->field_data)) {
      $this->field_data = array();
			$query = $this->db->query( 'SHOW COLUMNS FROM `'.$this->settings['table'].'`' );
			foreach ($query->result() as $field) {
				preg_match('/([^(]+)(\((\d+)\))?/', $field->Type, $matches);
				$type           = sizeof($matches) > 1 ? $matches[1] : NULL;
				$max_length     = sizeof($matches) > 3 ? $matches[3] : NULL;
        $info=array(
  				'name'        => $field->Field,
  				'type'        => $type,
  				'default'     => $field->Default,
  				'max_length'  => $max_length,
  				'primary_key' => ($field->Key == "PRI") ? 1 : 0,
  				'extra'       => $field->Extra,
        );
        if ( strpos($info['type'],'int')!==FALSE ) {
          $info['default'] = (int) $info['default'];
        }
        $this->field_data[$info['name']] = $info;
			}
			$query->free_result();
		}
    // return
    if ($asked_field) {
      if ($asked_key) return $this->field_data[$asked_field][$asked_key];
      return $this->field_data[$asked_field];
    }
    return $this->field_data;
	}
  

}
