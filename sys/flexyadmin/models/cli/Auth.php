<?php

/**
 * CLI auth
 *
 * @package default
 * @author Jan den Besten
 */
class Auth extends CI_Model {

  public function index()  {
    $this->load->library('user');
    $user_info = $this->user->get_user();
    if ($user_info) {
      echo "auth: logged in as '".$user_info['str_username']."'".PHP_EOL;
    }
    else {
      echo "auth: no user".PHP_EOL;
    }
  }
  
  public function help()  {
    return "== `auth` can authenticate a user (NOT READY) ==".PHP_EOL."auth".PHP_EOL;
  }
  
}




?>