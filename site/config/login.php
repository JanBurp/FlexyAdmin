<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// !! When registration and password resetting needs to be active: make sure that $config['query_urls']=TRUE; in site/config/config.php



/*
|--------------------------------------------------------------------------
| Login css class
|--------------------------------------------------------------------------
|
| This class will be added to the body tag if a uses is logged in (if the module is loaded offcoz)
|
*/
$config['class']='user_logged_in';


/*
|--------------------------------------------------------------------------
| Usergroup for new accounts
|--------------------------------------------------------------------------
|
| The id of the usergroup where new accounts belong to (see cfg_user_groups)
|
*/
$config['group_id']=4;	// 4 is visitors which can't login to the backend


/*
|--------------------------------------------------------------------------
| Admin needs to activate first
|--------------------------------------------------------------------------
|
| If this is false, a user gets an activate mail and can activate itself when clicking on the link in de mail
| if this is true, a user gets an activate email, when the administrator sends it. (Needs plugin_login_activate.php)
|
*/
$config['admin_activation']=false;



/*
|--------------------------------------------------------------------------
| Login uri settings
|--------------------------------------------------------------------------
|
| Set to auto will try to find the uri from the module field of $page.
| You can also set them by hand.
|
*/

$config['auto_uris']=true;
// $config['login_uri']='';
// $config['register_uri']='';
// $config['forgotten_password_uri']='';




/* End of file config.php */
/* Location: ./system/application/config/config.php */
