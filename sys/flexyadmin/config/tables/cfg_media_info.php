<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for table model 'cfg_media_info' --- */

/**
 * Database tabel waarvoor deze instellingen (en model) geld.
 */
$config['table'] = 'cfg_media_info';

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
$config['fields'] = array( 'id','order','path','b_visible','str_types','b_encrypt_name','fields_media_fields','b_pagination','b_add_empty_choice','b_dragndrop','str_order','int_last_uploads','fields_check_if_used_in','str_autofill','fields_autofill_fields','b_in_media_list','b_in_img_list','b_in_link_list','b_user_restricted','b_serve_restricted');

/**
 * Per veld mogelijk meer informatie:
 * - validation         - array met validation rules
 * - options            - array met opties
 * - multiple_options   - TRUE dan zijn er meer dan één van bovenstaande options mogelijk
 */
$config['field_info'] = array( 
		'id'                      => array( 'validation' => 'trim|integer|required' ), 
		'order'                   => array( 'validation' => 'trim' ), 
		'path'                    => array( 'validation' => 'trim|required|max_length[255]' ), 
		'b_visible'               => array( 'validation' => '' ), 
		'str_types'               => array( 'validation' => 'required|max_length[100]' ), 
		'b_encrypt_name'          => array( 'validation' => '' ), 
		'fields_media_fields'     => array( 'validation' => 'trim|max_length[100]' ), 
		'b_pagination'            => array( 'validation' => '' ), 
		'b_add_empty_choice'      => array( 'validation' => '' ), 
		'b_dragndrop'             => array( 'validation' => '' ), 
		'str_order'               => array( 'options' => 'name|_name|rawdate|_rawdate|type|_type|size|_size|width|_width|height|_height', 'multiple_options' => false, 'validation' => 'max_length[10]|valid_option[name,_name,rawdate,_rawdate,type,_type,size,_size,width,_width,height,_height]' ), 
		'int_last_uploads'        => array( 'validation' => 'trim|integer' ), 
		'fields_check_if_used_in' => array( 'validation' => 'trim|max_length[50]' ), 
		'str_autofill'            => array( 'options' => '|single upload|bulk upload|both', 'multiple_options' => false, 'validation' => 'max_length[20]|valid_option[,single upload,bulk upload,both]' ), 
		'fields_autofill_fields'  => array( 'validation' => 'trim|max_length[255]' ), 
		'b_in_media_list'         => array( 'validation' => '' ), 
		'b_in_img_list'           => array( 'validation' => '' ), 
		'b_in_link_list'          => array( 'validation' => '' ), 
		'b_user_restricted'       => array( 'validation' => '' ), 
		'b_serve_restricted'      => array( 'validation' => '' ), 
	);

/**
 * Hier kan een standaard volgorde worden ingesteld waarin de resultaten worden getoond.
 */
$config['order_by'] = 'order';

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
$config['abstract_fields'] = array( 'str_types','str_order');

/**
 * Een where SQL die wordt gebruikt om een abstract resultaat te filteren indiend nodig.
 */
$config['abstract_filter'] = '';

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
		'fields'        => array( 'id','order','path','b_visible','str_types','b_encrypt_name','fields_media_fields','b_pagination','b_add_empty_choice','b_dragndrop','str_order','int_last_uploads','fields_check_if_used_in','str_autofill','fields_autofill_fields','b_in_media_list','b_in_img_list','b_in_link_list','b_user_restricted','b_serve_restricted'), 
		'order_by'      => 'order', 
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
		'fields'    => array( 'id','order','path','b_visible','str_types','b_encrypt_name','fields_media_fields','b_pagination','b_add_empty_choice','b_dragndrop','str_order','int_last_uploads','fields_check_if_used_in','str_autofill','fields_autofill_fields','b_in_media_list','b_in_img_list','b_in_link_list','b_user_restricted','b_serve_restricted'), 
		'fieldsets' => array( 'cfg_media_info' => array( 'id','order','path','b_visible','str_types','b_encrypt_name','fields_media_fields','b_pagination','b_add_empty_choice','b_dragndrop'), 'More' => array( 'str_order','int_last_uploads','fields_check_if_used_in','str_autofill','fields_autofill_fields','b_in_media_list','b_in_img_list','b_in_link_list','b_user_restricted','b_serve_restricted') ), 
		'with'      => array( ''), 
	);