<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for table model '{NAME}' --- zie voor uitleg config/tables/table_model.php */

/**
 * Database tabel waarvoor deze instellingen (en model) geld.
 */
$config['table']            = NULL;

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
$config['fields']           = NULL;

/**
 * Per veld mogelijk meer informatie:
 * - validation         - array met validation rules
 * - options            - array met opties
 * - multiple_options   - TRUE dan zijn er meer dan één van bovenstaande options mogelijk
 * - path               - als het een media veld is dan komt hier het assets pad
 */
$config['field_info']       = NULL;

/**
 * Hier kan een standaard volgorde worden ingesteld waarin de resultaten worden getoond.
 * Kan een string zijn, een string gescheiden met komma's, of een array van strings.
 */
$config['order_by']         = NULL;

/**
 * Als de waarde groter is dan 0, dan is de tabel begrenst op een maximaal aantal rijen.
 * Een insert zal dat FALSE als resultaat geven
 */
$config['max_rows']         = NULL;

/**
 * Als een tabel een uri veld bevat kan deze automatisch worden aangepast na een update.
 * Hiermee kan dat aan of uit worden gezet.
 * Standaard staat deze optie aan (door _autoset)
 */
$config['update_uris']      = NULL;

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
 * Hier worden de relaties ingesteld die deze tabel heeft.
 * Hieronder enkele voorbeelden waarin alle foreign_keys en tabellen kunnen worden vastgelegd:
 *   
 * array(
 * 
 *  'many_to_one' => array(
 *
 *    'tbl_links'  => array(
 *      'other_table' => 'tbl_links',
 *      'foreign_key' => 'id_links',
 *    ),
 *
 *    'cfg_users_groups' => array(
 *      'other_table' => 'cfg_users_groups',
 *      'foreign_key' => 'id_user_group',
 *    ),
 *    
 *  'many_to_many' => array(
 *
 *    'tbl_links' => array(
 *      'other_table' => 'tbl_links',
 *      'rel_table'   => 'rel_menu__links',
 *      'this_key'    => 'id_menu',
 *      'other_key'   => 'id_links'
 *    ),
 *    
 *    'cfg_users_groups' => array(
 *      'other_table' => 'cfg_users_groups',
 *      'rel_table'   => 'rel_users__users_groups',
 *      'this_key'    => 'id_user',
 *      'other_key'   => 'id_user_group'
 *    ),
 *    
 */
$config['relations'] = NULL;

/**
 * Deze instellingen bepalen wat voor resultaat er wordt gegeven voor het admin grid.
 * Als een instelling leeg is wordt deze gezocht in de standaard instelling.
 * 
 * - fields         - Velden die meegegeven en getoond worden (afhankelijk van veld specifieke instellingen). Als leeg dan is dat hetzelfde als $this->fields
 * - order_by       - Volgorde voor het grid. Als leeg dan is dat hetzelfde als $this->order_by
 * - jump_to_today  - FALSE of een datum(tijd) veld waarmee in een grid met pagination naar de pagina gesprongen kan worden met de datum het dichtstbij vandaag.
 * - pagination     - Als true dan wordt het resultaat in pagina's gegeven.
 * - with           - Relaties die mee worden genomen en getoond. Zie $this->with()
 *                    Je kunt een specifiek datumveld instellen of TRUE: dan wordt het eerste datumveld opgezocht (wat extra resources kost)
 */
$config['grid_set'] = NULL;

/**
 * Deze instellingen bepalen wat voor resultaat er wordt gegeven voor het admin formulier.
 * Als een instelling leeg is wordt deze gezocht in de standaard instelling.
 * 
 * - fields         - Velden die meegegeven en getoond worden (afhankelijk van veld specifieke instellingen). Als leeg dan is dat hetzelfde als $this->fields
 * - with           - Relaties die mee worden genomen en getoond. Zie $this->with()
 * - fieldsets      - Fieldsets voor het formulier. Per fieldset kan aangegeven worden welke velden daarin verschijnen. Bijvoorbeeld: 'Fieldset naam' => array( 'str_title_en', 'txt_text_en' )
 */
$config['form_set'] = NULL;