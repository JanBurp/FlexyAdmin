<?php require_once("ApiTestModel.php");

class ApiHelpTest extends ApiTestModel {

  public function __construct() {
    parent::__construct('get_help');
  }

  public function testWithoutLogin() {
    
    $this->_testWithoutAuth('get_help');
  }

  public function testWithLogin() {
    
    $this->_testWithAuth(array(
      'model'   => 'get_help',
      'args'    => array('table'=>''),
      'asserts' => array(
        'data'  => array( 'type'  => 'array' ),
        'data'  => array( 'count' => 3 ),
        'data'  => array( 'hasKey' => 'title' ),
        'data|title'  => array( 'type' => 'string' ),
        'data'  => array( 'hasKey' => 'common_help' ),
        'data'  => array( 'hasKey' => 'help' ),
        'data|help'  => array( 'type' => 'string' ),
      )
    ));
  }
  
  
}


?>