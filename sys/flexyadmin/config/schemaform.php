<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Schemaform settings for basic field(type)s
 * http://schemaform.io
 *
 * @author			Jan den Besten
 **/



/**
 * Default field
 */
$config['FIELDS_default'] = array(
  'type'       => 'string',
  'form-type'  => 'text',
  'readonly'   => false,
  'format'     => 'string',
  'grid'       => '%s',
  'form'       => '',
  'validation' => '',
  'default'    => ''
);

/**
 * Fields by prefix
 */

$config['FIELDS_prefix'] = array (

  // Foreign key
	'id'				=> array (
    'type'        => 'number',
    'form-type'   => 'select',
    'grid'        => 'function_foreign_key',
    'form'        => 'dropdown',
    'validation'	=> 'trim|integer',
    'default'     => 0,
  ),
  
  // Self key
	'self'			=> array(
    'type'        => 'integer',
    'form-type'   => 'select',
    'grid'        => 'function_self',
    'form'        => 'function_self',
    'validation'	=> 'trim|integer',
    'default'     => 0,
  ),
  
	'rel'				=> array (
    'type'        => 'integer',
    'form-type'   => 'select',
    'grid'        => 'function_join',
    'form'        => 'function_join',
    'validation'	=> ''
  ),
	'tbl'				=> array (
    'type'        => 'integer',
    'form-type'   => 'select',
    'grid'        => 'function_join',
    'form'        => 'function_join',
    'validation'	=> ''
  ),

	'field'			=> array(
    'type'        => 'string',
    'form-type'   => 'select',
    'grid'        => '%s',
    'form'        => 'function_dropdown_field',
    'validation'	=> 'trim',
  ),
	'fields'		=> array(
    'type'        => 'string',
    'form-type'   => 'select',
    'grid'        => '%s',
    'form'        => 'function_dropdown_fields',
    'validation'	=> 'trim',
  ),							
  
  // Media/assets				
	'media'			=> array (
    'type'        => 'string',
    'form-type'   => 'media',
    'grid'        => 'function_dropdown_media',
    'form'        => 'function_dropdown_media',
    'validation'	=> 'trim'
  ),
	'medias'		=> array (
    'type'        => 'string',
    'form-type'   => 'media',
    'grid'        => 'function_dropdown_medias',
    'form'        => 'function_dropdown_media',
    'validation'	=> 'trim'
  ),
  
	'list'			=> array (
    'type'        => 'string',
    'form-type'   => 'select',
    'grid'        => '%s',
    'form'        => 'function_dropdown_list',
    'validation'	=> 'trim'
  ),
	'str'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'grid-edit'   => true,
    'form'        => ''
  ),
  
  // Large string
	'stx'				=> array (
    'type'        => 'string',
    'form-type'   => 'textarea',
    'grid'        => 'function_text',
    'form'        => 'textarea'
  ),
	'md'				=> array (
    'type'        => 'string',
    'form-type'   => 'textarea',
    'grid'        => 'function_text',
    'form'        => 'textarea'
  ),
  
  // HTML editor
	'txt'				=> array (
    'type'        => 'string',
    'form-type'   => 'wysiwyg',
    'grid'        => 'function_text',
    'form'        => 'htmleditor',
    'validation'	=> ''
  ),
  
  // Password
	'pwd'				=> array (
    'type'        => 'string',
    'form-type'   => 'password',
    'grid'        => '***',
    'form'        => 'password',
    'validation'  => 'trim|valid_password'
  ),
	'gpw'				=> array (
    'type'        => 'string',
    'form-type'   => 'password',
    'grid'        => '***',
    'form'        => 'password',
    'validation'  => 'trim|valid_password'
  ),
	'url'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '<a target="_blank" href="%s">%s</a>',
    'form'        => '',
    'validation'	=> 'trim|prep_url'
  ),
	'email'			=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '<a href="mailto:%s">%s</a>',
    'form'        => '',
    'validation'	=> 'trim|valid_email'
  ),
	'file'			=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'upload',
    'validation'	=> 'trim'
  ),
	'mp3'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'upload',
    'validation'	=> 'trim'
  ),
	'mov'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'upload',
    'validation'	=> 'trim'
  ),
	'img'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '<img src="#IMG_MAP#/%s" alt="%s" /><p class="img_text">%s</p>',
    'form'        => 'upload',
    'validation'	=> 'trim'
  ),
	'dat'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'date',
    'validation'	=> ''
  ),
	'date'			=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'date',
    'validation'	=> ''
  ),

	'tme'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'datetime',
    'validation'	=> ''
  ),
	'datetime'	=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'datetime',
    'validation'	=> ''
  ),
	'dtm'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'datetime',
    'validation'	=> ''
  ),
	'time'			=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'time',
    'validation'	=> ''
  ),
	'int'				=> array (
    'type'        => 'integer',
    'form-type'   => 'number',
    'grid'        => '%s',
    'form'        => '',
    'validation'	=> 'trim|integer'
  ),
	'dec'				=> array (
    'type'        => 'number',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => '',
    'validation'	=> 'trim|decimal'
  ),
	'ip'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => '',
    'validation'	=> 'trim|valid_ip'
  ),
	'rgb'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '<div class="rgb" style="background-color:%s;" title="%s"><span class="hide">%s</span></div>',
    'form'        => '',
    'validation'	=> 'trim|valid_rgb'
  ),
  
  // Booleans
	'b'					=> array (
    'type'        => 'boolean',
    'form-type'   => 'checkbox',
    'grid'        => 'function_boolean',
    'form'        => 'checkbox',
    'validation'	=> ''
  ),
	'is'					=> array (
    'type'        => 'boolean',
    'form-type'   => 'checkbox',
    'grid'        => 'function_boolean',
    'form'        => 'checkbox',
    'validation'	=> ''
	),
);



