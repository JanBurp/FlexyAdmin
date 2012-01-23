<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Form fields
|--------------------------------------------------------------------------
|
*/

$config['form_fields'] = array(
                                'str_naam'		  => array('label'=>'naam','validation'=>'required'),
              									'email_email'	  => array('label'=>'email','validation'	=>  'required|valid_email'),
              									'txt_text'	    => array('label'=>'opmerking:','type'=>'textarea','validation'=>'required'));
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
| Form name
|--------------------------------------------------------------------------
|
*/

$config['form_name'] = 'Contact';

