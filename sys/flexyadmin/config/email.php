<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Email
|
| Set you're email configuration here, it will override the standard settings|
| See http://codeigniter.com/user_guide/libraries/email.html
|
| Will be override by settings in site/config/email.php
| -------------------------------------------------------------------------
*/

$config['useragent'] = 'FlexyAdmin';

if (file_exists(SITEPATH."/config/email.php")) require_once(SITEPATH."/config/email.php");

?>