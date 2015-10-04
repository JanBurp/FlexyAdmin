<?php require_once("ApiTestModel.php");

class ApiTableTest extends ApiTestModel {

  public function __construct() {
    parent::__construct('table');
  }

  public function testWithoutLogin() {
    
    $this->_testWithoutAuth('table');
  }
  
  public function testWithWrongParameters() {
    
    $this->_testWithWrongParameters('table');
  }
  

  public function testWithLogin() {
    
    // Test all tables
    $this->_testWithAuth(array(
      'model'   => 'table',
      'args'    => array(),
      'asserts' => array(
        'data'  => array( 'type' => 'array' ),
        'data'  => array( 'countGreaterOrEqual' => 1 ),
        'info'  => array( 'type' => 'array' ),
      )
    ));

    // Test tbl_site
    $this->_testWithAuth(array(
      'model'   => 'table',
      'args'    => array(
        'table' => 'tbl_site'
      ),
      'asserts' => array(
        'data'  => array( 'type'  => 'array' ),
        'data'  => array( 'count' => 1 ),
        'data'  => array( 'hasKey' => 1 ),
        'data|1'  => array( 'type' => 'array' ),
        'data|1'  => array( 'countGreaterOrEqual' => 8 ),
        'data|1'  => array( 'hasKey' => 'id' ),
        'data|1|id' => array( 'Equals' => 1 ),
        'data|1'  => array( 'hasKey' => 'str_title' ),
        'data|1|str_title'  => array( 'type' => 'string' ),
        'data|1'  => array( 'hasKey' => 'str_author' ),
        'data|1|str_author'  => array( 'type' => 'string' ),
        'data|1'  => array( 'hasKey' => 'url_url' ),
        'data|1|url_url'  => array( 'type' => 'string' ),
        'data|1'  => array( 'hasKey' => 'email_email' ),
        'data|1|email_email'  => array( 'type' => 'string' ),
        'data|1'  => array( 'hasKey' => 'stx_description' ),
        'data|1'  => array( 'hasKey' => 'stx_keywords' ),
        'data|1'  => array( 'hasKey' => 'str_google_analytics' ),
        'info'    => array( 'type' => 'array' ),
        'info'    => array( 'count' => 3 ),
        'info'    => array( 'hasKey' => 'num_rows' ),
        'info'    => array( 'hasKey' => 'total_rows' ),
        'info|num_rows' => array( 'Equals' => 1 ),
        'info|total_rows' => array( 'Equals' => 1 ),
      )
    ));
    
    
  }
  
}


?>