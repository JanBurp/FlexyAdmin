<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for table model '{NAME}' --- */

/**
 * Database tabel waarvoor deze instellingen (en model) geld.
 */
$config['table']            = '';

/**
 * Primary key, standaard 'id'
 */
$config['primary_key']      = PRIMARY_KEY;

/**
 * Key die wordt gebruikt bij $this->get_result(), standaard hetzelfde als $this->primary_key
 */
$config['result_key']       = PRIMARY_KEY;

/**
 * Een array van velden die de tabel bevat.
 * Als het leeg is wordt het automatisch opgevraagd uit de database (met $this->db->list_fields() )
 * Dat heeft als voordeel dat het model 'out of the box' werkt, maar kost extra resources.
 */
$config['fields']           = array();

/**
 * Per veld mogelijk meer informatie:
 * - validation         - array met validation rules
 * - options            - array met opties
 * - multiple_options   - TRUE dan zijn er meer dan één van bovenstaande options mogelijk
 */
$config['field_info']       = array();

/**
 * Hier kan een standaard volgorde worden ingesteld waarin de resultaten worden getoond.
 */
$config['order_by']         = '';

/**
 * Als de waarde groter is dan 0, dan is de tabel begrenst op een maximaal aantal rijen.
 * Een insert zal dat FALSE als resultaat geven
 */
$config['max_rows']         = 0;

/**
 * Als een tabel een uri veld bevat kan deze automatisch worden aangepast na een update.
 * Hiermee kan dat aan of uit worden gezet.
 * Standaard staat deze optie aan (door _autoset)
 */
$config['update_uris']      = '';

/**
 * Velden die gebruikt worden om een abstract veld samen te stellen.
 * Bijvoorbeeld voor het gebruik van dropdown velden in formulieren.
 * Als dit NULL is en er wordt een abstract gevraagd zullen de meest voor de hand liggende velden gekozen worden uit $this->fields.
 * Dat laatste kost extra resources.
 */
$config['abstract_fields']  = NULL;

/**
 * Een where SQL die wordt gebruikt om een abstract resultaat te filteren indiend nodig.
 */
$config['abstract_filter']  = '';


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
$config['admin_grid']       = array();

/**
 * Deze instellingen bepalen wat voor resultaat er wordt gegeven voor het admin formulier.
 * Als een instelling leeg is wordt deze gezocht in de standaard instelling.
 * 
 * - fields         - Velden die meegegeven en getoond worden (afhankelijk van veld specifieke instellingen). Als leeg dan is dat hetzelfde als $this->fields
 * - with           - Relaties die mee worden genomen en getoond. Zie $this->with()
 * - fieldsets      - Fieldsets voor het formulier. Per fieldset kan aangegeven worden welke velden daarin verschijnen. Bijvoorbeeld: 'Fieldset naam' => array( 'str_title_en', 'txt_text_en' )
 */
$config['admin_form']        = array();