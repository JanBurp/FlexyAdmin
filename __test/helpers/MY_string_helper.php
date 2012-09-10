<?php
class test_MY_string_helper extends CodeIgniterUnitTestCase {

	public function __construct()	{
		parent::__construct('MY_string_helper');
    $this->rand = rand(500,15000);
	}

	public function setUp() {
  }

  public function tearDown() {
  }

	public function test_file_exists()	{
		$this->assertTrue(file_exists('/Users/jan/Sites/FlexyAdmin/FlexyAdminDEMO/sys/flexyadmin/helpers/MY_string_helper.php'),'Bestaat bestand?');
	}

  public function test_get_prefix() {
    $tests=array(
      array('tbl_menu','_','tbl'),
      array('id_menu','_','id'),
      array('id','_','id'),
      array('rel_menu__links','_','rel'),
      array('path/file','/','path')
    );
    foreach ($tests as $key => $test) {
      $this->assertEqual( get_prefix($test[0],$test[1]), $test[2], "get_prefix('$test[0]','$test[1]') == '$test[2]'");
    }
  }


}

/* End of file test_users_model.php */
/* Location: ./tests/models/test_users_model.php */
