<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Schemaform settings for basic field(type)s
 *
 * @author			Jan den Besten
 */


/**
 * Default field
 */
$config['FIELDS_default'] = array(
  'type'       => 'input',
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
    'type'        => 'select',
    'grid-type'   => 'abstract',
    'validation'	=> 'trim|integer',
    'default'     => 0,
  ),
  
  // Actie
	'action' => array(
    'type'      => 'hidden',
    'grid-type' => 'action',
    'readonly'  => true,
  ),
  
  // Self key
	'self'			=> array(
    'type'        => 'select',
    'validation'	=> 'trim|integer',
    'default'     => 0,
  ),
  
	'rel'				=> array(
    'type'        => 'select',
    'grid-type'   => 'relation',
    'validation'	=> ''
  ),
	'tbl'				=> array(
    'type'        => 'input',
    'grid-type'   => 'relation',
    'validation'	=> ''
  ),
	'cfg'				=> array(
    'type'        => 'input',
    'grid-type'   => 'relation',
    'validation'	=> ''
  ),

  // Media/assets				
	'media'			=> array(
    'type'        => 'media',
    'grid-type'   => 'media',
    'validation'	=> 'trim'
  ),
	'medias'		=> array(
    'type'        => 'media',
    'grid-type'   => 'media',
    'validation'	=> 'trim'
  ),
  
	'list'			=> array(
    'type'        => 'select',
    'validation'	=> 'trim'
  ),
	'str'				=> array(
    'type'        => 'input',
    'grid-edit'   => false,
  ),
  
  // Large string
	'stx'				=> array(
    'type'        => 'textarea',
  ),
	'md'				=> array(
    'type'        => 'textarea',
  ),
  
  // HTML editor
	'txt'				=> array(
    'type'        => 'wysiwyg',
    'validation'	=> ''
  ),
  
  // Password
	'pwd'				=> array(
    'type'        => 'password',
    'sortable'    => false,
    'validation'  => 'trim|valid_password'
  ),
	'gpw'				=> array(
    'type'        => 'password',
    'sortable'    => false,
    'validation'  => 'trim|valid_password'
  ),
	'url'				=> array(
    'type'        => 'input',
    'grid-type'   => 'url',
    'validation'	=> 'trim|prep_url_mail'
  ),
	'email'			=> array(
    'type'        => 'input',
    'validation'	=> 'trim|valid_email'
  ),
	'dat'				=> array(
    'type'        => 'date',
    'validation'	=> '',
    'default'     => '0000-00-00',
  ),
	'date'			=> array(
    'type'        => 'date',
    'validation'	=> '',
    'default'     => '0000-00-00',
  ),

	'tme'				=> array(
    'type'        => 'datetime',
    'validation'	=> '',
    'default'     => '0000-00-00 00:00',
  ),
	'datetime'	=> array(
    'type'        => 'datetime',
    'validation'	=> '',
    'default'     => '0000-00-00 00:00',
  ),
	'time'			=> array(
    'type'        => 'time',
    'validation'	=> '',
    'default'     => '00:00',
  ),

	'int'				=> array(
    'type'        => 'number',
    'validation'	=> 'trim|integer'
  ),
	'dec'				=> array(
    'type'        => 'number',
    'validation'	=> 'trim|decimal'
  ),
	'ip'				=> array(
    'type'        => 'input',
    'validation'	=> 'trim|valid_ip'
  ),
	'rgb'				=> array(
    'type'        => 'color',
    'validation'	=> 'trim|valid_rgb'
  ),
  
  // Booleans
	'b'					=> array(
    'type'        => 'checkbox',
    'grid-edit'   => true,
    'validation'	=> 'valid_option[0,1]'
  ),
	'is'					=> array(
    'type'        => 'checkbox',
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
    'type'        => 'hidden',
    'grid-type'   => 'primary',
    'readonly'    => true,
    'sortable'    => false,
    'validation'	=> 'trim|integer|required',
    'default'     => -1,
  ),

  // Aangemaakt door:
	'user'			=> array(
    'type'         => 'input',
    'readonly'     => true,
    'validation'   => 'trim|integer|required',
  ),
  // Aangepast door:
	'user_changed' => array(
    'type'        => 'hidden',
    'grid-type'   => 'abstract',
    'readonly'    => true,
    'validation'	=> 'trim|integer|required',
  ),
                      
	'uri'				=> array(
    'type'        => 'hidden',
    'readonly'    => true,
    'validation'	=> 'trim',
  ),
  'actions'   => array(
    'type'        => 'input',
    'sortable'    => false,
    'validation'	=> '',
  ),
  
  // Order, used for example in menu tables
	'order'			=> array(
    'type'       => 'hidden',
    'readonly'   => true,
    'sortable'   => false,
    'validation' => 'trim|integer|required',
    'default'    => 0,
  ),
	'self_parent'	  => array(
    'type'        => 'select',
    'grid-type'   => 'hidden',
    'sortable'    => false,
    'validation'	=> 'trim|integer',
    'default'     => 0,
  ),
  
	'abstract'	=> array(
    'type'        => 'input',
    'validation'	=> '',
  ),
  'rights'      => array(
    'type'        => 'string',
    'validation'  => 'trim',
  ),
	'file'			=> array(
    'type'        => 'input',
    'validation'	=> 'trim',
  ),
                      
  'last_login'    => array(
    'type'        => 'input',
  ),
  
  // Tijdstip van laatste aanpassing
  'tme_last_changed' => array(
    'type'        => 'hidden',
    'grid-type'   => 'string',
    'readonly'    => true,
  ),
  
  // FILES
  
  'path' => array(
    'type'       => 'select',
    'validation' => 'required|trim',
    'sortable'    => false,
  ),
  'media_thumb' => array(
    'type'       => 'media',
    'sortable'    => false,
    'validation' => 'required|trim',
  ),
  'type' => array(
    'type'      => 'input',
    'readonly'  => true
  ),
  'alt'        => array(
    'type'        => 'input',
    'grid-edit'   => true,
    'validation' => 'required|trim',
  ),
  'rawdate' => array(
    'type'      => 'hidden',
    'readonly'  => true,
    'sortable'  => false,
  ),
  'date' => array(
    'type'       => 'hidden',
    'grid-type'  => 'text',
    'readonly'   => true,
  ),
  'size' => array(
    'type'       => 'hidden',
    'grid-type'  => 'text',
    'readonly'   => true,
  ),
  'width' => array(
    'type'       => 'hidden',
    'grid-type'  => 'text',
    'readonly'   => true,
  ),
  'height' => array(
    'type'       => 'hidden',
    'grid-type'  => 'text',
    'readonly'   => true,
  ),
										
);

