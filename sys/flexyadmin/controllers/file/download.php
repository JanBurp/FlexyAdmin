<? require_once(APPPATH."core/FrontendController.php");

class Download extends FrontEndController {
	
	function __construct()	{
		parent::__construct();
		$this->load->helper('download');
	}

	function this($path='',$file='')	{
		if (!empty($path) and !empty($file)) {
			$path=SITEPATH.'assets/'.$path.'/'.$file;
			if (file_exists($path)) {
				$data=file_get_contents($path);
				$name=$file;
				force_download($name, $data);
			}
		}
	}
	
}


?>