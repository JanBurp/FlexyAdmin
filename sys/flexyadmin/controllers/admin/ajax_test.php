<?php require_once(APPPATH."core/AjaxController.php");


class Ajax_test extends AjaxController {

	public function __construct() {
		parent::__construct();
    $this->lang->load('ajax');
	}

  public function index() {
    return $this->_result(array());
  }

}

?>
