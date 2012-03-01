<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
|
| How many messages per page
*/
$config['messages_per_page'] = 10;

/*
|--------------------------------------------------------------------------
| Update emails
|--------------------------------------------------------------------------
|
*/
$config['send_mail_to_admin'] = TRUE;
$config['send_mail_to_thread_users'] = FALSE;



/*
|--------------------------------------------------------------------------
| Datetime format for messages
|--------------------------------------------------------------------------
|
| This will be used by the PHP's strftime() function.
*/
$config['datetime_format'] = '%e %b %Y %H:%M';

/*
|--------------------------------------------------------------------------
| Use tinyMCE editor
|--------------------------------------------------------------------------
*/
$config['use_tinymce'] = true;

/*
|--------------------------------------------------------------------------
| Attachments
|--------------------------------------------------------------------------
*/
$config['allow_attachments'] = true;
$config['attachment_folder'] = 'forum_files';
$config['attachment_types'] = 'gif|jpg|png|doc|docx|xls|xlsx|pdf|zip';


