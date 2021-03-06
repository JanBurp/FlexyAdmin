<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Instellingen voor unittest FrontEndTest
 */

/**
 * Standaard gebruikte testpagina $page
 */
$config['page']=array(
  'id'        => 1,
  'uri'       => 'home',
  'str_title' => 'Test',
  'txt_text'  => '<h1>Test</h1>',
);

/**
 * Welke modules moeten worden getest, en wat is de te verwachte output
 */
$config['modules'] = array(
  'Example' => array(
    'example'       => array('assertStringStartsWith','<h1>Voorbeeld Module</h1>'),
    'example.other' => array('assertStringStartsWith','<h1>Voorbeeld Module.Other</h1>'),
  ),
  // 'Ajax_example' => array(
  //   'ajax_example'        => array('assertEquals','{"message":"Ajax_example","module":"example","success":true}'),
  //   'ajax_example.other'  => array('assertEquals','{"message":"Ajax_example","method":"other","module":"example","success":true}'),
  // ),
);


/**
 * Welke formactions moeten worden getest, met welke instellingen
 */
$config['formactions'] = array( 
  
  'formaction_mail' => array(
    'settings' => array(
      'to' => 'test@flexyadmin.com',
    ),
    'fields' => array(
      'str_name'      => array( 'label'=> 'Name', 'validation'=>'required' ),
      'email_email'    => array( 'label'=> 'Email', 'validation'=>'required' ),
      'txt_text'      => array( 'label'=> 'Text', 'type'=>'txt', 'validation'=>'required' ),
    ),
    'message' => 'Formaction `formaction_mail` did not give success as result. Is a Email server ready?'
  ),
  
);



?>