<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * - Alle query-builder methods (en db methods) kunnen worden gebruikt met het model. Als je CodeIgniter kent, ken je dit model al bijna.
 * - Alle instellingen van een tabel en zijn velden tabel zijn te vinden in config/data/...
 * - Standaard get/crud zit in het model, voor elke tabel hetzelfde.
 * - Iedere tabel kan deze overerven en aanpassen naar wens, de aanroepen blijven hetzelfde voor iedere tabel.
 * - Naast ->get() die een query object teruggeeft ook ->get_result() die een aangepaste result array teruggeeft met relatie data als subarray en mogelijkheid tot caching heeft.
 * 
 * 
 * Enkele belangrijke methods (deels overgeerft van Query Builder):
 * 
 * ->table( $table )                              // Stelt tabel waarvoor het model wordt gebruikt (laad corresponderende settings als die bestaan, of analyseert de tabel en genereerd settings)
 * 
 * ->get( $limit=0, $offset=0 )                   // Geeft een $query object (zoals in Query Builder)
 * ->get_where( $where=NULL, $limit=0, $offset=0) // Geeft een $query object (zoals in Query Builder)
 * 
 * ->get_result( $limit=0, $offset=0 )            // Geeft een aangepaste $query->result_array: - key ingesteld als result_key (standaard zelfde als primary_key) - inclusief relatie data als subarray per item
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
 * ->tree( $tree_field, $original_field = '' )    // Geeft een veld aan dat een geheel pad aan waarden moet bevatten in een tree table (bijvoorbeeld een menu)
 * 
 * ->with( $type='', $what=array() )              // Voeg relaties toe (many_to_one, many_to_many) en specificeer eventueel welke tabellen en hun velden. Zie bij ->with()
 * ->with_json( $type='', $what=array() )         // Idem, maar dan komt de data in één JSON veld
 * ->with_flat_many_to_one( $what=array() )       // Idem, maar dan met platte foreign data
 * 
 * ->where($key, $value = NULL)                   // Zoals in Query Builder. Kan ook zoeken in many_to_many data (waar de many_to_many data ook gefilterd is)
 * ->where_exists( $key, $value = NULL )          // Idem met als resultaat dezelfde items maar met complete many_to_many data van dat item (ongefilterd)
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */


