<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ion Auth Model
 *
 * @package default
 * @author Ben Edmunds, Phil Sturgeon, Jan den Besten
 * @link http://github.com/benedmunds/CodeIgniter-Ion-Auth
 * @version 10.01.2009
 * @ignore
 *
 * Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
 * Original Author name has been kept but that does not mean that the method has not been modified.
 *
 * Used by User
 * 
 */

class Ion_auth_model extends CI_Model
{
	/**
	 * Holds an array of tables used
	 *
	 * @var string
	 **/
	public $tables = array();

	/**
	 * activation code
	 *
	 * @var string
	 **/
	public $activation_code;

	/**
	 * forgotten password key
	 *
	 * @var string
	 **/
	public $forgotten_password_code;

	/**
	 * new password
	 *
	 * @var string
	 **/
	public $new_password;

	/**
	 * Identity
	 *
	 * @var string
	 **/
	public $identity;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('ion_auth', TRUE);
		$this->load->helper('cookie');
		$this->load->helper('date');
		$this->load->library('session');

		$this->tables  = $this->config->item('tables', 'ion_auth');
		$this->columns = $this->config->item('columns', 'ion_auth');

		$this->identity_column = $this->config->item('identity', 'ion_auth');
		$this->store_salt      = $this->config->item('store_salt', 'ion_auth');
		$this->salt_length     = $this->config->item('salt_length', 'ion_auth');
		$this->meta_join       = $this->config->item('join', 'ion_auth');
	}

	/**
	 * Misc functions
	 *
	 * Hash password : Hashes the password to be stored in the database.
     * Hash password db : This function takes a password and validates it
     * against an entry in the users table.
     * Salt : Generates a random salt value.
	 *
	 * @author Mathew
	 */

	/**
	 * Hashes the password to be stored in the database.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password($password, $salt=false)
	{
	    if (empty($password))
	    {
	    	return FALSE;
	    }

	    if ($this->store_salt && $salt)
	    {
		    return  sha1($password . $salt);
	    }
	    else
	    {
		$salt = $this->salt();
		return  $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
	    }
	}

	/**
	 * This function takes a password and validates it
	 * against an entry in the users table.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password_db($identity, $password) {
	   if (empty($identity) || empty($password)) {
       return FALSE;
	   }

	   $query = $this->db->select('gpw_password')
			       ->select('str_salt')
			       ->where($this->identity_column, $identity)
			       ->where($this->user->_extra_where)
			       ->limit(1)
			       ->get($this->tables['users']);

	    $result = $query->row();
      
      // trace_(array('function'=>'ion_auth->hash_password_db','sql'=>$this->db->last_query(),'result'=>object2array($result)));
      

	    if ($query->num_rows() !== 1) {
        return FALSE;
	    }

	    if ($this->store_salt) {
        return sha1($password . $result->salt);
	    }
	    else {
        $salt = substr($result->gpw_password, 0, $this->salt_length);
        return $salt . substr(sha1($salt . $password), 0, - $this->salt_length);
	    }
	}

	/**
	 * Generates a random salt value.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function salt()
	{
	    return substr(md5(uniqid(rand(), true)), 0, $this->salt_length);
	}

	/**
	 * Activation functions
	 *
	 * Activate : Validates and removes activation code.
	 * Deactivae : Updates a users row with an activation code.
	 *
	 * @author Mathew
	 */

	/**
	 * activate
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function activate($id, $code = false)
	{
	    if ($code !== false)
	    {
			$query = $this->db->select($this->identity_column)
					  ->where('str_activation_code', $code)
					  ->limit(1)
					  ->get($this->tables['users']);

			$result = $query->row();

			if ($query->num_rows() !== 1)
			{
				return FALSE;
			}

			$identity = $result->{$this->identity_column};

			$data = array(
					'str_activation_code' => '',
					'b_active'	  => 1
					 );

			$this->db->where($this->user->_extra_where);
			$this->db->update($this->tables['users'], $data, array($this->identity_column => $identity));
	    }
	    else
	    {
			$data = array(
					'str_activation_code' => '',
					'b_active' => 1
					 );

			$this->db->where($this->user->_extra_where);
			$this->db->update($this->tables['users'], $data, array('id' => $id));
	    }

	    return $this->db->affected_rows() == 1;
	}


	/**
	 * Deactivate
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function deactivate($id = 0)
	{
	    if (empty($id))
	    {
		return FALSE;
	    }

	    $activation_code       = sha1(md5(microtime()));
	    $this->activation_code = $activation_code;

	    $data = array(
		    'str_activation_code' => $activation_code,
		    'b_active'	  => 0
	    );

	    $this->db->where($this->user->_extra_where);
	    $this->db->update($this->tables['users'], $data, array('id' => $id));

	    return $this->db->affected_rows() == 1;
	}

	/**
	 * change password
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function change_password($identity, $old, $new)
	{
	    $query = $this->db->select('gpw_password, salt')
			      ->where($this->identity_column, $identity)
			      ->where($this->user->_extra_where)
			      ->limit(1)
			      ->get($this->tables['users']);

	    $result = $query->row();

	    $db_password = $result->gpw_password;
	    $old	 = $this->hash_password_db($identity, $old);
	    $new	 = $this->hash_password($new, $result->salt);

	    if ($db_password === $old)
	    {
	    	//store the new password and reset the remember code so all remembered instances have to re-login
		$data = array(
			    'gpw_password' => $new,
			    'str_remember_code' => '',
			     );

		$this->db->where($this->user->_extra_where);
		$this->db->update($this->tables['users'], $data, array($this->identity_column => $identity));

		return $this->db->affected_rows() == 1;
	    }

	    return FALSE;
	}

	/**
	 * Checks username
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function username_check($username = '')
	{
	    if (empty($username)) {
        return FALSE;
	    }
      
	    return $this->db->where('str_username', $username)
			    ->where($this->user->_extra_where)
			    ->count_all_results($this->tables['users']) > 0;
	}

	/**
	 * Checks email
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function email_check($email = '') {
	    if (empty($email)) {
        return FALSE;
	    }

      if (!$this->config->item('check_double_email','ion_auth')) {
        return FALSE;
      }
      
	    return $this->db->where('email_email', $email)
		                  ->where($this->user->_extra_where)
		                  ->count_all_results($this->tables['users']) > 0;
	}

	/**
	 * Identity check
	 *
	 * @return bool
	 * @author Mathew
	 **/
	protected function identity_check($identity = '')
	{
	    if (empty($identity))
	    {
			return FALSE;
	    }
/*
		if (isset($this->user->_extra_where) && !empty($this->user->_extra_where))
	    {
			$this->db->where($this->user->_extra_where);
	    }
*/
	    return $this->db->where($this->identity_column, $identity)->count_all_results($this->tables['users']) > 0;
	}

	/**
	 * Insert a forgotten password key.
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function forgotten_password($email = '') {
    if (empty($email)) {
      return FALSE;
    }
    $key = $this->hash_password(microtime().$email);
    $this->forgotten_password_code = $key;
    $this->db->where($this->user->_extra_where);
    $this->db->update($this->tables['users'], array('str_forgotten_password_code' => $key), array('email_email' => $email));
    // Changed by JdB
    if ($this->db->affected_rows() == 1) return $key;
    return FALSE;
	}

	/**
	 * Forgotten Password Complete
	 *
	 * @return string
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code, $salt=FALSE) {
    if (empty($code)) {
      return FALSE;
    }

    $this->db->where('str_forgotten_password_code', $code);
    if ($this->db->count_all_results($this->tables['users']) > 0) {
      $password = $this->salt();
      $data = array(
          'gpw_password'			=> $this->hash_password($password, $salt),
          'str_forgotten_password_code'   => '0',
          'b_active'			=> 1,
           );
      $this->db->update($this->tables['users'], $data, array('str_forgotten_password_code' => $code));
      return $password;
    }

    return FALSE;
	}

	/**
	 * profile
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function profile($identity = '', $is_code = false)
	{
	    if (empty($identity))
	    {
				return FALSE;
	    }

	    $this->db->select(array(
				$this->tables['users'].'.*',
				$this->tables['groups'].'.str_name AS '. $this->db->protect_identifiers('group'),
				$this->tables['groups'].'.str_description AS '. $this->db->protect_identifiers('group_description')
	   ));

   		if (!empty($this->tables['meta']))
			{
		    if (!empty($this->columns))
		    {
						foreach ($this->columns as $field)
						{
						    $this->db->select($this->tables['meta'] .'.' . $field);
						}
		    }
				$this->db->join($this->tables['meta'], $this->tables['users'].'.id = '.$this->tables['meta'].'.'.$this->meta_join, 'left');
			}
			
	    $this->db->join($this->tables['groups'], $this->tables['users'].'.id_user_group = '.$this->tables['groups'].'.id', 'left');

	    if ($is_code)
	    {
				$this->db->where($this->tables['users'].'.str_forgotten_password_code', $identity);
	    }
	    else
	    {
				$this->db->where($this->tables['users'].'.'.$this->identity_column, $identity);
	    }

	    $this->db->where($this->user->_extra_where);

	    $this->db->limit(1);
	    $i = $this->db->get($this->tables['users']);

	    return ($i->num_rows > 0) ? $i->row() : FALSE;
	}

	/**
	 * Basic functionality
	 *
	 * Register
	 * Login
	 *
	 * @author Mathew
	 */

	/**
	 * register
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function register($username, $password, $email, $additional_data = false, $group_name = false)		{
		if ($this->identity_column == 'email_email' && $this->email_check($email))		{
			$this->user->set_error('account_creation_duplicate_email');
			return FALSE;
		}
		elseif ($this->identity_column == 'str_username' && $this->username_check($username))	{
			$this->user->set_error('account_creation_duplicate_username');
			return FALSE;
		}

		// If username is taken, use username1 or username2, etc.
		if ($this->identity_column != 'str_username')	{
			for($i = 0; $this->username_check($username); $i++)	{
				if($i > 0) $username .= $i;
			}
		}

		// If a group ID was passed, use it
		if(isset($additional_data['id_user_group']))	{
			$id_user_group = $additional_data['id_user_group'];
			unset($additional_data['id_user_group']);
		}
		// Otherwise use the group name if it exists
		else {
			// Group ID
			if(!$group_name) {
				$group_name = $this->config->item('default_group', 'ion_auth');
			}
			$id_user_group = $this->db->select('id')
			->where('str_name', $group_name)
			->get($this->tables['groups'])
			->row()->id;
		}

		// IP Address
		$ip_address = $this->input->ip_address();
		$salt	= $this->store_salt ? $this->salt() : FALSE;
		$password	= $this->hash_password($password, $salt);

		// Users table.
		$data = array(
		'str_username'   => $username,
		'gpw_password'   => $password,
		'email_email'    => $email,
		'id_user_group'  => $id_user_group,
		'ip_address'     => $ip_address,
		'created_on'     => now(),
		'last_login'     => now(),
		'b_active'       => 1
		);

		if ($this->store_salt) {
			$data['str_salt'] = $salt;
		}

		if($this->user->_extra_set)	{
			$this->db->set($this->user->_extra_set);
		}

		$this->db->insert($this->tables['users'], $data);
		$id = $this->db->insert_id();

		// Meta table.
		if (!empty($this->columns) and !empty($this->tables['meta'])) {
			$data = array($this->meta_join => $id);
			if (!empty($this->columns)) {
				foreach ($this->columns as $input) {
					if (is_array($additional_data) && isset($additional_data[$input])) {
						$data[$input] = $additional_data[$input];
					}
					elseif ($this->input->post($input)) {
						$data[$input] = $this->input->post($input);
					}
				}
			}
			$this->db->insert($this->tables['meta'], $data);
		}

		return $this->db->affected_rows() > 0 ? $id : false;
	}

	/**
	 * login
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function login($identity, $password, $remember=FALSE)
	{
		if (empty($identity) || empty($password) || !$this->identity_check($identity))
		{
			return FALSE;
		}

		$query = $this->db->select($this->identity_column.', id, gpw_password, id_user_group')
		   								->where($this->identity_column, $identity)
		   								->where('b_active', 1)
		   								->where($this->user->_extra_where)
		   								->limit(1)
		   								->get($this->tables['users']);

		$result = $query->row();
    
    // trace_(array('function'=>'ion_auth->login','sql'=>$this->db->last_query(),'result'=>object2array($result)));

		if ($query->num_rows() == 1)
		{
			$password = $this->hash_password_db($identity, $password);
      
			if ($result->gpw_password === $password) {
        // JdB: update last login not at login, but at logout!
        // $this->update_last_login($result->id);

				$group_row = $this->db->select('str_name')->where('id', $result->id_user_group)->get($this->tables['groups'])->row();

			  $session_data = array(
													$this->identity_column => $result->{$this->identity_column},
													'id'                   => $result->id, //kept for backwards compatibility
													'user_id'              => $result->id, //everyone likes to overwrite id so we'll use user_id
													'id_user_group'        => $result->id_user_group,
													'group'                => $group_row->str_name
													 );
				$this->session->set_userdata($session_data);

				if ($remember && $this->config->item('remember_users', 'ion_auth')) {
					$this->remember_user($result->id);
				}

				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * get_users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 * Changed by JdB: join only meta data if meta table is set
	 **/
	public function get_users($group=false, $limit=NULL, $offset=NULL)
	{
		$this->db->select(array(
						$this->tables['users'].'.*',
						$this->tables['groups'].'.str_name AS '. $this->db->protect_identifiers('group'),
						$this->tables['groups'].'.str_description AS '. $this->db->protect_identifiers('group_description')
				   	));

   	if (!empty($this->columns) and !empty($this->tables['meta']))	{
			foreach ($this->columns as $field) {
		    $this->db->select($this->tables['meta'].'.'. $field);
			}
	    $this->db->join($this->tables['meta'], $this->tables['users'].'.id = '.$this->tables['meta'].'.'.$this->meta_join, 'left');
	  }
    $this->db->join($this->tables['groups'], $this->tables['users'].'.id_user_group = '.$this->tables['groups'].'.id', 'left');
    if (is_string($group))  {
			$this->db->where($this->tables['groups'].'.str_name', $group);
	  }
	  else if (is_array($group)) {
			$this->db->where_in($this->tables['groups'].'.str_name', $group);
	  }
		
	  if (isset($this->user->_extra_where) && !empty($this->user->_extra_where)) {
			$this->db->where($this->user->_extra_where);
	  }

		if (isset($limit) && isset($offset)) $this->db->limit($limit, $offset);
    return $this->db->get($this->tables['users']);
	}

	/**
	 * get_users_count
	 *
	 * @return int Number of Users
	 * @author Sven Lueckenbach
	 **/
	public function get_users_count($group=false)
	{
	    if (is_string($group))
	    {
			$this->db->where($this->tables['groups'].'.str_name', $group);
	    }
	    else if (is_array($group))
	    {
			$this->db->where_in($this->tables['groups'].'.str_name', $group);
	    }

	    if (isset($this->user->_extra_where) && !empty($this->user->_extra_where))
	    {
			$this->db->where($this->user->_extra_where);
	    }		

		$this->db->from($this->tables['users']);

	    return $this->db->count_all_results();
	}

	/**
	 * get_active_users
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function get_active_users($group_name = false)
	{
	    $this->db->where($this->tables['users'].'.active', 1);

	    return $this->get_users($group_name);
	}

	/**
	 * get_inactive_users
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function get_inactive_users($group_name = false) {
  	$this->db->where($this->tables['users'].'.b_active', 0);
	  return $this->get_users($group_name);
	}


	/**
	 * get_inactive_old_users
	 *
	 * time as seconds since today
	 *  
	 * @return object
	 * @author Jan den Besten
	 **/
	public function get_inactive_old_users($group_name = false, $time=604800) {
		// day     86400
		// week    604800
		// 4 weeks 2419200
  	$this->db->where($this->tables['users'].'.created_on >', time()-$time);
  	$this->db->where($this->tables['users'].'.b_active', 0);
	  return $this->get_users($group_name);
	}

	
	/**
	 * get_user
	 *
	 * @return object
	 * @author Phil Sturgeon
	 **/
	public function get_user($id = false)
	{
	    //if no id was passed use the current users id
	    if (empty($id))
	    {
				$id = $this->session->userdata('user_id');
	    }

	    $this->db->where($this->tables['users'].'.id', $id);
	    $this->db->limit(1);

	    return $this->get_users();
	}

	/**
	 * get_user_by_email
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function get_user_by_email($email)
	{
	    $this->db->limit(1);

	    return $this->get_users_by_email($email);
	}

	/**
	 * get_users_by_email
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function get_users_by_email($email)
	{
	    $this->db->where($this->tables['users'].'.email_email', $email);

	    return $this->get_users();
	}

	/**
	 * get_user_by_username
	 *
	 * @return object
	 * @author Kevin Smith
	 **/
	public function get_user_by_username($username)
	{
	    $this->db->limit(1);

	    return $this->get_users_by_username($username);
	}

	/**
	 * get_users_by_username
	 *
	 * @return object
	 * @author Kevin Smith
	 **/
	public function get_users_by_username($username)
	{
	    $this->db->where($this->tables['users'].'.str_username', $username);

	    return $this->get_users();
	}
	
	/**
	 * get_user_by_identity
	 *                                      //copied from above ^
	 * @return object
	 * @author jondavidjohn
	 **/
	public function get_user_by_identity($identity)
	{
	    $this->db->where($this->tables['users'].'.'.$this->identity_column, $identity);
	    $this->db->limit(1);

	    return $this->get_users();
	}

	/**
	 * get_newest_users
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function get_newest_users($limit = 10)
  	{
	    $this->db->order_by($this->tables['users'].'.created_on', 'desc');
	    $this->db->limit($limit);

	    return $this->get_users();
  	}

	/**
	 * get_users_group
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function get_users_group($id=false)
	{
	    //if no id was passed use the current users id
	    $id || $id = $this->session->userdata('user_id');

	    $user = $this->db->select('id_user_group')
			     ->where('id', $id)
			     ->get($this->tables['users'])
			     ->row();

	    return $this->db->select('str_name, str_description')
			    ->where('id', $user->id_user_group)
			    ->get($this->tables['groups'])
			    ->row();
	}

	/**
	 * get_groups
	 *
	 * @return object
	 * @author Phil Sturgeon
	 **/
	public function get_groups()
  	{
	    return $this->db->get($this->tables['groups'])
			    ->result();
  	}

	/**
	 * get_group
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function get_group($id)
  	{
	    $this->db->where('id', $id);

	    return $this->db->get($this->tables['groups'])
			    ->row();
  	}

	/**
	 * get_group_by_name
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function get_group_by_name($name)
  	{
	    $this->db->where('str_name', $name);

	    return $this->db->get($this->tables['groups'])
			    ->row();
  	}

	/**
	 * update_user
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function update_user($id, $data)
	{
	    $user = $this->get_user($id)->row();
	
	    $this->db->trans_begin();

	    if (array_key_exists($this->identity_column, $data) && $this->identity_check($data[$this->identity_column]) && $user->{$this->identity_column} !== $data[$this->identity_column])
	    {
				$this->db->trans_rollback();
				$this->user->set_error('account_creation_duplicate_'.$this->identity_column);
				return FALSE;
	    }

	    if (!empty($this->columns))
	    {
				//filter the data passed by the columns in the config
				$meta_fields = array();
				foreach ($this->columns as $field)
				{
					if (is_array($data) && isset($data[$field]))
					{
					$meta_fields[$field] = $data[$field];
					unset($data[$field]);
					}
				}

				//update the meta data
				if (count($meta_fields) > 0)
				{
					// 'user_id' = $id
					$this->db->where($this->meta_join, $id);
					$this->db->set($meta_fields);
					$this->db->update($this->tables['meta']);
				}
	    }
	
	    if (array_key_exists('str_username', $data) || array_key_exists('gpw_password', $data) || array_key_exists('email_email', $data) || array_key_exists('id_user_group', $data) || array_key_exists('b_active',$data))
			{
				if (array_key_exists('gpw_password', $data))
				{
					$data['gpw_password'] = $this->hash_password($data['gpw_password'], $user->salt);
				}

				$this->db->where($this->user->_extra_where);

				$this->db->update($this->tables['users'], $data, array('id' => $id));
	    }

	    if ($this->db->trans_status() === FALSE)
	    {
				$this->db->trans_rollback();
				return FALSE;
	    }

	    $this->db->trans_commit();
	    return TRUE;
	}


	/**
	 * delete_user
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function delete_user($id)
	{
	    $this->db->trans_begin();
			
			if (!empty($this->columns) and !empty($this->tables['meta'])) {
		    $this->db->delete($this->tables['meta'], array($this->meta_join => $id));
			}
	    $this->db->delete($this->tables['users'], array('id' => $id));

	    if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return FALSE;
	    }

	    $this->db->trans_commit();
	    return TRUE;
	}


	/**
	 * update_last_login
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function update_last_login($id)
	{
	    $this->load->helper('date');

	    if (isset($this->user->_extra_where) && !empty($this->user->_extra_where))
	    {
			$this->db->where($this->user->_extra_where);
	    }

	    $this->db->update($this->tables['users'], array('last_login' => now()), array('id' => $id));

	    return $this->db->affected_rows() == 1;
	}


	/**
	 * set_lang
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function set_lang($lang = 'en')
	{
	    set_cookie(array(
			'name'   => 'lang_code',
			'value'  => $lang,
			'expire' => $this->config->item('user_expire', 'ion_auth') + time()
			    ));

	    return TRUE;
	}

	/**
	 * login_remembed_user
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function login_remembered_user()
	{
	    //check for valid data
	    if (!get_cookie('identity') || !get_cookie('remember_code') || !$this->identity_check(get_cookie('identity')))
	    {
		    return FALSE;
	    }

	    //get the user
	    if (isset($this->user->_extra_where) && !empty($this->user->_extra_where))
	    {
			$this->db->where($this->user->_extra_where);
	    }

	    $query = $this->db->select($this->identity_column.', id, id_user_group')
			      ->where($this->identity_column, get_cookie('identity'))
			      ->where('str_remember_code', get_cookie('remember_code'))
			      ->limit(1)
			      ->get($this->tables['users']);

	    //if the user was found, sign them in
	    if ($query->num_rows() == 1)
	    {
		$user = $query->row();

		$this->update_last_login($user->id);

		$group_row = $this->db->select('str_name')->where('id', $user->id_user_group)->get($this->tables['groups'])->row();

		$session_data = array(
				    $this->identity_column => $user->{$this->identity_column},
				    'id'                   => $user->id, //kept for backwards compatibility
				    'user_id'              => $user->id, //everyone likes to overwrite id so we'll use user_id
				    'id_user_group'             => $user->id_user_group,
				    'group'                => $group_row->name
				     );

		$this->session->set_userdata($session_data);


		//extend the users cookies if the option is enabled
		if ($this->config->item('user_extend_on_login', 'ion_auth'))
		{
		    $this->remember_user($user->id);
		}

		return TRUE;
	    }

	    return FALSE;
	}

	/**
	 * remember_user
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	private function remember_user($id)
	{
	    if (!$id)
	    {
			return FALSE;
	    }

	    $user = $this->get_user($id)->row();

      // $salt = sha1($user->gpw_password);
      $salt = random_string('unique',40);

	    $this->db->update($this->tables['users'], array('str_remember_code' => $salt), array('id' => $id));

	    if ($this->db->affected_rows() > -1)
	    {
			set_cookie(array(
					'name'   => 'identity',
					'value'  => $user->{$this->identity_column},
					'expire' => $this->config->item('user_expire', 'ion_auth'),
					));

			set_cookie(array(
					'name'   => 'remember_code',
					'value'  => $salt,
					'expire' => $this->config->item('user_expire', 'ion_auth'),
					));

			return TRUE;
	    }

	    return FALSE;
	}
}
