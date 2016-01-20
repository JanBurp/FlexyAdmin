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
  'form-type'  => 'input',
  'format'     => 'string',
  'readonly'   => false,
  'validation' => '',
  'default'    => ''
);

/**
 * Fields by prefix
 */
$config['FIELDS_prefix'] = array (
	'id'				=> array (
    'type'        => 'integer',
    'form-type'   => 'select',
    'validation'  => 'trim|integer'
  ),
	'self' => array(
    'type'       => 'integer',
    'form-type'  => 'select',
    'default'    => 0,
    'validation' => 'trim|integer',
  ),
	'rel'				=> array (
    'type'  => 'integer',
    'form-type'       => 'select',
    'validation' => ''
  ),
	'field'			=> array(
    'type'  => 'string',
    'form-type'       => 'select',
    'validation' => 'trim',
  ),
	'fields'		=> array(
    'type'  => 'string',
    'form-type'       => 'select',
    'validation' => 'trim',
  ),											
	'media'			=> array (
    'type'  => 'string',
    'form-type'       => 'select',
    'validation' => 'trim'
  ),
	'medias'		=> array (
    'type'  => 'string',
    'form-type'       => 'select',
    'validation' => 'trim'
  ),
	'list'			=> array (
    'type'  => 'string',
    'form-type'       => 'select',
    'validation' => 'trim'
  ),
	'str'				=> array (
    'type'        => 'string',
    'form-type'   => 'text',
  ),
	'md'				=> array (
    'type'      => 'string',
    'form-type' => 'textarea',
  ),
	'stx'				=> array (
    'form-type' => 'textarea',
  ),
	'txt'				=> array (
    'form-type' => 'wysiwyg',
    'validation'=> ''
  ),
	'pwd'				=> array (
    'type'  => 'string',
    'form-type'       => 'password',
    'validation' => 'trim|valid_password'
  ),
	'gpw'				=> array (
    'type'  => 'string',
    'form-type'       => 'password',
    'validation' => 'trim|valid_password'
  ),
	'url'				=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim|prep_url'
  ),
	'email'			=> array (
    'type'        => 'string',
    'form-type'   => 'email',
    'validation'  => 'trim|valid_email'
  ),
	'file'			=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim'
  ),
	'mp3'				=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim'
  ),
	'mov'				=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim'
  ),
	'img'				=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim'
  ),
	'dat'				=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => ''
  ),
	'date'			=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => ''
  ),
	'tme'				=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => ''
  ),
	'datetime'	=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => ''
  ),
	'dtm'				=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => ''
  ),
	'time'			=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => ''
  ),
	'int'				=> array (
    'type'  => 'integer',
    'form-type'       => 'text',
    'validation' => 'trim|integer'
  ),
	'dec'				=> array (
    'type'  => 'number',
    'form-type'       => 'text',
    'validation' => 'trim|numeric'
  ),
	'ip'				=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim|valid_ip'
  ),
	'rgb'				=> array (
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim|valid_rgb'
  ),
	'b'             => array (
    'type'       => 'boolean',
    'form-type'  => 'checkbox',
    'validation' => ''
  ),
	'is'					=> array (
    'type'      => 'boolean',
    'validation' => ''
	),
);
                                  
                                  
/**
 * Special fields
 */
$config['FIELDS_special'] = array(
	'id' => array(
    'type'       => 'integer',
    'form-type'  => 'hidden',
    'readonly'   => true,
    'validation' => 'trim|integer|required',
    'default'    => -1,
  ),
	'id_group'	=> array(
    'type'  => 'integer',
    'form-type'       => 'select',
    'validation' => 'integer|required',
  ),
	'user'			=> array(
    'type'  => 'integer',
    'form-type'       => 'text',
    'validation' => 'trim|integer',
  ),
	'user_changed' => array(
    'type'  => 'integer',
    'form-type'       => 'text',
  ),
	'uri'				=> array(
    'type'      => 'string',
    'form-type' => 'hidden',
    'readonly'  => true,
    'validation'=> 'trim',
  ),
	'api'				=> array(
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim',
  ),
	'plugin'		=> array(
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim',
  ),
  'actions'   => array(
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => '',
  ),
	'order' => array(
    'type'      => 'string',
    'form-type' => 'hidden',
    'readonly'  => true,
    'default'   => 0,
    'validation'=> 'trim',
  ),
	'abstract'	=> array(
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => '',
  ),
	'table'			=> array(
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim',
  ),
	'rights'			=> array(
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim',
  ),
	'path'			=> array(
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim',
  ),
	'file'			=> array(
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim',
  ),
	'str_fieldset' => array(
    'type'  => 'string',
    'form-type'       => 'text',
    'validation' => 'trim'
  ),
                      
  'last_login'    => array(
    'type'  => 'string',
    'form-type'       => 'text',
  ),
										
);


