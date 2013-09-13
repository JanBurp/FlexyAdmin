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
    if (!isset($this->config['actions'])) {
      $this->config['actions'] = array(
        'deny'          => TRUE,
        'accept'        => TRUE,
        'accept_send'   => FALSE,
        'all'           => FALSE
      );
    }
	}
	
  /**
   * Toont een lijst met niet geactiveerde gebruikers en knoppen om die gebruikers toe te staan of te weigeren
   * En een lijst met alle aktieve gebruikers (met emailadres), en de mogelijkheid om deze een (nieuw) wachtwoord toe te sturen.
   *
   * @param string $args 
   * @return string
   * @author Jan den Besten
   */
	public function _admin_api($args=NULL) {
		if ($this->_can_activate_users()) {
      
			// redirect ion_auth to other email_templates
			$this->CI->config->set_item('email_templates','login/'.$this->CI->language.'/','ion_auth');
      
			// $args holds action (deny/activate) and user_id
			$action=el('0',$args);
			$user_id=el('1',$args);
			switch ($action) {
				case 'deny':
					if ($this->config['actions']['deny']) $this->_deny_user($user_id);
					break;
				case 'accept':
					if ($this->config['actions']['accept']) $this->_accept_user($user_id);
					break;
				case 'accept_send':
					if ($this->config['actions']['accept_send']) $this->_accept_user_and_send($user_id);
					break;
				case 'all':
					if ($this->config['actions']['all']) $this->_all($user_id); // $user_id is action in this case
					break;
          
        case 'send_new_password':
          if ($this->config['active_actions']['send_new_password']) $this->_send_new_password($user_id);
          break;
			}
      
      // Accept/activate users
			$inactive_users=$this->_show_inactive_users();
      
      // Send new password to user(s)
      $active_users=$this->_show_active_users();

      return $this->view('plugin_login_activate',array('title'=>lang('title'),'inactive_users'=>$inactive_users,'active_users'=>$active_users));
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
  
  private function _send_new_password($user_id) {
		$user=$this->CI->user->get_user($user_id);
		if ($user) {
      $this->add_message(langp('user_send_password',$user->str_username));
      $code=$this->CI->ion_auth_model->forgotten_password($user->email_email);
      $this->CI->user->forgotten_password_complete($code,lang('new_password'));
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
    $html='';
    $html.=h(lang('activate_users'));
		$users=$this->CI->user->get_inactive_users_array();
		if ($users) {
      $top_menu='';
      foreach ($this->config['actions'] as $action => $active) {
        if ($active and $action!='all') {
          $top_menu=add_string($top_menu,'<a class="button" href="admin/plugin/'.$this->shortname.'/all/'.$action.'">'.lang($action).'</a>',' | ');
        }
      }
      if (!empty($top_menu)) $top_menu=lang('all_users').': '.$top_menu.br().br();
      $html.=$top_menu;
			$show_users=array();
			foreach ($users as $key => $u) {
				$show_users[$key]=array(
          $this->CI->ui->get('str_username')=>$u['str_username'],
          $this->CI->ui->get('email_email')=>'<a href="mailto:'.$u['email_email'].'">'.$u['email_email'].'</a>'
          // $this->CI->ui->get('group')=>$u['group']
        );
        $menu='';
        foreach ($this->config['actions'] as $action => $active) {
          if ($active and $action!='all') {
            $menu=add_string($menu,anchor('admin/plugin/'.$this->shortname.'/'.$action.'/'.$u['id'],lang($action),array('class'=>'button')),' | ');
          }
        }
        $show_users[$key]['']=$menu;
			}
			$this->CI->load->model('grid');
			$grid=new grid();
			$grid->set_data($show_users,lang('show_inactive_users'));
			$html.=$grid->view("html",'',"grid");
		}
    else {
      $html.=p().lang('no_inactive_users')._p();
    }
    return $html;
	}

  /**
   * Toont lijst met actieve gebruikers (met emailadres) en opties in een grid
   *
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	private function _show_active_users() {
    $html='';
    if (isset($this->config['active_actions'])) {
      $html.=h(lang('active_users'));
  		$users=$this->CI->user->get_active_users_array();
  		if ($users) {
  			$show_users=array();
  			foreach ($users as $key => $u) {
  				$show_users[$key]=array(
            $this->CI->ui->get('str_username')=>$u['str_username'],
            $this->CI->ui->get('email_email')=>'<a href="mailto:'.$u['email_email'].'">'.$u['email_email'].'</a>'
          );
          $menu='';
          foreach ($this->config['active_actions'] as $action => $active) {
            if ($active and $action!='all') {
              $menu=add_string($menu,anchor('admin/plugin/'.$this->shortname.'/'.$action.'/'.$u['id'],lang($action),array('class'=>'button')),' | ');
            }
          }
          $show_users[$key]['']=$menu;
  			}
  			$this->CI->load->model('grid');
  			$grid=new grid();
  			$grid->set_data($show_users,lang('show_active_users'));
        $html.=$grid->view("html",'',"grid");
  		}
  		else {
  			$this->add_message(lang('no_active_users'));
  		}
    }
    return $html;
	}



  private function _can_activate_users() {
    $user_group = $this->config('user_group','super_admin');
    $rights=$this->CI->user->get_rights();
    return ($rights['id_user_group']<=$user_group);
  }


}

?>