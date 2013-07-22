<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ion Auth
 *
 * @package default
 * @author Ben Edmunds, Phil Sturgeon, Jan den Besten
 * @link http://github.com/benedmunds/CodeIgniter-Ion-Auth
 * @version 10.01.2009
 *
 * Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
 * Original Author name has been kept but that does not mean that the method has not been modified.
 *
 * User inherits all, see there for more
 */

class Ion_auth
{
	/**
	 * CodeIgniter global
	 *
	 * @var string
	 **/
	protected $CI;

	/**
	 * account status ('not_activated', etc ...)
	 *
	 * @var string
	 **/
	protected $status;

	/**
	 * message (uses lang file)
	 *
	 * @var string
	 **/
	protected $messages;

	/**
	 * error message (uses lang file)
	 *
	 * @var string
	 **/
	protected $errors = array();

	/**
	 * error start delimiter
	 *
	 * @var string
	 **/
	protected $error_start_delimiter;

	/**
	 * error end delimiter
	 *
	 * @var string
	 **/
	protected $error_end_delimiter;

	/**
	 * extra where
	 *
	 * @var array
	 **/
	public $_extra_where = array();

	/**
	 * extra set
	 *
	 * @var array
	 **/
	public $_extra_set = array();


  /**
   * Added by JdB
   *
   * @var array
   */
  protected $tables;
  

	/**
	 * __construct
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function __construct($tables='')
	{
		$this->CI =& get_instance();
		$this->CI->load->config('ion_auth', TRUE);
		$this->CI->load->library('email');
		$this->CI->load->library('session');
		$this->CI->lang->load('ion_auth');
		$this->CI->load->model('ion_auth_model');
		$this->CI->load->helper('cookie');

		$this->messages = array();
		$this->errors = array();
		$this->message_start_delimiter = $this->CI->config->item('message_start_delimiter', 'ion_auth');
		$this->message_end_delimiter   = $this->CI->config->item('message_end_delimiter', 'ion_auth');
		$this->error_start_delimiter   = $this->CI->config->item('error_start_delimiter', 'ion_auth');
		$this->error_end_delimiter     = $this->CI->config->item('error_end_delimiter', 'ion_auth');

    // Added by Jdb
    if (empty($tables)) {
      $tables=$this->CI->config->item('tables','ion_auth');
    }
    $this->tables=$tables;
    $this->CI->ion_auth_model->tables=$this->tables;

		//auto-login the user if they are remembered
		if (!$this->logged_in() && get_cookie('identity') && get_cookie('remember_code'))
		{
			$this->CI->ion_auth = $this;
			$this->CI->ion_auth_model->login_remembered_user();
		}
	}

	/**
	 * __call
	 *
	 * Acts as a simple way to call model methods without loads of stupid alias'
	 *
	 **/
	public function __call($method, $arguments)
	{
		if (!method_exists( $this->CI->ion_auth_model, $method) )
		{
			throw new Exception('Undefined method Ion_auth::' . $method . '() called');
		}

		return call_user_func_array( array($this->CI->ion_auth_model, $method), $arguments);
	}

