<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for Data model '{NAME}' --- Created @ {DATE} */


/**
 * Database tabel waarvoor deze instellingen (en model) geld.
 */
$config['table']            = NULL;

/**
 * De tabellen die tegelijk met deze tabel verwijderd moeten worden uit de cache als één van die tabellen een update heeft gekregen.
 */
$config['cache_group']			= NULL;


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
 * - type               - form input type
 * - grid-type          - grid cell type
 * - default            - defaultwaarde
 * - readonly           - of het aangepast mag worden
 * - sortable           - of het in een grid een sorteerbare kolom word
 * - validation         - array met validation rules
 * - path               - als het een media veld is dan komt hier het assets pad
 */
$config['field_info']       = NULL;


/**
 * Per veld is het mogelijk om het aanpassen alleen voor bepaalde usergroups toe te laten.
 * Verwacht word een array met als key de veldnaam en als value een array met:
 * - 'types' 	- een array met edit types (INSERT,UPDATE,DELETE) - NB Werkt nu alleen nog met UPDATE #TODO#
 * - 'groups'	- een array met ids van de usergroups die dit veld mogen aanpassen
 * - 'where'	- hiermee kan nog een voorwaarde worden gesteld dmv een SQL WHERE. Zodat deze extra rechten alleen voor bepaalde items uit de tabel geld.
 */
/**
 * $config['restricted_rights'] = array(
 *   'b_restricted' => array(
 *     'groups' 	=> array(1),													// b_restricted mag alleen door super_admins(1) worden aangepast
 *     'where'  	=> '`str_module`="example"',					// dit geld alleen in het geval dat str_module="example", voor andere gevallen gelden de normale rechten
 *   ),
 * );
 */


/**
 * Per veld informatie over mogelijk opties
 * 
 * Eenvoudige opties met één keus:
 * - array( 'optie1'=>'optie1', 'optie2'=> 'optie2', '...' ) - in key=>value paren
 * 
 * Eenvoudige opties met meerdere keuzen:
 *  - array( 'data' => array(...bovenstaande array...), 'multiple' )
 * 
 * Alle mogelijkheden:
 *  - array(
 *    'data' => array()         - Array met opties (zie hierboven), of:
 *    'table' => ''             - naam van andere tabel waar de opties worden opgevraagd, of:
 *    'path'  => ''             - naam van map waar de opties worden opgevraagd (bestanden)
 *    'model' => ''             - naam van model waar de opties worden opgevraagd.
 *    'multiple' => TRUE/FALSE  - of het meerkeuze is
 * )
 */
$config['options'] = NULL;


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
 * Standaard staat deze optie aan [TRUE] (door _autoset)
 * 
 * Er kunnen ook opties in een array meegegeven worden in plaats van een TRUE:
 * - source           - Geef hier het veld aan dat gebruikt wordt als bron. Standaard is dat het eerste str veld wat gevonden kan worden. ['str_title']
 * - prefix           - Geef hier een eventuele prefix aan waarmee de uri altijd moet beginnen. ['']
 * - prefix_callback  - Geef hier een eventuele prefix callback method aan die de prefix bepaald. [object->method]
 * - freeze					  - Geef hier (in een array) eventuele items aan waarvan de uri niet mag worden aangepast. Bijvoorbeeld:
 * 	'uri' => 'freeze',
 * 	'uri' => array('freeze','keep','always_the_same'),
 * 	'id'	=> 2,
 * 	'id'	=> array(2,5,6),
 * 	
 * 	Als uris aangepast moeten kunnen worden, dan de volgende instellingen:
 * 	$config['field_info']['uri']['type'] = 'input';
 * 	$config['field_info']['uri']['readonly'] = false;
 * 	$config['update_uris'] = FALSE;
 */
$config['update_uris']      = NULL;

/**
 * NB Als uris aangepast moeten kunnen worden, dan de volgende instellingen:
 * $config['update_uris'] = FALSE;
 * $config['field_info']['uri']['type'] = 'input';
 * $config['field_info']['uri']['readonly'] = false;
 */


/**
 * Velden die gebruikt worden om een abstract veld samen te stellen.
 * Bijvoorbeeld voor het gebruik van dropdown velden in formulieren.
 * Als dit NULL is en er wordt een abstract gevraagd zullen de meest voor de hand liggende velden gekozen worden uit $this->fields.
 * Dat laatste kost extra resources.
 */
$config['abstract_fields']  = NULL;

/**
 * Karakter(s) die de diverse abstract_fields van elkaar scheiden.
 */
$config['abstract_delimiter']  = ' | ';


/**
 * Een where SQL die wordt gebruikt om een abstract resultaat te filteren indien nodig.
 */
