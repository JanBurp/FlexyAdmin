<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup core
 * 
 * Doelen:
 * - alle gegevens over een database tabel in een eigen model, zodat geen cfg_table_info en cfg_field_info meer nodig is
 *  - daardoor: veel sneller
 * - standaard get/crud zit al in het model, voor elke tabel
 * - inclusief standaard relaties
 * - ieder tabel kan deze overerven een aanpassen naar wens, de aanroepen blijven hetzelfde voor ieder tabel
 * - het is in feite een uitbreiding op de query-builder, zodat straks Crud & zelfs MY_DB... weg kan
 * - Geeft alle data als query objecten terug! (zie CI handleiding)
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
   * Een array van velden die de tabel bevat.
   * Als het leeg is wordt het automatisch opgevraagd uit de database (met $this->db->list_fields() )
   * Dat heeft als voordeel dat het model 'out of the box' werkt, maar kost extra tijd.
   */
  protected $fields           = array();
  
  /**
   * Hier kan een standaard volgorde worden ingesteld waarin de resultaten worden getoond.
   */
  protected $order_by         = '';

  /**
   * Een array met alle relaties die het model standaard meeneemt in de resultaten.
   * Kan bij elke aanroep altijd met with() worden overruled.
   */
  protected $relations        = array(
                                  'belongs_to'       => array(),
                                  'many_to_many'     => array(),
                                  // 'has_many'      => array(),
                                  // 'has_one'       => array(),
                                );
  
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
   * Als dit leeg is en er wordt een abstract gevraagd zullen deze velden automatisch gekozen worden aan de hand van $this->fields.
   */
  protected $abstract_fields  = array();
  
  /**
   * Een where SQL die wordt gebruikt om een abstract resultaat te filteren indiend nodig.
   */
  protected $abstract_filter  = '';
  
  /**
   * Deze instellingen bepalen wat voor resultaat er wordt gegeven voor het admin grid.
   * Als een instelling leeg is wordt deze gezocht in de standaard instelling.
   * 
   * - fields         - Velden die meegegeven en getoond worden (afhankelijk van veld specifieke instellingen). Als leeg dan is dat hetzelfde als $this->fields
   * - relations      - Relaties die mee worden genomen en getoond. Als leeg dan is dat hetzelfde als $this->relations
   * - order_by       - Volgorde voor het grid. Als leeg dan is dat hetzelfde als $this->order_by
   * - jump_to_today  - Als het resultaat een datumveld bevat dan begint het resultaat op de pagina waar de datum het dichst de huidige datum benaderd.
   *                    Je kunt een specifiek datumveld instellen of TRUE: dan wordt het eerste datumveld opgezocht (wat extra resources kost)
   */
  protected $admin_grid       = array(
                                  'fields'            => array(),
                                  'relations'         => array(),
                                  'order_by'          => '',
                                  'jump_to_today'     => TRUE,
                                );

  /**
   * Deze instellingen bepalen wat voor resultaat er wordt gegeven voor het admin formulier.
   * Als een instelling leeg is wordt deze gezocht in de standaard instelling.
   * 
   * - fields         - Velden die meegegeven en getoond worden (afhankelijk van veld specifieke instellingen). Als leeg dan is dat hetzelfde als $this->fields
   * - relations      - Relaties die mee worden genomen en getoond. Als leeg dan is dat hetzelfde als $this->relations
   * - fieldsets      - Fieldsets voor het formulier. Per fieldset kan aangegeven worden welke velden daarin verschijnen. Bijvoorbeeld: 'Fieldset naam' => array( 'str_title_en', 'txt_text_en' )
   */
  protected $admin_form        = array(
                                  'fields'            => array(),
                                  'relations'         => array(),
                                  'fieldsets'         => array(),
                                );
  
  /* --- CONFIG --- */
  
  /**
   * Primary key, standaard 'id'
   */
  protected $primary_key      = PRIMARY_KEY;
  
  /**
   * Methods of the query builder that give data back instead of the querybuilder object.
   * Needed by __call() to use all query_builder methods
   */
  private $returning_qb_methods = array( 'get', 'get_where','get_compiled_select', 'get_compiled_insert','get_compiled_update','get_compiled_delete','count_all_results','count_all' );

  
  /* --- CONSTRUCT --- */

	public function __construct() {
		parent::__construct();
    // Autoset stuff that are nog set allready
    if (empty($this->table)) $this->table = get_class($this);
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
      if ( in_array( $method, $this->returning_qb_methods ) ) {
        return $result;
      }
      return $this;
    }
    throw new Exception( $method . ' does not exist in '.__CLASS__);
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
  
  
  /* --- Methods om de query te vormen --- */
  
  
  /**
   * Geef aan welke relaties meegenomen moeten worden in het resultaat
   *
   * @param string $relations 
   * @return void
   * @author Jan den Besten
   */
  public function with($relations) {
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
