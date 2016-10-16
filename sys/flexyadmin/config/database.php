<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Set test database settings for phpunit testing
$db['phpunit']['hostname'] = "127.0.0.1";
$db['phpunit']['username'] = "root";
$db['phpunit']['password'] = "eonisme";
$db['phpunit']['database'] = "flexyadmin_test";

require_once(SITEPATH.'/config/database.php');


// Always on
$query_builder = TRUE;

// Make sure all settings are ok
foreach (array('default','local','import','phpunit') as $group) {
  $db[$group]['dbdriver'] = "mysqli";
  $db[$group]['dbprefix'] = "";
  $db[$group]['pconnect'] = FALSE;
  if (IS_LOCALHOST OR ENVIRONMENT==='testing')
    $db[$group]['db_debug'] = TRUE;
  else
    $db[$group]['db_debug'] = FALSE;
  $db[$group]['cache_on'] = FALSE;
  $db[$group]['cachedir'] = SITEPATH."cache/";
  $db[$group]['char_set'] = "utf8";
  $db[$group]['dbcollat'] = "utf8_general_ci";

  if (defined('PHPUNIT_TEST')) {
    $db[$group]['db_debug'] = FALSE;
  }
}




/* End of file database.php */
/* Location: ./system/application/config/database.php */
