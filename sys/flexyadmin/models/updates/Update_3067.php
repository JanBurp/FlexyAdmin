<?php 

class Update_3067 extends Model_updates {
  
  public	function __construct() {
    parent::__construct();
  }
  
  public function update() {
    $this->_classes_to_ucfirst();
    $this->_cleanup_database_configs();

    parent::update();
    return $this->messages;
  }
  
  // For CI3.0 all class filenames must start with CAPITAL
  private function _classes_to_ucfirst() {
    $classes=read_map('site','',TRUE,FALSE,FALSE,FALSE);
    // Only classes
    foreach ($classes as $key=>$file) {
      if ($file['type']!='php') {
        unset($classes[$key]);
      }
      else {
        $code=file_get_contents($file['path']);
        if (preg_match("/class\s(.*)\sextends\s(.*){/u", $code)!==1) {
          unset($classes[$key]);
        }
      }
    }
    // Ok rename
    foreach ($classes as $key => $file) {
      $first_letter=substr($file['name'],0,1);
      if ($first_letter!==strtoupper($first_letter)) {
        $uppername=remove_suffix($file['path'],'/').'/'.ucfirst($file['name']);
        if (rename($file['path'],$uppername)) {
          $this->_add_message('site','Renamed `'.$file['name'].'` to `'.$uppername.'`','glyphicon-ok btn-success');
        } else {
          $this->error=true;
          $this->_add_message('site','Could not rename `'.$file['path'].'`','glyphicon-remove btn-danger');
        }
      }
    }
  }
  
  
  private function _cleanup_database_configs() {
    $configs=read_map('site/config','php',FALSE,FALSE);
    $configs=filter_by($configs,'database');
    // $configs=array_keys($configs);
    foreach ($configs as $file) {
      $old=file_get_contents($file['path']);
      $new=$old;
      // Vervang active_record door query_builder
      $new = preg_replace("/\$active_record/uUs", "\$query_builder", $new, 1);
      if ($old!==$new) {
        // Vervang commentaar
        $new = preg_replace("/\/\*.*\*\//uUs", "/*\n * -------------------------------------------------------------------\n * DATABASE CONNECTIVITY SETTINGS\n * -------------------------------------------------------------------\n * This file will contain the settings needed to access your database.\n *\n * For complete instructions please consult the Database Connection\n * page of the User Guide.\n *\n * -------------------------------------------------------------------\n * EXPLANATION OF VARIABLES\n * -------------------------------------------------------------------\n *\n *	['hostname'] The hostname of your database server.\n *	['username'] The username used to connect to the database\n *	['password'] The password used to connect to the database\n *	['database'] The name of the database you want to connect to\n */", $new, 1);
        // Verwijder overbodige instellingen
        $new = preg_replace("/^.*\[\'(dbdriver|dbprefix|pconnect|db_debug|cache_on|cachedir|char_set|dbcollat)'\].*\n/um", "", $new, 1);
        $new = str_replace("\r",'',$new);
        if ($old!=$new) {
          if (file_put_contents($file['path'],$new)===false) {
            $this->error=true;
            $this->_add_message('site','Error replacing `'.$file['path'].'`','glyphicon-remove btn-danger');
          }
          $this->_add_message('site','Replaced `'.$file['path'].'`','glyphicon-ok btn-success');
        }
        else {
          $this->error=true;
          $this->_add_message('site','Error replacing `'.$file['path'].'`','glyphicon-remove btn-danger');
        }
      }
      else {
        // Allready done
      }
    }
  }
  

 }
?>
