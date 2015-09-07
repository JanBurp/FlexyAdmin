<?php require_once("ApiTestModel.php");

class ApiAdminNavTest extends ApiTestModel {

  public function __construct() {
    parent::__construct('get_admin_nav');
  }

  public function testWithoutLogin() {
    echo "demo_db/api/ApiAdminNavTest".__METHOD__."\n";
    $this->_testWithoutAuth('get_admin_nav');
  }

  public function testWithLogin() {
    echo "demo_db/api/ApiAdminNavTest".__METHOD__."\n";
    $this->_testWithAuth(array(
      'model'   => 'get_admin_nav',
      'args'    => array('table'=>''),
      'asserts' => array(
        'data'  => array( 'type'  => 'array' ),
        'data'  => array( 'count' => 3 ),
        'data'  => array( 'hasKey' => 'header' ),
        'data|header'  => array( 'type' => 'array' ),
        'data'  => array( 'hasKey' => 'sidebar' ),
        'data|sidebar'  => array( 'type' => 'array' ),
        'data'  => array( 'hasKey' => 'footer' ),
        'data|footer'  => array( 'type' => 'array' ),
      )
    ));
  }
  
}


?>