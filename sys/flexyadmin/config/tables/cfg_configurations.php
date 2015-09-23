<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for table model 'cfg_configurations' --- */

/**
 * Database tabel waarvoor deze instellingen (en model) geld.
 */
$config['table'] = 'cfg_configurations';

/**
 * Primary key, standaard 'id'
 */
$config['primary_key'] = 'id';

/**
 * Key die wordt gebruikt bij $this->get_result(), standaard hetzelfde als $this->primary_key
 */
$config['result_key'] = 'id';

/**
 * Een array van velden die de tabel bevat.
 * Als het leeg is wordt het automatisch opgevraagd uit de database (met $this->db->list_fields() )
 * Dat heeft als voordeel dat het model 'out of the box' werkt, maar kost extra resources.
 */
$config['fields'] = array( 'id','int_pagination','b_use_editor','str_class','str_valid_html','table','b_add_internal_links','str_buttons1','str_buttons2','str_buttons3','int_preview_width','int_preview_height','str_formats','str_styles','txt_help','str_revision');

/**
 * Per veld mogelijk meer informatie:
 * - validation         - array met validation rules
 * - options            - array met opties
 * - multiple_options   - TRUE dan zijn er meer dan één van bovenstaande options mogelijk
 */
$config['field_info'] = array( 
		'id'                   => array( 'validation' => 'trim|integer|required' ), 
		'int_pagination'       => array( 'validation' => 'trim|integer' ), 
		'b_use_editor'         => array( 'validation' => '' ), 
		'str_class'            => array( 'options' => 'normal|high|wide|big', 'multiple_options' => false, 'validation' => 'max_length[10]|valid_option[normal,high,wide,big]' ), 
		'str_valid_html'       => array( 'validation' => 'max_length[255]' ), 
		'table'                => array( 'validation' => 'trim|max_length[50]' ), 
		'b_add_internal_links' => array( 'validation' => '' ), 
		'str_buttons1'         => array( 'validation' => 'max_length[255]' ), 
		'str_buttons2'         => array( 'validation' => 'max_length[255]' ), 
		'str_buttons3'         => array( 'validation' => 'max_length[255]' ), 
		'int_preview_width'    => array( 'validation' => 'trim|integer|max_length[4]' ), 
		'int_preview_height'   => array( 'validation' => 'trim|integer|max_length[4]' ), 
		'str_formats'          => array( 'validation' => 'max_length[255]' ), 
		'str_styles'           => array( 'validation' => 'max_length[100]' ), 
		'txt_help'             => array( 'validation' => '' ), 
		'str_revision'         => array( 'validation' => 'max_length[10]' ), 
	);

/**
 * Hier kan een standaard volgorde worden ingesteld waarin de resultaten worden getoond.
 */
$config['order_by'] = 'str_class';

/**
 * Als de waarde groter is dan 0, dan is de tabel begrenst op een maximaal aantal rijen.
 * Een insert zal dat FALSE als resultaat geven
 */
$config['max_rows'] = 0;

/**
 * Als een tabel een uri veld bevat kan deze automatisch worden aangepast na een update.
 * Hiermee kan dat aan of uit worden gezet.
 * Standaard staat deze optie aan (door _autoset)
 */
$config['update_uris'] = true;

/**
 * Velden die gebruikt worden om een abstract veld samen te stellen.
 * Bijvoorbeeld voor het gebruik van dropdown velden in formulieren.
 * Als dit NULL is en er wordt een abstract gevraagd zullen de meest voor de hand liggende velden gekozen worden uit $this->fields.
 * Dat laatste kost extra resources.
 */
$config['abstract_fields'] = array( 'int_pagination','str_class');

/**
 * Een where SQL die wordt gebruikt om een abstract resultaat te filteren indiend nodig.
 */
$config['abstract_filter'] = '';


/**
 * Welke relaties de tabel heeft met hun gekoppelde tabellen
 */
$config['relations'] = array( '');


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
$config['admin_grid'] = array( 
		'fields'        => array( 'id','int_pagination','b_use_editor','str_class','str_valid_html','table','b_add_internal_links','str_buttons1','str_buttons2','str_buttons3','int_preview_width','int_preview_height','str_formats','str_styles','txt_help'), 
		'order_by'      => 'str_class', 
		'jump_to_today' => true, 
		'pagination'    => true, 
		'with'          => array( ''), 
	);

/**
 * Deze instellingen bepalen wat voor resultaat er wordt gegeven voor het admin formulier.
 * Als een instelling leeg is wordt deze gezocht in de standaard instelling.
 * 
 * - fields         - Velden die meegegeven en getoond worden (afhankelijk van veld specifieke instellingen). Als leeg dan is dat hetzelfde als $this->fields
 * - with           - Relaties die mee worden genomen en getoond. Zie $this->with()
 * - fieldsets      - Fieldsets voor het formulier. Per fieldset kan aangegeven worden welke velden daarin verschijnen. Bijvoorbeeld: 'Fieldset naam' => array( 'str_title_en', 'txt_text_en' )
 */
$config['admin_form'] = array( 
		'fields'    => array( 'id','int_pagination','b_use_editor','str_class','str_valid_html','table','b_add_internal_links','str_buttons1','str_buttons2','str_buttons3','int_preview_width','int_preview_height','str_formats','str_styles','txt_help'), 
		'fieldsets' => array( 'cfg_configurations' => array( 'id','int_pagination','txt_help'), 'Editor' => array( 'b_use_editor','str_class','str_valid_html','table','b_add_internal_links','str_buttons1','str_buttons2','str_buttons3','int_preview_width','int_preview_height','str_formats','str_styles') ), 
		'with'      => array( ''), 
	);