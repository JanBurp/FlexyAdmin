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

$active_group = "local";
$query_builder = TRUE;

$db['local']['hostname'] = "localhost";
$db['local']['username'] = "root";
$db['local']['password'] = "eonisme";
$db['local']['database'] = "flexyadmin_demo";

/* End of file database.php */
/* Location: ./system/application/config/database.php */