$config['abstract_filter']  = '';


/**
 * Hier worden de relaties ingesteld die deze tabel heeft.
 * 
 * De array is onderverdeeld per relatiesoort bijvoorbeeld: ('many_to_one','many_to_many').
 * 
 * 'many_to_one' en 'one_to_one'
 * -----------------------------
 * 
 * - other_table  -> de foreign table
 * - foreign_key  -> de foreign_key (ook bij one_to_one nodig)
 * - result_name  -> onder welke naam de relatie data wordt toegevoegd. Standaard hetzelfde als 'other_table', maar bijvoorbeeld als er meerdere verwijzingen naar dezelfde other_table zijn, dan geeft dat problemen. Dat kan dan worden opgelost door hier een andere naam te geven.
 * 
 * Hieronder enkele voorbeelden voor many_to_one:
 *   
 * array(
 * 
 *  'many_to_one' => array(
 *
 *    'id_links'  => array(
 *      'other_table' => 'tbl_links',
 *      'foreign_key' => 'id_links',
 *      'result_name' => 'tbl_links',
 *    ),
 *
 *    'id_links_extra'  => array(
 *      'other_table' => 'tbl_links',
 *      'foreign_key' => 'id_links_extra',
 *      'result_name' => 'links_extra',
 *    ),
 * 
 *  )
 * 
 * 'one_to_many'
 * -------------
 * 
 * De other_table is de key van de array van de relatie.
 * Daarin komen de volgende velden:
 * 
 * - other_table  -> de foreign table (nogmaals)
 * - foreign_keys -> de foreign_key in de other_table
 * - result_name  -> onder welke naam de relatie data wordt toegevoegd. Standaard hetzelfde als 'other_table', maar bijvoorbeeld als er meerdere verwijzingen naar dezelfde other_table zijn, dan geeft dat problemen. Dat kan dan worden opgelost door hier een andere naam te geven.
 * 
 * Hieronder enkele voorbeelden voor many_to_one:
 *   
 * array(
 * 
 *  'one_to_many' => array(
 *
 *    'tbl_links'  => array(
 *      'other_table' => 'tbl_links',
 *      'foreign_key' => 'id_post',
 *      'result_name' => 'tbl_links',
 *    ),
 *
 *    'tbl_links_extra'  => array(
 *      'other_table' => 'tbl_links',
 *      'foreign_key' => 'id_post',
 *      'result_name' => 'links_extra',
 *    ),
 * )
 * 
 * 
 * 
 * 'many_to_many'
 * --------------
 * 
 * De relatie tabel is de key van de array van de relatie.
 * Daarin komen de volgende velden:
 * 
 * - other_table  -> de andere table
 * - rel_table    -> de relatie table (nogmaals)
 * - this_key     -> key die verwijst naar de eigen tabel
 * - other_key    -> key die verwijst naar de andere tabel
 * - result_name  -> onder welke naam de relatie data wordt toegevoegd. Standaard hetzelfde als 'other_table', maar bijvoorbeeld als er meerdere verwijzingen naar dezelfde other_table zijn, dan geeft dat problemen. Dat kan dan worden opgelost door hier een andere naam te geven.
 * 
 * Hieronder enkele voorbeelden voor many_to_many:
 * 
 *  'many_to_many' => array(
 *
 *    'rel_menu__links' => array(
 *    	'this_table'	=> 'tbl_menu',
 *      'other_table' => 'tbl_links',
 *      'rel_table'   => 'rel_menu__links',
 *      'this_key'    => 'id_menu',
 *      'other_key'   => 'id_links',
 *      'result_name' => 'tbl_links,
 *    ),
 *    
 *    'rel_menu__linksextra' => array(
 *    	'this_table'	=> 'tbl_menu',
 *      'other_table' => 'tbl_links',
 *      'rel_table'   => 'rel_menu__linksextra',
 *      'this_key'    => 'id_menu',
 *      'other_key'   => 'id_links',
 *      'result_name' => 'linksextra,
 *    ),
 * 
 *    'cfg_users_groups' => array(
 *    	'this_table'	=> 'tbl_menu',
 *      'other_table' => 'cfg_users_groups',
 *      'rel_table'   => 'rel_users__users_groups',
 *      'this_key'    => 'id_user',
 *      'other_key'   => 'id_user_group'
 *      'result_name' => 'cfg_users_groups,
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
 * - [fieldsets]    - Fieldsets voor het formulier. Standaard één fieldset met de hierboven ingestelde velden. Per fieldset kan aangegeven worden welke velden daarin verschijnen. Bijvoorbeeld: 'Fieldset naam' => array( 'str_title_en', 'txt_text_en' )
 */
$config['form_set'] = NULL;