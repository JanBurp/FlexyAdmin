<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

/**
 * FlexyAdmin
 * 
 * A flexible userfriendly CMS build on CodeIgniter
 * 
 * Copyright (c) 2009-2016, Jan den Besten, www.flexyadmin.com
 * All rights reserved.
 * 
 * Disclaimer
 * 
 * De voorwaarden van deze disclaimer zijn van toepassing op het Content Management Systeem ‘FlexyAdmin’ (hierna te noemen ‘CMS’) ontwikkeld door Jan den Besten.
 * Door het CMS te gebruiken stemt u (hierna te noemen ‘gebruiker’) in met deze disclaimer.
 * 
 * De rechten op de inhoud van het CMS waaronder de rechten van intellectuele eigendom berusten bij Jan den Besten.
 * Onder de inhoud van dit CMS wordt onder meer verstaan: functionaliteit, ontwerpstructuur, database-structuur, teksten, lay-out, afbeeldingen, logo's, (beeld)merken, geluids- en/of videofragmenten, foto's, hulpdocumenten en andere artikelen exclusief alle inhoud die de gebruiker toevoegd.
 * Het maken van kopieën, aanpassingen, bewerkingen, wijzigingen van het geheel of van een gedeelte van het CMS in welke vorm of op welke manier dan ook zonder voorafgaande schriftelijke toestemming van Jan den Besten, is niet toegestaan.
 * 
 * Jan den Besten besteedt de uiterste zorg aan het zo actueel, toegankelijk, correct en compleet mogelijk maken en houden van de inhoud en de werking van het CMS.
 * De inhoud van het CMS houdt geen aanbieding in en er kunnen geen rechten aan worden ontleend.
 * 
 * Aanvullend biedt het CMS de mogelijkheid om te werken met profielen en persoonsinformatie.
 * Jan den Besten besteedt de uiterste zorg aan het zo veilig mogelijk maken en houden van deze informatie.
 * Door het CMS te gebruiken stemt u ermee in dat Jan den Besten op geen enkele wijze verantwoordelijk kan worden gehouden voor eventuele misstanden betreffende deze gegevens en/of voor eventuele gevolgschade.
 * Indien er zich een probleem voordoet, dient de gebruiker het probleem eerst en tijdig aan Jan den Besten aan te bieden, zodat naar een passende oplossing kan worden gezocht.
 * 
 * Dit CMS kan links bevatten naar websites of naar webpagina’s van derden.
 * Jan den Besten heeft geen zeggenschap over de inhoud of over andere kenmerken van deze websites en -pagina’s van derden en is in geen geval aansprakelijk of verantwoordelijk voor de inhoud ervan.
 * 
 * Alle rechten worden voorbehouden.
 * Op deze disclaimer is het Nederlands recht van toepassing.
 * 
 * Laatst bijgewerkt: mei 2016
 * 
 * @package	FlexyAdmin
 * @author	Jan den Besten
 * @copyright	(c) Jan den Besten
 */


/*
 *------------------------------------------------------------------------------------------------
 * FLEXYADMIN: Installed with safe folder structure
 *------------------------------------------------------------------------------------------------
 */
// define('SAFE_INSTALL', false);
define('SAFE_INSTALL', true);


/*
 *------------------------------------------------------------------------------------------------
 * FLEXYADMIN: Set debugging features on/off
 *------------------------------------------------------------------------------------------------
 */
define('DEBUGGING', false);
// define('DEBUGGING', true);

/*
 *------------------------------------------------------------------------------------------------
 * FLEXYADMIN: Set the emailadress of the webmaster here, bug reports will be send to this address
 *------------------------------------------------------------------------------------------------
 */
// define("ERROR_EMAIL","bug@flexyadmin.com");

/*
 *------------------------------------------------------------------------------------------------
 * FLEXYADMIN: Set the default timezone
 *------------------------------------------------------------------------------------------------
 */
date_default_timezone_set('Europe/Amsterdam');

/*
 *---------------------------------------------------------------
 * FLEXYADMIN: set IS_LOCALHOST, for a local enverinment
 *---------------------------------------------------------------
 * You can set several localhosts if needed.
 */
if (defined('PHPUNIT_TEST')) {
  define("IS_LOCALHOST",TRUE);
}
else {
  define("LOCALHOSTS","0.0.0.0,localhost,localhost:8888,10.37.129.2");
  function is_local_host() { $is=FALSE; $localhosts=explode(",",LOCALHOSTS); foreach ($localhosts as $host) { if ($host==$_SERVER['HTTP_HOST']) { $is=TRUE; } } return $is; }
  
  // HTTP SERVER?
  if (isset($_SERVER['HTTP_HOST'])) {
    if (is_local_host())
      define("IS_LOCALHOST",TRUE);
    else
    	define("IS_LOCALHOST",FALSE);
  }
  // or CLI?
  elseif (isset($_SERVER['SHELL']))
    define("IS_LOCALHOST",TRUE);
  else
    define("IS_LOCALHOST",FALSE);
}


