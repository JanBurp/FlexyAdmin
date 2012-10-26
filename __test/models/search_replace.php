<?php
class test_search_replace extends CodeIgniterUnitTestCase {

	public function __construct()	{
		parent::__construct('search_replace');
    $this->load->model('search_replace');
	}

	public function setUp() {
  }

  public function tearDown() {
  }

	public function test_file_exists()	{
		$this->assertTrue(file_exists('/Users/jan/Sites/FlexyAdmin/FlexyAdminDEMO/sys/flexyadmin/models/search_replace.php'),'Bestaat bestand?');
	}

  public function test_media() {
    $tests=array(
      'pictures/oor.jpg'=>'pictures/test.jpg'
      // 'pictures/test.jpg'=>'pictures/oor.jpg'
    );
    trace_($tests);
    $res=$this->search_replace->media($tests);
    trace_($res);
  }


}

/* End of file test_users_model.php */
/* Location: ./tests/models/test_users_model.php */
