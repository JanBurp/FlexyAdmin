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

$config['key_id']='id_menu';
// $config['key_id']='id_blog';


/*
|--------------------------------------------------------------------------
| Send mail to site owner and other comment placers
|--------------------------------------------------------------------------
|
|
*/

$config['mail_owner']=TRUE;
$config['mail_others']=FALSE;


/*
|--------------------------------------------------------------------------
| Comments table settings
|--------------------------------------------------------------------------
|
| Set the database table and field names.
|
*/

$config['table']='tbl_comments';

$config['field_date']='tme_date';
$config['field_name']='str_name';
$config['field_email']='email_email';
$config['field_title']='str_title';
$config['field_text']='txt_text';

$config['field_spamscore']='int_spamscore';


/* End of file config.php */
/* Location: ./system/application/config/config.php */