/**
 * Special fields
 */               

$config['FIELDS_special'] = array(

  // Primary key
	'id'				=> array(
    'type'        => 'number',
    'form-type'   => 'hidden',
    'readonly'    => true,
    'grid'        => 'function_primary_key',
    'form'        => 'function_primary_key',
    'validation'	=> 'trim|integer|required',
    'default'     => -1,
  ),

  // Aangemaakt door:
	'user'			=> array(
    'type'        => 'integer',
    'form-type'   => 'text',
    'grid'        => 'function_user',
    'form'        => 'function_user',
    'validation'	=> 'trim|integer',
  ),
  // Aangepast door:
	'user_changed' => array(
    'type'        => 'integer',
    'form-type'   => 'hidden',
    'readonly'    => true,
    'grid'        => 'function_user'
  ),
                      
	'uri'				=> array(
    'type'        => 'string',
    'form-type'   => 'hidden',
    'readonly'    => true,
    'grid'        => '/%s',
    'form'        => 'hidden',
    'validation'	=> 'trim',
  ),
	'api'				=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_api',
    'validation'	=> 'trim',
  ),
	'plugin'		=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_plugin',
    'validation'	=> 'trim',
  ),
  'actions'   => array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => 'function_actions',
    'form'        => '',
    'validation'	=> '',
  ),
  
  // Order, used for example in menu tables
	'order'			=> array(
    'type'        => 'number',
    'form-type'   => 'hidden',
    'readonly'    => true,
    'grid'        => 'function_order',
    'form'        => 'hidden',
    'validation'	=> 'trim',
    'default'     => 0,
  ),
  
	'abstract'	=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => '',
    'validation'	=> '',
  ),
	'table'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'dropdown',
    'validation'	=> 'trim',
  ),
	'rights'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_rights',
    'validation'	=> 'trim',
  ),
	'path'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'dropdown',
    'validation'	=> 'trim',
  ),
	'file'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_allfiles',
    'validation'	=> 'trim',
  ),
	'str_fieldset' => array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_fieldsets',
    'validation'	=> 'trim'
  ),
                      
  'last_login'    => array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid'        => 'function_date("Y-m-d H:i:s",%s)',
  ),
  
  // Tijdstip van laatste aanpassing
  'tme_last_changed' => array(
    'type'        => 'string',
    'form-type'   => 'hidden',
    'readonly'    => true,
  ),
										
);

