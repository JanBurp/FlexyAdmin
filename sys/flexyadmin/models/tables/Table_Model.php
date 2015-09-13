<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup tables
 * 
 * - Alle gegevens over een database en zijn velden tabel als properties in een model (zodat geen cfg_table_info en cfg_field_info meer nodig is)
 * - Daardoor: veel sneller omdat de gegevens al beschikbaar zijn en niet te hoeven worden opgezocht (in db of cfg)
 * - Standaard get/crud zit al in het model, voor elke tabel
 * - Inclusief standaard relaties
 * - Iedere tabel kan deze overerven en aanpassen naar wens, de aanroepen blijven hetzelfde voor ieder tabel
 * - Alle query-builder methods (en db methods) kunnen worden gebruikt
 * - Geeft data als query objecten terug (zie CI handleiding)
 * - En ook enkele methods die de data als result arrays teruggeven met mooiere key en relatie-data
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

Class Table_Model extends CI_Model {
  
  /* --- SETTINGS --- */

  /**
   * Database tabel waarvoor dit model geld.
   * Automatisch wordt de naam van het model gebruikt in lowercase.
   * Maar kan hier ook ingesteld worden zodat het model een andere naam kah hebben als de tabel.
   */
  protected $table            = '';
  
  /**
   * Primary key, standaard 'id'
   */
  protected $primary_key      = PRIMARY_KEY;

  /**
   * Key die wordt gebruikt bij $this->get_result(), standaard hetzelfde als $this->primary_key
   */
  protected $result_key       = PRIMARY_KEY;
  
  /**
   * Een array van velden die de tabel bevat.
   * Als het leeg is wordt het automatisch opgevraagd uit de database (met $this->db->list_fields() )
   * Dat heeft als voordeel dat het model 'out of the box' werkt, maar kost extra tijd.
   */
  protected $fields           = array();
  
  /**
   * Per veld mogelijk meer informatie:
   * - validation         - array met validation rules
   * - options            - array met opties
   * - multiple_options   - TRUE dan zijn er meet dan één van bovenstaande options mogelijk
   */
  protected $field_info       = array();
  
  /**
   * Hier kan een standaard volgorde worden ingesteld waarin de resultaten worden getoond.
   */
  protected $order_by         = '';

  /**
   * Als de waarde groter is dan 0, dan is de tabel begrenst op een maximaal aantal rijen.
   * Een insert zal dat FALSE als resultaat geven
   */
  protected $max_rows         = 0;
  
  /**
   * Als een tabel een uri veld bevat kan deze automatisch worden aangepast na een update.
   * Hiermee kan dat aan of uit worden gezet.
   * Standaard staat deze optie aan.
   */
  protected $update_uris      = TRUE;
  
  /**
   * Velden die gebruikt worden om een abstract veld samen te stellen.
   * Bijvoorbeeld voor het gebruik van dropdown velden in formulieren.
   * Als dit NULL is en er wordt een abstract gevraagd zullen deze velden automatisch gekozen worden aan de hand van $this->fields.
   */
  protected $abstract_fields  = NULL;
  
  /**
   * Een where SQL die wordt gebruikt om een abstract resultaat te filteren indiend nodig.
   */
  protected $abstract_filter  = '';
  
  /**
   * Deze instellingen bepalen wat voor resultaat er wordt gegeven voor het admin grid.
   * Als een instelling leeg is wordt deze gezocht in de standaard instelling.
   * 
   * - fields         - Velden die meegegeven en getoond worden (afhankelijk van veld specifieke instellingen). Als leeg dan is dat hetzelfde als $this->fields
   * - order_by       - Volgorde voor het grid. Als leeg dan is dat hetzelfde als $this->order_by
   * - jump_to_today  - Als het resultaat een datumveld bevat dan begint het resultaat op de pagina waar de datum het dichst de huidige datum benaderd.
   * - with           - Relaties die mee worden genomen en getoond. Zie $this->with()
   *                    Je kunt een specifiek datumveld instellen of TRUE: dan wordt het eerste datumveld opgezocht (wat extra resources kost)
   */
  protected $admin_grid       = array(
                                  'fields'        => array(),
                                  'order_by'      => '',
                                  'with'          => array(),
                                  'jump_to_today' => TRUE,
                                );

  /**
   * Deze instellingen bepalen wat voor resultaat er wordt gegeven voor het admin formulier.
   * Als een instelling leeg is wordt deze gezocht in de standaard instelling.
   * 
   * - fields         - Velden die meegegeven en getoond worden (afhankelijk van veld specifieke instellingen). Als leeg dan is dat hetzelfde als $this->fields
   * - with           - Relaties die mee worden genomen en getoond. Zie $this->with()
   * - fieldsets      - Fieldsets voor het formulier. Per fieldset kan aangegeven worden welke velden daarin verschijnen. Bijvoorbeeld: 'Fieldset naam' => array( 'str_title_en', 'txt_text_en' )
   */
  protected $admin_form        = array(
                                  'fields'    => array(),
                                  'with'      => array(),
                                  'fieldsets' => array(),
                                );
  /* --- VARS --- */
  

  /**
   * Relations that need to be joined into result.
   */
  private $with               = array();
  

  /* --- CONSTRUCT  & AUTOSET --- */

	public function __construct() {
		parent::__construct();
    $this->autoset_table();
	}
  
  /**
   * Check if table is set, if not, autoset it
   *
   * @return this
   * @author Jan den Besten
   */
  public function autoset_table() {
    if (empty($this->table))  $this->table = get_class($this);
    return $this;
  }
  
  /**
   * Autoset stuff that is not set allready
   * Also usefull by using this without a special model for a table and using this for setting all
   *
   * @return $this
   * @author Jan den Besten
   */
  public function autoset() {
    $this->autoset_table();
    // Set defaults
    $this->fields           = array();
    $this->order_by         = '';
    $this->max_rows         = '0';
    $this->update_uris      = true;
    $this->abstract_fields  = null;
    $this->abstract_filter  = '';
    return $this;
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
   * Set a table so you can use this model for every table as a standard model
   *
   * @param string $table 
   * @return void
   * @author Jan den Besten
   */
  public function table( $table ) {
    $this->table = $table;
    $this->autoset();
    return $this;
  }
  
  
  /**
   * Geeft abstract_fields, als die nog niet zijn ingesteld zoek ze op en stel ze in.
   *
   * @param mixed $fields [''] als je hier een array van strings, of een komma gescheiden string met velden meegeeft wordt dat gebruikt.
   * @return array
   * @author Jan den Besten
   */
  public function get_abstract_fields( $fields='' ) {
    if ( is_null($this->abstract_fields) ) {
      $abstract_fields = array();
      if ( !empty($fields) ) {
        if ( !is_array($fields) ) $fields = explode( ',', $fields );
      }
      else {
        $fields=$this->list_fields( $this->table );
      }
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
      $this->abstract_fields = $abstract_fields;
    }
    return $this->abstract_fields;
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
		$sql = '`'.$this->table.'`.`'.$this->primary_key."`, CONCAT_WS('|',`".$this->table.'`.`' . implode( "`,`".$this->table.'`.`' ,$abstract_fields ) . "`) AS `" . $this->config->item('ABSTRACT_field_name') . "`";
    return $sql;
	}
  
  
  /* --- Getters for properties --- */

  /**
   * Geeft instellingen voor admin_grid
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_admin_grid() {
    return $this->admin_grid;
  }

  /**
   * Geeft instellingen voor admin_form
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_admin_form() {
    return $this->admin_form;
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
    if ( !empty($this->order_by) ) $this->db->order_by( $this->order_by );
    return $this->db->get( $this->table, $limit, $offset );
  }

  /**
   * Geeft abstract resultaat als query object. Eventueel beperkt door limit en offset
   *
   * @param int $limit [0]
   * @param int $offset [0] 
   * @return object $query
   * @author Jan den Besten
   */
  public function get_as_abstract( $limit=0, $offset=0 ) {
    $this->select( $this->get_compiled_abstract_select(), FALSE );
    return $this->get( $limit,$offset );
  }
  
  
  
  /**
   * Geeft één rij uit de database tabel, als query_row object, met het meegegeven id
   *
   * @param int $id 
   * @return object $query->row
   * @author Jan den Besten
   */
  public function get_one($id) {
    return $this->get_one_by( $this->primary_key, $id);
  }
  
  
  /**
   * Geeft één (of de eerste) rij uit de database tabel, als query_row object, waarvoor geld dat 'field' = 'value'
   *
   * @param string $field 
   * @param mixed $value 
   * @return object $query->row
   * @author Jan den Besten
   */
  public function get_one_by($field,$value) {
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
    foreach ($query->result_array() as $row) {
      if ( isset( $row[$this->result_key] ) ) {
        $key = $row[ $this->result_key ];
      }
      // TODO has_many en many_to_many als subarrays toevoegen aan row
      $result[$key] = $row;
    }
    return $result;
  }
  
  
  /**
   * Geeft resultaat terug als array
   * - array key is standaard de PRIMARY KEY maar kan ingesteld worden met $this->set_result_key()
   * - voor many_to_many en has_many wordt resultaat sub arrays in het resultaat.
   * 
   * NB Gebruik dit niet bij grote resultaten, of bij grote resultaten alleen met limit / pagination
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
   * Zelfde als get_result() maar dan als abstract
   *
   * @param int $limit [0]
   * @param int $offset [0] 
   * @return array
   * @author Jan den Besten
   */
  public function get_result_as_abstract( $limit=0, $offset=0 ) {
    $query = $this->get_as_abstract( $limit, $offset );
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


  /**
   * Zelfde als get_row(), maar nu als een abstract.
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_row_as_abstract() {
    $result = $this->get_result_as_abstract( 1 );
    return current($result);
  }
  
  

  
  /* --- Methods om de query te vormen --- */


  /**
   * Stel result key in voor gebruik bij $this->get_result()
   * Standaard hetzelfde als primary_key.
   *
   * @param string $key [''] Als leeg dan wordt primary_key gebruikt
   * @return $this
   * @author Jan den Besten
   */
  public function set_result_key( $key='' ) {
    if (empty($key)) $key = $this->primary_key;
    $this->result_key = $key;
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
    if (empty($this->fields)) {
      $this->fields = $this->db->list_fields( $this->table );
    }
    return $this->fields;
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
