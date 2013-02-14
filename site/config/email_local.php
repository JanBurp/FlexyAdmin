<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
| -------------------------------------------------------------------------
| Local Email settings for testing
|
| Set you're email configuration here, it will override the standard settings
|
| See http://codeigniter.com/user_guide/libraries/email.html
|
| -------------------------------------------------------------------------
*/


// These settings are ready for the Mac App: MockSmtp 

$config['mailtype'] = 'html';
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'localhost';
$config['smtp_port'] = 1025;
$config['newline'] = "\r\n";



?>