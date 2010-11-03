<? require_once(APPPATH."controllers/admin/MY_Controller.php");

class Download extends FrontEndController {
	
	function Download()	{
		parent::FrontEndController();
		$this->load->helper('download');
	}

	function this($path='',$file='')	{
		if (!empty($path) and !empty($file)) {
			$path='site/assets/'.$path.'/'.$file;
			if (file_exists($path)) {
				$data=file_get_contents($path);
				$name=$file;
				force_download($name, $data);
			}
		}
	}
	
}


?>