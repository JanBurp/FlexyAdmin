<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(SITEPATH.'/config/database.php');

$db[$active_group]['dbdriver'] = "mysqli";
$db[$active_group]['dbprefix'] = "";
$db[$active_group]['pconnect'] = FALSE;
$db[$active_group]['db_debug'] = FALSE;
$db[$active_group]['cache_on'] = FALSE;
$db[$active_group]['cachedir'] = "";
$db[$active_group]['char_set'] = "utf8";
$db[$active_group]['dbcollat'] = "utf8_general_ci";


/* End of file database.php */
/* Location: ./system/application/config/database.php */
