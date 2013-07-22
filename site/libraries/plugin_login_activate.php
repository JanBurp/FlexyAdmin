<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
	* Verzorgt het activeren van gebruikers
	*
	* Is een onderdeel van de login module
	*
	* Bestanden
	* ----------------
	*
	* - site/libraries/login.php - De login module, kijk daar voor meer info.
	* - site/views/plugin_login_activate.php
	*
	* @author Jan den Besten
	* @package FlexyAdmin_login
	**/
 class Plugin_login_activate extends Plugin {
   
   /**
    * @ignore
    */
	public function __construct() {
		parent::__construct();
		$this->CI->load->language('login');
	}
	
  /**
   * Toont een lijst met niet geactiveerde gebruikers en knoppen om die gebruikers toe te staan of te weigeren
   *
   * @param string $args 
   * @return string
   * @author Jan den Besten
   */
	public function _admin_api($args=NULL) {
		if ($this->CI->user->can_activate_users()) {
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
				case 'accept_send':
					$this->_accept_user_and_send($user_id);
					break;
				case 'all':
					$this->_all($user_id); // $user_id is action in this case
					break;
			}
			$grid=$this->_show_inactive_users();
      if (!empty($grid)) {
        $this->add_message('<p>Alle gebruikers: <a class="button" href="admin/plugin/'.$this->shortname.'/all/deny">weigeren</a> | <a class="button" href="admin/plugin/'.$this->shortname.'/all/accept">accepteren</a> | <a class="button" href="admin/plugin/'.$this->shortname.'/all/accept_send">accepteren en inlog sturen</a>');
      }
      return $this->view('plugin_login_activate',array('title'=>lang('title'),'grid'=>$grid));
		}
	}
	
  /**
   * Weiger gebruiker
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	private function _deny_user($user_id) {
		$user=$this->CI->user->get_user($user_id);
		if ($user) {
			$this->add_message(langp('user_removed',$user->str_username));
			$this->CI->user->send_deny_mail($user_id,lang('mail_denied_subject'));
			$this->CI->user->delete_user($user_id);
		}
	}

  /**
   * Accepteer gebruiker
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	private function _accept_user($user_id) {
		$user=$this->CI->user->get_user($user_id);
		if ($user and !$user->b_active) {
			$this->add_message(langp('user_accepted',$user->str_username));
			$this->CI->user->send_accepted_mail($user_id,lang('mail_accepted_subject'));
			$this->CI->user->activate_user($user_id);
		}
	}

  /**
   * Accepteer gebruiker en stuur de inlog
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	private function _accept_user_and_send($user_id) {
		$user=$this->CI->user->get_user($user_id);
		if ($user and !$user->b_active) {
      $this->add_message(langp('user_accepted_send',$user->str_username));
      $this->CI->user->send_new_account_mail($user_id,lang('mail_accepted_subject'));
      $this->CI->user->activate_user($user_id);
		}
	}
  
  private function _all($action) {
		$users=$this->CI->user->get_inactive_users_array();
		if ($users) {
      foreach ($users as $user) {
  			switch ($action) {
  				case 'deny':
  					$this->_deny_user($user['id']);
  					break;
  				case 'accept':
  					$this->_accept_user($user['id']);
  					break;
  				case 'accept_send':
  					$this->_accept_user_and_send($user['id']);
  					break;
  			}
      }
		}
		else {
			$this->add_message(lang('no_inactive_users'));
		}
	}
	
  /**
   * Toont de lijst met inactieve gebruikers en opties in een grid
   *
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	private function _show_inactive_users() {
		$users=$this->CI->user->get_inactive_users_array();
		if ($users) {
			$show_users=array();
			foreach ($users as $key => $u) {
				$show_users[$key]=array( $this->CI->ui->get('str_username')=>$u['str_username'], $this->CI->ui->get('email_email')=>'<a href="mailto:'.$u['email_email'].'">'.$u['email_email'].'</a>', $this->CI->ui->get('group')=>$u['group']);
				$show_users[$key]['']=anchor('admin/plugin/'.$this->shortname.'/deny/'.$u['id'],lang('deny'),array('class'=>'button')).' | '.
                              anchor('admin/plugin/'.$this->shortname.'/accept/'.$u['id'],lang('accept'),array('class'=>'button')).' | '.
                              anchor('admin/plugin/'.$this->shortname.'/accept_send/'.$u['id'],lang('accept_send'),array('class'=>'button'));
			}
			$this->CI->load->model('grid');
			$grid=new grid();
			$grid->set_data($show_users,lang('show_inactive_users'));
			return $grid->view("html",'',"grid");
		}
		else {
			$this->add_message(lang('no_inactive_users'));
		}
	}


}

?>