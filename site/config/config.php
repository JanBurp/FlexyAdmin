<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 *--------------------------------------------------------------------------
 * Testmode
 *--------------------------------------------------------------------------
 *
 * If set to true and if not logged in as super_admin:
 * - frontend is invisible
 * - and sitemap.xml is removed
 * - sitemap.xml will be created after a logout from backend when testmode=false
 *
 */
$config['testmode'] = false;

/*
 *--------------------------------------------------------------------------
 * Styling framework
 *--------------------------------------------------------------------------
 *
 * Set the default frontend framework.
 */
$config['framework']='bootstrap';


/**
 * User Parser for the views
 */
$config['use_parser'] = FALSE;



/*
 *--------------------------------------------------------------------------
 * Menu options
 *--------------------------------------------------------------------------
 *
 * Set menu config here (see Menu for the options). These will override defaults.
 *
 */
// $config['menu']=array('fields'=>array(),'view_path'=>'menu');

/*
 *--------------------------------------------------------------------------
 * Caching
 *--------------------------------------------------------------------------
 *
 * Set to true if the pages need to be cached. Set also the time in minutes.
 *
 */
$config['caching'] = FALSE;
$config['caching_time'] = 1440;	// 1440 minutes is 24 hours

/*
 *--------------------------------------------------------------------------
 * Use minimized css/js files on production sites
 *--------------------------------------------------------------------------
 *
 * Set to TRUE if you use LESS and/or minification of css and js files.
 * Use gulpfile.js for that
 */
$config['use_minimized'] = TRUE;

/*
 *--------------------------------------------------------------------------
 * Parse content
 *--------------------------------------------------------------------------
 *
 * Set how the content must be parsed, possible settings:
 * 
 * - compress               - [TRUE] de HTML output wordt gecomprimeerd (overbodige spaties en returns worden verwijderd)
 * - safe_emails            - [TRUE] emaillinks worden vervangen door spambot veilige emaillinks
 * - auto_target_links      - [TRUE] alle link-tags naar externe adressen krijgen de attributen `target="_blank"` en `rel="external"` mee.
 * - auto_iframe_https      - [TRUE] bij een https website worden alle iframes met http veranderd in http
 * - site_links             - [FALSE] alle link-tags naar interne adressen worden aangepast met site_url(), zodat eventueel index.php ervoor wordt gezet.
 * - add_classes            - [FALSE] alle div, p, en img tags krijgen extra classes: een nr en 'odd' of 'even'
 * - remove_sizes           - [FALSE] Als TRUE dan worden width en height attributen van img tags verwijderd (zodat met css styling kan worden ingegrepen). Je kunt ook alleen de 'width' of 'height' attributen verwijderen door 'width' of 'height' (of 'style').
 * - replace_language_links - [FALSE] Links die beginnen met een taal, bijvoorbeeld _nl/contact_ worden vervangen worden door links met de juiste taal bv: _en/contact_
 * - replace_soft_hyphens   - [FALSE] Soft Hyphens karakters (standaard [-]) worden vervangen door de HTML entity: &#173;
 */
$config['parse_content']  = array( 'safe_emails'=>TRUE, 'auto_target_links'=> TRUE, 'remove_sizes'=>TRUE, 'compress' => TRUE );

/*
 *--------------------------------------------------------------------------
 * Languages
 *--------------------------------------------------------------------------
 *
 * Array containing all the possible language prefixes used by the site.
 *
 */
$config['languages'] = array('nl');
// $config['languages'] = array('nl','en');

/*
 *--------------------------------------------------------------------------
 * Default Language
 *--------------------------------------------------------------------------
 *
 * This determines which set of language files should be used if nothing is set in the uri or in the browser.
 * Make sure there is an available translation if you intend to use something other than english.
 *
 */
$config['language']	= "nl";

/*
 *--------------------------------------------------------------------------
 * Language table
 *--------------------------------------------------------------------------
 *
 * Standard are CodeIgniter language files used.
 * If you set a language table here, the table will be checked first, and if the language key doesn't exists (or is empty), the language files will be used as normal
 */
// $config['language_table']  = "cfg_lang";



/**
 * Query keys to keep in every link
 *
 * This enables and sets query keys that automatically will be remembered on every link (if not activily changed) created with site_url()
 */
$config['keep_query_keys'] = FALSE;
// $config['keep_query_keys'] = array('lang');

/*
 *--------------------------------------------------------------------------
 * Redirect
 *--------------------------------------------------------------------------
 *
 * Set this to true if you need to redirect pages to their underlying pages if empty
 *
 */
$config['redirect']	= FALSE;

/*
 *--------------------------------------------------------------------------
 * Auto Pagination
 *--------------------------------------------------------------------------
 *
 * Set this to true, if you need auto pagination somewhere on you're site
 *
 */
$config['auto_pagination']	= TRUE;