	/**
	 * Activate user.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function activate($id, $code=false) {
		if ($this->CI->ion_auth_model->activate($id, $code)) {
			$this->set_message('activate_successful');
			return TRUE;
		}
		$this->set_error('activate_unsuccessful');
		return FALSE;
	}

	/**
	 * Deactivate user.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function deactivate($id)
	{
		if ($this->CI->ion_auth_model->deactivate($id))
		{
			$this->set_message('deactivate_successful');
			return TRUE;
		}

		$this->set_error('deactivate_unsuccessful');
		return FALSE;
	}

	/**
	 * Change password.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function change_password($identity, $old, $new)
	{
		if ($this->CI->ion_auth_model->change_password($identity, $old, $new))
		{
			$this->set_message('password_change_successful');
			return TRUE;
		}

		$this->set_error('password_change_unsuccessful');
		return FALSE;
	}

	/**
	 * forgotten password feature
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password($identity)    //changed $email to $identity
	{
		// Get user information
		//changed by adityamenon on github (adityamenon90@gmail.com)
		//get the details of the user, and thus his email address **before** sending a request to the model
		$user = $this->get_user_by_identity($identity);
		$email = $user->email;
		
		if ( $this->CI->ion_auth_model->forgotten_password($email) )   //changed
		{
			$data = array(
				'identity'		=> $user->{$this->CI->config->item('identity', 'ion_auth')},
				'forgotten_password_code' => $this->CI->ion_auth_model->forgotten_password_code,
			);

			$message = $this->CI->load->view($this->CI->config->item('email_templates', 'ion_auth').$this->CI->config->item('email_forgot_password', 'ion_auth'), $data, true);
			$this->CI->email->clear();
			$config['mailtype'] = $this->CI->config->item('email_type', 'ion_auth');
			$this->CI->email->initialize($config);
			$this->CI->email->set_newline("\r\n");
			$this->CI->email->from($this->CI->config->item('admin_email', 'ion_auth'), $this->CI->config->item('site_title', 'ion_auth'));
			$this->CI->email->to($user->email);
			$this->CI->email->subject($this->CI->config->item('site_title', 'ion_auth') . ' - Forgotten Password Verification');
			$this->CI->email->message($message);

			if ($this->CI->email->send())
			{
				$this->set_message('forgot_password_successful');
				return TRUE;
			}
			else
			{
				$this->set_error('forgot_password_unsuccessful');
				return FALSE;
			}
		}
		else
		{
			$this->set_error('forgot_password_unsuccessful');
			return FALSE;
		}
	}

	/**
	 * forgotten_password_complete
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code)
	{
		$identity = $this->CI->config->item('identity', 'ion_auth');
		$profile  = $this->CI->ion_auth_model->profile($code, true); //pass the code to profile
		
		if (!is_object($profile))
		{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}

		$new_password = $this->CI->ion_auth_model->forgotten_password_complete($code, $profile->str_salt);

		if ($new_password)
		{
			$data = array(
				'identity'     => $profile->{$identity},
				'new_password' => $new_password
			);

			$message = $this->CI->load->view($this->CI->config->item('email_templates', 'ion_auth').$this->CI->config->item('email_forgot_password_complete', 'ion_auth'), $data, true);

			$this->CI->email->clear();
			$config['mailtype'] = $this->CI->config->item('email_type', 'ion_auth');
			$this->CI->email->initialize($config);
			$this->CI->email->set_newline("\r\n");
			$this->CI->email->from($this->CI->config->item('admin_email', 'ion_auth'), $this->CI->config->item('site_title', 'ion_auth'));
			$this->CI->email->to($profile->email_email);
			$this->CI->email->subject($this->CI->config->item('site_title', 'ion_auth') . ' - New Password');
			$this->CI->email->message($message);

			if ($this->CI->email->send())
			{
				$this->set_message('password_change_successful');
				return TRUE;
			}
			else
			{
				$this->set_error('password_change_unsuccessful');
				return FALSE;
			}
		}

		$this->set_error('password_change_unsuccessful');
		return FALSE;
	}

	/**
	 * register
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function register($username, $password, $email, $additional_data=array(), $group_name = false) {
		$email_activation = $this->CI->config->item('email_activation', 'ion_auth');
		if (!$email_activation)	{
			$id = $this->CI->ion_auth_model->register($username, $password, $email, $additional_data, $group_name);
			if ($id !== FALSE) {
				$this->set_message('account_creation_successful');
				return $id;
			}
			else {
				$this->set_error('account_creation_unsuccessful');
				return FALSE;
			}
		}
		else {
			$id = $this->CI->ion_auth_model->register($username, $password, $email, $additional_data, $group_name);
			if (!$id)	{
				$this->set_error('account_creation_unsuccessful');
				return FALSE;
			}

			$deactivate = $this->CI->ion_auth_model->deactivate($id);

			if (!$deactivate)	{
				$this->set_error('deactivate_unsuccessful');
				return FALSE;
			}

			$activation_code = $this->CI->ion_auth_model->activation_code;
			$identity        = $this->CI->config->item('identity', 'ion_auth');
			$user            = $this->CI->ion_auth_model->get_user($id)->row();

			$data = array(
				'identity'   => $user->{$identity},
				'id'         => $user->id,
				'email'      => $email,
				'activation' => $activation_code,
			);

			$message = $this->CI->load->view($this->CI->config->item('email_templates', 'ion_auth').$this->CI->config->item('email_activate', 'ion_auth'), $data, true);

			$this->CI->email->clear();
			$config['mailtype'] = $this->CI->config->item('email_type', 'ion_auth');
			$this->CI->email->initialize($config);
			// $this->CI->email->set_newline("\r\n");
			$this->CI->email->from($this->CI->config->item('admin_email', 'ion_auth'), $this->CI->config->item('site_title', 'ion_auth'));
			$this->CI->email->to($email);
			$this->CI->email->subject($this->CI->config->item('site_title', 'ion_auth') . ' - Account Activation');
			$this->CI->email->message($message);

			if ($this->CI->email->send() == TRUE)	{
				$this->set_message('activation_email_successful');
				return $id;
			}

			$this->set_error('activation_email_unsuccessful');
			return FALSE;
		}
	}

	/**
	 * login
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function login($identity, $password, $remember=false)
	{
		if ($this->CI->ion_auth_model->login($identity, $password, $remember))
		{
			$this->set_message('login_successful');
			return TRUE;
		}

		$this->set_error('login_unsuccessful');
		return FALSE;
	}

	/**
	 * logout
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function logout()
	{
    // JdB: update last login not at login, but at logout!
    $user_id=$this->CI->session->userdata('user_id');
    $this->CI->ion_auth_model->update_last_login($user_id);
    
		$identity = $this->CI->config->item('identity', 'ion_auth');
		$this->CI->session->unset_userdata($identity);
		$this->CI->session->unset_userdata('group');
		$this->CI->session->unset_userdata('id');
		$this->CI->session->unset_userdata('user_id');

		//delete the remember me cookies if they exist
		if (get_cookie('identity'))
		{
			delete_cookie('identity');
		}
		if (get_cookie('remember_code'))
		{
			delete_cookie('remember_code');
		}

		$this->CI->session->sess_destroy();

		$this->set_message('logout_successful');
		return TRUE;
	}

	/**
	 * logged_in
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function logged_in()
	{
		$identity = $this->CI->config->item('identity', 'ion_auth');

		return (bool) $this->CI->session->userdata($identity);
	}

	/**
	 * is_admin
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function is_admin()
	{
		$admin_group = $this->CI->config->item('admin_group', 'ion_auth');
		$user_group  = $this->CI->session->userdata('group');

		return $user_group == $admin_group;
	}

	/**
	 * is_group
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function is_group($check_group)
	{
		$user_group = $this->CI->session->userdata('group');

		if(is_array($check_group))
		{
			return in_array($user_group, $check_group);
		}

		return $user_group == $check_group;
	}

	/**
	 * Profile
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function profile()
	{
		$session  = $this->CI->config->item('identity', 'ion_auth');
		$identity = $this->CI->session->userdata($session);

		return $this->CI->ion_auth_model->profile($identity);
	}

	/**
	 * Get Users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_users($group_name=false, $limit=NULL, $offset=NULL)
	{
		return $this->CI->ion_auth_model->get_users($group_name, $limit, $offset)->result();
	}

	/**
	 * Get Number of Users
	 *
	 * @return int Number of Users
	 * @author Sven Lueckenbach
	 **/
	public function get_users_count($group_name=false)
	{
		return $this->CI->ion_auth_model->get_users_count($group_name);
	}

