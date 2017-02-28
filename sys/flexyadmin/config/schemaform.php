<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Schemaform settings for basic field(type)s
 * http://schemaform.io
 *
 * @author			Jan den Besten
 **/


/**
 * Set some values
 */
$config['current_date']     = date('Y-m-d');
$config['current_datetime'] = date('Y-m-d H:i:s');
$config['current_time']     = date('H:i:s');

/**
 * Default field
 */
$config['FIELDS_default'] = array(
  'type'       => 'string',
  'grid-type'  => 'text',
  'form-type'  => 'text',
  'readonly'   => false,
  'sortable'   => true,
  'validation' => '',
  'default'    => ''
);

/**
 * Fields by prefix
 */

$config['FIELDS_prefix'] = array (

  // Foreign key
	'id'				=> array(
    'type'        => 'number',
    'grid-type'   => 'abstract',
    'form-type'   => 'select',
    'validation'	=> 'trim|integer',
    'default'     => 0,
  ),
  
  // Actie
	'action' => array(
    'grid-type' => 'action',
    'form-type' => 'hidden',
    'readonly' => true,
  ),
  
  // Self key
	'self'			=> array(
    'type'        => 'integer',
    'form-type'   => 'select',
    'validation'	=> 'trim|integer',
    'default'     => 0,
  ),
  
	'rel'				=> array(
    'type'        => 'integer',
    'form-type'   => 'select',
    'grid-type'   => 'relation',
    'validation'	=> ''
  ),
	'tbl'				=> array(
    'type'        => 'integer',
    'form-type'   => 'string',
    'grid-type'   => 'relation',
    'validation'	=> ''
  ),
	'cfg'				=> array(
    'type'        => 'integer',
    'form-type'   => 'string',
    'grid-type'   => 'relation',
    'validation'	=> ''
  ),

	'field'			=> array(
    'type'        => 'string',
    'form-type'   => 'select',
    'validation'	=> 'trim',
  ),
	'fields'		=> array(
    'type'        => 'string',
    'form-type'   => 'select',
    'validation'	=> 'trim',
  ),							
  
  // Media/assets				
	'media'			=> array(
    'type'        => 'string',
    'grid-type'   => 'media',
    'form-type'   => 'media',
    'validation'	=> 'trim'
  ),
	'medias'		=> array(
    'type'        => 'string',
    'grid-type'   => 'media',
    'form-type'   => 'media',
    'validation'	=> 'trim'
  ),
  
	'list'			=> array(
    'type'        => 'string',
    'form-type'   => 'select',
    'validation'	=> 'trim'
  ),
	'str'				=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid-edit'   => false,
    'form'        => ''
  ),
  
  // Large string
	'stx'				=> array(
    'type'        => 'string',
    'form-type'   => 'textarea',
    'form'        => 'textarea'
  ),
	'md'				=> array(
    'type'        => 'string',
    'form-type'   => 'textarea',
    'form'        => 'textarea'
  ),
  
  // HTML editor
	'txt'				=> array(
    'type'        => 'string',
    'form-type'   => 'wysiwyg',
    'validation'	=> ''
  ),
  
  // Password
	'pwd'				=> array(
    'type'        => 'string',
    'form-type'   => 'password',
    'sortable'    => false,
    'validation'  => 'trim|valid_password'
  ),
	'gpw'				=> array(
    'type'        => 'string',
    'form-type'   => 'password',
    'sortable'    => false,
    'validation'  => 'trim|valid_password'
  ),
	'url'				=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid-type'   => 'url',
    'validation'	=> 'trim|prep_url_mail'
  ),
	'email'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim|valid_email'
  ),
	'file'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim'
  ),
	'mp3'				=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim'
  ),
	'mov'				=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim'
  ),
	'img'				=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim'
  ),
	'dat'				=> array(
    'type'        => 'string',
    'form-type'   => 'date',
    'validation'	=> '',
    'default'     => $config['current_date'],
  ),
	'date'			=> array(
    'type'        => 'string',
    'form-type'   => 'date',
    'validation'	=> '',
    'default'     => $config['current_date'],
  ),

	'tme'				=> array(
    'type'        => 'string',
    'form-type'   => 'datetime',
    'validation'	=> '',
    'default'     => $config['current_datetime'],
  ),
	'datetime'	=> array(
    'type'        => 'string',
    'form-type'   => 'datetime',
    'validation'	=> '',
    'default'     => $config['current_datetime'],
  ),
	'time'			=> array(
    'type'        => 'string',
    'form-type'   => 'time',
    'validation'	=> '',
    'default'     => $config['current_time'],
  ),

	'int'				=> array(
    'type'        => 'integer',
    'form-type'   => 'number',
    'validation'	=> 'trim|integer'
  ),
	'dec'				=> array(
    'type'        => 'number',
    'form-type'   => 'text',
    'validation'	=> 'trim|decimal'
  ),
	'ip'				=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim|valid_ip'
  ),
	'rgb'				=> array(
    'type'        => 'string',
    'form-type'   => 'color',
    'validation'	=> 'trim|valid_rgb'
  ),
  
  // Booleans
	'b'					=> array(
    'type'        => 'boolean',
    'grid-type'   => 'checkbox',
    'form-type'   => 'checkbox',
    'grid-edit'   => true,
    'validation'	=> 'valid_option[0,1]'
  ),
	'is'					=> array(
    'type'        => 'boolean',
    'grid-type'   => 'checkbox',
    'form-type'   => 'checkbox',
    'grid-edit'   => true,
    'validation'	=> 'valid_option[0,1]'
	),
);



