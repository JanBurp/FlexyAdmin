<?php

/** \ingroup models
 * API user
 * 
 *    
 * @author Jan den Besten
 */

class User extends Api_Model {
  
  var $needs = array(
    'action' => '',
  );
  
	public function __construct() {
		parent::__construct();
  }
  
  /**
   * Send am invite to the given emailadress
   *
   * @return mixed
   * @author Jan den Besten
   */
  public function index() {
    // Check rechten
    if (!$this->has_args()) return $this->_result_wrong_args(); 
    if (!$this->_has_rights('cfg_users')) {
      return $this->_result_status401();
    }
    
    $this->lang->load('help');
    
    $action = $this->args['action'];
    $email  = $this->args['email'];
    $visitor = $this->data->table('cfg_user_groups')->get_field('id',array('name'=>'visitor'));

    // Multiple users
    if (isset($this->args['where']) && is_array($this->args['where'])) {
      foreach ($this->args['where'] as $user_id) {
        $user  = $this->flexy_auth->get_user($user_id);
        if (!$user) {
          $this->result['data']=FALSE;
          $this->_set_error('NO USER FOUND');
        }
        else {
          if ($action=='setgroup') {
            $this->flexy_auth->add_to_group($visitor, $user['id']);
            $message = lang('users_setas_visitor');
            $this->_set_message( $message );
          }
          else {
            $send = $this->flexy_auth->send_new_password( $user );
            if (!$send) {
              $this->result['data']=FALSE;
              $this->_set_error( langp('user_send_error',$user['str_username']));
            }
            else {
              $message = lang('users_send_passwords');
              $this->_set_message( $message );
            }
          }
        }
      }
    }
    // One user
    else {
      $user  = $this->flexy_auth->get_user_by_email($email);      
      if (!$user) {
        $this->result['data']=FALSE;
        $this->_set_error('NO USER FOUND');
      }
      else {
        if ($action=='setgroup') {
          $this->flexy_auth->add_to_group($visitor, $user['id']);
          $message = langp('user_setas_visitor',$user['str_username']);
          $this->_set_message( $message );
        }
        else {
          if ($action==='invite') {
            $send = $this->flexy_auth->send_new_account( $user );
          }
          else {
            $send = $this->flexy_auth->send_new_password( $user );
          }
          // Error when sending
          if (!$send) {
            $this->result['data']=FALSE;
            $this->_set_error( langp('user_send_error',$user['str_username']));
          }
          else {
            $message = ($action==='invite')?langp('send_invitation',$user['str_username']):langp('user_send_password',$user['str_username']);
            $this->_set_message( $message );
          }
        }
      }
    }
    return $this->_result_ok();
  }
  
}


?>
