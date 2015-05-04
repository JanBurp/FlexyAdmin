<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(SITEPATH.'/config/database.php');

$query_builder = TRUE;

// Make sure all settings are ok
foreach (array('default','local') as $group) {
  $db[$group]['dbdriver'] = "mysqli";
  $db[$group]['dbprefix'] = "";
  $db[$group]['pconnect'] = FALSE;
  $db[$group]['db_debug'] = TRUE;
  $db[$group]['cache_on'] = FALSE;
  $db[$group]['cachedir'] = "";
  $db[$group]['char_set'] = "utf8";
  $db[$group]['dbcollat'] = "utf8_general_ci";
}

/* End of file database.php */
/* Location: ./system/application/config/database.php */
