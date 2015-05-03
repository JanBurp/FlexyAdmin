<?php require_once(APPPATH."core/FrontendController.php");

class Main extends FrontEndController {
	
	public function __construct()	{
		parent::__construct();
    $this->load->model('cronjob');
	}

	public function index()	{
    $this->cronjob->go();
  }
  

}




?>