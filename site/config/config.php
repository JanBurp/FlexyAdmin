<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


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
| Make sure there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= "nl";
// $config['language']	= "en";


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
| Uri as modules
|--------------------------------------------------------------------------
|
| If TRUE the controller loads modules (libraries) instead of collecting menu and pages from the database
| Modules are special libraries (site/libraries). Modules whith names starting with an underscore '_' can't be loaded this way.
| Example: uri= app/test/news/4 Will load site/libraries/app.php and will call its method show() with arguments 'news' and '4'.
|
*/

$config['uri_as_modules']=FALSE;

/*
|--------------------------------------------------------------------------
| Autoload Modules
|--------------------------------------------------------------------------
|
| You can autoload modules from the controller. They are also called.
| Usefull if you need to do something on every page and don't won't to change the controlle.
*/

$config['autoload_modules']=array();



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
	die("sorry no automatic 'base_url', edit 'site/config.php'.");
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
#$config['index_page'] = "index.php";

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
#$config['uri_protocol']	= "AUTO";



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

#$config['url_suffix'] = ".html";



/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
*/
#$config['charset'] = "UTF-8";


/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| If your PHP installation does not have short tag support enabled CI
| can rewrite the tags on-the-fly, enabling you to utilize that syntax
| in your view files.  Options are TRUE or FALSE (boolean)
|
*/
#$config['rewrite_short_tags'] = FALSE;



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
if (IS_LOCALHOST and file_exists('site/config/config_local.php')) {
	require("config_local.php");
}


/* End of file config.php */
/* Location: ./system/application/config/config.php */
