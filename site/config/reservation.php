<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Output routing of module
|--------------------------------------------------------------------------
*/

$config['__return']='';


/*
|--------------------------------------------------------------------------
| Database tabel
|--------------------------------------------------------------------------
|
| De tabel in de database waar de velden in staan die gevraagd en gevuld worden
*/

$config['table'] = 'tbl_example';


/*
|--------------------------------------------------------------------------
| Form buttons
|--------------------------------------------------------------------------
|
| De knoppen die onderaan het formulier komen te staan
*/

$config['form_buttons'] = array( 'submit'=>array('submit'=>'submit', 'value'=>'Submit') );

/*
|--------------------------------------------------------------------------
| Form action
|--------------------------------------------------------------------------
|
| Naar welke model wordt de actie doorgestuurd, standaard de database.
| Je kunt ook een array meegeven met meerdere acties
*/

// $config['formaction'] = 'formaction_database';
$config['formaction'] = array('formaction_database','formaction_mail');

