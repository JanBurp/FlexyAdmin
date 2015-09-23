<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for table model 'cfg_img_info' --- */

/**
 * Database tabel waarvoor deze instellingen (en model) geld.
 */
$config['table'] = 'cfg_img_info';

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
$config['fields'] = array( 'id','path','int_min_width','int_min_height','b_resize_img','int_img_width','int_img_height','b_create_1','int_width_1','int_height_1','str_prefix_1','str_suffix_1','b_create_2','int_width_2','int_height_2','str_prefix_2','str_suffix_2');

/**
 * Per veld mogelijk meer informatie:
 * - validation         - array met validation rules
 * - options            - array met opties
 * - multiple_options   - TRUE dan zijn er meer dan één van bovenstaande options mogelijk
 */
$config['field_info'] = array( 
		'id'             => array( 'validation' => 'trim|integer|required' ), 
		'path'           => array( 'validation' => 'trim|required|max_length[255]' ), 
		'int_min_width'  => array( 'validation' => 'trim|integer' ), 
		'int_min_height' => array( 'validation' => 'trim|integer' ), 
		'b_resize_img'   => array( 'validation' => '' ), 
		'int_img_width'  => array( 'validation' => 'trim|integer' ), 
		'int_img_height' => array( 'validation' => 'trim|integer' ), 
		'b_create_1'     => array( 'validation' => '' ), 
		'int_width_1'    => array( 'validation' => 'trim|integer' ), 
		'int_height_1'   => array( 'validation' => 'trim|integer' ), 
		'str_prefix_1'   => array( 'validation' => 'max_length[10]' ), 
		'str_suffix_1'   => array( 'validation' => 'max_length[10]' ), 
		'b_create_2'     => array( 'validation' => '' ), 
		'int_width_2'    => array( 'validation' => 'trim|integer' ), 
		'int_height_2'   => array( 'validation' => 'trim|integer' ), 
		'str_prefix_2'   => array( 'validation' => 'max_length[10]' ), 
		'str_suffix_2'   => array( 'validation' => 'max_length[10]' ), 
	);

/**
 * Hier kan een standaard volgorde worden ingesteld waarin de resultaten worden getoond.
 */
$config['order_by'] = 'str_prefix_1';

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
$config['abstract_fields'] = array( 'int_min_width','int_min_height');

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
		'fields'        => array( 'id','path','int_min_width','int_min_height','b_resize_img','int_img_width','int_img_height','b_create_1','int_width_1','int_height_1','str_prefix_1','str_suffix_1','b_create_2','int_width_2','int_height_2','str_prefix_2','str_suffix_2'), 
		'order_by'      => 'str_prefix_1', 
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
		'fields'    => array( 'id','path','int_min_width','int_min_height','b_resize_img','int_img_width','int_img_height','b_create_1','int_width_1','int_height_1','str_prefix_1','str_suffix_1','b_create_2','int_width_2','int_height_2','str_prefix_2','str_suffix_2'), 
		'fieldsets' => array( 'cfg_img_info' => array( 'id','path','int_min_width','int_min_height','b_resize_img','int_img_width','int_img_height'), 'Size 1' => array( 'b_create_1','int_width_1','int_height_1','str_prefix_1','str_suffix_1'), 'Size 2' => array( 'b_create_2','int_width_2','int_height_2','str_prefix_2','str_suffix_2') ), 
		'with'      => array( ''), 
	);