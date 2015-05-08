<?php 

/**
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Model_updates extends CI_Model {
  
  private   $messages=array();
  protected $error=false;
  protected $rev=0;

  public	function __construct() {
    parent::__construct();
    $this->rev=substr(get_class($this),7,4);
  }
  
  protected function _add_message($message,$glyphicon='',$class='') {
    $this->messages['code'][]=array(
      'message'   => '<b>'.$this->rev.'</b> '.$message,
      'glyphicon' => $glyphicon,
      'class'     => $class
    );
  }
  
  public function update() {
    if ($this->error) {
      $this->_add_message('<b>not up to date see errors</b>','glyphicon-remove btn-danger');
    }
    else {
      $this->_add_message('<b>up to date</b>','glyphicon-ok btn-success');
    }
    return $this->messages;
  }

 }
?>
