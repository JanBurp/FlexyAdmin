<?php

/**
 * Creates datamodels for table(s) in database (for Data)
 * 
 * - datamodel login _username_ _password_              // creates all tables
 * - datamodel _table_ login _username_ _password_      // creates one table _table_
 * - datamodel cleancache login _username_ _password_   // resets all data models caches
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */
class Datamodel extends CI_Model {
  
  public function index()  {
    if ( $this->flexy_auth->is_super_admin() ) {
      $this->load->model('data/data_create');
      $args = func_get_args();
      $table = array_shift($args);
      if ($table=='cleancache') {
        $table = array_shift($args);
        $this->data_create->resetcache($table);
      }
      else {
        $this->data_create->create($table);
      }
      echo $this->data_create->output();
    }
    else {
      echo "You nee to be an admin user...".PHP_EOL;
    }
  }
  
}




?>