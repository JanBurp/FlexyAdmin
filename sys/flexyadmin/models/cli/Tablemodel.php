<?php

/**
 * CLI Tablemodel
 *
 * @package default
 * @author Jan den Besten
 */
class Tablemodel extends CI_Model {
  
  public function index()  {
    $user_info = $this->user->get_user();
    if ($user_info and $user_info->group=='super_admin') {
      $args = func_get_args();
      $table = (string) el(0,$args,'');
      if ($table=='login') $table='';
      $this->load->model('tables/table_model_create');
      $this->table_model_create->create($table);
      echo $this->table_model_create->output();
    }
    else {
      echo "You nee to be an admin user...".PHP_EOL;
    }
  }
  
  public function help()  {
    return "== `tablemodel` creating table models ==".PHP_EOL."tablemodel <table> login <username> <password>".PHP_EOL;
  }
  
}




?>