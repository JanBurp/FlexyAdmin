<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."core/AdminController.php");


/**
	* Verzorgt het activeren van gebruikers
	*
	* @author Jan den Besten
	**/
class cfg_users extends AdminController {
   
   /**
     */
	public function __construct() {
		parent::__construct();
    $this->load->language('help');
    $this->config->load('login');
    if (file_exists(SITEPATH.'views/login/'.$this->language))
      $this->config->set_item('email_templates','login/'.$this->language.'/','ion_auth');
    elseif (file_exists(SITEPATH.'cfg_users/'.$this->language))
      $this->config->set_item('email_templates','cfg_users/'.$this->language.'/','ion_auth');
	}
	
  /**
   * Weiger gebruiker(s)
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   */
	public function deny($user_id=false) {
    return $this->_do_action('deny',$user_id);
	}

  /**
   * Accepteer gebruiker(s) en stuur de inlog
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   */
	public function accept($user_id=false) {
    return $this->_do_action('accept',$user_id);
	}

  /**
   * Stuur een uitnodiging (eerste mail)
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   */
  public function invite($user_id=false) {
      return $this->_do_action('invite',$user_id);
  }


  /**
   * Stuur gebruiker(s) nieuw wachtwoord
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   */
  public function send_new_password($user_id=false) {
    return $this->_do_action('send_new_password',$user_id);
  }


  private function _do_action($action,$user_id=false) {
		if ($this->_can_activate_users()) {
      if (!$user_id) {
        $users_ids=$this->input->get('users');
        if (!$users_ids) {
          $users_ids=$this->_get_inactive_user_ids();
          if (!$users_ids and $action=='send_new_password') {
            $users_ids=$this->_get_inactive_user_ids(true);
          } 
        }
      }
      else {
        $users_ids=array($user_id);
      }
      foreach ($users_ids as $user_id) {
        $user = $this->flexy_auth->get_user($user_id);
        $extra_emails=$this->_extra_emails($user_id);
        switch ($action) {
          case 'deny':
      			$message='user_removed';
      			$this->flexy_auth->user_denied_mail( $user_id );
            $this->flexy_auth->delete_user($user_id);
            break;
          case 'accept':
      			$message='user_accepted';
      			$this->flexy_auth->user_accepted_mail( $user_id );
      			$this->flexy_auth->activate_user($user_id);
            break;
          case 'invite':
            $message='send_invitation';
            $this->flexy_auth->send_new_account($user_id);
            $this->flexy_auth->activate_user($user_id);
            break;
          case 'send_new_password':
            $message='user_send_password';
            if (! $this->flexy_auth->send_new_password($user_id)) {
              $message='user_send_password_error';
            }
            break;
        }
        if (isset($message)) $this->message->add(langp($message,$user['username'].'('.$user['email'].','.$user['extra_email_string'].')'));
      }
		}
    redirect(api_uri('API_view_grid','cfg_users'));
  }
	
  
  /**
   * Geeft extra email van gebruiker
   *
   * @param string $user_id 
   * @return string
   * @author Jan den Besten
   */
  private function _extra_emails($user_id) {
    $extra_emails='';
    $table=$this->config->item('extra_email_table');
    if (!empty($table)) {
      $fields=$this->db->list_fields($table);
      $fields=filter_by($fields,'email');
      $this->db->select($fields);
      $this->db->where('id_user',$user_id);
      $u=$this->db->get_row($table);
      if ($u) $extra_emails=trim(implode(', ',$u),', ');
    }
    return $extra_emails;
  }


  private function _get_inactive_user_ids($active=false) {
    $user_ids=array();
    $users=$this->flexy_auth->get_users();
    $this_user=$this->flexy_auth->user();
    $this_user_id=$this_user->id;
    foreach ($users as $user) {
      if (($active and $user->id!=$this_user_id) or (!$user->b_active or empty($user->last_login))) $user_ids[$user->id]=$user->id;
    }
    return $user_ids;
  }

  private function _can_activate_users() {
    return ($this->flexy_auth->allowed_to_edit_users());
  }
  
}

?>