/*
 *--------------------------------------------------------------------------
 * Declare and initialise empty variables for $this->site
 *--------------------------------------------------------------------------
 *
 * If you need more variables in $this->site which are send to the main view you need to declare them.
 * Set this array with all the names of these variables and they will be declared automatic.
 *
 */
// $config['site_variables']	= array('submenu','comments');


/**
 * RSS feed model
 */
// $config['rss_model'] = '';


/*
 *--------------------------------------------------------------------------
 * Autoload Modules
 *--------------------------------------------------------------------------
 *
 * You can autoload and call modules from the controller.
 * Usefull if you need to do something on every page and don't won't to change the controller.
 * You can also autoload a module with a simple test on a field in $page
 * You can also autoload a module, examples
 * - $config['autoload_modules_if'] = array( 'homepage'=>array('uri'=>'home') );         // Loads module 'homepage' when uri='home'
 * - $config['autoload_modules_if'] = array( 'submenu'=>array('str_module'=>'page') );   // Loads module 'submenu' when str_module='page'
 * - $config['autoload_modules_if'] = array( 'page'=>array('str_module'=>'') );          // Loads module 'page' when str_module=''
 * - $config['autoload_modules_if'] = array( 'page'=>array('str_module'=>''), 'submenu'=>array('str_module'=>'page') );          // Combination
 * 
 * With 'error404_module' you can set a module if a page is not found
 */
// $config['autoload_modules'] = array('login');
// $config['autoload_modules_if'] = array( 'login'=>array('b_restricted'=>'true') );
// $config['error404_module'] = 'fallback';

/*
 *--------------------------------------------------------------------------
 * Fallback module
 *--------------------------------------------------------------------------
 *
 * If a module doesn't exists, you can call a fallback module.
 */
$config['fallback_module']='fallback';

/*
 *--------------------------------------------------------------------------
 * Module field
 *--------------------------------------------------------------------------
 *
 * Set here the fieldname of modules used in the controller.
 * Default value is 'str_module' (which is used in the standard demo database in 'tbl_menu' or 'res_menu_result' in case of merged menu's.
 * Other usefull options are 'uri' if the uri's are frozen. Or 'tbl_module__str_module' if a foreign module table is used.
 * You can also use a foreign key (id_module), the controller will load the field specified as the suffix. Example: 'id_module' will load tbl_module.str_module.
 */
$config['module_field']='str_module';

/*
 *--------------------------------------------------------------------------
 * Frontend menu automatic setting of homepage in menu
 *--------------------------------------------------------------------------
 *
 * If TRUE the frontend controller will find the homepage of the site.
 * This will be set to $this->uri.
 * The homepage is the page which is shown when only the url is typed in the browser and no uri's or queries.
 */
$config['menu_autoset_home']=TRUE;

/*
 *--------------------------------------------------------------------------
 * Frontend menu hand setting
 *--------------------------------------------------------------------------
 *
 * If the above setting is FALSE, you can set an homepage uri here.
 * If you don't you will need to set it in the controller
 */
// $config['menu_homepage_uri']='home';

/*
 *--------------------------------------------------------------------------
 * Main view
 *--------------------------------------------------------------------------
 *
 * The name of the view that the frontend controller will load if no view was given
 *
 */
$config['main_view']='site';

/*
 *--------------------------------------------------------------------------
 * Page view
 *--------------------------------------------------------------------------
 *
 * The name of the view that the frontend controller will load for a page
 *
 */
$config['page_view']='page';


/*
 *--------------------------------------------------------------------------
 * Add to Statistics
 *--------------------------------------------------------------------------
 *
 * If true, each page is added to log_stats
 *
 */
$config['add_to_statistics']=TRUE;

/*
 *--------------------------------------------------------------------------
 * Logout to site
 *--------------------------------------------------------------------------
 *
 * If true, after logout the CMS redirects to the site
 *
 */
$config['logout_to_site']=FALSE;

/*
 *--------------------------------------------------------------------------
 * Cronjobs
 *--------------------------------------------------------------------------
 *
 * Just call the url www.site.com/_cronjobs to run all the set cronjobs.
 * Every cronjob will be checked if it needs to be run at give time.
 * 
 * If server is not capable of running cronjobs set 'simulate_cronjobs'=TRUE and the site will test if it needs to run cronjobs every get of a frontend page
 * 
 * array(
 *  'name'  => name of your cronjob
 *  'every' => at wich moments the job should be called
 * );
 * 
 * every examples:
 * - '5'                // calls the job every 5 minutes
 * - 'day 10:15'        // every day at 10:15
 * - 'week 5 12:00      // every week at 12:00 on friday (day5)
 * - 'month 1 18:00     // every first day of the month at 18:00
 *
 * NB Zorg ervoor dat de tabel log_cronjobs bestaat (zie db/add_log_cronjobs)
 */
$config['cronjobs'] = array(
  'cronjob_example' => array('name'=>'cronjob_example','every'=>1),
);
$config['simulate_cronjobs'] = FALSE;


