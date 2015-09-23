<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for table model 'cfg_field_info' --- */

/**
 * Database tabel waarvoor deze instellingen (en model) geld.
 */
$config['table'] = 'cfg_field_info';

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
$config['fields'] = array( 'id','field_field','b_show_in_grid','b_show_in_form','str_show_in_form_where','str_fieldset','b_editable_in_grid','str_options','b_multi_options','b_ordered_options','str_options_where','str_validation_rules','str_validation_parameters');

/**
 * Per veld mogelijk meer informatie:
 * - validation         - array met validation rules
 * - options            - array met opties
 * - multiple_options   - TRUE dan zijn er meer dan één van bovenstaande options mogelijk
 */
$config['field_info'] = array( 
		'id'                        => array( 'validation' => 'trim|integer|required' ), 
		'field_field'               => array( 'validation' => 'trim|required|max_length[255]' ), 
		'b_show_in_grid'            => array( 'validation' => '' ), 
		'b_show_in_form'            => array( 'validation' => '' ), 
		'str_show_in_form_where'    => array( 'validation' => 'max_length[255]' ), 
		'str_fieldset'              => array( 'validation' => 'trim|max_length[100]' ), 
		'b_editable_in_grid'        => array( 'validation' => '' ), 
		'str_options'               => array( 'validation' => 'max_length[255]' ), 
		'b_multi_options'           => array( 'validation' => '' ), 
		'b_ordered_options'         => array( 'validation' => '' ), 
		'str_options_where'         => array( 'validation' => 'max_length[255]' ), 
		'str_validation_rules'      => array( 'options' => '|required|matches|min_length[]|max_length[]|exact_length[]|greater_than[]|less_than[]|alpha|alpha_numeric|alpha_dash|numeric|integer|decimal|is_natural|is_natural_no_zero|valid_email|valid_emails|valid_ip|valid_base64|prep_url|prep_url_mail|valid_rgb|valid_google_analytics|valid_password|valid_regex|valid_model_method', 'multiple_options' => true, 'validation' => 'max_length[255]|valid_options[,required,matches,min_length[],max_length[],exact_length[],greater_than[],less_than[],alpha,alpha_numeric,alpha_dash,numeric,integer,decimal,is_natural,is_natural_no_zero,valid_email,valid_emails,valid_ip,valid_base64,prep_url,prep_url_mail,valid_rgb,valid_google_analytics,valid_password,valid_regex,valid_model_method]' ), 
		'str_validation_parameters' => array( 'validation' => 'max_length[20]' ), 
	);

/**
 * Hier kan een standaard volgorde worden ingesteld waarin de resultaten worden getoond.
 */
$config['order_by'] = 'field_field';

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
$config['abstract_fields'] = array( 'str_show_in_form_where','str_fieldset');

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
		'fields'        => array( 'id','field_field','b_show_in_grid','b_show_in_form','str_show_in_form_where','str_fieldset','b_editable_in_grid','str_options','b_multi_options','b_ordered_options','str_options_where','str_validation_rules','str_validation_parameters'), 
		'order_by'      => 'field_field', 
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
		'fields'    => array( 'id','field_field','b_show_in_grid','b_show_in_form','str_show_in_form_where','str_fieldset','b_editable_in_grid','str_options','b_multi_options','b_ordered_options','str_options_where','str_validation_rules','str_validation_parameters'), 
		'fieldsets' => array( 'cfg_field_info' => array( 'id','field_field','b_show_in_grid','b_show_in_form','str_show_in_form_where','str_fieldset','b_editable_in_grid'), 'Options' => array( 'str_options','b_multi_options','b_ordered_options','str_options_where'), 'Validation' => array( 'str_validation_rules','str_validation_parameters') ), 
		'with'      => array( ''), 
	);