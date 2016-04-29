<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * - Alle query-builder methods (en db methods) kunnen worden gebruikt met het model. Als je CodeIgniter kent, ken je dit model al bijna.
 * - Alle instellingen van een tabel en zijn velden tabel zijn te vinden in config/data/...
 * - Standaard get/crud zit in het model, voor elke tabel hetzelfde.
 * - Iedere tabel kan deze overerven en aanpassen naar wens, de aanroepen blijven hetzelfde voor iedere tabel.
 * - Naast ->get() die een query object teruggeeft ook ->get_result() die een aangepaste result array teruggeeft met relatie data als subarray.
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
 * ->path( $path_field, $original_field = '' )    // Geeft een veld aan dat een geheel pad aan waarden moet bevatten in een tree table (bijvoorbeeld een menu)
 * 
 * ->with( $type='', $what=array() )              // Voeg relaties toe (many_to_one, many_to_many) en specificeer eventueel welke tabellen en hun velden. Zie bij ->with()
 * ->with_json( $type='', $what=array() )         // Idem, maar dan komt de data in één JSON veld
 * ->with_flat_many_to_one( $what=array() )       // Idem, maar dan met platte foreign data
 * 
 * ->where($key, $value = NULL)                   // Zoals in Query Builder. Kan ook zoeken in many_to_many data (waar de many_to_many data ook gefilterd is)
 * ->where_exists( $key, $value = NULL )          // Idem met als resultaat dezelfde items maar met complete many_to_many data van dat item (ongefilterd)
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */


