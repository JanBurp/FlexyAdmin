<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Config
* 
* Author: Ben Edmunds
* 	  ben.edmunds@gmail.com
*         @benedmunds
*          
* Added Awesomeness: Phil Sturgeon
* 
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth/
*          
* Created:  10.01.2009 
* 
* Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
* Original Author name has been kept but that does not mean that the method has not been modified.
* 
*/

	/**
	 * Tables.
	 **/
	$config['tables']['groups']  = 'cfg_user_groups';
	$config['tables']['users']   = 'cfg_users';
	$config['tables']['meta']    = '';
	
	/**
	 * Site Title, example.com
	 */
	$config['site_title']		   = "Example.com";
	
	/**
	 * Admin Email, admin@example.com
	 */
	$config['admin_email']		   = "info@flexyadmin.com";
	
	/**
	 * Default group, use name
	 */
	$config['default_group']       = 'user';
	
	/**
	 * Default administrators group, use name
	 */
	$config['admin_group']         = 'admin';
	 
	/**
	 * Meta table column you want to join WITH.
	 * Joins from users.id
	 **/
	$config['join']                = 'id_user';
	
	/**
	 * Columns in your meta table,
	 * id not required.
	 **/
	$config['columns']             = array();
	
	/**
	 * A database column which is used to
	 * login with.
	 **/
	$config['identity']            = 'str_username';
		 
	/**
	 * Minimum Required Length of Password
	 **/
	$config['min_password_length'] = 6;
	
	/**
	 * Maximum Allowed Length of Password
	 **/
	$config['max_password_length'] = 20;

	/**
	 * Email Activation for registration
	 **/
	$config['email_activation']    = true;
	
	/**
	 * Email activation: but send by Admin (plugin_login_activation)
	 **/
	$config['admin_activation']   = false;
	
	
	/**
	 * Allow users to be remembered and enable auto-login
	 **/
	$config['remember_users']      = true;
	
	/**
	 * How long to remember the user (seconds)
	 **/
	$config['user_expire']         = 86500;
	
	/**
	 * Extend the users cookies everytime they auto-login
	 **/
	$config['user_extend_on_login'] = false;
	
	/**
	 * Type of email to send (HTML or text)
	 * Default : html
	 **/
	$config['email_type'] = 'html';
	
	/**
	 * Folder where email templates are stored.
     * Default : auth/
	 **/
	$config['email_templates']     = 'auth/email/';
	
	/**
	 * activate Account Email Template
     * Default : activate.tpl.php
	 **/
	$config['email_activate']   = 'activate.tpl.php';

	/**
	 * accepted activation
	 **/
	$config['email_accepted']   = 'accepted.tpl.php';

	/**
	 * New account send
	 **/
	$config['email_new_account']   = 'new_account.tpl.php';

	/**
	 * New login/password send
	 **/
	$config['email_new_login']      = 'new_login.tpl.php';
  
  $config['email_new_password']   = 'new_password.tpl.php';


	/**
	 * Deny activation
	 **/
	$config['email_deny']   = 'deny.tpl.php';

	/**
	 * Mail for administrator when new registered
	 **/
	$config['email_admin_new_register']   = 'admin_new_register.tpl.php';

	
	/**
	 * Forgot Password Email Template
     * Default : forgot_password.tpl.php
	 **/
	$config['email_forgot_password']   = 'forgot_password.tpl.php';

	/**
	 * Forgot Password Complete Email Template
   * Default : new_password.tpl.php
	 **/
	$config['email_forgot_password_complete']   = 'new_password.tpl.php';
	
	/**
	 * Salt Length (never as long as password!!!!)
	 **/
	$config['salt_length'] = 10;

	/**
	 * Should the salt be stored in the database?
	 * This will change your password encryption algorithm, 
	 * default password, 'password', changes to 
	 * fbaa5e216d163a02ae630ab1a43372635dd374c0 with default salt.
	 **/
	$config['store_salt'] = false;
	
	/**
	 * Message Start Delimiter
	 **/
	$config['message_start_delimiter'] = '<p>';
	
	/**
	 * Message End Delimiter
	 **/
	$config['message_end_delimiter'] = '</p>';
	
	/**
	 * Error Start Delimiter
	 **/
	$config['error_start_delimiter'] = '<p>';
	
	/**
	 * Error End Delimiter
	 **/
	$config['error_end_delimiter'] = '</p>';
  
  
  // Added by JdB
  $config['check_double_email'] = true;
	
  
/* End of file ion_auth.php */
/* Location: ./system/application/config/ion_auth.php */