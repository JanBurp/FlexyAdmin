<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Schemaform settings for basic field(type)s
 * http://schemaform.io
 *
 * @author			Jan den Besten
 **/

$config['FIELDS_default'] = array(
    'schemaType' => 'string',
    'formType'   => 'text',
    'grid'       => '%s',
    'form'       => '',
    'validation' => '',
);
                                  
                                  

$config['FIELDS_special'] = array(

	'id'				=> array(
    'schemaType'  => 'integer',
    'formType'    => 'hidden',
    'grid'        => 'function_primary_key',
    'form'        => 'function_primary_key',
    'validation'	=> 'trim|integer|required',
  ),
	'id_group'	=> array(
    'schemaType'  => 'integer',
    'formType'    => 'select',
    'grid'        => 'function_foreign_key',
    'form'        => 'function_id_group',
    'validation'	=> 'integer|required',
  ),
	'user'			=> array(
    'schemaType'  => 'integer',
    'formType'    => 'text',
    'grid'        => 'function_user',
    'form'        => 'function_user',
    'validation'	=> 'trim|integer',
  ),
	'user_changed' => array(
    'schemaType'  => 'integer',
    'formType'    => 'text',
    'grid'        => 'function_user'
  ),
                      
	'uri'				=> array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '/%s',
    'form'        => 'hidden',
    'validation'	=> 'trim',
  ),
	'api'				=> array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_api',
    'validation'	=> 'trim',
  ),
	'plugin'		=> array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_plugin',
    'validation'	=> 'trim',
  ),
  'actions'   => array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => 'function_actions',
    'form'        => '',
    'validation'	=> '',
                      ),
	'order'			=> array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => 'function_order',
    'form'        => 'hidden',
    'validation'	=> 'trim',
  ),
	'abstract'	=> array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => '',
    'validation'	=> '',
  ),
	'table'			=> array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_tables',
    'validation'	=> 'trim',
  ),
	'rights'			=> array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_rights',
    'validation'	=> 'trim',
  ),
	'path'			=> array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_path',
    'validation'	=> 'trim',
  ),
	'file'			=> array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_allfiles',
    'validation'	=> 'trim',
  ),
	'str_fieldset' => array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'function_dropdown_fieldsets',
    'validation'	=> 'trim'
  ),
                      
  'last_login'    => array(
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => 'function_date("Y-m-d H:i:s",%s)',
  ),
										
);


$config['FIELDS_mysql'] = array(

);


$config['FIELDS_prefix'] = array (
	'id'				=> array (
    'schemaType'  => 'integer',
    'formType'    => 'select',
    'grid'        => 'function_foreign_key',
    'form'        => 'dropdown',
    'validation'	=> 'trim|integer'
  ),
	'self'			=> array(
    'schemaType'  => 'integer',
    'formType'    => 'select',
    'grid'        => 'function_self',
    'form'        => 'function_self',
    'validation'	=> 'trim|integer',
  ),
	'rel'				=> array (
    'schemaType'  => 'integer',
    'formType'    => 'select',
    'grid'        => 'function_join',
    'form'        => 'function_join',
    'validation'	=> ''
  ),
	'field'			=> array(
    'schemaType'  => 'string',
    'formType'    => 'select',
    'grid'        => '%s',
    'form'        => 'function_dropdown_field',
    'validation'	=> 'trim',
  ),
	'fields'		=> array(
    'schemaType'  => 'string',
    'formType'    => 'select',
    'grid'        => '%s',
    'form'        => 'function_dropdown_fields',
    'validation'	=> 'trim',
  ),											
	'media'			=> array (
    'schemaType'  => 'string',
    'formType'    => 'select',
    'grid'        => 'function_dropdown_media',
    'form'        => 'function_dropdown_media',
    'validation'	=> 'trim'
  ),
	'medias'		=> array (
    'schemaType'  => 'string',
    'formType'    => 'select',
    'grid'        => 'function_dropdown_medias',
    'form'        => 'function_dropdown_media',
    'validation'	=> 'trim'
  ),
	'list'			=> array (
    'schemaType'  => 'string',
    'formType'    => 'select',
    'grid'        => '%s',
    'form'        => 'function_dropdown_list',
    'validation'	=> 'trim'
  ),
	'str'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => ''
  ),
	'stx'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'textarea',
    'grid'        => 'function_text',
    'form'        => 'textarea'
  ),
	'md'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'textarea',
    'grid'        => 'function_text',
    'form'        => 'textarea'
  ),
	'txt'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'html',
    'grid'        => 'function_text',
    'form'        => 'htmleditor',
    'validation'	=> ''
  ),
	'pwd'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'password',
    'grid'        => '***',
    'form'        => 'password',
    'validation'	=> 'trim'
										),
	'gpw'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'password',
    'grid'        => '***',
    'form'        => 'password',
    'validation'	=> 'trim'
  ),
	'url'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '<a target="_blank" href="%s">%s</a>',
    'form'        => '',
    'validation'	=> 'trim|prep_url'
  ),
	'email'			=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '<a href="mailto:%s">%s</a>',
    'form'        => '',
    'validation'	=> 'trim|valid_email'
  ),
	'file'			=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'upload',
    'validation'	=> 'trim'
  ),
	'mp3'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'upload',
    'validation'	=> 'trim'
  ),
	'mov'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'upload',
    'validation'	=> 'trim'
  ),
	'img'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '<img src="#IMG_MAP#/%s" alt="%s" /><p class="img_text">%s</p>',
    'form'        => 'upload',
    'validation'	=> 'trim'
  ),
	'dat'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'date',
    'validation'	=> ''
  ),
	'date'			=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'date',
    'validation'	=> ''
  ),

	'tme'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'datetime',
    'validation'	=> ''
  ),
	'datetime'	=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'datetime',
    'validation'	=> ''
  ),
	'dtm'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'datetime',
    'validation'	=> ''
  ),
	'time'			=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => 'time',
    'validation'	=> ''
  ),
	'int'				=> array (
    'schemaType'  => 'integer',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => '',
    'validation'	=> 'trim|integer'
  ),
	'dec'				=> array (
    'schemaType'  => 'number',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => '',
    'validation'	=> 'trim|numeric'
  ),
	'ip'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '%s',
    'form'        => '',
    'validation'	=> 'trim|valid_ip'
  ),
	'rgb'				=> array (
    'schemaType'  => 'string',
    'formType'    => 'text',
    'grid'        => '<div class="rgb" style="background-color:%s;" title="%s"><span class="hide">%s</span></div>',
    'form'        => '',
    'validation'	=> 'trim|valid_rgb'
  ),
	'b'					=> array (
    'schemaType'  => 'boolean',
    'formType'    => 'checkbox',
    'grid'        => 'function_boolean',
    'form'        => 'checkbox',
    'validation'	=> ''
  ),
	'is'					=> array (
    'schemaType'  => 'boolean',
    'formType'    => 'checkbox',
    'grid'        => 'function_boolean',
    'form'        => 'checkbox',
    'validation'	=> ''
	),
);