/*
 *--------------------------------------------------------------------------
 * Base Site URL
 *--------------------------------------------------------------------------
 *
 * URL to your CodeIgniter root. Typically this will be your base URL,
 * WITH a trailing slash:
 *
 *	http://example.com/
 */

if (!isset($config['base_url'])) {
  if (!isset($config['base_url']) and !is_cli() and !IS_AJAX) {
  	// If not set automatic: comment this die() statement and set $config['base_url'] manually
  	die("<h3 style=\"color:#F00;\">FlexyAdmin could not set the 'base_url' automatic.</h3><p>See at line #".__LINE__." in '".__FILE__."'.</p>");
  	// $config['base_url']	= "http://www.flexyadmin.com/";
    // $config['base_url']	= "https://www.flexyadmin.com/";
  }
}

/*
 *--------------------------------------------------------------------------
 * Force https:// instead of http://
 *--------------------------------------------------------------------------
 *
 * Force https local off.
 */
$config['force_https'] = FALSE;


/*
 *--------------------------------------------------------------------------
 * Index File
 *--------------------------------------------------------------------------
 *
 * Typically this will be your index.php file, unless you've renamed it to
 * something else. If you are using mod_rewrite to remove the page set this
 * variable so that it is blank.
 *
 */
// $config['index_page'] = "index.php";

/*
 *--------------------------------------------------------------------------
 * URI PROTOCOL
 *--------------------------------------------------------------------------
 *
 * This item determines which server global should be used to retrieve the
 * URI string.  The default setting of "AUTO" works for most servers.
 * If your links do not seem to work, try one of the other delicious flavors:
 *
 * 'AUTO'			Default - auto detects
 * 'PATH_INFO'		Uses the PATH_INFO
 * 'QUERY_STRING'	Uses the QUERY_STRING
 * 'REQUEST_URI'		Uses the REQUEST_URI
 * 'ORIG_PATH_INFO'	Uses the ORIG_PATH_INFO
 *
 */
// $config['uri_protocol']	= "AUTO";

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'session_cookie_name' = the name you want for the cookie - automatic set at install
|
*/
$config['sess_cookie_name'] = 'FlexyAdminDEMO';

/*
|--------------------------------------------------------------------------
| Encryption key
|--------------------------------------------------------------------------
|
| - Used for encrypting backup files
| - Run sys/vendor/bin/generate-defuse-key to generate new key
*/
$config['encryption_key'] = 'def0000053155cc71f536a17448460857b88d20462604a844929ef354cee3615d6a1084f55507a19ed4f75b4077c59c86c7225ed517ecdb94695cd706e3e896486a1adfa';


/*
 *--------------------------------------------------------------------------
 * URL suffix
 *--------------------------------------------------------------------------
 *
 * This option allows you to add a suffix to all URLs generated by CodeIgniter.
 * For more information please see the user guide:
 *
 * http://codeigniter.com/user_guide/general/urls.html
 */
// $config['url_suffix'] = ".html";

/*
 *--------------------------------------------------------------------------
 * Default Character Set
 *--------------------------------------------------------------------------
 *
 * This determines which character set is used by default in various methods
 * that require a character set to be provided.
 *
 */
// $config['charset'] = "UTF-8";

/*
 *--------------------------------------------------------------------------
 * Global XSS Filtering
 *--------------------------------------------------------------------------
 *
 * Determines whether the XSS filter is always active when GET, POST or
 * COOKIE data is encountered
 *
 */
$config['global_xss_filtering'] = FALSE;

/*
 *--------------------------------------------------------------------------
 * Regex form_validation_rules for valid_regex($s, $regex_name)
 *--------------------------------------------------------------------------
 *
 * Set validation rules for the valid_regex form validation, error_key is een language key die verwijst naar regex_validation_lang.php
 */
$config['valid_regex_rules'] = array(
  'postcode' => array(
    'regex'     => '/^[1-9][0-9]{3}[\s]?[A-Za-z]{2}$/i',
    'error_key' => 'valid_zipcode'
  ),
  'telefoon'  => array(
    'regex'     => '/^\(?0\d{1,4}\)?[-\s]\d{5,8}$/',
    'error_key' => 'valid_phone'
  )
);

/*
 *--------------------------------------------------------------------------
 * Rewrite PHP Short Tags
 *--------------------------------------------------------------------------
 *
 * If your PHP installation does not have short tag support enabled CI
 * can rewrite the tags on-the-fly, enabling you to utilize that syntax
 * in your view files.  Options are TRUE or FALSE (boolean)
 *
 */
$config['rewrite_short_tags'] = FALSE;

/*
 * Check if localhost and config_local.php exists, load local settings.
 */
if (IS_LOCALHOST and file_exists(SITEPATH.'config/config_local.php')) {
	require("config_local.php");
}

/* End of file config.php */
/* Location: ./system/application/config/config.php */