Class Data_Core extends CI_Model {


  /**
   * Set off while developing
   */
  private $settings_caching = TRUE;

  /**
   * Testing this
   */
  private $caching = FALSE;

  /**
   * De instellingen voor deze tabel en velden.
   * Deze worden opgehaald uit het config bestand met dezelfde naam als die model. (config/data/data_model.php in dit geval)
   */
  protected $settings = array();


  /**
   * Noodzakelijk instellingen die automatisch worden ingesteld als ze niet bekend zijn.
   */
  protected $autoset = array(
    'table'              => '',
    'fields'             => array(),
    'abstract_fields'    => array(),
    'abstract_filter'    => '',
    'abstract_delimiter' => ' | ',
    'relations'          => array(),
    'field_info'         => array(),
    'options'            => array(),
    'order_by'           => '',
    'max_rows'           => 0,
    'update_uris'        => true,
    'grid_set'           => array(),
    'form_set'           => array(),
  );
  
  /**
   * Onthoud eventueel opgevraagde field_data
   */
  protected $field_data = NULL;
  
  /**
   * Onthoud eventueel al opgezochte relatie tabellen
   */
  protected $relation_tables = array();
  
  /**
   * Onthoud eventueel array van result_name met bijbehorende velden van de andere tabel
   */
  protected $relation_result_fields = FALSE;
  
  /**
   * Set to TRUE if a query has been prepared
   */
  protected $tm_query_prepared = FALSE;
  
  /**
   * Of huidig resultaat moet worden gecached of niet
   */
  protected $tm_cache_result = FALSE;

  /**
   * De naam van de cache van huidige query
   */
  protected $tm_cache_name = '';

  /**
   * Hou SELECT bij om ervoor te zorgen dat SELECT in orde is
   */
  protected $tm_select  = FALSE;
  protected $tm_select_include_primary  = TRUE;
  
  
  /**
   * Eventuele velden die niet in SELECT mogen voorkomen
   */
  protected $tm_unselect = FALSE;

  
  /**
   * Hou de FROM bij, kan aangepast worden om LIMIT bij one_to_many en many_to_many relaties mooi te krijgen
   */
  protected $tm_from = '';
  
  /**
   * Hou bij of er een WHERE of LIKE statement is
   */
  protected $tm_has_condition = FALSE;
  
  /**
   * Hier komen grid_set instellingen als het om een grid resultaat gaat
   */
  protected $tm_as_grid = FALSE;
  
  /**
   * Een eventueel veld dat een compleet pad moet bevatten in een tree table
   */
  protected $tm_tree       = FALSE;
  protected $tm_where_tree = array();
  
  /**
   * Maximale lengte van txt velden.
   * Als groter dan 0 dan worden txt_ velden gemaximaliseerd op aantal karakters en gestript van html tags
   */
  protected $tm_txt_abstract = 0;
  
  /**
   * Maak wachtwoord velden onzichtbaar: lege strings.
   * Kan handig zijn als je een formulier met wachtwoord wilt laten zien om eventueel aan te kunnen passen.
   * Kan TRUE zijn, of array van wachtwoord velden.
   */
  protected $tm_hidden_passwords = FALSE;
  
  /**
   * Of de result_array in het geval van ->select_abstract() plat moet worden. Zie bij ->select_abstract()
   */
  protected $tm_flat_abstracts = FALSE;

  /**
   * Hou ORDER BY bij als array van strings per veld en DESC eventueel achter het veld, met name 'jump_to_today' maakt daar gebruik van
   */
  protected $tm_order_by = array();
  
  /**
   * Hou LIMIT en OFFSET bij om eventueel total_rows te kunnen berekenen
   * En of er naar de pagina moet worden gegaan van het item het dichtsbij vandaag
   */
  protected $tm_limit         = 0;
  protected $tm_offset        = 0;
  protected $tm_jump_to_today = FALSE;
  protected $tm_where_limit   = FALSE;
  protected $tm_where_offset  = FALSE;

  /**
   * Welke relaties mee moeten worden genomen en op welke manier
   */
  protected $tm_with    = array();
  
  /**
   * Wat er gezocht gaat woren, de argumenten die meegegeven worden aan ->find()
   * - terms (string, array van strings, of assoc array met multiple finds)
   * - fields (array)
   * - settings (array)
   */
  protected $tm_find                      = FALSE;
  private $forbidden_find_fields          = array('id','order','self_parent','uri');
  private $forbidden_find_relation_fields = array('user_changed','tme_last_changed');

  /**
   * Set array voor insert/update
   */
  protected $tm_set     = NULL;

  
  /**
   * Moet de data voor een insert/update eerste gevalideerd worden?
   */
  protected $validation = FALSE;
  
  /**
   * Is nodig om eventueel te kunnen instellen in de database wie iets heeft aangepast.
   * En om eventueel alleen rijen terug te geven waarvoor de gebruiker rechten heeft.
   */
  protected $user_id    = NULL;
  
  /**
   * Bewaar de id van opgevraagde row als ->where( id ) wordt gebruikt.
   */
  protected $tm_where_primary_key = NULL;

  
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
  protected $query_info = array();

  
  /**
   * Deze wordt gebruikt om bij __call() te checken of een db-> aanroep conditioneel is.
   */
  private $conditional_methods = array( 'where','or_where','get_where','where_in','or_where_in','where_not_in','or_where_not_in','like','not_like','or_like','or_not_like' );



  /* --- CONSTRUCT & AUTOSET --- */

	public function __construct( $table='' ) {
		parent::__construct();
    $this->settings_caching = $this->config->item('CACHE_DATA_SETTINGS');
    $this->load->model('log_activity');
    $this->lang->load('data');
    $this->load->driver('cache', array('adapter' => 'file'));
    $this->_config( $table );
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
    if (empty($table)) {
      // Haal de default settings op
      $this->config->load( 'data/data', true);
      $default = $this->config->item( 'data/data' );
      $this->settings = $default;
      return $this->settings;
    };
    // Settings in cache?
    // $cached = FALSE;
    $cached = $this->cache->get( 'data_settings_'.$table );
    if ( $cached!==FALSE ) {
      $this->settings = $cached;
    }
    else {
      // Default settings
      $this->config->load( 'data/data', true);
      $default = $this->config->item( 'data/data' );
      $default = array_merge($default,$this->settings); // Default aanpassen met eventueel al eerder ingesteld settings
      $this->settings = $default;
      // Stel eventueel de tabel in als die is meegegeven
      if ($table) $this->settings['table'] = $table;
      // Of anders geef het de naam van het huidige model als die geen eigen settings heeft
      if ( empty($table) ) $table=get_class($this);
      // Haal de settings van huidige model op als die bestaan
      if ( get_class()!==$table ) {
        $table=strtolower($table);
        if ($load) {
          $this->config->load( 'data/'.$table, true);
          $settings = $this->config->item( 'data/'.$table );
          // Merge met default samen tot settings
          if ( $settings ) {
            $this->settings = array_merge( $default, $settings );
          }
        }
        // Test of de noodzakelijke settings zijn ingesteld, zo niet doe de rest automatisch
        $this->_autoset( );
      }
      if ($this->settings_caching) $this->cache->save('data_settings_'.$table, $this->settings, TIME_YEAR );
    }
    return $this->settings;
  }
  


  /**
   * Test of belangrijke settings zijn ingesteld. Zo niet doe dat dan automatisch.
   * Dit maakt plug'n play mogelijk, maar gebruikt meer resources.
   *
   * @return array $autoset
   * @author Jan den Besten
   */
  protected function _autoset() {
    foreach ($this->autoset as $key => $value) {
      if ( !isset($this->settings[$key])) {
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
      if ( $this->settings[$key]===NULL ) unset($this->settings[$key]);
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
    $this->config->load('field_info',true);
    $field_info_config = $this->config->item('field_info');
    $this->load->library('form_validation');
    if (empty($table)) $table = $this->settings['table'];
    if (empty($fields)) $fields = $this->settings['fields'];

    $field_info = array();
    foreach ($fields as $field) {
      $info = array();
      
      /**
       * Default, eerst uit field_info_config, dan uit database
       */
      $info['default'] = el(array('FIELDS_special',$field,'default'),$field_info_config);
      if ( !isset($info['default']) ) {
        $pre = get_prefix($field);
        $info['default'] = el(array('FIELDS_prefix',$pre,'default'),$field_info_config);
      }
      
      // Uit database
      if ( !isset($info['default'])) {
        $info['default'] = $this->field_data( $field, 'default' );
      }
      
      /**
       * Validation
       */
      $info['validation'] = explode('|',$this->form_validation->get_rules( $table, $field ));
      
      /**
       * Media path
       */
      if (in_array(get_prefix($field),array('media','medias'))) {
        $info['path'] = 'pictures';
      }
      
      $field_info[$field] = $info;
    }
    return $field_info;
  }
  
  /**
   * Autoset opties
   */
  protected function _autoset_options() {
    $settings_options = array();
    $table = $this->settings['table'];
    $fields = $this->settings['fields'];
    foreach ($fields as $field) {
      $options = array();

      $field_info = array();
      
      // Via many_to_one
      if ( get_prefix($field)==='id' and $field!==$this->settings['primary_key']) {
        $other_table = el( array('relations','many_to_one',$field,'other_table'), $this->settings);
        if ($other_table and $this->db->table_exists($other_table)) $options['table'] = $other_table;
      }
      
      // Speciale velden
      $type=get_prefix($field);
      switch ($type) {
        
        case 'media':
        case 'medias':
          $options['model'] = 'media';
          $options['path'] = $this->get_setting(array('field_info',$field,'path'));
          if ($type=='medias') $options['multiple']=true;
          break;
        
        case 'field':
        case 'fields':
          $options['model'] = 'fields';
          if ($type=='fields') $options['multiple']=true;
          break;
      }
      
      switch ($field) {
        
        // Self parent -> tree options
        case 'self_parent':
          $options['special'] = 'self_parent';
          break;
        
        case 'table':
          $options['model'] = 'tables';
          break;

        case 'path':
          $options['model'] = 'paths';
          break;

        case 'api':
          $options['model'] = 'apis';
          break;
        
      }

      if ( $options) {
        $settings_options[$field] = $options;
      }
    }
    return $settings_options;
  }
  


  /**
   * Autoset order_by
   * 
   * @param array $fields [array()], Standaard worden de velden gebruikt die in het huidige model zijn ingesteld. Geef hier eventueel een afwijkende velden lijst. 
   * @return string
   * @author Jan den Besten
   */
  protected function _autoset_order_by( $fields=array() ) {
    if (empty($fields)) $fields = $this->settings['fields'];
    $order_by = '';
    
    // Zoek mogelijke standaard order fields
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
    return 0;
  }

  /**
   * Autoset update_uris
   *
   * @return boolean
   * @author Jan den Besten
   */
  protected function _autoset_update_uris() {
    return true;
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
      foreach ($fields as $field) {
        if ($field!==$this->settings['primary_key']) array_push( $abstract_fields, $field );
      }
      $abstract_fields=array_slice($abstract_fields,0,$this->config->item('ABSTRACT_field_max'));
		}
    return $abstract_fields;
  }
  

  /**
   * Autoset abstract_delimiter
   *
   * @return string
   * @author Jan den Besten
   */
  protected function _autoset_abstract_delimiter() {
    return ' | ';
  }



  /**
   * Autoset abstract_filter
   *
   * @return string
   * @author Jan den Besten
   */
  protected function _autoset_abstract_filter() {
    return '';
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
    $foreign_keys = filter_by( $this->settings['fields'], $this->settings['primary_key'].'_' );
    $names = array();
    if ($foreign_keys) {
      $relations['many_to_one'] = array();
      foreach ($foreign_keys as $foreign_key) {
        // other table
        $table = $this->config->item('TABLE_prefix').'_'.remove_prefix($foreign_key);
        $name  = $table;
        if (in_array($name,$names)) {
          echo( 'Double many_to_one relations, name conflict ');
        }
        array_unshift($names,$name);
        $relations['many_to_one'][$foreign_key] = array(
          'other_table' => $table,
          'foreign_key' => $foreign_key,
          'result_name' => $name,
        );
      }
    }
    
    // user is ook een many_to_one:
    $user_keys = filter_by( $this->settings['fields'], 'user' );
    if ( in_array('id_user',$this->settings['fields'])) array_unshift($user_keys,'id_user');
    if ($user_keys) {
      if (!isset($relations['many_to_one'])) $relations['many_to_one'] = array();
      foreach ($user_keys as $user_key) {
        if ($user_key=='id_user')
          $result_name='cfg_users';
        else
          $result_name = '_'.$user_key;
        $relations['many_to_one'][$user_key] = array(
          'other_table' => 'cfg_users',
          'foreign_key' => $user_key,
          'result_name' => $result_name,
        );
      }
    }
    
    // one_to_many
    $tables = $this->get_relation_tables( 'one_to_many', $this->settings['table'] );
    if ($tables) {
      $relations['one_to_many'] = array();
      $foreign_key = 'id_'.remove_prefix( $this->settings['table'] );
      foreach ($tables as $other_table) {
        $relations['one_to_many'][$other_table] = array(
          'other_table' => $other_table,
          'foreign_key' => $foreign_key,
          'result_name' => $other_table,
        );
      }
    }

    // many_to_many
    $tables = $this->get_relation_tables( 'many_to_many' );
    $names=array();
    if ($tables) {
      $relations['many_to_many'] = array();
      foreach ($tables as $other_table) {
        $rel_table = 'rel_'.remove_prefix($this->settings['table']).'__'.remove_prefix($other_table);
        $this_key  = $this->settings['primary_key'].'_'.remove_prefix($this->settings['table']);
        $other_key = $this->settings['primary_key'].'_'.remove_prefix($other_table);
        $name      = $other_table;
        if (in_array($name,$names)) {
          echo( 'Double many_to_many relations, name conflict ');
        }
        array_unshift($names,$name);
        $relations['many_to_many'][$rel_table] = array(
          'this_table'  => $this->settings['table'],
          'other_table' => $other_table,
          'rel_table'   => $rel_table,
          'this_key'    => $this_key,
          'other_key'   => $other_key,
          'result_name' => $name,
        );
      }
    }
    
    // trace_([$this->settings['table'],$relations]);
    return $relations;
  }


  /**
   * Autoset admin_grid
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _autoset_grid_set() {
    $show_always = $this->config->item('ALWAYS_SHOW_FIELDS');

    $grid_set['fields']        = $this->settings['fields'];
    $grid_set['fields']        = array_values($grid_set['fields']); // reset keys
    $grid_set['order_by']      = $this->settings['order_by'];
    $grid_set['jump_to_today'] = false;
    if ($grid_set['jump_to_today']) {
      // Kan het wel? Is er een veld waarmee het zinvol is?
      $possible_jump = FALSE;
      $date_fields = $this->config->item('DATE_fields_pre');
      $fields = array_reverse($grid_set['fields']);
      foreach ($fields as $field) {
        $pre = get_prefix($field);
        if (in_array($pre,$date_fields)) {
          $possible_jump = $field;
        }
      }
      $grid_set['jump_to_today'] = $possible_jump;
    }
    $grid_set['pagination']    = true;

    // relaties, default
    $grid_set['with']  = array('many_to_one');
    
    return $grid_set;
  }



  /**
   * Autoset admin_form
   *
   * @return void
   * @author Jan den Besten
   */
  protected function _autoset_form_set() {
    $show_always = $this->config->item('ALWAYS_SHOW_FIELDS');
    $main_fieldset = $this->settings['table'];
    $fieldsets = array($main_fieldset=>array());
    
    // relaties default
    $form_set['with']      = array('many_to_one','many_to_many');
    
    // fields / formset
    $form_set['fields'] = $this->settings['fields'];
    // voeg eventueel ..._to_many velden toe
    if (isset($form_set['with']['many_to_many'])) {
      $many_to_many = el(array('relations','many_to_many'),$this->settings);
      if ($many_to_many) {
        foreach ($many_to_many as $what => $relation) {
          $form_set['fields'][] = $relation['result_name'];
        }
      }
    }
    
    foreach ($form_set['fields'] as $key => $field) {
      $fieldset=$main_fieldset;
      // trace_([$fieldset,$main_fieldset]);
      if (!isset($fieldsets[$fieldset])) $fieldsets[$fieldset]=array();
      array_push( $fieldsets[$fieldset], $field );
    }
    $form_set['fields'] = array_values($form_set['fields']); // reset keys
    $form_set['fieldsets'] = $fieldsets;
    
    return $form_set;
  }
  
  
  /* --- Informatie uit andere tabellen/models --- */
  
  protected function get_other_table_settings( $table ) {
    $settings = NULL;
    // Probeer eerst of het table model bestaat
    // if ( method_exists( $table, 'get_settings' ) ) {
    //   $settings = $this->$table->get_settings();
    // }
    // // Laad anders de config van die tabel/model
    // else {
      $current_table = $this->settings['table'];
      $settings = $this->data->table($table)->get_settings();
      $this->data->table( $current_table );
      // $this->config->load( 'data/'.$table, true);
      // $settings = $this->config->item( 'data/'.$table );
    // }
    return $settings;
  }
  

  /**
   * Haalt een setting op van een andere table (model) 
   *
   * @param string $table 
   * @param string $key
   * @param mixed $defaul [null]
   * @return mixed NULL als niet gevonden
   * @author Jan den Besten
   */
  protected function get_other_table_setting( $table, $key, $default=null ) {
    $settings = $this->get_other_table_settings( $table );
    $setting = el( $key, $settings, $default );
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
  


  
  /* --- DB methods --- */

  

  /**
   * Alle Query Builder en andere database methods zijn beschikbaar:
   * - Als een method niet hier bestaat, wordt die doorgeschakeld naar ->db->
   * - Bij alle conditionele methods (where/like etc) die niet bestaan wordt wel onthouden dat ze zijn aangeroepen
   *
   * @return mixed
   * @author Jan den Besten
   */
  public function __call($method,$arguments) {
    if (method_exists($this->db,$method)) {
      if ( in_array($method,$this->conditional_methods) ) {
        // Onthou dat er een conditie in de query zit
        $this->tm_has_condition = TRUE;
      }
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
    $this->tm_query_prepared         = FALSE;
    $this->tm_cache_result           = $this->caching;
    $this->tm_cache_name             = '';
    $this->tm_select                 = FALSE;
    $this->tm_select_include_primary = TRUE;
    $this->tm_unselect               = FALSE;
    $this->tm_from                   = '';
    $this->tm_tree                   = FALSE;
    $this->tm_where_tree             = array();
    $this->tm_order_by               = array();
    $this->tm_limit                  = 0;
    $this->tm_offset                 = 0;
    $this->tm_where_limit            = FALSE;
    $this->tm_where_offset           = FALSE;
    $this->tm_jump_to_today          = FALSE;
    $this->tm_find                   = FALSE;
    $this->tm_has_condition          = FALSE;
    $this->tm_as_grid                = FALSE;
    $this->with(FALSE);
    $this->db->reset_query();
    return $this;
  }
  


  /**
   * Stel hier eventueel een table in die gebruikt moet worden in data_model.
   * 
   * Je kunt data_model ook los gebruiken, zonder een eigen model voor een table.
   * Stel dan hier de table in die gebruikt moet worden.
   * Als de bijbehorende config bestaat (bijvoorbeeld config/tables/tbl_menu.php) dan wordt die geladen.
   * Als de bijbehorende config NIET bestaat, dat wordt zover het kan alles automatisch ingesteld met autoset.
   *
   * @param string $table 
   * @return void
   * @author Jan den Besten
   */
  public function table( $table ) {
    // Alleen aanpassen als het een andere tabel is dan nu ingesteld
    if ($this->settings['table']===$table) return $this;
    // Een andere tabel, reset alles en verander de instellingen
    $this->reset();
    if (empty($table)) $table = $this->_autoset_table();
    if (!empty($table) and $table!=='Data_Core') {
      $this->_config( $table );
      $this->settings['table'] = $table;
    }
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
        $this->load->library('flexy_auth');
        $this->user_id = $this->flexy_auth->get_user(NULL,'id');
      }
    }
    return $this;
  }
  
  /**
   * Geeft de ingestelde user_id
   *
   * @return int user_id
   * @author Jan den Besten
   */
  public function get_user_id() {
    if (is_null($this->user_id)) {
      $this->set_user_id();
    }
    return $this->user_id;
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
	 * @param string $as_table [''] een eventuele prefix string die voor de veldnaam 'abstract' wordt geplakt
	 * @return string
	 * @author Jan den Besten
	 */
  public function get_compiled_abstract_select( $table='', $abstract_fields='', $as_table = '' ) {
    $abstract_field_name = $this->config->item('ABSTRACT_field_name');
    if ($as_table) $abstract_field_name = $as_table.'.'.$abstract_field_name;
		if (empty($table)) $table = $this->settings['table'];
    if (empty($as_table)) $as_table = $table;
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
    $delimiter = $this->get_other_table_setting($table,'abstract_delimiter');
		$sql = "REPLACE( CONCAT_WS('".$delimiter."',`".$as_table.'`.`' . implode( "`,`".$as_table.'`.`' ,$abstract_fields ) . "`), '".$delimiter.$delimiter."','' )  AS `" . $abstract_field_name . "`";
    return $sql;
	}
  

  /**
   * Find relation tables of a given type
   *
   * @param string $type 
   * @return array
   * @author Jan den Besten
   */
  public function get_relation_tables( $type, $args=null ) {
    $method = '_get_'.$type.'_tables';
    if ( method_exists($this,$method) ) {
      if ( !is_null($args) )
        return $this->$method($args);
      else
        return $this->$method();
    }
    else {
      $this->reset();
      throw new ErrorException( __CLASS__.'->'.$method.' does not exists. So the relation tables cannot be found.' );
    }
    return $tables;
  }
  
  /**
   * Find one_to_many tables
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _get_one_to_many_tables( $table ) {
    $tables = $this->data->list_tables();
    $tables = filter_by( $tables, 'tbl_');
    // find foreign_keys to this table
    $foreign_key = 'id_'.remove_prefix($table);
    foreach ($tables as $key=>$table) {
      $fields = $this->db->list_fields($table);
      if (!in_array($foreign_key,$fields)) {
        unset($tables[$key]);
      }
    }
    $this->relation_tables['one_to_many'] = $tables;
    return $tables;
  }
  

  /**
   * Find many_to_many tables
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _get_many_to_many_tables() {
    if (get_prefix($this->settings['table'])==='rel') return array();
    $rel_tables = $this->data->list_tables();
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
   * Is alleen nodig als je tijdelijk een afwijkende instelling wilt, want standaard kun je alles al instellen in de het config bestand dat bij het data_model hoort.
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
   * Geeft alle settings
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_settings() {
    return $this->settings;
  }

  /**
   * Geeft een gevraagde setting.
   * - Werkt zoals el(), dus de key kan een array zijn van keys om dieper in de settings array iets op te graven
   * - Als de gevraagde setting niet bestaat dan wordt eerste geprobeerd een autoset waarde te geven, als dat niet lukt wordt default [NULL] teruggegeven.
   * - Als voor de gevraagde key(s) een method bestaat (get_setting_{key}) dan wordt die aangeroepen om de setting op te vragen.
   *
   * @param mixed $key een met de gevraagde key, of array van gevraagde keys
   * @param mixed $default [null]
   * @return mixed
   * @author Jan den Besten
   */
  public function get_setting( $key, $default=null ) {
    if (is_string($key) and method_exists($this,'get_setting_'.$key)) {
      $method = 'get_setting_'.$key;
      $arguments = func_get_args();
      return call_user_func_array( array($this,$method), $arguments );
    }
    return el( $key, $this->settings, el( $key, $this->autoset, $default ) );
  }
  
  
  /**
   * Geeft $settings['field_info'] Met alse extra:
   * - Standaard informatie uit config field_info_config voor het veld
   * - 'label'      - de ui name van het veld
   * - ['options']  - Als het veld options heeft, wordt hier de informatie ingestopt
   * - ['path']     - Als het een media veld betreft
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_setting_field_info_extended($fields=array(),$extra=array(),$include_options=FALSE) {
    $this->config->load('field_info',true);
    $field_info_config = $this->config->item('field_info');
    
    // Standaard velden, of meegegeven (met mogelijk extra) velden
    if (!$fields) $fields = $this->settings['fields'];
    $fields = array_combine($fields,$fields);
    // Vul field_info aan met eventuele extra velden
    $field_info = $this->settings['field_info'];
    $field_info = array_merge($fields,$field_info);
    // Alleen de meegegeven velden
    $field_info = array_keep_keys($field_info,$fields);
    
    // Loop alle velden en vul informatie aan
    foreach ($field_info as $field => $info) {
      if (!is_array($info)) $info=array();
      // UI name
      $info['label'] = $this->lang->ui($field);
      
      // Schema: default
      $schema = $field_info_config['FIELDS_default'];
      // Schema: from prefix
      $fieldPrefix  = get_prefix($field);
      $schema       = array_merge($schema, el(array('FIELDS_prefix',$fieldPrefix),$field_info_config,array()) );
      // Schema: from fieldname
      $schema       = array_merge($schema, el(array('FIELDS_special',$field),$field_info_config,array()) );
      // Grid-type?
      if (!isset($schema['grid-type'])) $schema['grid-type'] = $schema['type'];
      
      // Combineer
      $info = array_merge($info,$schema,$extra);
      
      // Validation als string
      $info['validation'] = is_array($info['validation']) ? implode('|',$info['validation']) : $info['validation'];
      
      // Options
      $options = $this->get_options($field,array('many_to_many','one_to_many'));
      if ($options) {
        $info['type'] = 'select';
        if ($fieldPrefix==='media' or $fieldPrefix==='medias') {
          $info['path'] = $options['path'];
          $info['type'] = 'media';
          unset($options['path']);
        }
        $options = array_keep_keys($options,array('table','data','multiple','api','insert_rights'));
        $info['_options'] = $options;
        if ($include_options) {
          $select_options = el('data',$options);
          if ($select_options) {
            $select_options = array_column($select_options,'name','value');
            $info['options']  = $select_options;
            $info['multiple'] = el('multiple',$options,FALSE)?'multiple':'';
          }
        }
      }

      $field_info[$field] = $info;
    }
    
    // Overschrijf los ingestelde settings
    if (isset($this->settings['field_info'])) {
      foreach ($field_info as $key => $info) {
        if (isset($this->settings['field_info'][$key])) {
          $field_info[$key] = array_merge_recursive_distinct($field_info[$key],$this->settings['field_info'][$key]);
          // Validation als string
          $field_info[$key]['validation'] = is_array($field_info[$key]['validation']) ? implode('|',$field_info[$key]['validation']) : $field_info[$key]['validation'];
        }
      }
    }

    return $field_info;
  }
  
  // /**
  //  * Geeft form_fields terug, klaar voor gebruik in een formulier
  //  *
  //  * @param array $fields
  //  * @param array $extra
  //  * @param bool $include_options
  //  * @return array
  //  * @author Jan den Besten
  //  */
  // public function get_field_info_as_formfields($fields=array(),$extra=array(),$include_options=TRUE) {
  //   $field_info = $this->get_setting_field_info_extended($fields,$extra,$include_options);
  //   $form_fields = array();
  //   foreach ($field_info as $field => $info) {
  //     // $form_fields[$field] = array(
  //     //   'label'      => $info['label'],
  //     //   'validation' => is_array($info['validation'])?implode('|',$info['validation']):$info['validation'],
  //     //   'type'       => $info['type'],
  //     // );
  //
  //     if (isset($info['options'])) {
  //       $options = el('data',$info['options']);
  //       if ($options) {
  //         $options = array_column($options,'name','value');
  //         $form_fields[$field]['options']  = $options;
  //         $form_fields[$field]['multiple'] = el('multiple',$info['options'],FALSE)?'multiple':'';
  //       }
  //     }
  //   }
  //   return $form_fields;
  // }
  
  /**
   * Geeft de grid_set settings, met als extra:
   * - field_info_extended
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_setting_grid_set() {
    $grid_set = el('grid_set',$this->settings);
    $grid_set = $this->_complete_relations_of_set($grid_set,'grid_set');

    $field_info = $this->get_setting_field_info_extended($grid_set['fields']);
    $grid_set['field_info'] = $field_info;

    $searchable_fields = array_combine($grid_set['fields'],$grid_set['fields']);
    $searchable_fields = array_unset_keys($searchable_fields,array('id','order','self_parent','uri'));
    $searchable_fields = not_filter_by_key($searchable_fields,array('b','action'));
    $grid_set['searchable_fields'] = array_values($searchable_fields);
    return $grid_set;
  }
  
  /**
   * Geeft de form_set settings, met als extra:
   * - field_info_extended
   * - options
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_setting_form_set() {
    $form_set = el('form_set',$this->settings);
    
    // Fields
    $fields = el('fields',$form_set);
    // Als fields niet bestaat, haal die uit de fieldsets
    if (!$fields) {
      $fields=array();
      foreach($form_set['fieldsets'] as $fieldsetfields) {
        $fields=array_merge($fields,$fieldsetfields);
      }
      $form_set['fields'] = $fields;
    }
    // Hernoem fieldset keys
    $fieldset_keys = array_keys($form_set['fieldsets']);
    $fieldset_keys = $this->lang->ui($fieldset_keys);
    $form_set['fieldsets'] = array_combine($fieldset_keys,$form_set['fieldsets']);
    
    // Relaties
    $form_set = $this->_complete_relations_of_set($form_set,'form_set');
    
    // Field info
    $form_set['field_info'] = $this->get_setting_field_info_extended($form_set['fields'],array(),true);
    return $form_set;
  }
  
  /**
   * Maak relatie settings compleet (en default) voor grid_set en form_set
   *
   * @param array $set 
   * @param string $set_type 
   * @return array
   * @author Jan den Besten
   */
  private function _complete_relations_of_set($set,$set_type) {
    // Default
    if (!isset($set['with'])) {
      if ($set_type==='grid_set') 
        $set['with'] = array('many_to_one');
      else 
        $set['with'] = array('many_to_one','many_to_many');
    }
    
    // Relaties
    if ( $set['with']!==FALSE ) {
      foreach ($set['with'] as $type => $relations) {
        // Vul aan als alleen maar de types zijn ingesteld
        if (is_numeric($type)) {
          unset($set['with'][$type]);
          $type = $relations;
          $relations = $this->get_setting(array('relations',$type));
        }
        else {
          $complete_relations = $this->get_setting(array('relations',$type));
          $relations = array_keep_keys($complete_relations,$relations);
          $set['with'][$type] = $relations;
        }
        // Loop alle relaties langs en complementeer die
        if ($relations) {
          foreach ($relations as $what => $info) {
            $field = 'abstract';
            if ($type==='one_to_one' and isset($info['other_table'])) {
              $field = $this->get_other_table_abstract_fields($info['other_table']);
            }
            $set['with'][$type][$what] = $field;
            // Vul ook de velden aan als relatie veld er nog niet instaat
            $relation_field = $what;
            if ($type==='many_to_many') $relation_field = $info['result_name'];
            if (!in_array($relation_field,$set['fields'])) {
              $set['fields'][] = $relation_field;
              if ($set_type==='form_set') {
                $first_fieldset = array_keys($set['fieldsets']);
                $first_fieldset = current($first_fieldset);
                $set['fieldsets'][$first_fieldset][] = $relation_field;
              }
            }
          }
        }
      }
    }
    
    return $set;
  }
  
  /**
   * Maak een handige array van [result_name => ['type'=>'',fields'=>[other_table_fields],'other_table'=>'']] voor intern gebruik
   *
   * @return void
   * @author Jan den Besten
   */
  private function _set_relation_result_fields() {
    $this->relation_result_fields = array();
    foreach ($this->settings['relations'] as $type => $relations) {
      foreach ($relations as $key => $relation) {
        if (isset($relation['result_name']) and isset($relation['other_table'])) {
          $this->relation_result_fields[ $relation['result_name'] ] = array(
            'relation'    => $type,
            'other_table' => $relation['other_table'],
            'fields'      => $this->get_other_table_fields( $relation['other_table'] ),
          );
        }
      }
    }
    return $this;
  }
  
  /**
   * Geeft de result_name informatie van een result_name
   *
   * @param string $result_name 
   * @return array
   * @author Jan den Besten
   */
  private function _get_relation_result($result_name,$with=NULL) {
    if ( !$this->relation_result_fields ) $this->_set_relation_result_fields();
    $result = el($result_name,$this->relation_result_fields);
    if (isset($with)) {
      if ($with) {
        $relation = $result['relation'];
        if (!isset($with[$relation])) $result = FALSE;
      }
      else {
        $result = FALSE;
      }
    }
    return $result;
  }
  
  /**
   * Test of string is een result_name van een (ingestelde) relatie
   *
   * @param string $name
   * @return bool
   * @author Jan den Besten
   */
  private function _is_result_name($name,$with=NULL) {
    if ( !$this->relation_result_fields ) $this->_set_relation_result_fields();
    $is_result_name = isset($this->relation_result_fields[$name]);
    if (isset($with)) {
      if ($with) {
        $relation = el($name,$this->relation_result_fields);
        if (!isset($relation['relation'],$with)) $is_result_name = FALSE;
      }
      else {
        $is_result_name = FALSE;
      }
    }
    return $is_result_name;
  }
  
  
  /**
   * Geeft ingestelde relatie met gegeven key/waarde van die relaties setting (bv 'other_table' oid)
   *
   * @param string $type 
   * @param string $key 
   * @param string $value 
   * @return array
   * @author Jan den Besten
   */
  private function _find_relation_setting_by($type,$key,$value) {
    $relations = $this->get_setting( array('relations',$type) );
    if (is_null($relations)) return NULL;
    $relations = find_row_by_value($relations,$value,$key);
    return current($relations);
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
   * NB Roep altijd aan na een ->get..() variant.
   * 
   * Resultaat bij één veld:
   * 
   * Alle mogelijkheden:
   *  - array(
   *    'data' => array()         - Array met opties
   *    'table' => ''             - naam van andere tabel waar de opties worden opgevraagd, of:
   *    'path' => ''              - als het een media(s) veld is het pad van de bestanden
   *    'model' => ''             - naam van model waar de opties worden opgevraagd.
   *    'multiple' => TRUE/FALSE  - of het meerkeuze is
   * )
   *
   * 
   * Waar de 'data' array er zo uit ziet:
   * 
   * array(
   *  'value' => 'name',
   *  ...
   * ) 
   * 
   * Of als 'as_object' = TRUE:
   * 
   * array(
   *  array( 'value' => ..., 'name' => .... )
   *  ...
   * ) 
   * 
   * 
   * of met gegroupeerde data (voor groepen in selects)
   * 
   * array(
   *  'groep_naam_1' => array(...),
   *  'groep_naam_2' => array(...),
   *  ...
   * )
   * 
   * Als geen veld wordt meegegeven worden de opties van alle velden uit de tabel teruggegeven:
   * 
   * array(
   *    '...veldnaam...' => array( ...zie hierboven... ),
   *    ....
   * )
   *
   * @param mixed $fields ['']
   * @return array
   * @author Jan den Besten
   */
  public function get_options( $fields='', $with=array('many_to_many'), $as_object = TRUE ) {
    // Eén of alle velden?
    $one = FALSE;
    if (!empty($fields)) {
      if (!is_array($fields)) {
        $one = $fields;
        $fields = array($fields);
      }
    }
    else {
      $fields = array_keys($this->settings['options']);
    }
    
    // Alle opties van de velden verzamelen
    $options=array();
    $where_primary_key = $this->tm_where_primary_key; // Bewaar dit voor opties uit andere tabellen
    foreach ($fields as $field) {
      $field_options = el( array($field), $this->settings['options'] );
      $field_options['field'] = $field;
      
      if ($field_options) {
        $field_options;
        
        // table
        if ( isset($field_options['table']) ) {
          $other_table = $field_options['table'];
          // Zijn er teveel opties?
          if ($this->db->count_all($other_table)>10000) {
            $field_options['api'] = '_api/table?table='.$other_table.'&as_options=true';
          }
          // Anders geef gewoon de opties terug
          else {
            $current_table = $this->settings['table'];
            $field_options['data'] = $this->data->table( $other_table )->get_result_as_options(0,0, $where_primary_key );
            // $field_options['data'] = array_unshift_assoc($field_options['data'],'','');
            $this->data->table($current_table); // Terug naar huidige data table.
          }
          // Rechten om nieuwe aan te maken?
          if ($this->flexy_auth->has_rights($other_table)) {
            $field_options['insert_rights'] = TRUE;
          }
        }
        
        // special (fields)
        if ( isset($field_options['special']) ) {
          switch ($field_options['special']) {
            case 'self_parent':
              $first_abstract_field = $this->settings['abstract_fields'];
              $first_abstract_field = current($first_abstract_field);
              $this->select('id,self_parent,order,'.$first_abstract_field);
              if ($this->tm_where_primary_key) $this->where( $this->settings['primary_key'].'!=',$this->tm_where_primary_key);
              $this->tree( $first_abstract_field, '', ' / ' );
              $this->order_by( 'order,self_parent' );
              $field_options['data'] = $this->get_result();
              foreach ($field_options['data'] as $key => $option) {
                $field_options['data'][$key] = $option[$first_abstract_field];
              }
              $field_options['data'] = array_unshift_assoc( $field_options['data'], '','');
              break;
          }
        }
        
        // model (external)
        if ( isset($field_options['model']) ) {
          $current_table = $this->settings['table'];
          $model = 'Options_'.ucfirst($field_options['model']);
          $this->load->model( 'data/'.$model );
          $field_options['table'] = $this->settings['table'];
          $field_options['data'] = $this->$model->get_options( $field_options );
          $this->data->table($current_table);
        }
      }
      $options[$field] = $field_options;
    }
    
    // one_to_one opties: die opties toevoegen
    if ( in_array('one_to_one',$with) ) {
      $relations = $this->settings['relations']['one_to_one'];
      foreach ($relations as $relation) {
        $other_table = $relation['other_table'];
        $other_options = $this->data->table( $other_table )->get_options();
        $this->data->table($this->settings['table']); // Terug naar huidige data table.
        unset($other_options[$relation['foreign_key']]);
        if ($other_options) {
          foreach ($other_options as $field => $info) {
            $options[$other_table.'.'.$field] = $info;
          }
        }
      }
    }
    
    // ..._to_many opties
    if ( in_array('many_to_many',$with) or in_array('one_to_many',$with) ) {
      foreach ($with as $type) {
        $relations = $this->get_setting(array('relations',$type));
        if ($relations) {
          foreach ( $relations as $what => $relation ) {
            if (!isset($options[$what]['data'])) {
              $other_table = $relation['other_table'];
              $result_name = $relation['result_name'];
              $this->data->table($other_table);
              $options[$result_name] = array(
                'table'         =>$other_table,
                'data'          =>$this->data->get_result_as_options(),
                'multiple'      =>true,
                'insert_rights' => $this->flexy_auth->has_rights($other_table),
              );
              $this->data->table($this->settings['table']); // Weer terug naar huidige tabel
            }
          }
        }
      }
    }

    // Return
    if ($as_object) {
      foreach ($options as $key => $row) {
        if (isset($row['data'])) {
          $data = array();
          foreach ($row['data'] as $value => $name) {
            $data[] = array('value'=>$value,'name'=>$name);
          }
          $options[$key]['data'] = $data;
        }
      }
    }
    
    foreach ($options as $field => $row) {
      // Empty?
      if (count($row)===1 && isset($row['field'])) $options[$field] = FALSE;
    }
    
    if ($one!==FALSE) return $options[$one];
    return $options;
  }
  
  

  
  /**
   * Geeft default waarden van een row. Wordt uit de database gehaald.
   *
   * @param $set [FALSE] 'form', hiermee bepaal je eventueel de set en daarmee welke velden in het resultaat terugkomen
   * @return array
   * @author Jan den Besten
   */
  public function get_defaults( $set=FALSE ) {
    $defaults = array();
    
    if ($set=='form') {
      $fields = el(array('form_set','fields'),$this->settings);
      if (empty($fields)) {
        // van fieldsets
        $fields=array();
        $fieldsets = el(array('form_set','fieldsets'),$this->settings);
        foreach ($fieldsets as $fieldset) {
          $fields = array_merge($fields,$fieldset);
        }
      }
    }
    else {
      $fields = $this->settings['fields'];
    }
    
    if ($this->tm_select) {
      $fields = array_intersect($fields,$this->tm_select);
    }

		foreach ($fields as $field) {
      // Default from field_info/field_info_config
      $defaults[$field] = $this->get_setting( array('field_info',$field,'default') );
      // Default from database?
      if (is_null($defaults[$field])) $defaults[$field] = $this->field_data( $field, 'default' );
		}
    $defaults[$this->settings['primary_key']] = -1;
    
    // Stel eventuele eigenaar van de row in op huidige user.
    if (isset($defaults['user'])) {
      $defaults['user'] = $this->get_user_id();
    }
    
    
    // Relaties
    if (is_array($this->tm_with)) {
      
      // one_to_one
      if (isset($this->tm_with['one_to_one'])) {
        foreach ($this->tm_with['one_to_one'] as $what => $relation) {
          $other_table  = $relation['table'];
          $other_fields = $relation['fields'];
          $other_defaults = $this->data->table($other_table)->get_defaults();
          $this->data->table($this->settings['table']); // Terug naar huidige data table.
          $other_defaults = array_keep_keys($other_defaults,$other_fields);
          foreach ($other_defaults as $key => $value) {
            $defaults[$other_table.'.'.$key] = $value;
          }
          // $defaults = array_merge($defaults,$other_defaults);
        }
      }
      
      // .._to_many
      if (isset($this->tm_with['many_to_many']) or isset($this->tm_with['one_to_many'])) {
        if (isset($this->tm_with['many_to_many'])) {
          foreach ($this->tm_with['many_to_many'] as $what => $relation) {
            $defaults[$relation['as']]=array();
          }
        }
        if (isset($this->tm_with['one_to_many'])) {
          foreach ($this->tm_with['one_to_many'] as $what => $relation) {
            $defaults[$relation['as']]=array();
          }
        }
      }
    }
    return $defaults;
  }
  
  
  
  /**
   * Geeft random waarden voor een row, eventueel voor gespecificeerde velden
   *
   * @param array $fields
   * @return array $result
   * @author Jan den Besten
   */
  public function get_random( $fields=array() ) {
    if (empty($fields)) $fields = $this->settings['fields'];
    $result = array();
    foreach ($fields as $field) {
      if ($field!==$this->settings['primary_key']) $result[$field] = $this->random_field_value( $field );
    }
    return $result;
  }

  /**
   * Geeft random waarde voor gegeven veld
   *
   * @param string $field
   * @return mixed
   * @author Jan den Besten
   */
  public function random_field_value($field,$id=FALSE) {
    $value = NULL;
    $type  = get_prefix($field,'_');
    switch($type) {
      case 'id' :
        if ($field!==$this->settings['primary_key']) {
          $relation = $this->get_setting(array('relations','many_to_one',$field));
          if ($relation) {
            $other_table = $relation['other_table'];
            $sql = "SELECT `id` FROM `".$other_table."`";
            $query = $this->db->query($sql);
            if ($query) {
              $results = $query->result_array();
              $value = random_element($results);
              $value = $value['id'];
            }
          }
        }
        break;
      case 'rel':
        if ($id) {
          $field = remove_prefix($field);
          $relation = $this->get_setting(array('relations','many_to_many',$field));
          if ($relation) {
            $other_table = $relation['other_table'];
            $sql = "SELECT `id` FROM `".$other_table."`";
            $query = $this->db->query($sql);
            if ($query) {
              $results = $query->result_array();
              shuffle($results);
              $max = count($results);
              if ($max>4) $max = 4;
              $results = array_slice($results,0,rand(1,$max));
              $ids = array();
              foreach ($results as $item) {
                $ids[] = $item['id'];
              }
              // Remove
              $sql = "DELETE FROM `".$relation['rel_table']."` WHERE `".$relation['this_key']."` = ".$id;
              $this->db->query($sql);
              // Add Random items
              foreach ($ids as $other_id) {
                $sql = "INSERT INTO `".$relation['rel_table']."` (`".$relation['this_key']."`, `".$relation['other_key']."`) VALUES ('".$id."', '".$other_id."')";
                $this->db->query($sql);
              }
              $value = implode($this->settings['abstract_delimiter'],$ids);
            }
          }
        }
        break;
      case 'int':
        $value = rand(0,100);
        break;
      case 'dec':
        $value = rand(10,99).'.'.rand(10,99);
        break;
      case 'b':
      case 'is':
      case 'has':
        $value = false;
        if (rand(0,1)==1) $value = true;
        break;
      case 'txt':
        $this->load->library('Lorem');
        $value = $this->lorem->getContent(rand(50,500),'html');
        break;
      case 'stx':
        $this->load->library('Lorem');
        $value = $this->lorem->getContent(rand(10,50),'plain');
        break;
      case 'medias':
      case 'media':
        $files = $this->assets->get_files('pictures');
        shuffle($files);
        if ($type==='media') {
          $value = current($files);
          $value = $value['file'];
        }
        else {
          $files = array_slice($files,0,rand(1,4));
          foreach ($files as $file) {
            $value[] = $file['file'];
          }
          $value = implode('|',$value);
        }
        break;
      case 'url' :
        $value='';
        if (rand(1,4)>2) {
          // Link from link table
          if (!isset($links_table)) $links_table=$this->get_result('tbl_links');
          $url=random_element($links_table);
          $value=$url['url_url'];
        }
        break;
      case 'email':
        $value = strtolower(random_string('alpha',rand(2,8)).'@'.random_string('alpha',rand(2,8)).'.'.random_string('alpha',rand(2,3)));
        break;
      case 'date':
      case 'dat':
        $year = (int) date('Y');
        $value = rand($year-5,$year+5).'-'.rand(1,12).'-'.rand(1,31);
        break;
      case 'tme':
        $year = (int) date('Y');
        $value = rand($year-5,$year+5).'-'.rand(1,12).'-'.rand(1,31). ' '.rand(0,23).':'.rand(0,59).':'.rand(0,59);
        break;
      case 'time':
        $value = rand(0,23).':'.rand(0,59).':'.rand(0,59);
        break;
      case 'rgb':
      case 'str':
        $value='';
        if ($field=='str_video') {
          if (rand(1,4)>2) {
            // Get youtube homepage, and all the youtube links from them
            if (!isset($YouTubeHTML)) {
              $YouTubeHTML = file_get_contents('https://www.youtube.com/');
              if (preg_match_all("/href=\"\\/watch\\?v=(.*)\"/uiUsm", $YouTubeHTML,$matches)) {
                $YouTubeCodes=$matches[1];
              }
            }
            $value = random_element($YouTubeCodes);
          }
        }
        else {
          $value = str_replace(array('.',','),'',$this->lorem->getContent(rand(1,5),'plain'));
        }
        break;
      default:
        $value = random_string();
        break;
    }
    return $value;
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
    
    $this->_prepare_query($limit,$offset);
    
    // get
    $query = $this->db->get();
    
    // Jump to today? Pas query aan.
    if ( $query AND $this->tm_jump_to_today AND $this->tm_limit>1 ) {
      $this->query_info['limit']      = (int) $this->tm_limit;
      $this->query_info['total_rows'] = $this->total_rows( true );
      // Jump to today nodig?
      if ($this->query_info['total_rows']>$this->query_info['limit']) {
        // Is (eerste) order_by het jump_to_today veld?
        $order_by = $this->tm_order_by[0];
        $date_field=remove_suffix($order_by,' ');
        if ( $date_field==$this->tm_jump_to_today ) {
          // Tel aantal items eerder dan vandaag
          $direction=get_suffix($order_by,' ');
          $last_full_sql = $this->last_query();
          $last_clean_sql = $this->last_clean_query();
          unset($this->query_info['last_query']); // reset last_query
          if ($direction=='DESC')
            $direction='>';
          else
            $direction='<';
          $count_sql = $last_clean_sql . ' WHERE DATE(`'.$date_field.'`) '.$direction.'= DATE(NOW()) ORDER BY '.$order_by;
          $count_query = $this->db->query( $count_sql );
          $jump_offset = $count_query->num_rows();
          $page = (int) floor($jump_offset / $this->tm_limit);
          $this->tm_offset = $page * $this->tm_limit;
          $sql = str_replace( 'LIMIT '.$this->tm_limit, 'LIMIT '.$this->tm_offset.','.$this->tm_limit, $last_full_sql);
          $this->_create_cache_name($sql);
          $query = $this->db->query( $sql );
          $this->query_info['today'] = true;
        }
      }
    }

    
    // Query Info Complete
    if ($query) {
      $this->query_info['from_cache'] = FALSE;
      $this->query_info['num_rows']   = $query->num_rows();
      $this->query_info['total_rows'] = $query->num_rows();
      if ($this->tm_where_limit)  $this->tm_limit = $this->tm_where_limit;
      if ($this->tm_where_offset) $this->tm_offset = $this->tm_where_offset;
      if ($this->tm_limit>1) {
        $this->query_info['limit']      = (int) $this->tm_limit;
        $this->query_info['offset']     = $this->tm_offset;
        $this->query_info['page']       = (int) floor($this->tm_offset / $this->tm_limit);
        $this->query_info['total_rows'] = $this->total_rows( true );
        $this->query_info['num_pages']  = (int) ceil($this->query_info['total_rows'] / $this->tm_limit);
      }
      $this->query_info['num_fields'] = $query->num_fields();
      $this->query_info['last_query'] = $this->last_query();
    }
    
    if ( $reset ) $this->reset();
    return $query;
  }
  
  
  /**
   * Prepares query (if not done allready) before calling get()
   *
   * @return void
   * @author Jan den Besten
   */
  private function _prepare_query( $limit=0, $offset=0 ) {
    if ( $this->tm_query_prepared ) return $this;
    
    // Bewaar limit & offset als ingesteld (overruled eerder ingestelde door ->limit() )
    if ($limit!=0 or $offset!=0) $this->limit( $limit,$offset );

    // bouw select query op
    $this->_select();
        
    // bouw relatie queries
    $this->_with();
    
    // bouw find query op
    $this->_find();
    
    // maak select concreet
    $this->db->select( $this->tm_select, FALSE );
    
    // order_by
    if ( empty($this->tm_order_by) and !empty($this->settings['order_by']) ) {
      $this->order_by( $this->settings['order_by'] );
    }
    if ( !empty($this->tm_order_by) ) {
      foreach ($this->tm_order_by as $order_by) {
        $split = $this->_split_order($order_by);
        $this->db->order_by( $split['field'], $split['direction'] );
      }
    }

    // FROM
    $this->_from();
    
    // limit & offset
    $this->query_info = array();
    $this->db->limit( $this->tm_limit );
    $this->db->offset( $this->tm_offset );
    
    // Cache name
    $this->_create_cache_name( $this->db->get_compiled_select( '',FALSE ) );
    
    $this->tm_query_prepared = TRUE;
    return $this;
  }
  
  /**
   * Split één order item in veld en direction
   *
   * @param string $order 
   * @return array ['field'=>'...','direction'=>['ASC','DESC']] 
   * @author Jan den Besten
   */
  private function _split_order($order) {
    $order     = trim($order);
    $direction = '';
    $order     = explode(' ',$order);
    $direction = trim(el(1,$order,'ASC'));
    $order     = trim($order[0]);
    // Relations?
    if (has_string('.',$order) and !has_string('.abstract',$order)) {
      $order = str_replace('.','`.`',$order);
    }
    return array('field'=>$order,'direction'=>$direction);
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
   * - Met relaties gekoppeld (bijvoorbeeld als subarrays)
   * - Met default data voor one_to_one relaties
   * - Als select_txt_abstract() is ingesteld dan worden die velden ook nog gestript van HTML tags
   *
   * @param object $query 
   * @return array
   * @author Jan den Besten
   */
  protected function _make_result_array( $query ) {
    if ( $query===FALSE) return array();
    
    $id        = -1;
    $key       = el( 'result_key', $this->settings, el( 'primary_key',$this->settings ) );
    $result    = array();
    $with_data = array();
    
    // Pad fields
    if ($this->tm_tree) {
      $tree = array();
      $needed_tree_fields = array_merge(array_keys($this->tm_tree),array($this->settings['primary_key'],'self_parent'));
    }
    
    // Eventuele defaults bewaren bij een niet bestaanden one_to_one
    $one_to_one = el('one_to_one',$this->tm_with);
    $one_to_one_defaults=array();
    
    while ( $row = $query->unbuffered_row('array') ) {
    // foreach ( $query->result_array() as $row) {

      // primary_key
      if ($this->tm_select_include_primary) {
        $id = $row[$this->settings['primary_key']];
      }
      else {
        $id++;
      }
      
      // defaults bij niet bestaande one_to_one
      if ($one_to_one and in_array(NULL,$row)) {
        foreach ($row as $field => $value) {
          if (is_null($value)) {
            $other_table = get_prefix($field,'.');
            if ($other_table and !isset($one_to_one_defaults[$other_table])) {
              $one_to_one_defaults[$other_table] = $this->data->table($other_table)->get_defaults();
              $this->data->table($this->settings['table']); // Terug naar huidige data table.
              $value       = el(array($other_table,get_suffix($field,'.')),$one_to_one_defaults,'DEFAULT');
              $row[$field] = $value;
            }
          }
        }
      }
      
      // tree
      if ($this->tm_tree)  {
        // Remember current row with necessary fields
        $tree[$id] = array_keep_keys($row,$needed_tree_fields);
        // Recursive create current tree field
        foreach ($this->tm_tree as $field => $tree_info) {
          $row[$tree_info['tree_field']] = $this->_fill_tree( $tree, $id, $tree_info );
        }
      }
      
      // result_key
      $result_key = el($key,$row,$id);
      
      // Voeg relatie data aan row
      if ($this->tm_with) {
        
        foreach ($this->tm_with as $with_type => $this_with) {
          
          foreach ($this_with as $what => $info) {

            $other_table = $info['table'];
            $as = el('as',$info,$other_table);
            
            // Flat many_to_one
            if ( $with_type==='many_to_one' AND ( (el('fields', $info)==='abstract') OR (el('flat', $info, false)===true) )) {
              $foreign_key = $this->settings['relations'][$with_type][$what]['foreign_key'];
              $abstract_field = $other_table.'__abstract';
              if (isset($row[$abstract_field])) {
                $row[$foreign_key] = $row[$abstract_field];
                unset($row[$abstract_field]);
              }
            }
            
            // JSON: schoon lege abstract resulaten op
            elseif ( el('json',$info,FALSE) ) {
              if (el('fields',$info)==='abstract') {
                if ($row[$as]==='{}') $row[$as] = '';
              }
            }
            
            // Niet JSON en niet flat => als subarray
            elseif ($with_type!=='one_to_one') {
              $fields   = $info['fields'];
              // split row and with data
              $keys = array_keys($row);
              // $many_data_index = array_preg_search( $as, $keys );
              $many_data_index = array_search( $as, $keys );
              $row_with_data = filter_by_key( $row, $as.'.' );
              $row = array_diff_assoc( $row, $row_with_data );
              // process 'with' data
              foreach ($row_with_data as $oldkey => $values) {
                $newkey = remove_prefix( $oldkey, '.');
                $row_with_data[$newkey] = $values;
                unset($row_with_data[$oldkey]);
              }
              // remember 'with' data
              if (!isset($with_data[$result_key][$as])) $with_data[$result_key][$as]=array();
              if ($with_type==='many_to_one') {
                $with_data[$result_key][$as] = $row_with_data;
              }
              else {
                if ( el( $this->settings['primary_key'], $row_with_data ) ) {
                  $with_data[$result_key][$as][$row_with_data[$this->settings['primary_key']]] = $row_with_data;
                }
              }
            }
            
          }
          // Merge with data met normale data in row, als mogelijk op gewenste plek
          if (isset($with_data[$result_key])) {
            if ($many_data_index) {
              if (is_array($many_data_index)) $many_data_index = current($many_data_index);
              $row = array_merge(array_slice($row,0,$many_data_index),$with_data[$result_key],array_slice($row,$many_data_index));
            }
            else {
              $row = array_merge($row,$with_data[$result_key]);
            }
          }
        }
      }
            
      // tm_txt_abstract
      if ($this->tm_txt_abstract>0) {
        $txt_row = $row;
        $txt_row = filter_by_key($txt_row,'txt_');
        $txt_row = array_keys($txt_row);
        foreach ($txt_row as $txt_field) {
          $row[$txt_field] = preg_replace( "/[\n\r]/"," ", strip_tags($row[$txt_field]));
          $row[$txt_field] = str_replace( "&nbsp;"," ", $row[$txt_field]);
        }
      }
      
      // tm_flat_abstracts
      if ($this->tm_flat_abstracts and isset($row['abstract'])) {
        $row = $row['abstract'];
      }
      
      // result_key
      $result[ $result_key ] = $row;
    }
    
    // pas query info aan
    $this->query_info['num_rows']     = count($result);
    $this->query_info['num_fields']   = count(current($result));
    if ( isset($this->tm_with['many_to_many']) or isset($this->tm_with['one_to_many']) ) {
      $this->query_info['total_rows'] = $this->total_rows(true,true);
    }
    
    // where tree?
    if ( !empty($this->tm_where_tree) and !empty($result) ) {
      if (!$this->tm_tree) {
        throw new ErrorException( __CLASS__.'->where_tree() You need to set ->tree() when using ->where_tree()' );
      }
      foreach ($this->tm_where_tree as $where_tree) {
        $result = find_row_by_value( $result, $where_tree['value'], $where_tree['field'] );
      }
      $this->query_info['num_rows'] = count($result);
    }
    
    return $result;
  }
  
  
  /**
   * Vul een tree veld recursief
   *
   * @param array $result 
   * @param int $key
   * @param array $tree_info 
   * @return string
   * @author Jan den Besten
   */
  protected function _fill_tree( &$result, $key, $tree_info, $counter=0 ) {
    $value = '';
    $parent = el( array($key,'self_parent'), $result, 0 );
    if ( $parent>0 and $counter<20) {
      // Counter voorkomt onneindige recursieve aanroep in het geval er een fout is ontstaan in de tabel.
      $value .= $this->_fill_tree( $result, $parent, $tree_info, $counter+1) . $tree_info['split'];
    }
    $part = el( array($key,$tree_info['original_field']), $result );
    // Als parent niet in resultaat zit (bij where/like statements) zoek die dan op
    if (is_null($part) and $key!==0) {
      $order = array();
      foreach ($this->tm_order_by as $order_by) {
        $split = $this->_split_order($order_by);
        $order[]='`'.$split['field'].'` '.$split['direction'];
      }
      $sql = 'SELECT `'.$tree_info['original_field'].'` FROM `'.$this->settings['table'].'` WHERE `'.$this->settings['primary_key'].'` = "'.$key.'" ORDER BY '.implode(',',$order).' LIMIT 1';
      $query = $this->db->query($sql);
      if ($query) {
        $row = $query->unbuffered_row('array'); ;
        $part = el( $tree_info['original_field'],$row );
      }
    }
    $value .= $part;
    return $value;
  }


  /**
   * Interne method voor get_result(), andere models kunnen zo get_result() zonder problemen aanpassen zoder de interne werking te beinvloeden.
   *
   * @param int $limit [0]
   * @param int $offset [0] 
   * @return array
   * @author Jan den Besten
   */
  protected function _get_result( $limit=0, $offset=0 ) {
    // First check if there is a cached result
    if ($this->tm_cache_result) {
      $this->_prepare_query($limit,$offset);
      $result = $this->_get_cached_result();
      if ($result) {
        $this->reset();
        return $result;
      }
    }
    
    // No cache, just create result from database
    $result = array();
    $query = $this->get( $limit, $offset, FALSE );
    if ($query) {
      $result = $this->_make_result_array( $query );
      if ($this->tm_cache_result) $this->_cache_result($result);
      $query->free_result();
    }

    $this->reset();
    return $result;
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
    $result = $this->_get_result($limit,$offset);
    return $result;
  }
  


  /**
   * Zelfde als get_result(), maar geeft nu alleen maar de eerstgevonden rij.
   * 
   * @param mixed $where [NULL]
   * @param string $set [''] 'form' als de aanroep voor de form_set wordt gebruikt
   * @return array
   * @author Jan den Besten
   */
  public function get_row( $where = NULL, $set='' ) {
    // Nieuwe row? Geef dan defaults terug
    if ($where==-1) {
      return $this->get_defaults($set);
    }
    
    if ($where) $this->where( $where );
    // Als er many_to_many data is die niet JSON is dan kan het zijn dat er meer resultaten nodig zijn om één row samen te stellen
    if ( isset($this->tm_with['many_to_many']) ) {
      $result = $this->_get_result();
    }
    else {
      $result = $this->_get_result( 1 );
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
  
  
  /**
   * Geeft resulaat terug als opties. Een resultaat is combinatie van hetvolgende:
   * - de key is de PRIMARY_KEY
   * - de rijen zijn geen array, maar een abstract (string). Zie select_abstract().
   * - als geen volgorde is aangegeven in de config en niet is ingesteld worden de abstract velden als volgorde gebruikt
   *
   * @param int $limit [0]
   * @param int $offset [0] 
   * @return array
   * @author Jan den Besten
   */
  public function get_result_as_options( $limit=0, $offset=0, $where_primary_key='' ) {
    $this->select_abstract();
    if (empty($this->tm_order_by) and !el('order_by',$this->settings) ) {
      $abstract_fields = $this->settings['abstract_fields'];
      $this->order_by( $abstract_fields );
    }
    $query = $this->get( $limit,$offset );
    $options = array();
    if ($query) {
      $options = $this->_make_options_result($query);
      $query->free_result();
    }
    return $options;
  }
  
  
  protected function _make_options_result( $query,$key='' ) {
    if ( $query===FALSE) return array();
    if (empty($key)) $key=$this->settings['primary_key'];
    $options=array();
    foreach ( $query->result_array() as $row ) {
      $id = $row[$key];
      $options[$id] = $row['abstract'];
    }
    return $options;
  }
  
  
  /**
   * Zet caching voor dit resultaat aan of uit (werk alleen in combinatie met get_result() en get_row() )
   *
   * @param bool [$caching=TRUE] 
   * @return $this
   * @author Jan den Besten
   */
  public function cache($caching=TRUE) {
    $this->tm_cache_result = $caching;
    return $this;
  }
  
  /**
   * Maakt naam voor cache bestand specifiek voor deze query
   *
   * @param string $sql 
   * @return string
   * @author Jan den Besten
   */
  private function _create_cache_name($sql) {
    $this->tm_cache_name = 'data_result_'.$this->settings['table'].'_'.md5($sql);
    return $this->tm_cache_name;
  }
  
  /**
   * Bewaar huidige resultaat in de cache
   *
   * @param string $result 
   * @return this
   * @author Jan den Besten
   */
  protected function _cache_result($result,$name='') {
    if (empty($name)) $name = $this->tm_cache_name;
    $this->cache->save( $name, $result, TIME_YEAR );
    return $this;
  }
  
  /**
   * Haalt resultaat van huidige query op uit de cache (of geef FALSE)
   *
   * @return mixed
   * @author Jan den Besten
   */
  protected function _get_cached_result($name='') {
    if (empty($name)) $name = $this->tm_cache_name;
    $cached = $this->cache->get( $name );
    if ($cached) $this->query_info['from_cache'] = TRUE;
    return $cached;
  }
  
  /**
   * Verwijder alle result caches
   *
   * @return void
   * @author Jan den Besten
   */
  public function clear_cache() {
    $cached_results = $this->cache->cache_info();
    foreach ($cached_results as $cache) {
      if ( substr($cache['name'],0,12)==='data_result_' ) {
        $this->cache->delete($cache['name']);
      }
    }
    return $this;
  }
  
  
  
  
  /**
   * Geeft resultaat terug specifiek voor het admin grid:
   * - pagination
   * - zoeken
   * - abstracts van many_to_one
   *
   * @param mixed $limit [20] 
   * @param mixed $offset [FALSE] De start van het resultaat, als FALSE dan is jump_to_today aktief, anders niet.
   * @return array
   * @author Jan den Besten
   */
  public function get_grid( $limit = 20, $offset = FALSE ) {
    $grid_set = $this->get_setting_grid_set();
    $this->tm_as_grid = $grid_set;
    
    // Select
    $this->select( $grid_set['fields'] );
    
    // Relations
    $flatten_fields = array();
    foreach ($grid_set['with'] as $type => $relations) {
      foreach ($relations as $what => $fields) {
        $json = (in_array($type,array('one_to_many','many_to_many')));
        $this->with( $type, array( $what=>$fields), $json, FALSE );
      }
      if ($type==='many_to_one') $flatten_fields = array_merge($flatten_fields, array_keys($grid_set['with'][$type]) );
    }
    
    // Tree als menu tabel
    if ( $this->is_menu_table() ) {
      $title_field = $this->list_fields( 'str',1 );
      $this->tree( 'uri' );//->tree( $title_field );
    }
    
    // Pagination
    if (el('pagination',$grid_set,true) and $limit!==0) {
      if (is_numeric($offset) or $offset!==TRUE) $this->limit( $limit, $offset );
    }

    // Jump to today?
    if (is_bool($offset) AND $offset===FALSE and el('jump_to_today',$grid_set)) {
      $this->tm_jump_to_today = TRUE;
    }

    $result = $this->_get_result();
    
    // Prepare as grid result, flatten (foreign keys include abstract and foreign data in a json)
    if (isset($grid_set['with']['many_to_one'])) {
      foreach ($result as $id => $row) {
        foreach ($row as $field => $value) {
          if (in_array($field,$flatten_fields)) {
            $result_name = $this->settings['relations']['many_to_one'][$field]['result_name'];
            if (!isset($row[$result_name])) {
              $result_name .= '.abstract';
            }
            if (isset($row[$result_name])) {
              $result_value = $row[$result_name];
              if (is_array($result_value)) {
                array_shift($result_value);
                $result_value = implode($this->settings['abstract_delimiter'],$result_value);
              }
              $result[$id][$field] = '{"'.$value.'":"'.trim(trim($result_value,$this->settings['abstract_delimiter'])).'"}';
              unset($result[$id][$result_name]);
            }
          }
        }
      }
    }
    reset($result);
    return $result;
  }
  
  
  /**
   * Geeft resultaat terug specifiek voor een formulier van één item
   *
   * @param mixed $where Meestal alleen het id nummer
   * @return array
   * @author Jan den Besten
   */
  public function get_form( $where = '' ) {
    $form_set = $this->get_setting_form_set();

    // Select
    if (empty($this->tm_select)) $this->select( $form_set['fields'] );
    
    // Relations
    foreach ($form_set['with'] as $type => $relations) {
      if ($type!=='many_to_one') {
        foreach ($relations as $what => $fields ) {
          if ( $what!=='user_changed') {
            $this->with( $type, array( $what=>$fields) );
          }
        }
      }
    }
    $result = $this->get_row( $where, 'form' );
    // trace_sql($this->last_query());
    // trace_($result);
    return $result;
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
    if ($select=='*') return $this;
    if (is_string($select)) $select=explode(',',$select);
    if (!is_array($select)) $select=array($select);
    // Bewaar
    foreach ($select as $value) {
      $key = remove_prefix( $value,'.' );
      $this->tm_select[$key] = $value;
    }
		return $this;
	}
  
  
  /**
   * Geef aan dat de primary_key niet hoeft mee te worden genomen in de select.
   * Oa bij ->distinct() word dit standaard ingesteld.
   *
   * @return $this
   * @author Jan den Besten
   */
  public function exclude_primary_from_select() {
    $this->tm_select_include_primary = FALSE;
    return $this;
  }
  
  /**
   * Zelfde als Query Builder distinct(), maar nu wordt de primary_key niet meegenomen in select statement.
   *
   * @param bool [$distinct=TRUE]
   * @return $this
   * @author Jan den Besten
   */
  public function distinct( $distinct = TRUE ) {
    $this->db->distinct( $distinct );
    if ($distinct) $this->exclude_primary_from_select();
    return $this;
  }
  
  
  /**
   * Zorg ervoor dat de meegegeven veld(en) niet in het SELECT deel van de query komen.
   *
   * @param mixed $unselect Veldnaam die uit de selectlijst gehaald moet worden, of een string met veldnamen gescheiden door komma's of een array van veldnamen.
   * @return $this
   * @author Jan den Besten
   */
  public function unselect( $unselect ) {
    if (is_string($unselect)) $unselect=explode(',',$unselect);
    if (!is_array($unselect)) $unselect=array($unselect);
    if (!is_array($this->tm_unselect)) $this->tm_unselect = array();
    foreach ($unselect as $key => $field) {
      $this->tm_unselect[$key] = $field;
    }
    return $this;
  }


  /**
   * Bouwt select deel van de query op
   *
   * @return $this
   * @author Jan den Besten
   */
  protected function _select() {
    if ( !$this->tm_select ) {
      // Niet '*' maar alle velden expliciet maken
      $this->tm_select = array_combine($this->settings['fields'],$this->settings['fields']);
    }
    // Zorgt ervoor dat iig primary_key wordt geselecteerd
    if ( $this->tm_select_include_primary and !in_array( $this->settings['primary_key'], $this->tm_select ) ) {
      $id = $this->settings['primary_key'];
      $this->tm_select = array($id=>$id) + $this->tm_select;
    }
    // Eventuele unselect verwijderen
    if ($this->tm_unselect) {
      foreach ($this->tm_unselect as $key => $field) {
        if ( $found=array_search($field,$this->tm_select) ) {
          unset($this->tm_select[$found]);
        }
      }
    }
    // Maak de SELECT query
    foreach ( $this->tm_select as $key => $field ) {
      $prefix = get_prefix($field);
      // Zorg ervoor dat alle velden geprefixed worden door de eigen tabelnaam om dubbelingen te voorkomen
      if (in_array($field,$this->settings['fields'])) {
        $this->tm_select[$key] = '`'.$this->settings['table'].'`.`'.$field.'`';
      }
      // tm_txt_abstract?
      if ( $this->tm_txt_abstract and $prefix=='txt' ) {
        $this->tm_select[$key] = 'SUBSTRING(`'.$this->settings['table'].'`.`'.$field.'`,1,'.$this->tm_txt_abstract.') AS `'.$field.'`';
      }
      // Onzichtbare wachtwoorden
      if ( $this->tm_hidden_passwords and in_array($prefix,array('gpw','pwd'))) {
        if (is_array($this->tm_hidden_passwords) and in_array($field,$this->tm_hidden_passwords)) {
          $this->tm_select[$key] = 'SPACE(0) AS `'.$field.'`';
        }
        else {
          $this->tm_hidden_passwords;
        }
      }
    }
    return $this;
  }
  
  

  /**
   * Selecteert abstract fields
   *
   * @param bool $flat [FALSE] Als true dan worden de rijen in de result_array geen arrays van een row, maar alleen de abstract value.
   * @return $this
   * @author Jan den Besten
   */
  public function select_abstract( $flat = FALSE ) {
    $this->tm_select[] = $this->get_compiled_abstract_select();
    $this->tm_flat_abstracts = $flat;
    return $this;
  }
  
  /**
   * Veranderd all txt velden tot een string met een maximale lengte, zonder html tags en zonder linebreaks.
   *
   * @param mixed $txt_abstract [TRUE] FALSE = geen aanpassingen, TRUE = standaard aanpassingen, int = lengte bepalen
   * @return $this
   * @author Jan den Besten
   */
  public function select_txt_abstract( $txt_abstract = TRUE ) {
    if ( $txt_abstract===TRUE or strtolower($txt_abstract)==='true') $txt_abstract = 100;
    $this->tm_txt_abstract = $txt_abstract;
    return $this;
  }
  
  /**
   * Verander alle wachtwoord velden (pwd_.. en gpw_..) in onzichtbare velden: het resultaat is een lege string.
   *
   * @param mixed $hidden_passwords [boolean of string van wachtwoord veld of array van wachtwoord velden]
   * @return $this
   * @author Jan den Besten
   */
  public function select_hidden_password( $hidden_passwords = TRUE ) {
    if (is_string($hidden_passwords)) $hidden_passwords = array($hidden_passwords);
    if (is_array($this->tm_hidden_passwords) and is_array($hidden_passwords)) {
      $this->tm_hidden_passwords = array_merge($this->tm_hidden_passwords,$hidden_passwords);
    }
    else {
      $this->tm_hidden_passwords = $hidden_passwords;
    }
    return $this;
  }
  
  
  /**
   * Selecteert een veld waarvan de waarde een samengevoegde string is van alle waarden in een pad van een tree table.
   * Een tree table is een tabel met rijen die in een boomstructuur aan elkaar gekoppeld zijn, bijvoorbeeld een menu.
   * Een tree table bevat altijd de velden order en self_parent
   * 
   * Voorbeeld:
   * 
   * ->tree( 'uri' )
   * 
   * Een andere optie is om het originele veld te behouden en een extra veld toe te voegen met het hele pad.
   * In het voorbeeld hieronder zal het veld 'tree' worden toegevoegd en dezefde waarden hebben als het veld 'uri' in het voorbeeld hierboven.
   * 
   * ->tree( 'tree', 'uri' );
   * 
   * NB Kan alleen gebruikt worden in combinate met ->get_result() en varianten.
   * NB2 In combinatie met een ->where() statement kan het zijn dat de resultaten niet compleet zijn omdat rijen kunnen ontbreken die een tak in een tree zijn.
   *
   * @param string $tree_field Het veld wat een pad moet worden
   * @param string $original_field [''] Je kunt bij bij $tree_field ook een andere naam geven voor het pad, en hier de naam van het originele veld.
   * @param string $split ['/'] Eventueel kan een andere string worden meegegeven die tussen de diverse paden in komt.
   * @return $this
   * @author Jan den Besten
   */
  public function tree( $tree_field, $original_field = '', $split = '/' ) {
    if ( !$this->field_exists('order') and !$this->field_exists('self_parent') ) {
      $this->reset();
      throw new ErrorException( __CLASS__.'->'.__METHOD__.'() table is not a tree table. (tables whith the fields `order` and `self_parent`)' );
      return $this;
    }
    if (empty($original_field)) $original_field = $tree_field;
    $this->tm_tree[$original_field] = array(
      'tree_field'      => $tree_field,
      'original_field'  => $original_field,
      'split'           => $split
    );
    return $this;
  }
  
  /**
   * Speciaal where method voor het zoeken in tree velden. Kan alleen in combinatie met ->get_result() en ->tree()
   * 
   * NB Dit gebeurt niet met de database, maar wordt aan het eind van een volledige result nog gefilterd:
   * - Het is daarom niet erg snel.
   * - Het wijkt af van normale ->where methoden.
   * - Gebruik dit alleen als het echt niet anders kan (bij Menu structuren bijvoorbeeld, die zijn niet zo groot)
   *
   * @param string $field 
   * @param mixed $value 
   * @return $this
   * @author Jan den Besten
   */
  public function where_tree( $field, $value ) {
    $this->tm_where_tree[$field]=array(
      'field' => $field,
      'value' => $value,
    );
    return $this;
  }
  

  /**
   * Zelfde als QueryBuilder from().
   * Met dit verschil, FROM deel kan eventueel aangepast worden als dat nodig is voor relaties in combinatie met LIMIT
   *
   * @param string $from 
   * @return $this
   * @author Jan den Besten
   */
  public function from( $from ) {
    $this->tm_from = $from;
    return $this;
  }
  
  /**
   * Bouwt het FROM deel van de query op
   *
   * @return void
   * @author Jan den Besten
   */
  private function _from() {
    // Als geen expliciete FROM (meestal) bouw die dan op
    if ( empty($this->tm_from) ) {
      
      // Default is de ingestelde tabel
      $this->tm_from = $this->settings['table'];
      
      // Als 'one_to_many' of 'many_to_many' relatie, maak dan een subselect met gevraagde LIMIT en ORDER
      if ( isset($this->tm_with['one_to_many']) or isset($this->tm_with['many_to_many']) ) {
        // table
        $table = $this->settings['table'];
        // als WHERE en LIMIT en één relatie die niet JSON is, dan een Exception
        $sql = $this->db->get_compiled_select('',FALSE);
        $has_where = has_string('WHERE',$sql);
        // Geen exception als de WHERE alleen op id zoekt en limit=1 (->get_row())
        if ($has_where AND $this->tm_limit==1 AND has_string('WHERE `'.$this->settings['table'].'`.`'.$this->settings['primary_key'].'`',$sql) ) {
          $this->tm_limit=0;
        }
        if ($has_where AND $this->tm_limit>0) {
          $json = TRUE;
          foreach ($this->tm_with as $type => $with) {
            if ($type==='one_to_many' or $type==='many_to_many') {
              foreach ($with as $what => $relation) {
                $json = ($json && $relation['json']);
              }
            }
          }
          if (!$json) throw new Exception( __CLASS__.": The combination of a '...to_many' result, LIMIT and WHERE gives unexpected (numbers of) results. Try using ->with_json().");
        }
        // ORDER BY ?
        $order_on_self = TRUE;
        reset($this->tm_order_by);
        $order_by = current($this->tm_order_by);
        $order_by = explode(' ',$order_by);
        // Bestaat het order veld? Zo niet pak gewoon de primary_key
        if (!$this->field_exists($order_by[0])) {
          $order_on_self = FALSE; 
          $order_by = array($this->settings['primary_key'],'');
        }
        // Compile the subquery:
        $this->tm_from = '(SELECT * FROM '.$this->db->protect_identifiers($table);
        if (!empty($where)) $this->tm_from.= ' WHERE ('.$where.') ';
        $this->tm_from .= ' ORDER BY '.$this->db->protect_identifiers($order_by[0]).' '.el(1,$order_by,'');
        
        // Limit in subquery alleen als de volgorde géén invloed heeft op resultaat. (met limit is wel sneller)
        if ( $order_on_self AND !$has_where AND $this->tm_limit>0) {
          if ($this->tm_offset===FALSE) $this->tm_offset=0;
          $this->tm_where_limit = $this->tm_limit;
          $this->tm_where_offset = $this->tm_offset;
          $this->tm_from .= ' LIMIT '.$this->tm_offset.','.$this->tm_limit;
          $this->tm_limit = 0;
          $this->tm_offset = 0;
        }
        $this->tm_from .= ') AS '.$this->db->protect_identifiers($table).'';
      }
      
    }
    return $this->db->from( $this->tm_from );
  }
  
  

  /**
   * Zorgt ervoor dat alleen de rijen van de ingestelde of meegegeven user worden teruggegeven
   *
   * @param int [$user_id] Als dit niet expliciet wordt meegegeven wordt de ingestelde user gebruikt ($this->set_user_id()) 
   * @return $this
   * @author Jan den Besten
   */
  public function where_user( $user_id=NULL ) {
    if (is_null($user_id)) $user_id = $this->user_id;
		if ( $this->field_exists('user') ) {
      $this->where( $this->settings['table'].'.user', $user_id );
      $this->unselect( 'user' );
		}
    return $this;
  }
  
  


  /**
   * Zelfde als 'where' van Query Builder, met deze uitbreidingen:
   * - Je kunt als enig argument de primary_key meegeven of de strings 'first'
   * - Als $value een array is wordt 'where_in' aangeroepen.
   * - Je kunt ook where statements voor relaties aangeven.
   * 
   * primary_key ea
   * --------------
   * ->where( 2 );        // Zoekt naar het resultaat met de primary_key 2
   * ->where( 'first' );  // Zoekt naar het eerste resultaat
   * 
   * 
   * many_to_one
   * -----------
   * ->where( 'tbl_links.str_title', 'test' );    // Zoekt het resultaat op het veld 'str_title' uit de many_to_one relatie met tbl_links.
   * 
   * NB Als er meerdere many_to_one relaties zijn naar dezelfde tabel is de naamgeving anders.
   * 
   * many_to_many
   * ------------
   * ->where( 'tbl_links.str_title', 'text' );    // Zoekt het resultaat op het veld 'str_title' uit de many_to_many relatie met tbl_links.
   * ->where( 'tbl_links.id', 3 );                // idem op 'id'
   * 
   * 
   * LET OP: Bovenstaand many_to_many voorbeelden zijn snel, maar geven many_to_many data die voldoet aan het where statement.
   * Als je wilt zoeken, maar wel de complete many_to_many data voor een bepaald item gebruik dan ->where_exists() of ->like_exists()
   *
   * @param string $key 
   * @param mixed $value [NULL]
   * @param mixed $escape [NULL]
   * @return $this
   * @author Jan den Besten
   */
	public function where($key, $value = NULL, $escape = NULL) {
    $this->_where($key,$value,$escape,'AND');
    return $this;
  }


  /**
   * Zelfd als 'where' maar dan OR
   *
   * @param string $key 
   * @param string $value[NULL] 
   * @param string $escape[NULL]
   * @return $this
   * @author Jan den Besten
   */
	public function or_where($key, $value = NULL, $escape = NULL) {
    $this->_where($key,$value,$escape,'OR');
    return $this;
  }

  /**
   * Maakt where en or_where
   *
   * @param string $key 
   * @param string $value 
   * @param string $escape 
   * @param string $type 
   * @return $this
   * @author Jan den Besten
   */
  private function _where( $key, $value=NULL, $escape = NULL, $type = 'AND') {
    // Onthou dat er een conditie in de query zit
    $this->tm_has_condition = TRUE;
    
    $this->tm_where_primary_key = NULL;
    // Als value een array is, dan ->where_in()
    if (isset($value) and is_array($value)) {
      if ($type=='AND')
        $this->db->where_in($key,$value,$escape);
      else
        $this->db->or_where_in($key,$value,$escape);
      return $this;
    }
    
    // Als geen value maar alleen een key (die geen array is), dat wordt alleen op primary_key gevraagd als het een nummer is
    if (!isset($value) and !is_array($key)) {
      // 'first'
      if ($key==='first') {
        unset($key);
        unset($value);
        $this->limit( 1 );
      }
      // primary_key als nummer
      elseif (is_numeric($key)) {
        $value = $key;
        $key = $this->settings['table'].'.'.$this->settings['primary_key'];
        $this->tm_where_primary_key = $value;
      }
    }
    // where
    if (isset($key)) {
      if ($type=='AND')
        $this->db->where($key,$value);
      else
        $this->db->or_where($key,$value);
    }
    return $this;
  }
  


  /**
   * where_exists zoekt in many_to_many data en toont data waarbinnen de zoekcriteria voldoet maar met de complete many_to_many subdata.
   * In tegenstelling tot where() waar bij zoeken in 'many_to_many' alleen de subdate worden meegegeven die aan de zoekcriteria voldoen.
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
    return $this->_exists( $key, $value, FALSE, 'AND');
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
    return $this->_exists( $key, $value, FALSE, 'OR');
  }
  
  
  /**
   * like_exists zoekt in many_to_many data en toont data waarbinnen de zoekcriteria voldoet maar met de complete many_to_many subdata.
   * 
   * many_to_many
   * ------------
   * ->like_exists( 'tbl_links.str_title', 'text' );            // Zoekt '%text%' op het in het 'str_title' uit de many_to_many relatie met tbl_links.
   * ->like_exists( 'tbl_links.str_title', 'text', 'before );   // Idem maar dan '%text'
   *
   * @param string $field Moet in het formaat table.field zijn.
   * @param string $match De te zoeken waarde
   * @param string $side [both] [both|before|after|]
   * @return $this
   * @author Jan den Besten
   */
  public function like_exists( $field, $match, $side = 'both' ) {
    return $this->_exists( $field, $match, $side, 'AND' );
  }

  /**
   * Zelfde als like_exists maar dan een OR
   *
   * @param string $field Moet in het formaat table.field zijn.
   * @param string $match De te zoeken waarde
   * @param string $side [both] [both|before|after|]
   * @return $this
   * @author Jan den Besten
   */
  public function or_like_exists( $field, $match, $side = 'both' ) {
    return $this->_exists( $field, $match, $side, 'OR');
  }

  /**
   * _exists of een bepaalde waarde in many_to_many data bestaat, en geeft dan alle many_to_many date terug, en niet allen die waar de waarde in gevonden is.
   * 
   * Bouwt een WHERE of een LIKE sql statement.
   * 
   * WHERE `tbl_menu`.`id` IN (
   * 	SELECT `rel_menu__links`.`id_menu`
   * 	FROM `rel_menu__links`
   * 	WHERE `rel_menu__links`.`id_links` IN (
   * 		SELECT `tbl_links`.`id`
   * 		FROM `tbl_links`
   * 		WHERE `str_title` = "text"   / of / WHERE `str_title` LIKE "%text%"
   * 	)	
   * )
   *
   * @param string $key 
   * @param string $value 
   * @param mixed $side[FALSE] als [both|before|after] dan is het een LIKE
   * @param string $type ['AND'|'OR']
   * @return $this
   * @author Jan den Besten
   */
  protected function _exists( $key, $value = NULL, $side=FALSE, $type = 'AND' ) {
    // trace_(['_exists',$key,$value,$side,$type]);
    
    if ( !isset($this->tm_with['many_to_many'])) {
      $this->reset();
      throw new ErrorException( __CLASS__.'->'.__METHOD__.'(): No `many_to_many` relation set. This is needed when using `..._exists`.' );
      return $this;
    }
    
    $other_table = trim(get_prefix($key,'.'),'` ');
    $key         = trim(get_suffix($key,'.'),'` ');
    if (empty($other_table) or empty($key) or $key==$other_table) {
      $this->reset();
      throw new ErrorException( __CLASS__.'->'.__METHOD__.'(): First argument of `..._exists` needs to be of this format: `table.field`.' );
    }
    
    $relation          = $this->_find_relation_setting_by('many_to_many','other_table',$other_table);
    $id                = $this->settings['primary_key'];
    $this_table        = $this->settings['table'];
    $rel_table         = $relation['rel_table']; //'rel_'.remove_prefix($this_table).'__'.remove_prefix($other_table);
    $this_foreign_key  = $relation['this_key'];//  $id.'_'.remove_prefix($this_table);
    $other_foreign_key = $relation['other_key'];//$id.'_'.remove_prefix($other_table);
    
    $sql = ' `'.$this_table.'`.`'.$id.'` IN (
    	SELECT `'.$rel_table.'`.`'.$this_foreign_key.'`
    	FROM `'.$rel_table.'`
    	WHERE `'.$rel_table.'`.`'.$other_foreign_key.'` IN (
    		SELECT `'.$other_table.'`.`'.$id.'` FROM `'.$other_table.'` WHERE `'.$key.'` ';
    if ($side) {
      // like_exists
      $sql.=' LIKE ';
      switch ($side) {
        case 'both':
          $sql.='"%'.$value.'%"';
          break;
        case 'before':
          $sql.='"%'.$value.'"';
          break;
        case 'after':
          $sql.='"'.$value.'%"';
          break;
      }
    }
    else {
      // where_exists
      $sql.='= "'.$value.'"';
    }
    $sql.='))';
    
    if ($type=='OR')
      $this->db->or_where( $sql, NULL, FALSE );
    else
      $this->db->where( $sql, NULL, FALSE );
    
    // Onthou dat er een conditie in de query zit
    $this->tm_has_condition = TRUE;
    return $this;
  }
  
  
  /**
   * Zoekt de gevraagde zoekterm(en).
   * Bouwt een uitgebreide zoekquery op.
   * 
   * Voorbeelden met diverse termen
   * ------------------------------
   * 
   * ->find( 'zoek' )               // Zoekt naar de letters 'zoek' in alle velden
   * ->find( 'zoek ook')            // Zoekt naar de letters 'zoek' of 'ook'.
   * ->find( array( 'zoek ook' ) )  // idem
   * ->find( '"zoek ook"' )         // Zoekt naar de letters 'zoek ook'      
   * ->find( array( '"zoek ook"' )  // idem
   * 
   * In specifieke velden
   * --------------------
   * 
   * Alle bovenstaande combinaties zijn mogelijk en:
   * 
   * ->find( 'zoek', array( 'str_title' ) )             // Zoekt naar de letters 'zoek' in in het veld 'str_title'
   * ->find( 'zoek', array( 'str_title', 'txt_text ) )  // Zoekt naar de letters 'zoek' in in het veld 'str_title' en 'txt_text'
   * 
   * Specifieke instellingen
   * -----------------------
   * 
   * Er zijn nog diverse instellingen om de zoekfunctie verder te verfijnen:
   * 
   * - 'and'         - ['OR'] Als er meerdere zoekopdrachten worden gegeven kun je hier aangeven of ze AND of OR moeten worden gekoppeld. Default is 'OR' (wat afwijkt van ->where(), maar voor zoeken logischer).
   * - 'equals'      - [like|exact|word] Default [like]. Hiermee kun je aangeven hoe precies er gezocht moet worden:
   *                   - 'like'  - In het veld wordt op een willekeurige plaats de zoekterm te vinden zijn.
   *                   - 'word'  - In het veld moet de zoekterm als afgezonderd geheeld (een woord) worden gezocht.
   *                   - 'exact' - Het veld moet precies hetzelfde zijn als de zoekterm.
   * - 'with'        - [array('many_to_one','one_to_many','many_to_many')] Geef aan welke relaties mee moeten worden genomen.
   * - 'many_exists' - [TRUE] Net als where_exists()
   * 
   * Zoeken in relaties
   * ------------------
   * 
   * Als je wilt dat ook in relaties wordt gezocht, roep dan ook een ->with() variant aan.
   * 
   * - In alle 'many_to_one' relaties wordt gezocht zolang het foreign_key veld in de zoekvelden zit.
   * - Automatisch wordt in alle 'many_to_many' en 'one_to_many' relaties gezocht (zoals bij like_exists())
   * - Je kunt specifieker instellen met $settings['with'] welke relaties mee moeten worden genomen met het zoeken
   * 
   * Verfijnd zoeken
   * ---------------
   * 
   * Je kunt ook verfijnder zoeken door een array mee te geven waarin de zoektermen, velden en settings gespecificeerd zijn.
   * Daarmee kun je termen in specifieke velden zoeken.
   * 
   * Die array ziet er dan zo uit:
   * 
   * array(
   *  array(
   *    'term'    => '',       // Zoekterm (string, of array van strings)
   *    'fields'  => array(),  // Array van velden waarin term gezocht moet worden. Ook result_name's van relaties kunnen meegegeven worden.
   *    'and'     => 'AND|OR'
   *    'equals'  => ['like'|'word'|'exact']
   *  ),
   *  ...
   *  ...
   * )
   * 
   * 
   * @param mixed $terms Zoekterm(en) als een string of array van strings. Letterlijk zoeken kan door termen tussen "" te zetten.
   * @param array $fields [array()] De velden waarop gezocht wordt. Standaard alle velden (behalve id,order,self_parent). Kan ook in relatietabellen zoeken, bijvoorbeeld 'tbl_links.str_title' als een veld in een gerelateerde tabel
   * @param array $settings [array()] Extra instelingen.
   * @return $this
   * @author Jan den Besten
   */
  public function find( $terms, $fields = array(), $settings = array() ) {
    if (empty($terms)) return $this;

    $this->tm_find = array(
      'terms'    => $terms,
      'fields'   => $fields,
      'settings' => $settings,
    );
    
    return $this;
  }
    

  /**
   * Bouw een gehele zoekquery op aan de hand van ingestelde ->tm_find
   *
   * @return void
   * @author Jan den Besten
   */
  private function _find() {
    if (!$this->tm_find) return $this;
    
    $terms    = el('terms',$this->tm_find,'');
    $fields   = el('fields',$this->tm_find,array());
    $settings = el('settings',$this->tm_find,array());
    
    if (empty($terms)) return $this;
    
    // Settings
    $with = el('with',$this->tm_as_grid );
    if (empty($with)) $with = array('many_to_one','one_to_many','many_to_many');
    $defaults = array(
      'and'             => 'OR',
      'equals'          => 'like',
      'with'            => $with,
      'many_exists'     => TRUE,
    );
    $settings = array_merge( $defaults,$settings );
    if (!is_string($settings['and']) or is_numeric($settings['and']) or empty($settings['and'])) {
      if ($settings['and']==TRUE)  $settings['and'] = 'AND';
      if ($settings['and']==FALSE) $settings['and'] = 'OR';
    }
    $settings['and'] = strtoupper( $settings['and'] );

    // De complete zoek array
    $search = array();
    
    // Is het een verfijnde zoekopdracht? Dan zijn we al klaar.
    if (is_array($terms) and is_multi($terms)) {
      $search = $this->_add_result_names_find($terms,$settings);
    }
    // Zo niet, maak van de simpele zoekopdracht een verfijnde zoekopdracht
    else {
      $search = $this->_create_splitted_find($terms,$fields,$settings);
    }
    $this->_create_complete_search( $search );
    return $this;
  }
  
  private function _add_result_names_find($search,$settings) {
    foreach ($search as $key => $find) {
      $fields = $find['field'];
      if (!is_array($fields)) $fields = array($fields);
      // Inclusief result_name van de meegegeven relaties (word later omgezet in velden van die tabel)
      if (is_array($settings['with'])) {
        foreach ($settings['with'] as $type => $with) {
          if (is_string($type)) $with = $type;
          if ($type==='many_to_one') {
            $relations = el(array('relations',$with), $this->settings, FALSE);
            if ($relations) {
              foreach ($relations as $what => $info) {
                if (in_array($info['foreign_key'],$fields)) array_push($fields,$info['result_name']);
              }
            }
          }
        }
      }
      $search[$key]['field'] = $fields;
    };
    return $search;
  }
  
  /**
   * Maak van een eenvoudig zoekopdracht een verfijnde zoekopdracht
   *
   * @param mixed $terms 
   * @param array $fields 
   * @param array $settings 
   * @return array
   * @author Jan den Besten
   */
  private function _create_splitted_find($terms,$fields,$settings) {
    // Welke velden?
    if ( is_string($fields) ) $fields = array($fields);
    // Geen velden meegegeven, gebruik dan alle velden van deze tabel, of van de grid_set (zoals ingesteld).
    if ( empty($fields) ) {
      if ($this->tm_as_grid) $fields = el('fields',$this->tm_as_grid, array() );
      if (empty($fields)) $fields = $this->settings['fields'];
    }
    // Inclusief result_name van de meegegeven relaties (word later omgezet in velden van die tabel)
    if (is_array($settings['with'])) {
      foreach ($settings['with'] as $type => $with) {
        $relation_type = $with;
        if (is_string($type)) $relation_type = $type;
        $relations = el(array('relations',$relation_type), $this->settings, FALSE);
        if (is_array($with)) $relations = array_keep_keys($relations,array_keys($with));
        if ($relations) {
          foreach ($relations as $what => $info) {
            array_push($fields,$info['result_name']);
          }
        }
      }
    }
  
    // Zet om naar (complete) zoekopdracht
    if (!is_array($terms)) $terms=array($terms);
    foreach($terms as $term) {
      $search[] = array(
        'term'        => $terms,
        'field'       => $fields,
        'and'         => $settings['and'],
        'equals'      => $settings['equals'],
        // ??
        'with'        => $settings['with'],
        'many_exists' => $settings['many_exists'],
      );
    }
    return $search;
  }
  
  /**
   * Zet verfijnde zoekopdracht per term om naar SQL
   *
   * @param array $search 
   * @return $this
   * @author Jan den Besten
   */
  private function _create_complete_search( $search ) {
    foreach ( $search as $item) {

      // Splits termen als er meerdere door spaties zijn gescheiden (rekening houdend met quotes)
      $terms = $item['term'];
      if ( is_array($terms) ) $terms = implode(' ',$terms);
      $terms = preg_split('~(?:"[^"]*")?\K[/\s]+~', ' '.$terms.' ', -1, PREG_SPLIT_NO_EMPTY );
      
      $fields = $item['field'];
      if (!is_array($fields)) $fields = array($fields);
      // Sommige velden hoeft nooit in gezocht te worden:
      $fields = array_diff($fields,$this->forbidden_find_fields);
      
      // Verwijder niet bestaande velden, TODO: of vervang ze door de velden uit een relatietabel array('relation'=>'','fields'=>array())
      foreach ($fields as $key => $field) {
        if (!in_array($field,$this->settings['fields'])) {
          if ( !$this->_is_result_name($field,$this->tm_with) ) {
            unset($fields[$key]);
          }
          else {
            $fields[$key] = $this->_get_relation_result($field,$this->tm_with);
            if ($fields[$key]) {
              $fields[$key]['fields'] = array_diff($fields[$key]['fields'],$this->forbidden_find_fields);
              $fields[$key]['fields'] = array_diff($fields[$key]['fields'],$this->forbidden_find_relation_fields);
            }
            else {
              unset($fields[$key]);
            }
          }
        }
      }
      
      // Plak tabelnaam voor elk veld, als dat nog niet zo is, en escape
      foreach ( $fields as $key => $field ) {
        if (is_array($field)) {
          foreach ($field['fields'] as $k=>$other_field) {
            $fields[$key]['fields'][$k] = $this->_protect_field($other_field,$field['other_table']);
          }
        }
        else {
          $fields[$key] = $this->_protect_field($field,$this->settings['table']);
        }
      }
      
      // Zoek in alle termen
      foreach ($terms as $term) {
        // Begin van deze term
        if ($item['and']==='AND') {
          $this->db->group_start();
        }
        else {
          $this->db->or_group_start();
        }
        // Zoek in de term
        $this->_find_term( $term, $fields, $item['equals'], el('many_exists',$item,TRUE));
        // Einde van deze term
        $this->db->group_end();
      }
      
    }
  }
  
  private function _protect_field($field,$table='') {
    if (strpos($field,'.')===FALSE and isset($table)) $field = $table.'.'.$field;
    return $this->db->protect_identifiers($field);
  }
  
  
  /**
   * Bouw de query van een zoekterm op
   *
   * @param array $term
   * @param arrat $fields 
   * @param array $settings 
   * @return void
   * @author Jan den Besten
   */
  private function _find_term( $term, $fields = array(), $equals='like', $many_exists=TRUE ) {
    // Schoon term wat op (geen quotes en spaties)
    $term = trim($term,"\"' ");
    
    // Per veld:
    foreach ($fields as $sub_fields) {
      $relation = FALSE;
      if (is_array($sub_fields)) {
        $relation   = $sub_fields['relation'];
        $sub_fields = $sub_fields['fields'];
      }
      else {
        $sub_fields = array($sub_fields);
      }
      
      foreach ($sub_fields as $field) {
        
        // many_to_many exists...
        if ( $many_exists AND $relation==='many_to_many') {
          switch ($equals) {
            case 'exact':
              $this->or_where_exists( $field, $term );
              break;
            case 'word':
            case 'like':
            default:
              $this->or_like_exists( $field, $term, 'both' );
              break;
          }
        }

        // Normaal
        else {
          switch ($equals) {
            case 'exact': 
              $this->or_where( $field, $term, FALSE);
              break;
            case 'word':
              $this->db->or_where( $field.' REGEXP \'[[:<:]]'.$term.'[[:>:]]\'', NULL, FALSE);
              break;
            case 'like':
            default:
              $this->db->or_like( $field, $term, 'both', FALSE);
              break;
          }
        }
      }
    }
    
    return $this;
  }
  
  



  /**
   * Geef aan welke relaties meegenomen moeten worden in het resultaat, en hoe.
   * Deze method kan vaker achter elkaar worden aangeroepen.
   * 
   * NB Alleen de relaties die bekend zijn in settings['relations'] worden meegenomen.
   * 
   * Voorbeelden zijn te vinden in /_admin/test/relations (NB als de 'flexyadmin_test' is geselecteerd)
   * 
   * Reset alle relaties:
   * (wordt automatisch aangeroepen na iedere ->get() variant)
   * 
   * ->with( FALSE );
   * 
   * 
   * one_to_one
   * ----------
   * 
   * Wordt niet vaak gebruikt. Maar in sommige gevallen toch handig om data van een tabel in meerdere tabellen te splitsen.
   * Werkt hetzelfde als many_to_one.
   * 
   * ->with( 'one_to_one' );
   * 
   * one_to_one resultaat
   * --------------------
   * 
   * De velden uit de extra tabel krijgen de naam: 'extra_tabel.veld' om vewarringen te voorkomen.
   * Bij ->get_result() varianten wordt niet een mogelijk niet bestaande rij uit de extra tabel vervangen door default data.
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
   * Specificeer welke relatietabellen mee moeten worden genomen in het resultaat (als er meerdere foreign_keys verwijzen naar dezelfde tabel, dan worden ze allemaal toegevoegd):
   * 
   * ->with( 'many_to_one', [ 'id_posts' ] );
   * ->with( 'many_to_one', [ 'id_posts', 'id_links' ] );
   * 
   * Specificeer per tabel welke velden meegenomen moeten worden in het resultaat:
   * 
   * ->with( 'many_to_one', [ 'id_posts' => 'str_title,txt_text' ] );
   * ->with( 'many_to_one', [ 'id_posts' => ['str_title','txt_text'] ] );
   * ->with( 'many_to_one', [ 'id_posts' => 'str_title,txt_text', 'tbl_links' ] );
   * 
   * many_to_one resultaat
   * ---------------------
   * 
   * Bij ->get() varianten krijgen de resultaat arrays/objects extra velden, bijvoorbeeld:
   * - tbl_posts.str_title
   * - tbl_posts.txt_text
   * 
   * Bij ->get_result() varianten krijgen de resultaat arrays extra velden met de data in een array, bijvoorbeeld:
   * - tbl__posts => array( ... en hier alle gevraagde velden van de foreign table ... )
   * 
   * NB. Als er meerdere many_to_one verwijzingen zijn naar dezelfde tabel, dan wordt het resultaat anders.
   * 
   * 
   * many_to_one abstract
   * --------------------
   * 
   * Een abstract resultaat is dat een deel van de velden van de andere tabel wordt samengevoegd tot een nieuw veld. Een samenvatting.
   * 
   * Zo worden alle 'many_to_one' relaties toegevoegd als abstract:
   * 
   * ->with( 'many_to_one', 'abstract' );
   * 
   * En zo kun je dat per relatie aanpassen:
   * 
   * ->with( 'many_to_one', [ 'id_posts' => 'abstract ] );
   * 
   * Het resultaat komt in een extra veld: tbl_posts.abstract
   * 
   * 
   * many_to_one flat
   * ----------------
   * 
   * Zorgt ervoor dat het resultaat van een ->get_result() en varrianten niet anders is dan die van ->get() varianten.
   * Dus het resultaat komt niet in een array.
   * 
   * 
   * one_to_many
   * -----------
   * 
   * Voegt alle one_to_many relaties met al hun velden toe aan het resultaat:
   * 
   * ->with( 'one_to_many' );
   * ->with( 'one_to_many', [] );
   * 
   * Specificeer welke tabellen en welke van hun velden worden meegenomen in het resultaat:
   * 
   * ->with( 'one_to_many', ['tbl_posts'] );
   * ->with( 'one_to_many', ['tbl_posts'=>['str_title','txt_text]] );
   * 
   * Geef aan dat de velden een abstract moeten zijn in het resultaat:
   * 
   * ->with( 'one_to_many', 'abstract' );
   * ->with( 'one_to_many', ['tbl_posts'=>'abstract'] );
   * 
   * one_to_many resultaat
   * ---------------------
   * 
   * Het resultaat van een one_to_many relatie wordt net als bij many_to_one relaties toegevoegd als extra velden van de andere tabel. Op dezelfde manier als bij many_to_one:
   * - tbl_posts.....
   * 
   * 
   * many_to_many
   * ------------
   * 
   * Voegt alle many_to_many relaties met al hun velden toe aan resultaat:
   * 
   * ->with( 'many_to_many' );
   * ->with( 'many_to_many', [] );
   * 
   * Specificeer welke relatietabellen mee moeten worden meegenomen in het resultaat:
   * 
   * ->with( 'many_to_many', [ 'rel_menu__posts' ] );
   * ->with( 'many_to_many', [ 'rel_menu__posts', 'rel_menu__links' ] );
   * 
   * Specificeer per tabel welke velden meegenomen moeten worden in het resultaat:
   * 
   * ->with( 'many_to_many', [ 'rel_menu__posts' => 'str_title,txt_text' ] );
   * ->with( 'many_to_many', [ 'rel_menu__posts' => ['str_title','txt_text'] ] );
   * ->with( 'many_to_many', [ 'rel_menu__posts' => 'str_title,txt_text', 'rel_menu__links' ] );
   * 
   * Geef aan dat bij een tabel een abstract van de velden moet worden meegenomen in plaats van specifieke velden:
   * 
   * ->with( 'many_to_many', [ 'rel_menu__posts' => 'abstract ] );
   * 
   * 
   * many_to_many resultaat
   * ----------------------
   * 
   * Het resultaat van een many_to_many relatie wordt net als bij many_to_one relaties toegevoegd als extra velden van de andere tabel. Op dezelfde manier als bij many_to_one:
   * - tbl_posts.....
   * 
   * json
   * ----
   * 
   * Hiermee wordt de relatie data in één JSON string gestopt:
   * Voor many_to_one:
   * - tbl_posts.json => { "id":4, "str_title":"titel", "txt_text": "tekst" }
   * Voor many_to_many:
   * - tbl_posts.json => { "4": { "id":4, "str_title":"titel", "txt_text": "tekst" }, "18" { "id":18, "str_title":"test", "txt_text": "lorum" }, ... etc... }
   * 
   * 
   * LET OP: ->num_rows() bij 'one_to_many' en 'many_to_many'
   * --------------------------------------------------------
   * 
   * Bij 'one_to_many' en 'many_to_many' relaties:
   * 
   * - Kan ->get()->result_array() méér rijen als resultaat geven dan het gevraagde aantal wat met ->limit() is ingesteld.
   * - Dat is omdat voor elke relatie-rij een extra rij is toegevoegd.
   * - Dit kan voorkomen worden door de relatie data 'json' bij te voegen (zie hierboven)
   * 
   * - Bij ->get_result() worden deze relatie rijen samengevoegd en klopt het aantal rijen wél met de ingestelde ->limit().
   * 
   * 
   * @param string $type De soort relatie ['many_to_one'|'many_to_many']
   * @param array  $what Een array van welke relaties meegenomen moeten worden bij deze relatie-vorm.
   *                      - Als deze paramater niet wordt meegegeven worden automatisch alle relaties gezocht en meegenomen met al hun velden
   *                      - Voor 'many_to_one' relaties kun je een array meegeven van foreign_keys
   *                      - Voor 'many_to_many' een array van relatie tabellen.
   *                      - Eventueel kun je per relatie de velden of 'abstract' meegeven
   * @param bool $json [FALSE] bepaalt of een resultaat gegroupeerd moet worden op rij niveau. (zie ->with_json())
   * @param bool $flat [FALSE] bepaalt of een 'many_to_one' resultaat plat moet worden geintegreerd. (zie ->with_flat())
   * @return $this
   * @author Jan den Besten
   */
  public function with( $type='', $what=array(), $json=FALSE, $flat=FALSE ) {
    // Reset?
    if ($type===FALSE or empty($type)) {
      $this->tm_with = array();
      return $this;
    }
    
    // Bestaat relatie wel?
    if (! el( array('relations',$type), $this->settings)) return $this;

    // Als geen $what is meegegeven, haal ze uit de settings
    $abstract = ($what==='abstract');
    if ( empty($what) or $abstract) {
      $what = el( array('relations',$type), $this->settings, array() );
      if ($what) $what = array_keys($what);
    }
    // $what moet een array zijn
    if ( ! is_array($what) ) $what=array($what);
    
    // Zorg ervoor dat $what in dit formaat komt: '$what' => array( 'table'=>'', 'fields'=>'' )
    $what_new = array();
    foreach ($what as $key => $value) {
      // Als de velden expliciet zijn meegegeven zit dat in $value
      $what   = $key;
      $fields = $value;
      // Als de velden niet expliciet zijn meegegeven dan is $value $what
      if ( is_integer($key) ) {
        $what   = $value;
        $fields = array();
      }
      if ($abstract) $fields='abstract';
      // bij 'many_to_many' is $what hetzelfde als de tabel, bij 'many_to_one' moet dat uit de relaties settings worden gehaald
      $table = $what;
      if ($type==='many_to_one') $table = $this->settings['relations'][$type][$what]['other_table'];
      // fields moet een (lege) array of een string ('abstract') zijn.
      if (is_string($fields) and $fields!==$this->config->item('ABSTRACT_field_name')) {
        $fields = explode( ',',$fields );
      }
      // Als fields een lege array is, stop dan alle velden van die tabel erin
      if (is_array($fields) and empty($fields)) {
        if ($type=='one_to_one')   $fields = $this->get_other_table_fields( $this->settings['relations']['one_to_one'][$what]['other_table'] );
        if ($type=='many_to_one')  $fields = $this->get_other_table_fields( $table );
        if ($type=='one_to_many')  $fields = $this->get_other_table_fields( $this->settings['relations']['one_to_many'][$what]['other_table'] );
        if ($type=='many_to_many') $fields = $this->get_other_table_fields( $this->settings['relations']['many_to_many'][$what]['other_table'] );
      }
      // fields moet iig ook de primary key bevatten (behalve bij one_to_one)
      if ($type!=='one_to_one') {
        $other_primary_key = $this->get_other_table_setting( $table,'primary_key', PRIMARY_KEY );
        if ( is_array($fields) AND !in_array($other_primary_key,$fields) ) array_unshift($fields,$other_primary_key);
      }
      // Bewaar
      $what_new[$what] = array(
        'table'   => $table,
        'fields'  => $fields,
      );
      if ($type==='many_to_one') {
        $what_new[$what]['as'] = $this->settings['relations']['many_to_one'][$what]['result_name'];
      }
    }
    
    // Merge met bestaande
    $tm_with_before = el( $type, $this->tm_with, array() );
    $tm_with_new    = array();
    foreach ($what_new as $what => $value) {
      $tm_with_new[$what] = array(
        'table'   => $value['table'],
        'fields'  => $value['fields'],
        'json' => $json,
      );
      if ($type!=='one_to_one') {
        $tm_with_new[$what]['as'] = el('as',$value, $this->settings['relations'][$type][$what]['result_name'] );
      }
      if ($type=='many_to_one') {
        $tm_with_new[$what]['flat'] = $flat;
      }
    }
    $tm_with_new = array_replace_recursive( $tm_with_before, $tm_with_new );
    // Bewaar deze relatie instelling
    $this->tm_with[$type] = $tm_with_new;
    // trace_([$this->settings['table'],$this->tm_with]);
    return $this;
  }
  
  
  /**
   * Geeft aan welke many_to_one relaties hetzelfde moeten blijven als bij ->get() varianten
   * Zie ook bij ->with()
   *
   * @param string $forein_keys 
   * @return $this
   * @author Jan den Besten
   */
  public function with_flat_many_to_one( $forein_keys = array() ) {
    return $this->with( 'many_to_one', $forein_keys, FALSE, TRUE );
  }
  
  
  /**
   * Geef aan welke relaties in één JSON veld moeten worden meegenomen
   * Zie ook bij ->with()
   *
   * @param string $type ['many_to_many'] 
   * @param array $what [array()]
   * @return $this
   * @author Jan den Besten
   */
  public function with_json( $type='many_to_many', $what=array() ) {
    return $this->with( $type, $what, TRUE);
  }
  


  /**
   * Bouwt de query op voor relaties, roept voor elke soort relatie een eigen method aan.
   *
   * @return $this
   * @author Jan den Besten
   */
  protected function _with() {
    if (!empty($this->tm_with)) {
      foreach ( $this->tm_with as $type => $what ) {
        $method = '_with_'.$type;
        if ( method_exists( $this, $method ) ) {
          $this->$method( $what );
        }
        else {
          $this->reset();
          throw new ErrorException( __CLASS__.'->'.__METHOD__.'() does not exists. The `'.$type.'` relation could not be included in the result.' );
        }
      }
    }
    
    return $this;
  }
  
  
  /**
   * Bouwt one_to_one relatie op
   *
   * @author Jan den Besten
   */
  protected function _with_one_to_one( $what ) {
    $id = $this->settings['primary_key'];
    foreach ($what as $key => $info) {
      $fields      = $info['fields'];
      $json        = el('json',$info,false);
      $other_table = $this->settings['relations']['one_to_one'][$key]['other_table'];
      $as          = el('as',$info, $other_table);
      // Select fields
      $this->_select_with_fields( 'one_to_one', $other_table, $as, $fields, $id, $json );
      // Join
      $this->join( $other_table.' AS '.$as, $as.'.'.$id.' = '.$this->settings['table'].".".$id, 'left');
    }
    return $this;
  }
  


  /**
   * Bouwt many_to_one join query
   *
   * @param array $what 
   * @return $this
   * @author Jan den Besten
   */
  protected function _with_many_to_one( $what ) {
    $id = $this->settings['primary_key'];
    foreach ($what as $key => $info) {
      $fields      = $info['fields'];
      $json        = el('json',$info,false);
      $foreign_key = $this->settings['relations']['many_to_one'][$key]['foreign_key'];
      $other_table = $this->settings['relations']['many_to_one'][$key]['other_table'];
      $as          = el('as',$info, $other_table);
      // Select fields
      $this->_select_with_fields( 'many_to_one', $other_table, $as, $fields, $foreign_key, $json );
      // Join
      $this->join( $other_table.' AS '.$as, $as.'.'.$id.' = '.$this->settings['table'].".".$foreign_key, 'left');
    }
    return $this;
  }
  
  
  /**
   * Bouwt one_to_many join query
   *
   * @param array $what 
   * @return $this
   * @author Jan den Besten
   */
  protected function _with_one_to_many( $what ) {
    $id = $this->settings['primary_key'];
    foreach ($what as $key => $info) {
      $fields      = $info['fields'];
      $json        = el('json',$info,false);
      $foreign_key = $this->settings['relations']['one_to_many'][$key]['foreign_key'];
      $other_table = $this->settings['relations']['one_to_many'][$key]['other_table'];
      $as          = $this->settings['relations']['one_to_many'][$key]['result_name'];
      // Select fields
      $this->_select_with_fields( 'one_to_many', $other_table, $as, $fields, $foreign_key, $json );
      // Join
      $this->join( $other_table.' AS '.$as, $as.'.'.$foreign_key.' = '.$this->settings['table'].".".$id, 'left');
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
    $id = $this->settings['primary_key'];
    foreach ( $tables as $what => $info ) {
      $fields   = $info['fields'];
      $json  = $info['json'];
      $rel_table         = $this->settings['relations']['many_to_many'][$what]['rel_table'];
      $this_table        = $this->settings['relations']['many_to_many'][$what]['this_table'];
      $other_table       = $this->settings['relations']['many_to_many'][$what]['other_table'];
      $this_foreign_key  = $this->settings['relations']['many_to_many'][$what]['this_key'];
      $other_foreign_key = $this->settings['relations']['many_to_many'][$what]['other_key'];
      $as                = $this->settings['relations']['many_to_many'][$what]['result_name'];
      // $sub_as            = '_'.$as.'_';
      $sub_as            = $rel_table;
      // Select fields
      $this->_select_with_fields( 'many_to_many', $other_table, $as, $fields, '', $json );
      // Joins
      $this->join( $rel_table.' AS '.$sub_as, $this_table.'.'.$id.' = '.$sub_as.".".$this_foreign_key, 'left');
      $this->join( $other_table.' AS '.$as,   $sub_as.'.'.$other_foreign_key.' = '.$as.".".$id, 'left');
    }
    return $this;
  }
  
  
  /**
   * Selecteerd de velden die bij SELECT moeten komen bij relaties
   *
   * @param string $type het soort relatie
   * @param string $other_table de gerelateerde tabel
   * @param string $as_table naamgeving
   * @param array $fields velden van de gerelateerde tabel
   * @param string $foreign_key eventuele foreignkey als many_to_one
   * @param bool $json of de many_to_many data gegroupeerd worden in één veld met de naam van de relatie tabel
   * @return $this
   * @author Jan den Besten
   */
  protected function _select_with_fields( $type, $other_table, $as_table, $fields, $foreign_key='', $json = FALSE ) {
    $abstract = FALSE;
    $select   = '';
    
    // Welke velden van de gerelateerde tabel?
    if ( empty($fields) ) {
      $fields = $this->get_other_table_fields( $other_table );
    }
    elseif ( $fields === 'abstract' ) {
      $abstract_fields = $this->get_other_table_abstract_fields( $other_table );
      $abstract = $this->get_compiled_abstract_select( $other_table, $abstract_fields, $as_table );
      $other_table_order = $this->get_other_table_setting($other_table,'order_by');
      if (!is_array($other_table_order)) $other_table_order = explode(',',$other_table_order);
      $other_table_order = current($other_table_order);
      $abstract_order = $this->db->protect_identifiers(  $as_table.'.'.$other_table_order );
      $abstract_order = str_replace(array('`ASC`','`DESC`'),array('ASC','DESC'),$abstract_order);
    }
    
    //
    // SELECT abstract
    //
    if ($abstract) {
      $select = $abstract;
      if ($json) {
        $abstract = remove_suffix($abstract,' AS ');
        if (!isset($abstract_order)) $abstract_order = $abstract;
        $select = 'GROUP_CONCAT( DISTINCT "{",'.$abstract.',"}" ORDER BY '.$abstract_order.' SEPARATOR ", ") `'.$as_table.'`';
      }
      else {
        // Als geen JSON, voeg dan ook de primary_key erbij (behalve bij many_to_one, daar is die al bekend)
        if ($type!=='many_to_one' and $type!=='one_to_one') {
          $other_primary_key = $this->get_other_table_setting( $other_table, 'primary_key', PRIMARY_KEY);
          $select = '`'.$as_table.'`.`'.$other_primary_key.'` AS `'.$as_table.'.'.$other_primary_key.'`, '.$select;
        }
      }
    }
    
    //
    // SELECT anderen
    //
    else {
      // primary_key hoeft er niet in
      if ( $key=array_search( $this->settings['primary_key'], $fields ) ) {
        unset($fields[$key]);
      }
      // Verzamel de velden
      $select_fields = array();
      foreach ($fields as $field) {
        $field_type = get_prefix($field);
        $select_fields[$field] = array(
          'type'        => $field_type,
          'add_slashes' => !in_array($field_type, $this->config->item('FIELDS_number_fields')) and !in_array($field_type, $this->config->item('FIELDS_bool_fields')),
          'field'       => $field,
          'select'      => '`' . $as_table . '`.`'.$field.'`',
          // 'select'      => '`' . ( $type==='many_to_many' ? $other_table : $as_table) . '`.`'.$field.'`',
        );
      }
      
      // SELECT normaal
      if (!$json) {
        foreach ($select_fields as $field => $select_field) {
          $select .= $select_field['select'].' AS `'.$as_table.'.'.$field.'`, ';
        }
        $select = trim($select,',');
      }
      
      // SELECT grouped JSON
      else {
        $this->db->simple_query('SET SESSION group_concat_max_len=1048576'); // (1mb) Zorg ervoor dat het resultaat van GROUP_CONCAT lang genoeg is
        $last_slashes = FALSE;
        foreach ($select_fields as $field => $select_field) {
          $last_slashes = $select_field['add_slashes'];
          // numbers/booleans etc
          if ( !$select_field['add_slashes'] ) {
            $select .= '"\"'.$field.'\":", '  .$select_field['select'].', ",", ';
          }
          else {
            $select .= '"\"'.$field.'\":\"", '.$select_field['select'].', "\",", ';
          }
        }
        // remove last ","
        if ($last_slashes) {
          $select = substr($select,0,strlen($select)-9);
          $select .= ', "\""';
        }
        else {
          $select = substr($select,0,strlen($select)-7);
          $select .= ', ""';
        }
        // ready
        $select = 'CONCAT( "{", IFNULL( GROUP_CONCAT( "\"",'.$select_fields['id']['select'].',"\":{", '.$select.', "}" SEPARATOR ", "), ""), "}" ) `'.$as_table.'.json`';
      }
    }
    $select = trim(trim($select),',');
    
    // Stop select in query, als het kan direct na foreign_key
    if (isset($foreign_key) and isset($this->tm_select[$foreign_key]) and $type!=='one_to_one') {
      $this->tm_select = array_add_after( $this->tm_select, $foreign_key, array($as_table=>$select) );
    }
    else {
      $this->tm_select[$as_table] = $select;
    }

    // json?
    if ($json) $this->db->group_by( $this->settings['table'].'.'.$this->settings['primary_key'] );
    
    return $this;
  }
  
  
  
  
  /**
   * Zelfde als Query Builder, met deze verschillen:
   * - Als order_by() niet specifiek wordt aangeroepen, dan wordt de in de config van de tabel ingesteld order_by gebruikt.
   * - De eerste parameter kan een array van strings kan zijn.
   * - Als de eerste parameter een array is en de tweede parameter (direction) is meegegeven, dan geld die direction alleen voor de eerste waarde in de array.
   * - De direction parameter kan naast 'DESC','ASC' en 'RANDOM' ook 'RAND' zijn (dit lijkt meer op de SQL)
   * - Als de naam van het veld begint met '_' dan wordt automatisch de direction op 'DESC' gezet
   * 
   * eerste parameter is een array
   * -----------------------------
   * 
   * ->order_by( array( 'str_title', 'dat_date DESC' ) );
   * 
   * many_to_one
   * -----------
   * 
   * ->order_by( 'tbl_posts.str_title' );
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
    if (empty($orderby)) return $this;
    // Zorg ervoor dat order_by een array is met direction erbij en verder volgens specs van Query Builder
    if (is_string($orderby)) $orderby = explode(',',$orderby);
    if (!empty($direction)) {
      if ( in_array($direction,array('RANDOM','RAND')) ) {
        if (is_numeric($orderby[0])) {
          $orderby[0] = 'RAND('.$orderby[0].')';
        }
        else {
          $orderby[0] = 'RAND()';
        }
      }
      else {
        $orderby[0] = $orderby[0].' '.$direction;
      }
    }
    // Vervang _field door field DESC
    foreach ($orderby as $key => $order) {
      if (substr($order,0,1)==='_') {
        $orderby[$key] = trim($order,'_').' DESC';
      }
    }
    // merge met bestaande
    $this->tm_order_by = array_merge( $this->tm_order_by, $orderby );
    return $this;
  }
  
  /**
   * Zelfde als bij Query Builder, met als extra dat de limit instelling wordt bewaard voor intern gebruik.
   *
   * @param int $limit 
   * @param int $offset [0]
   * @return $this
   * @author Jan den Besten
   */
	public function limit( $limit, $offset = 0) {
    $this->tm_limit = $limit;
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
	 * @return mixed FALSE als niet gelukt, anders de id van het nieuwe item
	 * @author Jan den Besten
	 */
  public function insert( $set = NULL, $escape = NULL ) {
    // Alleen doorgaan als max_rows = 0 of total_rows < max_rows
    if ($this->settings['max_rows']>0) {
      $total = $this->count_all();
      if ( $total >= $this->settings['max_rows'] ) {
        $this->query_info['affected_rows'] = 0;
        $this->query_info['insert_id']     = FALSE;
        $this->query_info['error']         = langp('data_max_rows_exceeded',$this->settings['table']);
        $this->reset();
        return FALSE;
      }
    }
    return $this->_update_insert( 'INSERT', $set );
	}
  

  /**
   * Zelfde als in Query Builder, maar ook met verwijzingen naar bestaande many_to_many data
   *
   * @param array $set [NULL]
   * @param string $where [NULL]
   * @param int $limit [NULL]
   * @return mixed FALSE als niet gelukt, anders de id van het aangepaste item
   * @author Jan den Besten
   */
	public function update( $set = NULL, $where = NULL, $limit = NULL) {
    return $this->_update_insert( 'UPDATE', $set, $where, $limit);
	}
  
  
  /**
   * Maak kopie van rijen en pas eventueel bepaalde velden aan
   *
   * @param array $set[NULL] Velden die aangepast moeten worden tijdens het kopieren
   * @param array $where[NULL] Welke velden?
   * @param number $limit[NULL] Maximum aantal?
   * @return mixed
   * @author Jan den Besten
   */
	public function copy( $set = NULL, $where = NULL, $limit = NULL) {
    // Is er een data set?
    if (!is_null($set)) $this->set( $set );
    
    // Where/Limit
    if ($where) $this->where( $where );
    if ($limit) $this->limit( $limit );
    
		$set  = $this->tm_set;
    $copy = $this->unselect( array('tme_last_changed','user_changed' ));
    $copy = $this->get_result();
    // Pas copy aan met set
    foreach ($copy as $id => $row) {
      $copy[$id] = array_merge($row,$set);
      unset($copy[$id]['id']);
    }
    
    // Maak copy
    foreach ($copy as $id => $row) {
      $ok = $this->insert( $row );
    }

    return $ok;
	}
  
  

  /**
   * Voert insert/update uit
   *
   * @param string $type [INSERT|UPDATE]
   * @param array $set = NULL
   * @param mixed $where = NULL
   * @param int $limit = NULL
   * @return mixed FALSE als niet gelukt, anders de id van het aangepaste item
   * @author Jan den Besten
   */
	protected function _update_insert( $type, $set = NULL, $where = NULL, $limit = NULL ) {
    
    // Is een type meegegeven?
    $types = array('INSERT','UPDATE');
    if ( ! in_array($type,$types) ) {
      throw new ErrorException( __CLASS__.'->'.__METHOD__.'(): no type set, should be one of `'.implode(',',$types).'`' );
    }
    
    // Is user id nodig?
    if ( $this->field_exists('user_changed')) {
      if ( !isset( $this->user_id )) {
        $this->set_user_id();
      }
    }
    
    // Is er een data set?
    if (!is_null($set)) $this->set( $set );
    
    // Als er een lege set is, dan zijn we al klaar
    if (empty( $this->tm_set )) {
      $this->reset();
      return FALSE;
    }

    
    /**
     * Ok we kunnen! Stel nog even alles in en maak cache leeg
     */
    if ($where) $this->where( $where );
    if ($limit) $this->limit( $limit );
		$set = $this->tm_set;
    $id = NULL;
    $this->clear_cache();
    
    /**
     * Als een UPDATE check of er wel een WHERE is om te voorkomen dat een hele tabel wordt overschreven.
     */
    if ( $type=='UPDATE' and !$this->tm_has_condition ) {
      throw new ErrorException( __CLASS__.'->'.__METHOD__.'(): no condition set (WHERE,LIKE etc). Could result in overwriting all rows in `'.$this->settings['table'].'`' );
    }
    

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
      // Niet gevalideerd, dus we kunnen geen update doen, FALSE als return
      if ( ! $this->form_validation->validate_data( $set, $this->settings['table'] ) ) {
        $this->query_info = array(
          'validation'        => FALSE,
          'validation_errors' => $this->form_validation->get_error_messages()
        );
        $this->reset();
        return FALSE;
      }
      // Goed gevalideerd, maar wellicht is de data geprepped
      else {
        $set = array_merge( $set, $this->form_validation->get_validated_data( array_keys($set)) );
      }
    }
    
    /**
     * Split eventuele one_to_one data
     */
    if ( isset($this->settings['relations']['one_to_one'])) {
      $to_one = array();
      foreach ($this->settings['relations']['one_to_one'] as $what=>$relation) {
        // $other_table = $relation['other_table'];
        // $foreign_key = $relation['foreign_key'];
        $result_name = $relation['result_name'];
        $to_one[$what] = filter_by_key($set,$result_name.'.');
        if ($to_one[$what]) {
          $set = array_unset_keys($set,array_keys($to_one[$what]));
        }
      }
    }

    /**
     * Split eventuele many_to_many data
     */
    if ( isset($this->settings['relations']['many_to_many']) or isset($this->settings['relations']['one_to_many'])) {
      $to_many = array();
      foreach ($this->settings['relations'] as $rel_type => $relation) {
        // many_to_many
        if (in_array($rel_type,array('many_to_many','one_to_many'))) {
          $to_many[$rel_type]=array();
          foreach ( $relation as $what => $relation_info ) {
            if ($rel_type=='many_to_many') $rel_table   = $relation_info['rel_table'];
            $other_table = $relation_info['other_table'];
            $result_name = $relation_info['result_name'];
            if ( array_key_exists($result_name,$set) ) {
              $to_many[$rel_type][$what] = $set[$result_name];
              unset($set[$result_name]);
            }
          }
        }
      }
    }
    
    /**
     * Verwijder onnodige velden
     */
    unset($set[$this->settings['primary_key']]);
    unset($set['tme_last_changed']);

    /**
     * Verwijder data die NULL is of waarvan het veld niet in de table bestaat.
     */
    foreach ( $set as $key => $value ) {
      if ( !isset($value) or !$this->field_exists( $key) ) unset( $set[$key] );
    }
    
    
    /**
     * Maak een hash van wachtwoordvelden
     */
    foreach ( $set as $key => $value ) {
      $pre = get_prefix($key);
      if (in_array($pre,$this->config->item('PASSWORD_field_types'))) {
        $set[$key] = $this->flexy_auth->hash_password( $value );
      }
    }

    
    /**
     * Ga door als de set niet leeg is
     */
    if (!empty($set) or isset($to_many) or isset($to_one)) {
      
      /**
       * User fields toevoegen aan set?
       */
      if ( $this->user_id!==FALSE ) $set = $this->_add_user_fields_to_set( $set,$type );
      
      /**
       * Eindelijk, we kunnen...
       */
      $this->db->trans_start();

      /**
       * Als set leeg is (maar wel ..to_many) zoek dan de id en stel WHERE opnieuw in
       */
      if (empty($set)) {
        // WHERE is al ingesteld, dus we kunnen gewoon de id's vinden
        $result = $this->select( $this->settings['primary_key'] )->get_result();
        $ids = array_keys($result);
        $id = current($ids);
        $log = array(
          'query' => $this->db->last_query(),
          'table' => $this->settings['table'],
          'id'    => $id
        );
        $this->query_info = array(
          'affected_rows' => 0,
          'affected_ids'  => $ids,
        );
      }
      else {
        
        $this->db->set($set);
    
        /**
         * INSERT of UPDATE doen
         */
        if ($type=='INSERT') {
  				$this->db->insert( $this->settings['table'] );
  				$id = $this->db->insert_id();
      
          $log = array(
            'query' => $this->db->last_query(),
            'table' => $this->settings['table'],
            'id'    => $id
          );
          $this->query_info = array(
            'insert_id'     => $id,
            'affected_rows' => 1,
          );
  			}
      	else {
          $sql = $this->db->get_compiled_update( $this->settings['table'], FALSE );
  				$this->db->update( $this->settings['table'], NULL,NULL, $this->tm_limit );
          $log = array(
            'query' => $this->db->last_query(),
            'table' => $this->settings['table'],
            'id'    => $id
          );
          $ids = $this->_get_ids( $sql );
          $id = current( $ids );
          $this->query_info = array(
            'affected_rows' => $this->db->affected_rows(),
            'affected_ids'  => $ids,
          );
          $log['id']=implode(',',$ids);
  			}
      }
      
      /**
       * Als er to_one data is, update/insert die ook
       */
      if ( !empty($to_one)) {
        foreach ($to_one as $what => $other_set) {
          if (!empty($other_set)) {
            $other_table = $this->settings['relations']['one_to_one'][$what]['other_table'];
            $foreign_key = $this->settings['relations']['one_to_one'][$what]['foreign_key'];
            $result_name = $this->settings['relations']['one_to_one'][$what]['result_name'];
            
            if ( $this->user_id!==FALSE ) $other_set = $this->_add_user_fields_to_set( $other_set,$type,$other_table );
            
            /**
             * INSERT als niet bestaat, anders UPDATE
             */
            $existing = $this->db->where( $foreign_key, $id )->get( $other_table )->num_rows();
            if ( !$existing ) {
              $other_set[$foreign_key]=$id;
              $this->db->set($other_set);
      				$this->db->insert( $other_table );
              $log['query'] .= '; '.$this->db->last_query();
      			}
          	else {
              $this->db->set($other_set);
              $this->db->where( $foreign_key,$id );
      				$this->db->update( $other_table );
              $log['query'] .= '; '.$this->db->last_query();
      			}
          }
        }
      }
      
      
			/**
			 * Als er ..._to_many data is, update/insert die ook
			 */
			if ( !empty($to_many) ) {
        $affected = 0;
        
        // many_to_many
        if (isset($to_many['many_to_many'])) {
  				foreach( $to_many['many_to_many'] as $what => $other_ids ) {
            $other_ids         = $this->_check_other_ids($other_ids);
            $other_table       = $this->settings['relations']['many_to_many'][$what]['other_table'];
            $rel_table         = $this->settings['relations']['many_to_many'][$what]['rel_table'];
  					$this_foreign_key  = $this->settings['relations']['many_to_many'][$what]['this_key'];
            $other_foreign_key = $this->settings['relations']['many_to_many'][$what]['other_key'];
            
            // Haal bestaande items op
            $existing = $this->db->where( $this_foreign_key, $id )->get( $rel_table )->result_array();
            // Update/Insert nieuwe items
            foreach ($other_ids as $key => $other_id) {
              // Update bestaande item als die nog bestaat
              if (count($existing)>0) {
                $existing_item = array_shift($existing);
    						$this->db->where( 'id', $existing_item['id'] ); // Juiste existing item
    						$this->db->set( $other_foreign_key, $other_id );
    						$this->db->update( $rel_table );
                $affected++;
                $log['query'] .= ';'.PHP_EOL.PHP_EOL.$this->db->last_query();
              }
              // Geen bestaande items meer, dus vanaf nu nieuwe items
              else {
    						$this->db->set( $this_foreign_key,  $id );
    						$this->db->set( $other_foreign_key, $other_id );
    						$this->db->insert( $rel_table );
                $affected++;
                $log['query'] .= ';'.PHP_EOL.PHP_EOL.$this->db->last_query();
              }
            }
            // Zijn er nog oude over? -> verwijder deze
            if (count($existing)>0) {
              foreach ($existing as $existing_item) {
      					$this->db->where( 'id', $existing_item['id'] );
      					$this->db->delete( $rel_table );
                $log['query'] .= ';'.PHP_EOL.PHP_EOL.$this->db->last_query();
              }
            }
  				}
        }
        
        // one_to_many
        if (isset($to_many['one_to_many'])) {
  				foreach( $to_many['one_to_many'] as $what => $other_ids ) {
            $other_ids    = $this->_check_other_ids($other_ids);
            $other_table  = $this->settings['relations']['one_to_many'][$what]['other_table'];
  					$foreign_key  = $this->settings['relations']['one_to_many'][$what]['foreign_key'];
            
            // 1) Verwijder de oude verwijzingen (maak ze 0)
            $this->db->set( $foreign_key, 0);
  					$this->db->where( $foreign_key, $id );
  					$this->db->update( $other_table );
            $log['query'] .= ';'.PHP_EOL.PHP_EOL.$this->db->last_query();
            // 2) Maak de nieuwe verwijzingen
            foreach ($other_ids as $other_id) {
              $this->db->set( $foreign_key, $id);
    					$this->db->where( $this->settings['primary_key'], $other_id );
    					$this->db->update( $other_table );
              $affected++;
              $log['query'] .= ';'.PHP_EOL.PHP_EOL.$this->db->last_query();
            }
            
          }
        }
        
        $this->query_info['affected_rel_rows'] = $affected;
			}
      $this->db->trans_complete();
		}
    
    if (isset($log)) {
      $this->log_activity->database( $log['query'], $log['table'], $log['id'] );
    }
    
    $this->reset();
		return intval($id);
	}
  
  /**
   * Voeg user velden to aan set zodat kan worden bijgehouden wie wat heeft aangepast/aangemaakt.
   *
   * @param array $set 
   * @param string $type INSERT/UPDATE
   * @param string $table['']
   * @return arra
   * @author Jan den Besten
   */
  private function _add_user_fields_to_set( $set, $type, $table='') {
    if (empty($table) or $table===$this->settings['table']) {
      if ( $this->field_exists('user_changed'))               $set['user_changed'] = $this->user_id;
      if ( $type==='INSERT' and $this->field_exists('user'))  $set['user'] = $this->user_id;
    }
    else {
      if ( $this->db->field_exists('user_changed',$table))               $set['user_changed'] = $this->user_id;
      if ( $type==='INSERT' and $this->db->field_exists('user',$table))  $set['user'] = $this->user_id;
    }
    return $set;
  }
  
  /**
   * Zorg ervoor dat other_ids in orde zijn (geen string oid, maar altijd een array van ids)
   *
   * @param array $other_ids 
   * @return array
   * @author Jan den Besten
   */
  private function _check_other_ids($other_ids) {
    if (is_string($other_ids)) $other_ids=preg_split('/[|,'.$this->settings['abstract_delimiter'].']/',$other_ids);
    if (is_null($other_ids)) $other_ids = array();
    if (!is_array($other_ids)) $other_ids=array($other_ids);
		foreach ( $other_ids as $okey => $other_id ) {
      if (is_array($other_id) and isset($other_id[$this->settings['primary_key']])) {
        $other_ids[$okey]=$other_id[$this->settings['primary_key']];
      }
    }
    return $other_ids;
  }
  
  
  /**
   * Net als Query Builder, en met verwijderen van bijbehorende many_to_many verwijzingen
   *
   * @param mixed $where ['']
   * @param int $limit [NULL]
   * @param bool $reset_data 
   * @return mixed FALSE als niet gelukt, anders array_result van verwijderde data
   * @author Jan den Besten
   */
	public function delete( $where = '', $limit = NULL, $reset_data = TRUE ) {
    $this->clear_cache();
    
    /**
     * Is het een ordered tabel?
     */
    $is_ordered_table = $this->field_exists( 'order' );
    if ($is_ordered_table) $this->load->model('order','_order');

    /**
     * Bouw query op, bewaar deze, en reset query
     */
    if ($where) $this->where( $where );
    if ($limit) $this->limit( $limit );
    // Bewaar query en zorg ervoor dat LIMIT mee komt
    if ($this->tm_limit) $limit = $this->tm_limit;
    $compiled_delete = $this->db->get_compiled_delete( $this->settings['table'], TRUE );
    if ($limit) $compiled_delete .= ' LIMIT '.$this->tm_limit;

		/**
		 * Wat zijn de id's van de te verwijderen items?
		 * - Nodig om eventuele many_to_many data te verwijderen
		 * - En om huidige data op te vragen
		 */
    $ids = $this->_get_ids( $compiled_delete );
    
    /**
     * Als geen te verwijderen ids, dan zijn we al klaar
     */
    if (empty($ids)) {
      $this->query_info = array(
        'affected_rows' => 0,
        'affected_ids'  => $ids,
      );
      return FALSE;
    }
    
    /**
     * Onthoud huidige data van te deleten records om terug te geven.
     */
    $deleted_data = FALSE;
    $this->db->where_in( $this->settings['primary_key'], $ids );
    $query = $this->db->get( $this->settings['table'] );
    if ($query) $deleted_data = $query->result_array();
    $this->reset();
    
    /**
     * Start DELETE
     */
    $this->db->trans_start();
		
    // $is_deleted = $this->db->delete( $this->settings['table'], '', $this->tm_limit, $reset_data );
    $is_deleted = $this->db->query( $compiled_delete );
    
    // trace_(['is_deleted' => $is_deleted,'compiled_delete'=>$compiled_delete,'ids'=>$ids,'deleted_data'=>$deleted_data]);
    
    if ($is_deleted) {
      $log = array(
        'query' => $this->db->last_query(),
        'table' => $this->settings['table'],
        'id'    => implode(',',$ids),
      );
    }
    
    
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
      if ( isset($this->settings['relations']['many_to_many']) and !empty($ids)) {
        $other_tables = $this->settings['relations']['many_to_many'];
        $other_tables = array_keys($other_tables);
        // $this_foreign_key = $this->settings['relations']['many_to_many'][$other_table]['this_key'];
        // $rel_tables = $this->relation_tables['many_to_many__rel'];
        $affected = 0;
        foreach ( $other_tables as $other_table ) {
          $rel_table        = $this->settings['relations']['many_to_many'][$other_table]['rel_table'];
          $this_foreign_key = $this->settings['relations']['many_to_many'][$other_table]['this_key'];
          $this->db->where_in( $this_foreign_key, $ids );
          $this->db->delete( $rel_table );
          $log['query'] .= ';'.PHP_EOL.PHP_EOL.$this->db->last_query();
          $affected = $affected + $this->db->affected_rows();
        }
        $this->query_info['affected_rel_rows'] = $affected;
      }
      
    }
    
    $this->db->trans_complete();

    if (isset($log)) {
      $this->log_activity->database( $log['query'], $log['table'], $log['id'] );
    }

    $this->reset();
    if ($is_deleted) return $deleted_data;
		return FALSE;
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
    if (!empty($sql)) {
      $query = $this->db->query( $sql );
      if ( is_object($query) ) {
        $result = $query->result_array();
        foreach ($result as $row) {
          $ids[] = $row[$this->settings['primary_key']];
        }
      }
    }
    return $ids;
  }
  
  
  

  /* --- Informatieve methods --- */
  
  /**
   * Geeft informatie van laatste query, zoals oa:
   * 
   * - total_rows
   * - num_rows
   * - limit
   * - offset
   * - page
   * - num_pages
   * - num_fields
   *
   * @param string $what ['']
   * @param bool $last_query [FALSE]
   * @return mixed
   * @author Jan den Besten
   */
  public function get_query_info( $what='', $last_query = FALSE ) {
    if (!empty($what)) return el($what,$this->query_info);
    $query_info = $this->query_info;
    if ( ! $last_query ) {
      unset($query_info['last_query']);
      unset($query_info['last_clean_query']);
    }
    return $query_info;
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
  public function total_rows( $calculate=FALSE, $json=FALSE ) {
    if ($calculate) {
      // perform simple query count
      $query = $this->db->query( $this->last_clean_query( $json ) );
      $total_rows = $query->num_rows();
      return $total_rows;
    }
    return $this->get_query_info('total_rows');
  }
  
  /**
   * Geeft aantal rijen van de tabel
   *
   * @param string $table ['']
   * @return int
   * @author Jan den Besten
   */
  public function count_all($table='') {
    if (empty($table)) $table=$this->settings['table'];
    return $this->db->count_all($table);
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
  protected function last_clean_query( $groupby=FALSE, $query='' ) {
    if (empty($query)) $query = trim($this->last_query());
    // $query = preg_replace("/(WHERE.*)GROUP/uUs", " GROUP", $query);
    // $query = preg_replace("/(WHERE.*)ORDER/uUs", " ORDER", $query);
    // $query = preg_replace("/(WHERE.*)LIMIT/uUs", " LIMIT", $query);
    $query = preg_replace("/SELECT.*FROM/uUs", 'SELECT `'.$this->settings['table'].'`.`'.$this->settings['primary_key'].'` FROM', $query, 1);
    $query = preg_replace("/LIMIT\s+\d*/us", " ", $query);
    $query = preg_replace("/ORDER\sBY[^)]*/us", "", $query);
    if ($groupby and strpos($query,'GROUP BY')===FALSE) {
      $query.=' GROUP BY `'.$this->settings['table'].'`.`'.$this->settings['primary_key'].'`';
    }
    $this->query_info['last_clean_query'] = $query;
    return $this->query_info['last_clean_query'];
  }
  
  /**
   * Geeft het WHERE deel van de laatste query
   *
   * @return string
   * @author Jan den Besten
   */
  protected function get_compiled_where() {
    $sql = $this->last_clean_query();
    $where='';
    if (preg_match("/(WHERE)(.*)(|LIMIT|GROUP|ORDER)/u", $sql, $matches)) {
      $where=trim(el(2,$matches,''));
    }
    return $where;
  }



  /**
   * Geeft velden van tabel terug.
   * Eventueel alleen van een bepaald type, en eventueel een maximum aantal
   *
   * @param string $type [''] Geef hier eventueel de prefix van de velden die je terug wilt.
   * @param int $count [0] Geef hier eventueel het maximum aantal terug te geven velden.
   * @return mixded geeft een array terug, of een string als $count=1
   * @author Jan den Besten
   */
  public function list_fields($type='',$count=0) {
    $fields = $this->settings['fields'];
    if ($type) {
      foreach ($fields as $key => $field) {
        if ( get_prefix($field)!==$type ) unset($fields[$key]);
      }
    }
    if ($count>0) {
      $fields = array_slice($fields,0,$count);
      if ($count===1) return current($fields);
    }
    return $fields;
  }
  


  /**
   * Test of een veld bestaat
   *
   * @param string $field 
   * @param string $set [''] Je kunt hier 'grid' of 'form' aangeven
   * @return boolean
   * @author Jan den Besten
   */
  public function field_exists( $field, $set='' ) {
    if ($set)
      $fields = $this->get_setting( array($set.'_set','fields'), $this->list_fields() );
    else
      $fields = $this->get_setting( 'fields', $this->list_fields() );
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
  				'label'       => $field->Field,
  				'type'        => $type,
  				'default'     => $field->Default,
  				'max_length'  => $max_length,
  				'primary_key' => ($field->Key == "PRI") ? 1 : 0,
  				'extra'       => $field->Extra,
        );
        if ( strpos($info['type'],'int')!==FALSE ) {
          $info['default'] = (int) $info['default'];
        }
        $this->field_data[$info['label']] = $info;
			}
			$query->free_result();
		}

    // return
    if ($asked_field) {
      if ($asked_key) return el(array($asked_field,$asked_key),$this->field_data);
      return el($asked_field,$this->field_data);
    }
    return $this->field_data;
	}
  
  /**
   * Geeft database informatie over de tabel
   *
   * @return array
   * @author Jan den Besten
   */
  public function table_status() {
    $query  = $this->query("SHOW TABLE STATUS WHERE NAME = '".$this->settings['table']."'");
    $status = current($query->result_array());
    return array_change_key_case($status);
  }
  
  
  /**
   * Geeft terug of de tabel een menu-achtige tabel is (met de velden 'order','self_parent' en 'uri')
   *
   * @return bool
   * @author Jan den Besten
   */
  public function is_menu_table() {
    return ( $this->field_exists('self_parent') and $this->field_exists('order') and $this->field_exists('uri') );
  }
  

}