/**
 * Special fields
 */               

$config['FIELDS_special'] = array(

  // Primary key
	'id'				=> array(
    'type'        => 'number',
    'grid-type'   => 'primary',
    'form-type'   => 'primary',
    'readonly'    => true,
    'sortable'    => false,
    'validation'	=> 'trim|integer|required',
    'default'     => -1,
  ),

  // Aangemaakt door:
	'user'			=> array(
    'type'        => 'integer',
    'form-type'   => 'text',
    'validation'	=> 'trim|integer|required',
  ),
  // Aangepast door:
	'user_changed' => array(
    'type'        => 'integer',
    'grid-type'   => 'abstract',
    'form-type'   => 'hidden',
    'readonly'    => true,
    'validation'	=> 'trim|integer|required',
  ),
                      
	'uri'				=> array(
    'type'        => 'string',
    'grid-type'   => 'hidden',
    'form-type'   => 'hidden',
    'readonly'    => true,
    'validation'	=> 'trim',
  ),
	'api'				=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim',
  ),
	'plugin'		=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim',
  ),
  'actions'   => array(
    'type'        => 'string',
    'form-type'   => 'text',
    'sortable'    => false,
    'validation'	=> '',
  ),
  
  // Order, used for example in menu tables
	'order'			=> array(
    'type'        => 'number',
    'grid-type'   => 'hidden',
    'form-type'   => 'hidden',
    'readonly'    => true,
    'sortable'    => false,
    'validation'	=> 'trim|integer|required',
    'default'     => 0,
  ),
	'self_parent'	  => array(
    'type'        => 'integer',
    'grid-type'   => 'hidden',
    'form-type'   => 'select',
    'sortable'    => false,
    'grid-type'   => 'hidden',
    'validation'	=> 'trim|integer',
    'default'     => 0,
  ),
  
	'abstract'	=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> '',
  ),
	'table'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim',
  ),
	'rights'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim',
  ),
	'path'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim',
  ),
	'file'			=> array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim',
  ),
	'str_fieldset' => array(
    'type'        => 'string',
    'form-type'   => 'text',
    'validation'	=> 'trim'
  ),
                      
  'last_login'    => array(
    'type'        => 'string',
    'form-type'   => 'text',
  ),
  
  // Tijdstip van laatste aanpassing
  'tme_last_changed' => array(
    'type'        => 'string',
    'grid-type'   => 'string',
    'form-type'   => 'hidden',
    'readonly'    => true,
  ),
  
  // FILES
  
  'path' => array(
    'type'       => 'string',
    'form-type'  => 'select',
    'grid-type'  => 'select',
    'validation' => 'required|trim',
    'sortable'    => false,
  ),
  'media_thumb' => array(
    'type'       => 'string',
    'form-type'  => 'media',
    'grid-type'  => 'media',
    'sortable'    => false,
    'validation' => 'required|trim',
  ),
  'type' => array(
    'type'      => 'string',
    'form-type' => 'text',
    'grid-type' => 'text',
    'readonly'  => true
  ),
  'alt'        => array(
    'type'        => 'string',
    'form-type'   => 'text',
    'grid-edit'   => true,
    'validation' => 'required|trim',
  ),
  'rawdate' => array(
    'type'      => 'date',
    'form-type' => 'hidden',
    'grid-type' => 'hidden',
    'readonly'  => true,
    'sortable'  => false,
  ),
  'date' => array(
    'type'       => 'date',
    'form-type'  => 'hidden',
    'grid-type'  => 'text',
    'readonly'   => true,
  ),
  'size' => array(
    'type'       => 'string',
    'form-type'  => 'hidden',
    'grid-type'  => 'text',
    'readonly'   => true,
  ),
  'width' => array(
    'type'       => 'string',
    'form-type'  => 'hidden',
    'grid-type'  => 'text',
    'readonly'   => true,
  ),
  'height' => array(
    'type'       => 'string',
    'form-type'  => 'hidden',
    'grid-type'  => 'text',
    'readonly'   => true,
  ),
										
);

