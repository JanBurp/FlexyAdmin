<?php

/**
 * Creates datamodels for table(s) in database
 * 
 * - datamodel login <username> <password> // creates all tables
 * - datamodel <table> login <username> <password> // creates one table <table>
 * 
 * @package default
 * @author Jan den Besten
 */
class Datamodel extends CI_Model {
  
  public function index()  {
    $user_info = $this->user->get_user();
    if ($user_info and $user_info->group=='super_admin') {
      $args = func_get_args();
      $table = (string) el(0,$args,'');
      if ($table=='login') $table='';
      $this->load->model('data/data_model_create');
      $this->data_model_create->create($table);
      echo $this->data_model_create->output();
    }
    else {
      echo "You nee to be an admin user...".PHP_EOL;
    }
  }
  
}




?>