/*
 *---------------------------------------------------------------
 * FLEXYADMIN: IS AJAX request?
 *---------------------------------------------------------------
 */
if ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
  define("IS_AJAX",true);
else
  define("IS_AJAX",false);




/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */

/*
 * FLEXYADMIN: Set according to IS_LOCALHOST
 */
if (defined('PHPUNIT_TEST') or DEBUGGING) {
  define('ENVIRONMENT','testing');
}
elseif (IS_LOCALHOST) {
  define('ENVIRONMENT', 'development');
}
else {
  define('ENVIRONMENT', 'production');
}


/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch (ENVIRONMENT)
{
	case 'development':
	case 'testing':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR
}

/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same directory
 * as this file.
 */

// FLEXYADMIN changes
if (SAFE_INSTALL) {
	$system_path = '../sys'.DIRECTORY_SEPARATOR.'codeigniter';
  $site_folder = '../site';
}
else {
  $system_path = 'sys'.DIRECTORY_SEPARATOR.'codeigniter';
  $site_folder = 'site';
}



/*
 *---------------------------------------------------------------
 * APPLICATION DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * directory than the default one you can set its name here. The directory
 * can also be renamed or relocated anywhere on your server. If you do,
 * use an absolute (full) server path.
 * For more info please see the user guide:
 *
 * https://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 */

// FLEXYADMIN changes
if (SAFE_INSTALL) {
	$application_folder = '../sys'.DIRECTORY_SEPARATOR.'flexyadmin';
}
else {
  $application_folder = 'sys'.DIRECTORY_SEPARATOR.'flexyadmin';
}


/*
 *---------------------------------------------------------------
 * VIEW DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want to move the view directory out of the application
 * directory, set the path to it here. The directory can be renamed
 * and relocated anywhere on your server. If blank, it will default
 * to the standard location inside your application directory.
 * If you do move this, use an absolute (full) server path.
 *
 * NO TRAILING SLASH!
 */
	$view_folder = '';


/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here. For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT: If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller. Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 */
	// The directory name, relative to the "controllers" directory.  Leave blank
	// if your controller is not in a sub-directory within the "controllers" one
	// $routing['directory'] = '';

	// The controller class file name.  Example:  mycontroller
	// $routing['controller'] = '';

	// The controller function you wish to be called.
	// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 */
	// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (($_temp = realpath($system_path)) !== FALSE)
	{
		$system_path = $_temp.DIRECTORY_SEPARATOR;
	}
	else
	{
		// Ensure there's a trailing slash
		$system_path = strtr(
			rtrim($system_path, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		).DIRECTORY_SEPARATOR;
	}

	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
		exit(3); // EXIT_CONFIG
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// Path to the system directory
	define('BASEPATH', $system_path);

	// Path to the front controller (this file) directory
	define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

	// Name of the "system" directory
	define('SYSDIR', basename(BASEPATH));

	// The path to the "application" directory
	if (is_dir($application_folder))
	{
		if (($_temp = realpath($application_folder)) !== FALSE)
		{
			$application_folder = $_temp;
		}
		else
		{
			$application_folder = strtr(
				rtrim($application_folder, '/\\'),
				'/\\',
				DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
			);
		}
	}
	elseif (is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR))
	{
		$application_folder = BASEPATH.strtr(
			trim($application_folder, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		);
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}

	define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);

	// The path to the "views" directory
	if ( ! isset($view_folder[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
	{
		$view_folder = APPPATH.'views';
	}
	elseif (is_dir($view_folder))
	{
		if (($_temp = realpath($view_folder)) !== FALSE)
		{
			$view_folder = $_temp;
		}
		else
		{
			$view_folder = strtr(
				rtrim($view_folder, '/\\'),
				'/\\',
				DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
			);
		}
	}
	elseif (is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR))
	{
		$view_folder = APPPATH.strtr(
			trim($view_folder, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		);
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}

	define('VIEWPATH', $view_folder.DIRECTORY_SEPARATOR);

  define('SITEPATH', $site_folder.DIRECTORY_SEPARATOR);			// FLEXYADMIN change

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 */
require_once BASEPATH.'core/CodeIgniter.php';
