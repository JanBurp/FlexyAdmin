<?

class Fallback extends Module {


	// index is the standard method
	
	public function index($page) {
		$content='<h1>Fallback Module: '.$this->name.'</h1>';
		return $content;
	}

}

?>