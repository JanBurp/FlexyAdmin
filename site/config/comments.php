<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Comments Foreign key
|--------------------------------------------------------------------------
|
| This field sets the connection to the the table where the comments belong to
| Change this also in you're database
|
*/

$config['comments']['key_id']='id_menu';
// $config['comments']['key_id']='id_blog';


/*
|--------------------------------------------------------------------------
| Send mail to site owner and other comment placers
|--------------------------------------------------------------------------
|
|
*/

$config['comments']['mail_owner']=TRUE;
$config['comments']['mail_others']=FALSE;


/*
|--------------------------------------------------------------------------
| Comments table settings
|--------------------------------------------------------------------------
|
| Set the database table and field names.
|
*/

$config['comments']['table']='tbl_comments';

$config['comments']['field_date']='date_date';
$config['comments']['field_name']='str_name';
$config['comments']['field_email']='email_email';
$config['comments']['field_title']='str_title';
$config['comments']['field_text']='txt_text';

$config['comments']['field_spamscore']='int_spamscore';


/*
|--------------------------------------------------------------------------
| Comments Language settings
|--------------------------------------------------------------------------
|
*/

$config['comments']['language']=''; // if empty it gets the language from the site

$config['comments']['nl']['title']='Reacties';
$config['comments']['nl']['submit']='Plaats reactie';
$config['comments']['nl']['spam']='<p class="error">Je reactie is gekenmerkt als spam en wordt niet geplaatst.</p>';
// for each field you can give an alternative label text:
$config['comments']['nl']['str_name']='Naam';
$config['comments']['nl']['email_email']='Email';
$config['comments']['nl']['str_title']='Titel';
$config['comments']['nl']['txt_text']='Je reactie';
$config['comments']['nl']['mail_to_owner_subject']='Nieuwe reactie op site: %s.';
$config['comments']['nl']['mail_to_owner_body']='Er is een nieuwe reactie geplaats bij %s.';
$config['comments']['nl']['mail_to_others_subject']='Nieuwe reactie op site: %s.';
$config['comments']['nl']['mail_to_others_body']='Er is een nieuwe reactie geplaats bij %s.';


$config['comments']['en']['title']='Comments';
$config['comments']['en']['submit']='Place comment';
$config['comments']['en']['spam']='<p class="error">This comment is recognised as spam.</p>';
// for each field you can give an alternative label text:
$config['comments']['en']['txt_text']='Comment';






/* End of file config.php */
/* Location: ./system/application/config/config.php */
