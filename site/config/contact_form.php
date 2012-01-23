<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Form fields
|--------------------------------------------------------------------------
|
*/

$config['form_fields'] = array(
                                'str_name'		  => array( 'label'=>lang('field__str_name'), 'validation'=>'required' ),
              									'email_email'	  => array( 'label'=>lang('field__email_email'), 'validation'	=>  'required|valid_email' ),
              									'txt_text'	    => array( 'label'=>lang('field__txt_text'), 'type'=>'textarea', 'validation'=>'required' )
                          );
                          

/*
|--------------------------------------------------------------------------
| Form buttons
|--------------------------------------------------------------------------
|
*/

$config['form_buttons'] = array( 'submit'=>array('submit'=>'submit', 'value'=>lang('contact_submit')) );


/*
|--------------------------------------------------------------------------
| From address field
|--------------------------------------------------------------------------
|
*/

$config['from_address_field'] = 'email_email';


