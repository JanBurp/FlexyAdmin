<?php

/**
 * CLI example of authentication
 * 
 * - auth .... login <username> <password>
 *
 * @package default
 * @author Jan den Besten
 */
class Auth extends CI_Model {
  
  public function index()  {
    $user_info = $this->user->get_user();
    if ($user_info) {
      echo "auth: logged in as '".$user_info->str_username."'".PHP_EOL;
      if ($user_info->group=='super_admin') echo "auth: is super admin".PHP_EOL;
    }
    else {
      echo "auth: no user".PHP_EOL;
    }
  }
  
}




?>