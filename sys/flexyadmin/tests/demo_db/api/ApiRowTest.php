<?php require_once("ApiTestModel.php");

class ApiRowTest extends ApiTestModel {

  private $testData = array(
    array(
      'table' => 'tbl_links',
      'insert'  => array(
        'url_url'   => 'http://www.burp.nl',
        'str_title' => 'Insert',
      ),
      'update'  => array(
        'url_url'   => 'http://www.burp.nl2',
        'str_title' => 'Update',
      ),
    ),
    array(
      'table' => 'tbl_menu',
      'insert'  => array(
        'str_title' => 'TEST PAGINA',
        'txt_text'  => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.',
      ),
      'update'  => array(
        'str_title' => 'TEST PAGINA UPDATE',
        'txt_text'  => '<h2>Lorem ipsum dolor sit amet</h2><p>Consectetur adipiscing elit. Vivamus in augue ac justo posuere luctus sodales vel justo. Integer blandit, quam id porttitor consequat, lorem libero bibendum ipsum, non auctor sem ipsum eu mauris. <b>Vestibulum condimentum,</b> lectus sed aliquam rutrum, est velit pellentesque mauris, sed mattis sapien ante vitae enim. Quisque cursus facilisis molestie. Sed rhoncus lacus ac nunc interdum in laoreet mi rhoncus. Suspendisse ultrices fringilla felis, in porta mi pretium ut. Nunc nisl nulla, varius in lobortis a, dictum a purus. Sed consequat felis ut erat lobortis hendrerit. Donec bibendum lorem lorem. Fusce suscipit sapien id lorem mollis vel placerat nunc congue. Aenean non nunc tortor. <i>Curabitur rhoncus neque eget nulla adipiscing euismod.</i></p>',
      ),
    )
  );
  
  private $wrongData = array(
    array(
      'table' => 'tbl_menu',
      'insert'  => array(
        'str_title' => '',
      ),
      'update'  => array(
        'str_title' => '',
      ),
    ),
    
    
  );


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
        'info'  => array( 'type' => 'array' ),
        'info'  => array( 'count' => 3 ),
        'info'  => array( 'hasKey' => 'rows' ),
        'info'  => array( 'hasKey' => 'total_rows' ),
        'info'  => array( 'hasKey' => 'table_rows' ),
        'info|rows'  => array( 'Equals' => 1 ),
      )
    ));
  }


  public function testWithWrongParameters() {
    $this->_testWithWrongParameters('row');
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


  public function testCrud() {
    
    foreach ($this->testData as $test) {
      $table=$test['table'];
      
      // start situation
      $status = $this->CI->db->table_status($table);
      $auto_increment = $status['auto_increment'];
      
      // data
      $data=$test['insert'];
      $update = $test['update'];
      
      // TEST INSERT DATA
      $results = $this->_testWithAuth(array(
        'model'   => 'row',
        'args'    => array(
          'POST' => array(
            'table'     => $table,
            'data'      => $data
          ),
        ),
        'asserts' => array(
          'data'      => array( 'hasKey' => 'id' ),
          'data|id'   => array( 'type'   => 'integer' ),
          'data|id'   => array( 'assertGreaterThan' => 0 ),
          'info'      => array( 'type'  => 'array'),
          'info'      => array( 'count' => 1),
          'info'      => array( 'hasKey' => 'insert_id'),
          'info|insert_id' => array( 'assertGreaterThan' => 0 ),
        )
      ));
      // trace_($results);

      // IDS
      $ids=array();
      foreach ($results as $result) {
        $ids[]=$result['data']['id'];
      }
      
      foreach ($ids as $id) {
        
        // TEST IF DATA WAS INSERTED
        $checkData = $test['insert'];
        $checkData=array_unshift_assoc($checkData, 'id',$id);
        $this->_testWithAuth(array(
          'model'   => 'row',
          'args'    => array(
            'GET' => array(
              'table'     => $table,
              'where'     => $id
            ),
          ),
          'asserts' => array(
            'data'      => array( 'type'    => 'array' ),
            'data'      => array( 'hasKey'  => 'id' ),
            'data|id'   => array( 'equals'  => $id ),
            'data'      => array( 'equals'  => $checkData ),
          )
        ));
        
        // UPDATE DATA
        $this->_testWithAuth(array(
          'model'   => 'row',
          'args'    => array(
            'POST' => array(
              'table'     => $table,
              'where'     => $id,
              'data'      => $update
            ),
          ),
          'asserts' => array(
            'data'      => array( 'hasKey' => 'id' ),
            'data|id'   => array( 'type'   => 'integer' ),
            'data|id'   => array( 'assertGreaterThan' => 0 ),
            'info'      => array( 'type' => 'array'),
            'info'      => array( 'count' => 1),
            'info'      => array( 'hasKey' => 'affected_rows'),
          )
        ));

        // TEST UPDATE
        $checkData=$update;
        $checkData=array_unshift_assoc($checkData, 'id',$id);
        $this->_testWithAuth(array(
          'model'   => 'row',
          'args'    => array(
            'GET' => array(
              'table'     => $table,
              'where'     => $id
            ),
          ),
          'asserts' => array(
            'data'      => array( 'type'    => 'array' ),
            'data'      => array( 'hasKey'  => 'id' ),
            'data|id'   => array( 'equals'  => $id ),
            'data'      => array( 'equals'  => $checkData ),
          )
        ));

        // DELETE DATA
        $this->_testWithAuth(array(
          'model'   => 'row',
          'args'    => array(
            'POST' => array(
              'table'     => $table,
              'where'     => $id,
            ),
          ),
          'asserts' => array(
            'data'   => array( 'type'    => 'bool' ),
            'data'   => array( 'equals'  => true ),
            'info'   => array( 'type'  => 'array' ),
            'info'   => array( 'count'  => 1 ),
            'info'   => array( 'hasKey'  => 'affected_rows' ),
          )
        ));

        // TEST DELETED DATA
        $this->_testWithAuth(array(
          'model'   => 'row',
          'args'    => array(
            'GET' => array(
              'table'     => $table,
              'where'     => $id,
            ),
          ),
          'asserts' => array(
            'data'      => array( 'equals' => false ),
          )
        ));
      }
     
     
      // cleanup
      $this->CI->db->where('id >=',$auto_increment);
      $this->CI->db->delete($table);
    }
    
  }
  
  
  
  public function testWrongData() {

    foreach ($this->wrongData as $test) {
      $table=$test['table'];

      // data
      $data=$test['insert'];
      $update = $test['update'];

      // TEST INSERT DATA WRONG VALIDATION
      $results = $this->_testWithAuth(array(
        'model'   => 'row',
        'args'    => array(
          'POST' => array(
            'table'     => $table,
            'data'      => $data
          ),
        ),
        'asserts' => array(
          'data'      => array( 'hasKey' => 'id' ),
          'data|id'   => array( 'equals' => false ),
          'info'      => array( 'type'  => 'array'),
          'info'      => array( 'count' => 2),
          'info'      => array( 'hasKey' => 'validation'),
          'info|validation'      => array( 'equals' => false),
          'info'      => array( 'hasKey' => 'validation_errors'),
          'info|validation_errors'  => array( 'type' => 'array'),
          'info|validation_errors'  => array( 'countGreaterOrEqual' => 1),
        )
      ));
      

    }


  }

  
  

  
}


?>
