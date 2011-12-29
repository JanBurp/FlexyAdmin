<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plugin_login_activate extends Plugin_ {


	public function __construct() {
		parent::__construct();
		$this->CI->load->language('login');
	}
	
	public function _admin_api($args=NULL) {
		if ($this->CI->user->can_activate_users()) {
			$this->add_content(h(lang('activate_users'),1));
			
			// redirect ion_auth to other email_templates
			$this->CI->config->set_item('email_templates','login/'.$this->CI->language.'/','ion_auth');
			
			// $args holds action (deny/activate) and user_id
			$action=el('0',$args);
			$user_id=el('1',$args);
			
			switch ($action) {
				case 'deny':
					$this->_deny_user($user_id);
					break;
				case 'accept':
					$this->_accept_user($user_id);
					break;
			}
			$this->_show_inactive_users();
		}
	}
	
	private function _deny_user($user_id) {
		$user=$this->CI->user->get_user($user_id);
		if ($user) {
			$this->add_content(p().langp('user_removed',$user->str_username)._p());
			$this->CI->user->send_deny_mail($user_id,lang('mail_denied_subject'));
			$this->CI->user->delete_user($user_id);
		}
	}

	private function _accept_user($user_id) {
		$user=$this->CI->user->get_user($user_id);
		if ($user and !$user->b_active) {
			$this->add_content(p().langp('user_accepted',$user->str_username)._p());
			$this->CI->user->send_accepted_mail($user_id,lang('mail_accepted_subject'));
			$this->CI->user->activate_user($user_id);
		}
	}
	
	private function _show_inactive_users() {
		$users=$this->CI->user->get_inactive_users_array();
		if ($users) {
			$show_users=array();
			foreach ($users as $key => $u) {
				$show_users[$key]=array( $this->CI->ui->get('str_username')=>$u['str_username'], $this->CI->ui->get('email_email')=>'<a href="mailto:'.$u['email_email'].'">'.$u['email_email'].'</a>', $this->CI->ui->get('group')=>$u['group']);
				$show_users[$key]['']=anchor('admin/plugin/'.$this->shortname.'/deny/'.$u['id'],lang('deny'),array('class'=>'button')).' | '.anchor('admin/plugin/'.$this->shortname.'/accept/'.$u['id'],lang('accept'),array('class'=>'button'));
			}
			$this->CI->load->model('grid');
			$grid=new grid();
			$grid->set_data($show_users,lang('show_inactive_users'));
			$html=$grid->view("html",'',"grid");
			$this->add_content($html);
		}
		else {
			$this->add_content(lang('no_inactive_users'));
		}
	}


}

?>