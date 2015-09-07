<?php require_once("ApiTestModel.php");

class ApiPluginTest extends ApiTestModel {

  public function __construct() {
    parent::__construct('get_plugin');
  }

  public function testWithoutLogin() {
    echo "demo_db/api/ApiPluginTest".__METHOD__."\n";
    
    $this->_testWithoutAuth('get_plugin');
  }

  public function testWithLogin() {
    echo "demo_db/api/ApiPluginTest".__METHOD__."\n";
    
    $this->_testWithAuth(array(
      'model'   => 'get_plugin',
      'args'    => array('table'=>'','plugin'=>'stats'),
      'asserts' => array(
        'data'  => array( 'type'  => 'array' ),
        'data'  => array( 'count' => 3 ),
        'data'  => array( 'hasKey' => 'title' ),
        'data|title'  => array( 'type' => 'string' ),
        'data'  => array( 'hasKey' => 'plugin' ),
        'data|plugin'  => array( 'type' => 'string' ),
        'data'  => array( 'hasKey' => 'html' ),
        'data|html'  => array( 'type' => 'string' ),
      )
    ));
  }

  public function testWithWrongParameters() {
    echo "demo_db/api/ApiPluginTest".__METHOD__."\n";
    
    $this->_testWithWrongParameters('get_plugin');
  }
  
}


?>