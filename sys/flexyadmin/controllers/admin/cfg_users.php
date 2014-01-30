<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."core/AdminController.php");


/**
	* Verzorgt het activeren van gebruikers
	*
	* @author Jan den Besten
	* @package FlexyAdmin_login
	**/
class cfg_users extends AdminController {
   
   /**
    * @ignore
    */
	public function __construct() {
		parent::__construct();
    $this->load->language('help');
    $this->config->load('login');
    if (file_exists(SITEPATH.'views/login'))
      $this->config->set_item('email_templates','login/'.$this->language.'/','ion_auth');
    else
      $this->config->set_item('email_templates','cfg_users/'.$this->language.'/','ion_auth');
	}
	
  /**
   * Weiger gebruiker(s)
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   * @ignore
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
   * @ignore
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
   * @ignore
   */
  public function invite($user_id) {
      return $this->_do_action('invite',$user_id);
  }


  /**
   * Stuur gebruiker(s) nieuw wachtwoord
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   */
  public function send_new_password($user_id) {
    return $this->_do_action('send_new_password',$user_id);
  }


  private function _do_action($action,$user_id=false) {
		if ($this->_can_activate_users()) {
      if (!$user_id) {
        $users_ids=$this->_get_inactive_user_ids();
      }
      else {
        $users_ids=array($user_id);
      }
      foreach ($users_ids as $user_id) {
        $user=$this->user->get_user($user_id);
        $extra_emails=$this->_extra_emails($user_id);
        switch ($action) {
          case 'deny':
      			$message='user_removed';
      			$this->user->send_deny_mail($user_id,lang('mail_denied_subject'),$extra_emails);
      			$this->user->delete_user($user_id);
            break;
          case 'accept':
      			$message='user_accepted';
      			$this->user->send_accepted_mail($user_id,lang('mail_accepted_subject'),$extra_emails);
      			$this->user->activate_user($user_id);
            break;
          case 'invite':
            $message='send_invitation';
            $this->user->send_new_account_mail($user_id,lang('mail_new_subject'),$extra_emails);
            $this->user->activate_user($user_id);
            break;
          case 'send_new_password':
            $message='user_send_password';
            $code=$this->ion_auth_model->forgotten_password_by_id($user_id);
            $this->user->forgotten_password_complete($code,lang('new_password'),$extra_emails);
            break;
        }
        if (isset($message)) $this->message->add(langp($message,$user->str_username.'('.$user->email_email.','.$extra_emails.')'));
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


  private function _get_inactive_user_ids() {
    $user_ids=array();
    $users=$this->user->get_inactive_users_array();
    foreach ($users as $user) {
      $user_ids[$user['id']]=$user['id'];
    }
    return $user_ids;
  }

  private function _can_activate_users() {
    return ($this->user->can_activate_users());
  }
  
}

?>