	/**
	 * Get Users Array
	 *
	 * @return array Users
	 * @author Ben Edmunds
	 **/
	public function get_users_array($group_name=false, $limit=NULL, $offset=NULL)
	{
		return $this->CI->ion_auth_model->get_users($group_name, $limit, $offset)->result_array();
	}

	/**
	 * Get Newest Users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_newest_users($limit = 10)
	{
		return $this->CI->ion_auth_model->get_newest_users($limit)->result();
	}

	/**
	 * Get Newest Users Array
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_newest_users_array($limit = 10)
	{
		return $this->CI->ion_auth_model->get_newest_users($limit)->result_array();
	}

	/**
	 * Get Active Users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_active_users($group_name = false)
	{
		return $this->CI->ion_auth_model->get_active_users($group_name)->result();
	}

	/**
	 * Get Active Users Array
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_active_users_array($group_name = false)
	{
		return $this->CI->ion_auth_model->get_active_users($group_name)->result_array();
	}

	/**
	 * Get In-Active Users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_inactive_users($group_name = false)
	{
		return $this->CI->ion_auth_model->get_inactive_users($group_name)->result();
	}

	/**
	 * Get In-Active Users Array
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_inactive_users_array($group_name = false)
	{
		return $this->CI->ion_auth_model->get_inactive_users($group_name)->result_array();
	}

	/**
	 * Get User
	 *
	 * @return object User
	 * @author Ben Edmunds
	 **/
	public function get_user($id=false)
	{
		return $this->CI->ion_auth_model->get_user($id)->row();
	}

