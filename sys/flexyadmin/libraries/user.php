<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:  User
*
* Exends ion_auth for FlexyAdmin use, make sure ion_auth is loaded before
*/

class User Extends Ion_auth {
	
	protected $rights;
	protected $user_id;

	public function __construct() {
		parent::__construct();
	}
	
	
	public function login($identity, $password, $remember=false) {
		if ( ! $this->_check_if_userdate_ok()) {
			$this->set_message('update_needed');
		}
		else {
			if ( $this->_check_if_old_password($identity) ) {
				$this->_update_old_passwords();
			}
			if ($this->ci->ion_auth_model->login($identity, $password, $remember)) {
				return TRUE;
			}
		}
		$this->set_error('login_unsuccessful');
		return FALSE;
	}
	
	private function _check_if_userdate_ok() {
		return ($this->ci->db->field_exists('str_username','cfg_users') and $this->ci->db->field_exists('gpw_password','cfg_users') );
	}
	
	private function _check_if_old_password($identity) {
		$new_password = FALSE;
		// check if password field length = 40 and the password itself also 40 chars long, that should do it
		$field_data=$this->ci->db->field_data('cfg_users');
		foreach ($field_data as $field_info) {
			$field_info=object2array($field_info);
			if ($field_info['name']=='gpw_password') {
				$password = $this->ci->db->get_field_where('cfg_users','gpw_password','str_username',$identity);
				$new_password = ( $field_info['max_length']==40 and strlen($password)==40 );
			}
		}
		return ! $new_password;
	}
	
	private function _update_old_passwords() {
		// update all users:
		$this->ci->db->select('id,gpw_password,id_user_group');
		$users=$this->ci->db->get_results('cfg_users');
		foreach ($users as $id => $user) {
			$set=array();
			// hash password, and get usergroup
			$set['gpw_password']=$this->ci->ion_auth_model->hash_password($user['gpw_password']);
			$set['id_user_group']=$this->ci->db->get_field_where('rel_users__rights','id_rights','id_users',$id);
			// update
			$this->ci->db->set($set);
			$this->ci->db->where('id',$id);
			$this->ci->db->update('cfg_users');
		}
		// remove 'rel_users__rights'
		$this->ci->load->dbforge();
		$this->ci->dbforge->drop_table('rel_users__rights');
		// set a message
		$this->set_message('update_to_safe_passwords');
	}
	
	
	
	public function logged_in() {
		$logged_in = parent::logged_in();
		if ($logged_in) {
			$this->user_id = $this->ci->session->userdata("user_id");
			$this->rights = $this->create_rights( $this->user_id );
		}
		return (bool) $logged_in;
	}
	
	
	
	

	function is_super_admin() {
		return ($this->rights["rights"]=="*");
	}

	function can_backup() {
		if ($this->rights['b_backup']) return TRUE;
		return FALSE;
	}

	function can_use_tools() {
		if ($this->rights['b_tools']) return TRUE;
		return FALSE;
	}

	function create_rights($userId) {
		$this->ci->db->select('id,id_user_group');
		$this->ci->db->where("cfg_users.id",$userId);
		// $this->ci->db->add_foreigns(array('cfg_user_groups'=>array('rights','b_all_users','b_backup','b_tools','b_delete','b_add','b_edit','b_show')));
		$this->ci->db->add_foreigns();
		$user=$this->ci->db->get_row('cfg_users');
		$rights=array();
		foreach ($user as $key => $value) {
			if (!in_array($key,array('id','id_group'))) {
				$rights[str_replace('cfg_user_groups__','',$key)]=$value;
			}
		}
		return $rights;
	}


	/**
		* Returns rights:
		*		RIGHTS_ALL		= 15 (all added)
		*		RIGHTS_DELETE	= 8
		*		RIGHTS_ADD		= 4
		*		RIGHTS_EDIT		= 2
		*		RIGHTS_SHOW		= 1
		*		RIGHTS_NO			= 0 
		* Or FALSE/TRUE if it has minimal these rights
		*/
	function _change_rights(&$found,$rights) {
		foreach ($found as $key => $value) {
			if ($rights[$key]) $found[$key]=TRUE;
		}
	}
	function has_rights($item,$id="",$whatRight=0) {
		// No rights if cfg_users and id is smaller (higher rights)
		if ($item=='cfg_users' and !empty($id) and ($id!=-1) and ($id<$this->user_id)) return false;
		
		$found=array('b_delete'=>FALSE,'b_add'=>FALSE,'b_edit'=>FALSE,'b_show'=>FALSE);
		$pre=get_prefix($item);
		$preAll=$pre."_*";

		$foundRights=RIGHTS_NO;
		
		// $condition=($item=='media_knipsels');
		// trace_if($condition,$item);
		// trace_if($condition,$this->rights);
		// trace_if($condition,array('item'=>$item,'pre'=>$pre,'preAll'=>$preAll));

		$rights=$this->rights;
		if ($rights['rights']=="*" or (strpos($rights['rights'],$preAll)!==FALSE) or (strpos($rights['rights'],$item)!==FALSE) ) {
			$this->_change_rights($found,$rights);
		}
		// trace_if($condition,$found);
		if (!empty($found['b_delete'])	and $found['b_delete'])	$foundRights+=RIGHTS_DELETE;
		if (!empty($found['b_add']) 		and $found['b_add'])		$foundRights+=RIGHTS_ADD;
		if (!empty($found['b_edit'])		and $found['b_edit'])		$foundRights+=RIGHTS_EDIT;
		if (!empty($found['b_show'])		and $found['b_show'])		$foundRights+=RIGHTS_SHOW;

		// trace_if($condition,$foundRights);
		// trace_if($condition,$whatRight);

		if ($whatRight==0)
			return $foundRights;
		else
			return ($foundRights>=$whatRight);
	}
	
	// returns NULL if no user restrictions, else it gives back the user_id
	function restricted_id($table) {
		$restricted=TRUE;
		$pre=get_prefix($table);
		$preAll=$pre."_*";
		$rights=$this->rights;
		if ($rights['rights']=="")
			$restricted=FALSE;
		if ($rights['rights']=="*" or (strpos($rights['rights'],$preAll)!==FALSE) or (strpos($rights['rights'],$table)!==FALSE) )
			$restricted=$restricted and TRUE;
		if ($restricted) {
			return $this->user_id;
		}
		else
			return FALSE;
	}

	function get_table_rights($atLeast=RIGHTS_ALL) {
		$tables=$this->ci->db->list_tables();
		$tableRights=array();
		foreach ($tables as $key => $table) {
			$pre=get_prefix($table);
			if ($pre==$this->ci->config->item('REL_table_prefix')) {
				$rTable=table_from_rel_table($table);
				$rights=$this->has_rights($rTable);
			}
			else {
				$rights=$this->has_rights($table);
			}
			if ($rights>=$atLeast) $tableRights[]=$table;
		}
		return $tableRights;
	}
	
	function get_rights() {
		return $this->rights;
	}



}
