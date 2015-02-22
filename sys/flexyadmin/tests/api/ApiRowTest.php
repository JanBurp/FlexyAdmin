<?php require_once("ApiTestModel.php");

class ApiRowTest extends ApiTestModel {

  public function __construct() {
    parent::__construct('row');
  }

  public function testWithoutLogin() {
    $this->_testWithoutAuth('row');
  }


  public function testWithLogin() {
    $this->_testWithAuth(array(
      'model'   => 'row',
      'args'    => array(
        'where' =>'first'
      ),
      'asserts' => array(
        'data'  => array( 'hasKey'                => 'id' ),
        'data'  => array( 'countGreaterOrEqual'   => 2 ),
      )
    ));
  }


  public function testConfig() {
    
    // Test config of tbl_menu
    $this->_testWithAuth(array(
      'model'   => 'row',
      'args'    => array(
        'table' => 'tbl_menu',
        'where' =>'first',
        'config'=>array('table_info','field_info'),
      ),
      'asserts' => array(
        'config' => array( 'type'   => 'array' ),
        'config' => array( 'hasKey' => 'table_info' ),
        'config' => array( 'hasKey' => 'field_info' ),
        'config|field_info' => array( 'type' => 'array' ),
        'config|table_info' => array( 'type' => 'array' ),

        'config|table_info'          => array( 'hasKey' => 'tree' ),
        'config|table_info|tree'     => array( 'Equals' => true ),
        'config|table_info'          => array( 'hasKey' => 'sortable' ),
        'config|table_info|sortable' => array( 'Equals' => false ),
        'config|table_info'          => array( 'hasKey' => 'fields' ),
        'config|table_info|fields'   => array( 'type' => 'array' ),
        'config|table_info'          => array( 'hasKey' => 'ui_name' ),
        'config|table_info|ui_name'  => array( 'type' => 'string' ),
        
      )
    ));
    
  }


  
}


?>
