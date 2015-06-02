<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(SITEPATH.'/config/database.php');

// Set test database settings for phpunit testing
$db['phpunit']['hostname'] = "127.0.0.1";
$db['phpunit']['username'] = "root";
$db['phpunit']['password'] = "eonisme";
$db['phpunit']['database'] = "flexyadmin_demo";


// Always on
$query_builder = TRUE;

// Make sure all settings are ok
foreach (array('default','local','phpunit') as $group) {
  $db[$group]['dbdriver'] = "mysqli";
  $db[$group]['dbprefix'] = "";
  $db[$group]['pconnect'] = FALSE;
  $db[$group]['db_debug'] = TRUE;
  $db[$group]['cache_on'] = FALSE;
  $db[$group]['cachedir'] = "";
  $db[$group]['char_set'] = "utf8";
  $db[$group]['dbcollat'] = "utf8_general_ci";

  if (defined('PHPUNIT_TEST')) {
    $db[$group]['db_debug'] = FALSE;
  }
}

/* End of file database.php */
/* Location: ./system/application/config/database.php */
