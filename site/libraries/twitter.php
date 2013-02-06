<?

class Twitter extends Module {

  public function __construct() {
    parent::__construct();
  }

	public function index($page) {
    $this->CI->site['twitter'] = $this->CI->show('twitter',array('user'=>$this->config('user')),true);
    return $page;
	}


}

?>