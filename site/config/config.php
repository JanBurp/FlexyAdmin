<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Caching
|--------------------------------------------------------------------------
|
| Set to true if the pages need to be cached. Set also the time in minutes.
|
*/
$config['caching'] = FALSE;
$config['caching_time'] = 1440;	// 1440 minutes is 24 hours


/*
|--------------------------------------------------------------------------
| Languages
|--------------------------------------------------------------------------
|
| Array containing all the possible language prefixes used by the site.
|
*/
$config['languages'] = array('nl');
// $config['languages'] = array('en');
// $config['languages'] = array('nl','en');

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used if nothing is set in the uri or in the browser.
| Make sure there is an available translation if you intend to use something other than english.
|
*/
$config['language']	= "nl";
// $config['language']	= "en";

/*
|--------------------------------------------------------------------------
| Declare and initialise empty variables for $this->site
|--------------------------------------------------------------------------
|
| If you need more variables in $this->site which are send to the main view you need to declare them.
| Set this array with all the names of these variables and they will be declared automatic.
|
*/
// $config['site_variables']	= array('submenu','comments');


/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
|
| Set this to true if you need to redirect pages to their underlying pages if empty
|
*/
$config['redirect']	= FALSE;

/*
|--------------------------------------------------------------------------
| Auto Pagination
|--------------------------------------------------------------------------
|
| Set this to true, if you need auto pagination somewhere on you're site
|
*/
$config['auto_pagination']	= FALSE;

/*
|--------------------------------------------------------------------------
| Autoload Modules
|--------------------------------------------------------------------------
|
| You can autoload and call modules from the controller.
| Usefull if you need to do something on every page and don't won't to change the controller.
| You can also autoload a module with a simple test on a field in $item
*/
// $config['autoload_modules'] = array('submenu');
// $config['autoload_modules_if'] = array( 'comments'=>array('b_allow_comments'=>true) );


/*
|--------------------------------------------------------------------------
| Fallback module
|--------------------------------------------------------------------------
|
| If a module doesn't exists, you can call a fallback module.
*/
$config['fallback_module']='fallback';

/*
|--------------------------------------------------------------------------
| Module field
|--------------------------------------------------------------------------
|
| Set here the fieldname of modules used in the controller.
| Default value is 'str_module' (which is used in the standard demo database in 'tbl_menu' or 'res_menu_result' in case of merged menu's.
| Other usefull options are 'uri' if the uri's are frozen. Or 'tbl_module__str_module' if a foreign module table is used.
| You can also use a foreign key (id_module), the controller will load the field specified as the suffix. Example: 'id_module' will load tbl_module.str_module.
*/
$config['module_field']='str_module';

/*
|--------------------------------------------------------------------------
| Frontend menu automatic setting of homepage in menu
|--------------------------------------------------------------------------
|
| If TRUE the frontend controller will find the homepage of the site.
| This will be set to $this->uri.
| The homepage is the page which is shown when only the url is typed in the browser and no uri's or queries.
*/
$config['menu_autoset_home']=TRUE;

/*
|--------------------------------------------------------------------------
| Frontend menu hand setting
|--------------------------------------------------------------------------
|
| If the above setting is FALSE, you can set an homepage uri here.
| If you don't you will need to set it in the controller
*/
// $config['menu_homepage_uri']='home';

/*
|--------------------------------------------------------------------------
| Main view
|--------------------------------------------------------------------------
|
| The name of the view that the frontend controller will load if no view was given
|
*/
$config['main_view']='site';

/*
|--------------------------------------------------------------------------
| Logout to site
|--------------------------------------------------------------------------
|
| If true, after logout the CMS redirects to the site
|
*/
$config['logout_to_site']=FALSE;

/*
|--------------------------------------------------------------------------
| Query URL's
|--------------------------------------------------------------------------
|
| If true, $_GET will be preserved in the frontend controller, so url queries are possible, and the use of $this->input->get()
|
*/
$config['query_urls']=FALSE;

/*
|--------------------------------------------------------------------------
| Add to Statistics
|--------------------------------------------------------------------------
|
| If true, each page is added to log_stats
|
*/
$config['add_to_statistics']=TRUE;

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
*/
if (!isset($config['base_url'])) {
	// If no automatic base_url, comment this:
	die("sorry no automatic 'base_url', edit '".SITEPATH."config.php'.");
	// And uncomment this with right base_url if needed
	// $config['base_url']	= "http://www.flexyadmin.com/";
}


/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
// $config['index_page'] = "index.php";

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of "AUTO" works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'AUTO'			Default - auto detects
| 'PATH_INFO'		Uses the PATH_INFO
| 'QUERY_STRING'	Uses the QUERY_STRING
| 'REQUEST_URI'		Uses the REQUEST_URI
| 'ORIG_PATH_INFO'	Uses the ORIG_PATH_INFO
|
*/
// $config['uri_protocol']	= "AUTO";

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| This option allows you to add a suffix to all URLs generated by CodeIgniter.
| For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/urls.html
*/
// $config['url_suffix'] = ".html";

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
*/
// $config['charset'] = "UTF-8";

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or
| COOKIE data is encountered
|
*/
$config['global_xss_filtering'] = FALSE;


/*
 * Check if localhost and config_local.php exists, load local settings.
 */
if (IS_LOCALHOST and file_exists(SITEPATH.'config/config_local.php')) {
	require("config_local.php");
}


/* End of file config.php */
/* Location: ./system/application/config/config.php */