Class Data_Core extends CI_Model {


  /**
   * De instellingen voor deze tabel en velden.
   * Deze worden opgehaald uit het config bestand met dezelfde naam als die model. (config/data/data_model.php in dit geval)
   */
  protected $settings = array();


  /**
   * Enkele noodzakelijk instellingen die automatisch worden ingesteld als ze niet bekend zijn.
   */
  protected $autoset = array(
    'table'           => '',
    'fields'          => array(),
    'abstract_fields' => array(),
    'abstract_filter' => '',
    'relations'       => array(),
    'field_info'      => array(),
    'order_by'        => 'id',
    'max_rows'        => 0,
    'update_uris'     => true,
    'grid_set'        => array(),
    'form_set'        => array(),
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
   * Hou de FROM bij, kan aangepast worden om LIMIT bij one_to_many en many_to_many relaties mooi te krijgen
   */
  private $tm_from = '';
  
  /**
   * Een eventueel veld dat een compleet pad moet bevatten in een tree table
   */
  private $tm_path = FALSE;
  
  /**
   * Maximale lengte van txt velden.
   * Als groter dan 0 dan worden txt_ velden gemaximaliseerd op aantal karakters en gestript van html tags
   */
  private $tm_txt_abstract = 0;
  
  /**
   * Of de result_array in het geval van ->select_abstract() plat moet worden. Zie bij ->select_abstract()
   */
  private $tm_flat_abstracts = FALSE;

  /**
   * Hou ORDER BY bij als array van strings per veld en DESC eventueel achter het veld, met name 'jump_to_today' maakt daar gebruik van
   */
  private $tm_order_by = array();
  
  /**
   * Hou LIMIT en OFFSET bij om eventueel total_rows te kunnen berekenen
   * En of er naar de pagina moet worden gegaan van het item het dichtsbij vandaag
   */
  private $tm_limit         = 0;
  private $tm_offset        = 0;
  private $tm_jump_to_today = FALSE;


  /**
   * Welke relaties mee moeten worden genomen en op welke manier
   * 
   * Kan er bijvoorbeeld zo uit zien:
   * 
   * array(
   *  'many_to_one' => array(
   *    'id_links' => array(
   *      'fields'  => ....,
   *      'flat'    => FALSE,
   *     ),
   *  ),
   *  'many_to_many' => array(
   *    'rel_menu__links' => array(
   *       'fields'   => 'abstract',
   *       'json'     => FALSE
   *     ),
   *    'rel_menu__posts' => array(
   *       'fields'   => array('id','str_title',')
   *       'json'     => true
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
    $this->load->model('log_activity');
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
    $this->config->load( 'data/data', true);
    $default = $this->config->item( 'data/data' );
    $this->settings = $default;
    if ($table) $this->settings['table'] = $table; // Voor het los instellen van een table zonder eigen model
    // Haal de settings van huidige model op
    if ( empty($table) ) $table=get_class($this);
    if ( get_class()!=$table ) {
      if ($load) {
        $this->config->load( 'data/'.$table, true);
        $settings = $this->config->item( 'data/'.$table );
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
    $this->load->model('cfg');
    $this->load->library('form_validation');
    if (empty($table)) $table = $this->settings['table'];
    if (empty($fields)) $fields = $this->settings['fields'];
    $fields_info = array();
    $settings_fields_info = array();
    foreach ($fields as $field) {
      $field_info = $this->cfg->get( 'cfg_field_info', $table.'.'.$field);
      
      /**
       * Default, eerst uit schemaform, dan uit database
       */
      $schemaform = $this->config->item('FIELDS_special');
      $field_info['default'] = el(array($field,'default'),$schemaform);
      // Uit database
      if ( !isset($field_info['default'])) {
        $field_info['default'] = $this->cfg->field_data( $table,$field, 'default' );
      }
      
      // Verzamal de rest van de field info, eerst uit depricated cfg_field_info en uit de db
      $field_info_db = $this->db->where('field_field',$table.'.'.$field)->get_row('cfg_field_info');
      if (is_array($field_info) and is_array($field_info_db)) {
        $field_info = array_merge($field_info,$field_info_db);
      }
      else {
        if (!is_array($field_info)) $field_info=$field_info_db;
        if (!is_array($field_info)) $field_info=NULL;
      }
      $fields_info[$field] = $field_info;
      // Combineer, met oa de default waarde
      $settings_fields_info[$field] = array('default'=>$field_info['default']);
      
      
      /**
       * Options
       */
      $options = array();
      // Uit (depricated) cfg_field_info
      if (!empty($field_info['str_options'])) {
        $options['data'] = explode('|',$field_info['str_options']);
        foreach ($options['data'] as $key=>$option) {
          $options['data'][$key] = array( 'value'=>$option, 'name'=>$option );
        }
        $options['multiple'] = el('b_multi_options', $field_info, FALSE)?true:FALSE;
      }

      // Via foreign_keys of self_parent
      if ( get_prefix($field)==='id' and $field!==$this->settings['primary_key']) {
        
        $foreignTable = el( array('relations','many_to_one',$field,'other_table'), $this->settings);
        if ($foreignTable and $this->db->table_exists($foreignTable)) {
          // Zijn er teveel opties?
          if ($this->db->count_all($foreignTable)>100) {
            // Geef dan de api aanroep terug waarmee de opties opgehaald kunnen worden
            $options['api'] = '_api/table?table='.$foreignTable.'&as_options=true';
          }
          else {
            // Anders geef gewoon de opties terug
            $other_model = new Data_core();
            $other_model->table( $foreignTable )->select_abstract();
            $options['data'] = $other_model->get_result();
            foreach ($options['data'] as $key => $option) {
              $options['data'][$key] = array( 'value'=>$key, 'name'=>$option['abstract'] );
            }
          }
        }
      }

      // Self parent -> tree options
      if ($field==='self_parent') {
        $first_abstract_field = $this->settings['abstract_fields'];
        $first_abstract_field = current($first_abstract_field);
        $this->select('id,self_parent,order,'.$first_abstract_field);
        $this->path( $first_abstract_field, '', ' / ' );
        $options['data'] = $this->get_result();
        foreach ($options['data'] as $key => $option) {
          $options['data'][$key] = array( 'value'=>$key, 'name'=> $option[$first_abstract_field] );
        }
      }

      if ( $options) {
        $settings_fields_info[$field]['options'] = $options;
      }
      
      /**
       * Validation
       */
      $settings_fields_info[$field]['validation'] = explode('|',$this->form_validation->get_validations( $table, $field ));
      
      /**
       * Media path
       */
      if (in_array(get_prefix($field),array('media','medias'))) {
        // find in (depricated) media_info
        $full_field=$table.'.'.$field;
        $media_info = $this->db->like('fields_media_fields',$full_field)->get_row('cfg_media_info');
        $settings_fields_info[$field]['path'] = $media_info['path'];
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
    $this->load->model('cfg');
    // Haal eerst indien mogelijk uit (depricated) cfg_table_info
    $update_uris = ! $this->cfg->get( 'cfg_table_info', $this->settings['table'], 'b_freeze_uris');
    return settype($update_uris,'bool');
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
    $foreign_keys = filter_by( $this->settings['fields'], $this->settings['primary_key'].'_' );
    $names = array();
    if ($foreign_keys) {
      $relations['many_to_one'] = array();
      foreach ($foreign_keys as $foreign_key) {
        // Enkele keys worden standaard anders genoemd: TODO: aanpassen bij uiteindelijke models
        $special_foreigns = array('id_users'=>'id_user','id_user_groups'=>'id_user_group');
        $foreign_key = el($foreign_key,$special_foreigns,$foreign_key);
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
        $name  = $other_table;
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
    
    // trace_($relations);
    return $relations;
  }


  /**
   * Autoset admin_grid
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _autoset_grid_set() {
    $this->load->model('cfg');
    $table_info = $this->cfg->get( 'cfg_table_info',$this->settings['table'] );
    $show_always = $this->config->item('ALWAYS_SHOW_FIELDS');

    $grid_set['fields'] = $this->settings['fields'];
    foreach ($grid_set['fields'] as $key => $field) {
      $field_info = $this->cfg->get('cfg_field_info', $this->settings['table'].'.'.$field );
      if ( !in_array($field,$show_always) and !el('b_show_in_grid',$field_info,TRUE) ) unset($grid_set['fields'][$key]);
    }
    $grid_set['fields']        = array_values($grid_set['fields']); // reset keys
    $grid_set['order_by']      = $this->settings['order_by'];
    $grid_set['jump_to_today'] = (el('b_jump_to_today',$table_info,TRUE)?true:false);
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
    $grid_set['pagination']    = (el('b_pagination',$table_info,TRUE)?true:false);
    $many_to_one               = el( array('relations','many_to_one'), $this->settings, array() );
    // Kan ook met de opties, voor als formdata uit tabledata gebruikt moet gaan worden
    foreach ($many_to_one as $key => $info) {
      $many_to_one[$key]['fields'] = 'abstract';
      $many_to_one[$key]['flat']   = TRUE;
    }
    $grid_set['relations'] = array('many_to_one'=>$many_to_one);
    
    // trace_($grid_set);

    return $grid_set;
  }



  /**
   * Autoset admin_form
   *
   * @return void
   * @author Jan den Besten
   */
  protected function _autoset_form_set() {
    $this->load->model('cfg');
    $table_info = $this->cfg->get( 'cfg_table_info',$this->settings['table'] );
    $show_always = $this->config->item('ALWAYS_SHOW_FIELDS');
    $main_fieldset = $this->settings['table'];
    $fieldsets = array($main_fieldset=>array());

    $form_set['fields'] = $this->settings['fields'];
    foreach ($form_set['fields'] as $key => $field) {
      $field_info = $this->cfg->get('cfg_field_info', $this->settings['table'].'.'.$field );
      // Show?
      if ( !in_array($field,$show_always) and !el('b_show_in_form',$field_info,TRUE) ) {
        unset($form_set['fields'][$key]);
      }
      // in which fieldset/tab?
      else {
        $fieldset = el('str_fieldset',$field_info, $main_fieldset );
        if (!isset($fieldsets[$fieldset])) $fieldsets[$fieldset]=array();
        array_push( $fieldsets[$fieldset], $field );
      }
    }
    $form_set['fields'] = array_values($form_set['fields']); // reset keys
    $form_set['fieldsets'] = $fieldsets;
    $form_set['with']      = array();
    // trace_($form_set);
    return $form_set;
  }
  
  
  /* --- Informatie uit andere tabellen/models --- */
  

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
    $setting = NULL;
    // Probeer eerst of het table model bestaat
    if ( method_exists( $table, 'get_setting' ) ) {
      $setting = $this->$table->get_setting( $key, $default );
    }
    // Laad anders de config van die tabel/model
    else {
      $this->config->load( 'tables/'.$table, true);
      $settings = $this->config->item( 'tables/'.$table );
      $setting = el( $key, $settings, $default );
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
    $this->tm_select        = FALSE;
    $this->tm_from          = '';
    $this->tm_path          = FALSE;
    $this->tm_order_by      = array();
    $this->tm_limit         = 0;
    $this->tm_offset        = 0;
    $this->tm_jump_to_today = FALSE;
    $this->with();
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
    $this->reset();
    if (empty($table)) {
      $table = $this->_autoset_table();
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
        $this->user_id = $this->user->user_id;
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
		$sql = "CONCAT_WS(' | ',`".$table.'`.`' . implode( "`,`".$table.'`.`' ,$abstract_fields ) . "`) AS `" . $abstract_field_name . "`";
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
    $tables = $this->list_tables();
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
   * Geeft een setting. Als deze niet bestaan dan wordt eerste geprobeerd een autoset waarde te geven, anders default [NULL].
   *
   * @param mixed $key een met de gevraagde key, of array van gevraagde keys
   * @param mixed $default [null]
   * @return mixed
   * @author Jan den Besten
   */
  public function get_setting( $key, $default=null ) {
    return el( $key, $this->settings, el( $key, $this->autoset, $default ) );
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
   *  'api'       => '',
   *  'data'      => array(),
   *  'multiple'  => [TRUE|FALSE],
   * )
   * 
   * Waar de 'data' array er zo uit ziet:
   * 
   * array(
   *  'value'   => '', // De waarde zelf (bijvoorbeeld een primary key)
   *  'name'    => '', // Doe de waarde getoont wordt (bijvoorbeeld een abstract)
   *  ['group'   => '', // Een eventuele groep, voor selects die verdeeld zijn in groepen. ]
   * ) 
   * 
   * Als geen veld wordt meegegeven worden de opties van alle velden uit de tabel teruggegeven:
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
    // Eén of alle velden?
    $one = FALSE;
    if (!empty($field)) {
      $one = true;
      $fields = array($field);
    }
    else {
      $fields = array_keys($this->settings['field_info']);
    }
    // Alle opties van de velden verzamelen
    $options=array();
    foreach ($fields as $field) {
      $field_options = el( array($field,'options'), $this->settings['field_info'] );
      if ($field_options) $options[$field] = $field_options;
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

    // bouw select query op
    $this->_select();
    
    // bouw relatie queries
    if ( !empty( $this->tm_with ) ) $this->_with( $this->tm_with );
    
    // maak select concreet
    $this->db->select( $this->tm_select, FALSE );
    
    // order_by
    if ( empty($this->tm_order_by) and !empty($this->settings['order_by']) ) {
      $this->order_by( $this->settings['order_by'] );
    }
    if ( !empty($this->tm_order_by) ) {
      foreach ($this->tm_order_by as $order_by) {
        $this->db->order_by( $order_by );
      }
    }

    // FROM
    $this->_from();
    
    // limit & offset
    $this->query_info = array();
    $this->db->limit( $this->tm_limit );
    $this->db->offset( $this->tm_offset );
    
    // get
    $query = $this->db->get();
    
    // Jump to today?
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
          $query = $this->db->query( $sql );
          $this->query_info['today'] = true;
        }
      }
    }
    
    // Query Info Complete
    if ($query) {
      $this->query_info['num_rows']   = $query->num_rows();
      $this->query_info['total_rows'] = $query->num_rows();
      if ($this->tm_limit>1) {
        $this->query_info['limit']      = (int) $this->tm_limit;
        $this->query_info['offset']     = $this->tm_offset;
        $this->query_info['page']       = $this->tm_offset / $this->tm_limit;
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
   * - Als select_txt_abstract() is ingesteld dan worden die velden ook nog gestript van HTML tags
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
    
    // Pad fields
    if ($this->tm_path) {
      $paths=array();
      $needed_path_fields = array_merge(array_keys($this->tm_path),array($this->settings['primary_key'],'self_parent'));
    }
    
    foreach ( $query->result_array() as $row ) {
      $id = $row[$this->settings['primary_key']];
      $result_key = $row[$key];
      
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
            
            // Niet JSON en niet flat => als subarray
            elseif ( ! el('json',$info,FALSE) ) {
              $fields   = $info['fields'];
              // split row and with data
              $row_with_data = filter_by_key( $row, $as.'.' );
              $row = array_diff_assoc( $row, $row_with_data );
              // process with data
              foreach ($row_with_data as $oldkey => $values) {
                $newkey = remove_prefix( $oldkey, '.');
                $row_with_data[$newkey] = $values;
                unset($row_with_data[$oldkey]);
              }
              // remember with data
              if ($with_type==='many_to_one') {
                $with_data[$result_key][$as] = $row_with_data;
              }
              else {
                if ( isset($row_with_data[ $this->settings['primary_key'] ]) ) {
                  $with_data[$result_key][$as][$row_with_data[$this->settings['primary_key']]] = $row_with_data;
                }
                else {
                  $with_data[$result_key][$as][] = $row_with_data;
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
      
      // tm_txt_abstract
      if ($this->tm_txt_abstract>0) {
        $txt_row = $row;
        $txt_row = filter_by_key($txt_row,'txt_');
        $txt_row = array_keys($txt_row);
        foreach ($txt_row as $txt_field) {
          $row[$txt_field] = preg_replace( "/[\n\r]/"," ", strip_tags($row[$txt_field]));
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
    $parent = el(array($key,'self_parent'),$result,0);
    if ( $parent>0 ) {
      $value .= $this->_fill_path( $result, $parent, $path_info) . $path_info['split'];
    }
    $value .= el(array($key,$path_info['original_field']),$result);
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
  private function _get_result( $limit=0, $offset=0 ) {
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
    // Nieuwe row? Geef dan defaults terug
    if ($where===-1) {
      return $this->get_defaults();
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
   * Geeft resultaat terug specifiek voor het admin grid:
   * - pagination
   * - zoeken
   * - abstracts van many_to_one
   *
   * @param mixed $limit [20] 
   * @param mixed $offset [FALSE] De start van het resultaat, als FALSE dan kan is jump_to_today aktief, anders niet.
   * @param string $sort [''] Veld dat de volgorde van het resultaat bepaalt, als het DESC moet, dan beginnen met een '_'
   * @param mixed $find [''] Een string waarde die gevonden moet worden, of een array met alle parameters van ->find()
   * @param mixed $where [''] Een where statement zoals aan de eerste paramater van ->where() gegeven kan worden.
   * @return array
   * @author Jan den Besten
   */
  public function get_grid( $limit = 20, $offset = FALSE, $sort = '', $find = '', $where = '' ) {
    $grid_set = $this->settings['grid_set'];
    
    // Select
    $this->select( $grid_set['fields'] );
    
    // Relations
    if (isset($grid_set['relations'])) {
      foreach ($grid_set['relations'] as $type => $relations) {
        foreach ($relations as $what => $info) {
          $this->with( $type, array( $what=>'abstract'), FALSE, TRUE );
        }
      }
    }
    
    // Order_by
    if (empty($sort)) {
      if (!empty($this->tm_order_by)) {
        $sort=$this->tm_order_by;
        $this->tm_order_by = array();
      }
      else {
        $sort=$this->settings['order_by'];
      }
    }
    else {
      if (substr($sort,0,1)=='_') {
        $sort=trim($sort,'_').' DESC';
      }
    }
    $this->order_by( $sort );
    
    // Where
    if ( $where ) {
      $this->where( $where );
    }
    
    // Find
    if ( $find ) {
      if ( !isset($find['terms']))  {
        $fields = $grid_set['fields'];
        if (isset($grid_set['with'])) {
          foreach ($grid_set['with'] as $type => $with) {
            foreach ($with as $other_table => $with_info) {
              $other_fields = $this->get_other_table_abstract_fields( $other_table );
              if ($other_fields) {
                foreach ($other_fields as $other_field) {
                  $fields[] = $other_table.'.'.$other_field;
                }
              }
            }
          }
        }
        $find=array( 'terms'=>$find, 'fields'=>$fields, 'settings'=>array() );
      }
      // trace_($find);
      $this->find( $find['terms'], $find['fields'], $find['settings'] );
    }
    
    // Pagination
    if ($grid_set['pagination']) {
      if (is_numeric($offset) or $offset!==TRUE) $this->limit( $limit, $offset );
    }

    // Jump to today?
    if (is_bool($offset) AND $offset===FALSE and $grid_set['jump_to_today']) {
      $this->tm_jump_to_today = TRUE;
    }

    return $this->_get_result();
  }
  
  
  /**
   * Creert schemaform van een standaard row uit de database
   *
   * @param array $row 
   * @param string $name 
   * @return array $schemaform
   * @author Jan den Besten
   */
  public function schemaform( $row, $name='row' ) {
    if (!is_array($row)) $row=array();
    $this->config->load('schemaform');
    $this->load->model('ui');
    
    // Alleen de rijen die in form_set ingesteld zijn
    $row = array_keep_keys($row, $this->settings['form_set']['fields'] );
    
    // Default schema
    $sf  = array(
      'schema' => array(
        'type'       => 'object',
        'title'      => $name,
        'properties' => array(),
        'required'   => array(),
      ),
      'form'  => array(),
    );
    // Load schemaform config
    $cfgDefault = $this->config->item('FIELDS_default');
    $cfgPrefix  = $this->config->item('FIELDS_prefix');
    $cfgSpecial = $this->config->item('FIELDS_special');
    
    foreach ($row as $name => $value) {
      // default
      $fieldProperties = $cfgDefault;
      
      // from prefix
      $prefix = get_prefix($name);
      if (el($prefix,$cfgPrefix)) {
        $fieldProperties = array_merge($fieldProperties,el($prefix,$cfgPrefix));
      }
      // special
      if (el($name,$cfgSpecial)) {
        $fieldProperties = array_merge($fieldProperties,el($name,$cfgSpecial));
      }
      // keep only the needed info
      $fieldProperties=array_unset_keys($fieldProperties,array('grid','form','default' )); // TODO kan (deels) weg als oude ui weg is
      
      // name
      $fieldProperties['title'] = $name;
      
      // type (maak select als er opties zijn)
      if (isset($this->settings['field_info'][$name]['options'])) {
        $fieldProperties['form-type'] = 'select';
      }

      // get validation (set in field_info)
      $validation = el( array('field_info',$name,'validation'), $this->settings, array() );
      // split validation / required
      $key = array_search('required',$validation);
      if ( $key!==false ) {
        unset($validation[$key]);
        // Add requered
        $sf['schema']['required'][]=$name;
      }
      
      $fieldProperties['validation']=$validation;
      
      // Put in Schema
      $sf['schema']['properties'][$name] = $fieldProperties;
    }
    
    // FIELDSETS / TABS
    $tabs = array();

    // Prepare fieldsets
    $default_fieldset = array( $this->settings['table'] => $this->settings['form_set']['fields'] );
    $fieldsets = el(array('form_set','fieldsets'), $this->settings, $default_fieldset );
    foreach ($fieldsets as $tabtitle => $items) {
      foreach ($items as $i=>$item) {
        $items[$i] = array(
          'key'  => el( array('schema','properties',$item,'title'), $sf ),
          'type' => el( array('schema','properties',$item,'form-type'), $sf ),
        );
      }
      $tabs[]=array(
        'title' => $this->ui->get($tabtitle),
        'items' => $items,
      );
    }
    // Tabs
    if (count($tabs)>1) {
      $sf['form'][] = array(
        'type' => 'tabs',
        'tabs' => $tabs,
      );
    }
    // No tabs -> keep order of fields in form
    else {
      foreach ($sf['schema']['properties'] as $key => $item) {
        $sf['form'][] = array(
          'key'  => $key,
          'type' => $item['form-type'],
        );
      }
    }

    return $sf;
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
   * Bouwt select deel van de query op
   *
   * @return $this
   * @author Jan den Besten
   */
  private function _select() {
    if ( !$this->tm_select ) {
      // Niet '*' maar alle velden expliciet maken
      $this->tm_select = array_combine($this->settings['fields'],$this->settings['fields']);
    }
    // Zorgt ervoor dat iig primary_key wordt geselecteerd
    if (!in_array( $this->settings['primary_key'], $this->tm_select ) ) {
      $id = $this->settings['primary_key'];
      $this->tm_select = array($id=>$id) + $this->tm_select;
    }
    foreach ( $this->tm_select as $key => $field ) {
      // Zorg ervoor dat alle velden geprefixed worden door de eigen tabelnaam om dubbelingen te voorkomen
      if (in_array($field,$this->settings['fields'])) {
        $this->tm_select[$key] = '`'.$this->settings['table'].'`.`'.$field.'`';
      }
      // tm_txt_abstract?
      if ( $this->tm_txt_abstract and get_prefix($field)=='txt' ) {
        $this->tm_select[$key] = 'SUBSTRING(`'.$this->settings['table'].'`.`'.$field.'`,1,'.$this->tm_txt_abstract.') AS `'.$field.'`';
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
   * @param mixed $txt_abstract [0] 0 = geen aanpassingen, TRUE = standaard aanpassingen, int = lengte bepalen
   * @return $this
   * @author Jan den Besten
   */
  public function select_txt_abstract( $txt_abstract = 0 ) {
    if ( $txt_abstract===TRUE or strtolower($txt_abstract)==='true') $txt_abstract = 100;
    $this->tm_txt_abstract = $txt_abstract;
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
   * Een andere optie is om het originele veld te behouden en een extra veld toe te voegen met het hele pad.
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
        reset($this->tm_order_by);
        $order_by = current($this->tm_order_by);
        $order_by = explode(' ',$order_by);
        // Compile the subquery:
        $this->tm_from = '(SELECT * FROM '.$this->db->protect_identifiers($table);
        if (!empty($where)) $this->tm_from.= ' WHERE ('.$where.') ';
        $this->tm_from .= ' ORDER BY '.$this->db->protect_identifiers($order_by[0]).' '.el(1,$order_by,'');
        if ( !$has_where AND $this->tm_limit > 0) {
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
   * Als je wilt zoeken, maar wel de complete many_to_many data voor een bepaald item gebruik dan ->where_exists()
   *
   * @param string $key 
   * @param mixed $value [NULL]
   * @param mixed $escape [NULL]
   * @return $this
   * @author Jan den Besten
   */
	public function where($key, $value = NULL, $escape = NULL) {
    // Als value een array is, dan ->where_in()
    if (isset($value) and is_array($value)) {
      $this->db->where_in($key,$value,$escape);
      return $this;
    }
    // Als geen value maar alleen een key (die geen array is), dat wordt alleen op primary_key gevraagd
    if (!isset($value) and !is_array($key)) {
      // 'first'
      if ($key==='first') {
        unset($key);
        unset($value);
        $this->limit( 1 );
      }
      // primary_key
      else {
        $value = $key;
        $key = $this->settings['primary_key'];
      }
    }
    // where
    if (isset($key)) $this->db->where($key,$value,$escape);
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
   * 
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
   * Voorbeelden met diverse termen
   * ------------------------------
   * 
   * ->find( 'zoek' )               // Zoekt naar de letters 'zoek' in alle velden
   * ->find( 'zoek ook')            // Zoekt naar de letters 'zoek' of 'ook'
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
   * AND in plaats van OR, alle voorbeelden met meerdere zoektermen en velden maken een OR query, zo kun je er een AND query van maken;
   * ->find( 'zoek ook', null, array( 'and' => TRUE ) )  // Zoekt naar rows in het resultaat waar 'zoek' EN 'ook' in voorkomen
   * ->find( 'zoek ook', null, array( 'and' => 'AND' ) ) // idem
   * ->find( 'zoek ook', null, array( 'and' => 'OR' ) )  // Dit is default, en zoekt naar resultaten waar 'zoek' OF 'ook' in voorkomen
   * ->find( 'zoek ook', null, array( 'and' => FALSE ) ) // idem
   * 
   * Zoeken op hele woorden in plaats van letters:
   * ->find( 'zoek', null, array( 'word_boundaries' =>TRUE ) )  // Zoekt het woord 'zoek' ipv de letters 'zoek'
   * 
   * Zoeken op specifieke term in plaats van een deel:
   * ->find( 'zoek', 'str_tag' , array( 'exact_term' =>TRUE ) ) // Zoekt het veld 'str_tag' dat precies de waarde 'zoek' heeft
   * 
   * Zoeken in relaties
   * ------------------
   * 
   * Als je wilt dat ook in relaties wordt gezocht, zorg dan dat eerst de relaties worden ingesteld met een ->with() aanroep (of een variant).
   * En daarna pas de ->find() aanroep.
   * 
   * Verder geld:
   * 
   * - In alle 'many_to_one' relaties wordt gezocht zolang het foreign_key veld in de zoekvelden zit.
   * - Automatisch wordt in alle 'many_to_many' en 'one_to_many' relaties gezocht
   * - Je kunt specifieker instellen met $settings['with'] welke relaties mee moeten worden genomen met het zoeken
   * 
   * @param mixed $terms Zoekterm(en) als een string of array van strings. Letterlijk zoeken kan door termen tussen "" te zetten.
   * @param array $fields [array()] De velden waarop gezocht wordt. Standaard alle velden. Kan ook in relatietabellen zoeken, bijvoorbeeld 'tbl_links.str_title' als een veld in een gerelateerde tabel
   * @param array $settings [array()] Extra instelingen, default: array( 'and' => 'OR, 'word_boundaries' => FALSE, , 'exact_term' => FALSE, 'with'=>array('many_to_one','one_to_many','many_to_many') )
   * @return $this
   * @author Jan den Besten
   */
  public function find( $terms, $fields = array(), $settings = array() ) {
    // settings
    $defaults = array(
      'and'             => 'OR',
      'word_boundaries' => FALSE,
      'exact_term'      => FALSE,
      'with'            => array('many_to_one','one_to_many','many_to_many'),
    );
    $settings = array_merge( $defaults,$settings );
    if ($settings['and']===TRUE)  $settings['and'] = 'AND';
    if ($settings['and']===FALSE) $settings['and'] = 'OR';
    $settings['and'] = strtoupper( $settings['and'] );
    
    // In welke velden zoeken?
    if ( is_string($fields) ) $fields = array($fields);
    
    // Geen velden meegegeven, gebruik dan alle velden van deze tabel (zoals ingesteld).
    if ( empty($fields) ) {
      $fields = $this->settings['fields'];
    }
    
    // Ook nog in relaties zoeken?
    if ( isset($this->tm_with) and !empty($this->tm_with) ) {
      foreach ($this->tm_with as $type => $tm_with) {
        if ( in_array($type,$settings['with']) ) {
          foreach ($tm_with as $what => $with) {
            // Alleen 'many_to_one' relaties die in de velden staan
            if ( $type==='many_to_one' and !in_array($what,$fields) ) continue;
            //
            $other_table  = $with['table'];
            $as           = el('as',$with,$other_table);
            $other_fields = $with['fields'];
            if ($other_fields=='abstract') $other_fields = $this->get_other_table_fields( $other_table );
            if (!is_array($other_fields)) $other_fields = explode(',',$other_fields);
            foreach ($other_fields as $other_field) {
              switch ($type) {
                case 'many_to_one':
                  array_push( $fields, $this->db->protect_identifiers($as.'.'.$other_field) );
                  break;
                case 'one_to_many':
                  array_push( $fields, $this->db->protect_identifiers( $other_table.'.'.$other_field) );
                  break;
                case 'many_to_many':
                  array_push( $fields, $this->db->protect_identifiers( $this->settings['relations']['many_to_many'][$other_table]['other_table'].'.'.$other_field) );
                  break;
              }
            }
          }
        }
      }
    }
    
    // Plak tabelnaam voor elk veld, als dat nog niet zo is, en escape
    foreach ( $fields as $key => $field ) {
      if (strpos($field,'.')===FALSE) $fields[$key] = $this->db->protect_identifiers($this->settings['table'].'.'.$field);
    }

    // Splits terms
    if ( is_array($terms) ) $terms = implode(' ',$terms);
    $terms=preg_split('~(?:"[^"]*")?\K[/\s]+~', ' '.$terms.' ', -1, PREG_SPLIT_NO_EMPTY );
    
    // trace_(['fields'=>$fields,'terms'=>$terms]);
    
    // Bouw 'query' op voor elke term
    $tm_find=array();
    
    foreach ($terms as $terms) {
      $terms = trim($terms,'"');
      
      // Start term query AND/OR?
      if ($settings['and']==='AND') {
        $this->db->group_start();
      }
      else {
        $this->db->or_group_start();
      }
      
      // Per veld:
      foreach ($fields as $field) {
        // exact_term, of word_boundaries of normaal
        if ( $settings['exact_term'] ) {
          $this->db->or_where( $field, $terms, FALSE);
        }
        elseif ( $settings['word_boundaries'] ) {
          $this->db->or_where( $field.' REGEXP \'[[:<:]]'.$terms.'[[:>:]]\'', NULL, FALSE);
        }
        else {
          $this->db->or_like( $field, $terms, 'both', FALSE);
        }
      }
      
      // End of term query
      $this->db->group_end();
    }
    
    return $this;
  }



  /**
   * Geef aan welke relaties meegenomen moeten worden in het resultaat, en hoe.
   * Deze method kan vaker achter elkaar worden aangeroepen.
   * 
   * Voorbeelden zijn te vinden in /admin/test/relations (NB als de 'flexyadmin_test' is geselecteerd)
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
    if ( empty($type) ) {
      $this->tm_with = array();
      return $this;
    }

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
      if ($type==='many_to_one') $table = $this->settings['relations']['many_to_one'][$what]['other_table'];
      // fields moet een (lege) array of een string ('abstract') zijn.
      if (is_string($fields) and $fields!==$this->config->item('ABSTRACT_field_name')) {
        $fields = explode( ',',$fields );
      }
      // Als fields een lege array is, stop dan alle velden van die tabel erin
      if (is_array($fields) and empty($fields)) {
        if ($type=='many_to_one')  $fields = $this->get_other_table_fields( $table );
        if ($type=='one_to_many')  $fields = $this->get_other_table_fields( $this->settings['relations']['one_to_many'][$what]['other_table'] );
        if ($type=='many_to_many') $fields = $this->get_other_table_fields( $this->settings['relations']['many_to_many'][$what]['other_table'] );
      }
      // fields moet iig ook de primary key bevatten
      $other_primary_key = $this->get_other_table_setting( $table,'primary_key', PRIMARY_KEY );
      if ( is_array($fields) AND !in_array($other_primary_key,$fields) ) array_unshift($fields,$other_primary_key);
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
        'as'      => el('as',$value, $this->settings['relations'][$type][$what]['result_name'] ),
        'fields'  => $value['fields'],
        'json' => $json,
      );
      if ($type=='many_to_one') {
        $tm_with_new[$what]['flat'] = $flat;
      }
    }
    $tm_with_new = array_replace_recursive( $tm_with_before, $tm_with_new );
    // Bewaar deze relatie instelling
    $this->tm_with[$type] = $tm_with_new;
    // trace_($this->tm_with);
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
   * @param string $with 
   * @return $this
   * @author Jan den Besten
   */
  protected function _with( $with ) {
    foreach ( $with as $type => $what ) {
      $method = '_with_'.$type;
      if ( method_exists( $this, $method ) ) {
        $this->$method( $what );
      }
      else {
        $this->reset();
        throw new ErrorException( __CLASS__.'->'.$method.'() does not exists. The `'.$type.'` relation could not be included in the result.' );
      }
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
    // trace_($what);
    $id = $this->settings['primary_key'];
    foreach ($what as $key => $info) {
      $fields      = $info['fields'];
      $json     = el('json',$info,false);
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
      $json     = el('json',$info,false);
      $foreign_key = $this->settings['relations']['one_to_many'][$key]['foreign_key'];
      $other_table = $this->settings['relations']['one_to_many'][$key]['other_table'];
      $as          = el('as',$info, $other_table);
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
    foreach ( $tables as $rel_table => $info ) {
      $fields   = $info['fields'];
      $json  = $info['json'];
      $this_table        = $this->settings['relations']['many_to_many'][$rel_table]['this_table'];
      $other_table       = $this->settings['relations']['many_to_many'][$rel_table]['other_table'];
      $this_foreign_key  = $this->settings['relations']['many_to_many'][$rel_table]['this_key'];
      $other_foreign_key = $this->settings['relations']['many_to_many'][$rel_table]['other_key'];
      $as                = $this->settings['relations']['many_to_many'][$rel_table]['result_name'];
      // Select fields
      $this->_select_with_fields( 'many_to_many', $other_table, $as, $fields, '', $json );
      // Joins
      $this->join( $rel_table,    $this_table.'.'.$id.' = '.$rel_table.".".$this_foreign_key,     'left');
      $this->join( $other_table,  $rel_table. '.'.$other_foreign_key.' = '.$other_table.".".$id,  'left');
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
      if ($type=='many_to_one') {
        $abstract = $this->get_compiled_abstract_select( $as_table, $abstract_fields, $as_table.'.' );
      }
      else {
        $abstract = $this->get_compiled_abstract_select( $other_table, $abstract_fields, $as_table.'.' );
      }
    }
    
    // SELECT abstract
    if ($abstract) {
      $select = $abstract;
      if ($json) {
        $abstract = remove_suffix($abstract,' AS ');
        $select = 'GROUP_CONCAT( "[",'.$abstract.',"]" SEPARATOR ",") `'.$other_table.'`';
      }
      // Voeg ook de primary_key erbij (behalve bij many_to_one, daar is die al bekend)
      if ($type!=='many_to_one') {
        $other_primary_key = $this->get_other_table_setting( $other_table, 'primary_key', PRIMARY_KEY);
        $select = '`'.$other_table.'`.`'.$other_primary_key.'` AS `'.$as_table.'.'.$other_primary_key.'`, '.$select;
      }
    }
    
    // SELECT anderen
    else {
      
      // primary_key hoeft er niet in
      if ( $key=array_search( $this->settings['primary_key'], $fields ) ) {
        unset($fields[$key]);
      }
      
      // Verzamel de velden
      $select_fields = array();
      foreach ($fields as $field) {
        $type = get_prefix($field);
        $select_fields[$field] = array(
          'type'        => $type,
          'add_slashes' => !in_array($type, $this->config->item('FIELDS_number_fields')) and !in_array($type, $this->config->item('FIELDS_bool_fields')),
          'field'       => $field,
          'select'      => '`' . ( $type==='many_to_one' ? $as_table : $other_table) . '`.`'.$field.'`',
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
    if (isset($foreign_key) and isset($this->tm_select[$foreign_key])) {
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
   * @return mixed FALSE als niet gelukt, anders de id van het aangepaste item
   * @author Jan den Besten
   */
	protected function _update_insert( $type, $set = NULL, $where = NULL, $limit = NULL ) {
    
    // Is een type meegegeven?
    $types = array('INSERT','UPDATE');
    if ( ! in_array($type,$types) ) {
      throw new ErrorException( __CLASS__.'->'.$method.'(): no type set, should be one of `'.implode(',',$types).'`' );
    }
    
    // Is user id nodig?
    if ( $this->field_exists('user_changed')) {
      if ( !isset( $this->user_id )) {
        $this->set_user_id();
      }
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
    if (isset($this->settings['relations']['many_to_many'])) {
      $many_to_many = array();
      foreach ( $this->settings['relations']['many_to_many'] as $rel_table => $relation_info ) {
        $other_table = $relation_info['other_table'];
        // tbl_... nieuwe manier van many_to_many data
        if (isset($set[$other_table])) {
          $many_to_many[$rel_table] = $set[$other_table];
          unset($set[$other_table]);
        }
        // rel_... oude manier van many_to_many data
        elseif (isset($set[$rel_table])) {
          $many_to_many[$rel_table] = $set[$rel_table];
          $many_to_many[$rel_table] = array_keys($many_to_many[$other_table]);
          unset($set[$rel_table]);
        }
      }
    }

    /**
     * Verwijder onnodige velden
     */
    unset($set[$this->settings['primary_key']]);
    unset($set['tme_last_changed']);

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
        $log = array(
          'query' => $this->db->last_query(),
          'table' => $this->settings['table'],
          'id'    => $id
        );
        $this->query_info = array(
          'insert_id' => $id
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
      
      
			/**
			 * Als er many_to_many data is, update/insert die ook
			 */
			if ( ! empty($many_to_many) ) {
        $affected = 0;
				foreach( $many_to_many as $rel_table => $other_ids ) {
          $other_table       = $this->settings['relations']['many_to_many'][$rel_table]['other_table'];
					$this_foreign_key  = $this->settings['relations']['many_to_many'][$rel_table]['this_key'];
          $other_foreign_key = $this->settings['relations']['many_to_many'][$rel_table]['other_key'];
          // if ( $this_foreign_key==$other_foreign_key ) $other_foreign_key.="_"; // TODO : self relaties?

					// DELETE eerst huidige items
					$this->db->where( $this_foreign_key, $id );
					$this->db->delete( $rel_table );
          $log['query'] .= ';'.PHP_EOL.PHP_EOL.$this->db->last_query();

					// INSERT dan nieuwe many_to_many ids
					foreach ( $other_ids as $other_id ) {
						$this->db->set( $this_foreign_key,  $id );
						$this->db->set( $other_foreign_key, $other_id );
						$this->db->insert( $rel_table );
            $affected++;
            // $rel_id=$this->db->insert_id();
            $log['query'] .= ';'.PHP_EOL.PHP_EOL.$this->db->last_query();
					}
				}
        $this->query_info['affected_rel_rows'] = $affected;
			}
		}
    
    if (isset($log)) {
      $this->log_activity->database( $log['query'], $log['table'], $log['id'] );
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
   * @return mixed FALSE als niet gelukt, anders array_result van verwijderde data
   * @author Jan den Besten
   */
	public function delete( $where = '', $limit = NULL, $reset_data = TRUE ) {
    
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
   * Geeft informatie van laatste query
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