	/**
	 * Get User by Email
	 *
	 * @return object User
	 * @author Ben Edmunds
	 **/
	public function get_user_by_email($email)
	{
		return $this->CI->ion_auth_model->get_user_by_email($email)->row();
	}

	/**
	 * Get Users by Email
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_users_by_email($email)
	{
		return $this->CI->ion_auth_model->get_users_by_email($email)->result();
	}
	
	/**
	 * Get User by Username
	 *
	 * @return object User
	 * @author Kevin Smith
	 **/
	public function get_user_by_username($username)
	{
		return $this->CI->ion_auth_model->get_user_by_username($username)->row();
	}

	/**
	 * Get Users by Username
	 *
	 * @return object Users
	 * @author Kevin Smith
	 **/
	public function get_users_by_username($username)
	{
		return $this->CI->ion_auth_model->get_users_by_username($username)->result();
	}
	
	/**
	 * Get User by Identity
	 *                              //copied from above ^
	 * @return object User
	 * @author jondavidjohn
	 **/
	public function get_user_by_identity($identity)
	{
		return $this->CI->ion_auth_model->get_user_by_identity($identity)->row();
	}

	/**
	 * Get User as Array
	 *
	 * @return array User
	 * @author Ben Edmunds
	 **/
	public function get_user_array($id=false)
	{
		return $this->CI->ion_auth_model->get_user($id)->row_array();
	}


	/**
	 * update_user
	 *
	 * @return void
	 * @author Phil Sturgeon
	 **/
	public function update_user($id, $data)
	{
		if ($this->CI->ion_auth_model->update_user($id, $data))
		{
			$this->set_message('update_successful');
			return TRUE;
		}

		$this->set_error('update_unsuccessful');
		return FALSE;
	}


	/**
	 * delete_user
	 *
	 * @return void
	 * @author Phil Sturgeon
	 **/
	public function delete_user($id)
	{
		if ($this->CI->ion_auth_model->delete_user($id))
		{
			$this->set_message('delete_successful');
			return TRUE;
		}

		$this->set_error('delete_unsuccessful');
		return FALSE;
	}


	/**
	 * extra_where
	 *
	 * Crazy function that allows extra where field to be used for user fetching/unique checking etc.
	 * Basically this allows users to be unique based on one other thing than the identifier which is helpful
	 * for sites using multiple domains on a single database.
	 *
	 * @return void
	 * @author Phil Sturgeon
	 **/
	public function extra_where()
	{
		$where =& func_get_args();

		$this->_extra_where = count($where) == 1 ? $where[0] : array($where[0] => $where[1]);
	}

	/**
	 * extra_set
	 *
	 * Set your extra field for registration
	 *
	 * @return void
	 * @author Phil Sturgeon
	 **/
	public function extra_set()
	{
		$set =& func_get_args();

		$this->_extra_set = count($set) == 1 ? $set[0] : array($set[0] => $set[1]);
	}

	/**
	 * set_message_delimiters
	 *
	 * Set the message delimiters
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message_delimiters($start_delimiter, $end_delimiter)
	{
		$this->message_start_delimiter = $start_delimiter;
		$this->message_end_delimiter   = $end_delimiter;

		return TRUE;
	}

	/**
	 * set_error_delimiters
	 *
	 * Set the error delimiters
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error_delimiters($start_delimiter, $end_delimiter)
	{
		$this->error_start_delimiter = $start_delimiter;
		$this->error_end_delimiter   = $end_delimiter;

		return TRUE;
	}

	/**
	 * set_message
	 *
	 * Set a message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message($message)
	{
		$this->messages[] = $message;

		return $message;
	}

	/**
	 * messages
	 *
	 * Get the messages
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function messages()
	{
		$_output = '';
		foreach ($this->messages as $message)
		{
			$_output .= $this->message_start_delimiter . $this->CI->lang->line($message) . $this->message_end_delimiter;
		}

		return $_output;
	}

	/**
	 * set_error
	 *
	 * Set an error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error($error)
	{
		$this->errors[] = $error;

		return $error;
	}

	/**
	 * errors
	 *
	 * Get the error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function errors()
	{
		$_output = '';
		foreach ($this->errors as $error)
		{
			$_output .= $this->error_start_delimiter . $this->CI->lang->line($error) . $this->error_end_delimiter;
		}

		return $_output;
	}

}