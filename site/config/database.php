<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * -------------------------------------------------------------------
 * DATABASE CONNECTIVITY SETTINGS
 * -------------------------------------------------------------------
 * This file will contain the settings needed to access your database.
 *
 * For complete instructions please consult the Database Connection
 * page of the User Guide.
 *
 * -------------------------------------------------------------------
 * EXPLANATION OF VARIABLES
 * -------------------------------------------------------------------
 *
 *	['hostname'] The hostname of your database server.
 *	['username'] The username used to connect to the database
 *	['password'] The password used to connect to the database
 *	['database'] The name of the database you want to connect to
 */

$active_group = "default";
$query_builder = TRUE;

$db['default']['hostname'] = "";
$db['default']['username'] = "";
$db['default']['password'] = "";
$db['default']['database'] = "";


/*
 * Check if localhost and database_local.php exists, load local settings.
 */
if (IS_LOCALHOST) {
	include("database_local.php");
}

/* End of file database.php */
/* Location: ./system/application/config/database.php */
