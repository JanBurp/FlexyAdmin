<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Form fields
|--------------------------------------------------------------------------
|
| Velden gebruikt door het contact formulier.
| Je kunt hier meer velden toevoegen als je wilt.
| De labels kun je in de taalbestanden aanpassen.  
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
| De knoppen die onderaan het formulier komen te staan
*/

$config['form_buttons'] = array( 'submit'=>array('submit'=>'submit', 'value'=>lang('contact_submit')) );


/*
|--------------------------------------------------------------------------
| Sender gets copy
|--------------------------------------------------------------------------
|
| Als TRUE dan krijgt de bezoeker die het formulier invult zelf ook een exemplaar toegestuurd.
*/

$config['send_copy_to_sender'] = FALSE;


/*
|--------------------------------------------------------------------------
| From address field
|--------------------------------------------------------------------------
|
| Veld van het formulier waar het emailadres van de bezoeker in staat
*/

$config['from_address_field'] = 'email_email';


/*
|--------------------------------------------------------------------------
| Attachment folder
|--------------------------------------------------------------------------
|
| Als attachments meegestuurd moeten kunnen worden stel dan hier de map in waar ze naar worden geupload en welke bestanden zijn toegestaan.
*/

$config['attachment_folder'] = 'downloads';
$config['attachment_types'] = 'gif|jpg|png|doc|docx|xls|xlsx|pdf|zip';


