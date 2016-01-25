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
    'type'        => 'number',
    'form-type'   => 'select',
    'validation'  => ''
  ),
  
  'self' => array(
    'type'       => 'number',
    'form-type'  => 'select',
    'default'    => 0,
    'validation' => '',
  ),
  
  'b'             => array (
    'type'       => 'boolean',
    'form-type'  => 'checkbox',
    'validation' => ''
  ),
  'is'          => array (
    'type'      => 'boolean',
    'form-type'  => 'checkbox',
    'validation' => ''
  ),
  
  'stx'        => array (
    'form-type' => 'textarea',
  ),
  'txt'        => array (
    'form-type' => 'wysiwyg',
    'validation'=> ''
  ),
  
  'media'      => array (
    'type'      => 'string',
    'form-type' => 'select',
    'validation'=> ''
  ),
  'medias'    => array (
    'type'      => 'string',
    'form-type' => 'select',
    'validation'=> ''
  ),


  
  // 'rel'        => array (
  //     'type'  => 'number',
  //     'form-type'       => 'select',
  //     'validation' => ''
  //   ),
  // 'field'      => array(
  //     'type'  => 'string',
  //     'form-type'       => 'select',
  //     'validation' => '',
  //   ),
  // 'fields'    => array(
  //     'type'  => 'string',
  //     'form-type'       => 'select',
  //     'validation' => '',
  //   ),
  // 'list'      => array (
  //     'type'  => 'string',
  //     'form-type'       => 'select',
  //     'validation' => ''
  //   ),
  // 'str'        => array (
  //     'type'        => 'string',
  //     'form-type'   => 'text',
  //   ),
  // 'md'        => array (
  //     'type'      => 'string',
  //     'form-type' => 'textarea',
  //   ),
  // 'pwd'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'password',
  //     'validation' => '|valid_password'
  //   ),
  // 'gpw'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'password',
  //     'validation' => '|valid_password'
  //   ),
  // 'url'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '|prep_url'
  //   ),
  // 'email'      => array (
  //     'type'        => 'string',
  //     'form-type'   => 'email',
  //     'validation'  => '|valid_email'
  //   ),
  // 'file'      => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'mp3'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'mov'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'img'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'dat'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'date'      => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'tme'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'datetime'  => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'dtm'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'time'      => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  // 'int'        => array (
  //     'type'  => 'number',
  //     'form-type'       => 'text',
  //     'validation' => '|number'
  //   ),
  // 'dec'        => array (
  //     'type'  => 'number',
  //     'form-type'       => 'text',
  //     'validation' => '|numeric'
  //   ),
  // 'ip'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '|valid_ip'
  //   ),
  // 'rgb'        => array (
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '|valid_rgb'
  //   ),
);
                                  
                                  
/**
 * Special fields
 */
$config['FIELDS_special'] = array(
	'id' => array(
    'type'       => 'number',
    'form-type'  => 'hidden',
    'readonly'   => true,
    'validation' => '|number|required',
    'default'    => -1,
  ),
  'order' => array(
    'type'      => 'number',
    'form-type' => 'hidden',
    'readonly'  => true,
    'default'   => 0,
    'validation'=> '',
  ),
  
  // 'id_group'  => array(
  //     'type'       => 'number',
  //     'form-type'  => 'select',
  //     'validation' => 'number|required',
  //   ),
  // 'user'      => array(
  //     'type'        => 'number',
  //     'form-type'   => 'text',
  //     'validation'  => '|number',
  //   ),
  // 'user_changed' => array(
  //     'type'      => 'number',
  //     'form-type' => 'hidden',
  //     'readonly'  => true,
  //   ),
  //
  // 'uri'        => array(
  //     'type'      => 'string',
  //     'form-type' => 'hidden',
  //     'readonly'  => true,
  //     'validation'=> '',
  //   ),
  // 'api'        => array(
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '',
  //   ),
  // 'plugin'    => array(
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '',
  //   ),
  //   'actions'   => array(
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '',
  //   ),
  // 'abstract'  => array(
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '',
  //   ),
  // 'table'      => array(
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '',
  //   ),
  // 'rights'      => array(
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '',
  //   ),
  // 'path'      => array(
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '',
  //   ),
  // 'file'      => array(
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => '',
  //   ),
  // 'str_fieldset' => array(
  //     'type'  => 'string',
  //     'form-type'       => 'text',
  //     'validation' => ''
  //   ),
  //
  //   'last_login'    => array(
  //     'type'      => 'string',
  //     'form-type' => 'text',
  //   ),
  //   'tme_last_changed' => array(
  //     'type'      => 'string',
  //     'form-type' => 'hidden',
  //     'readonly'  => true,
  //   ),
										
);


