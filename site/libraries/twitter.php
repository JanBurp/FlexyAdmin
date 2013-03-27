<?


/**
	* Bestanden
	* ----------------
	*
	* - site/views/twitter.php - De view waarin de de twitter widget komt. Hier kun je specifieke twitter widget dingen instellen, zie https://dev.twitter.com/docs/embedded-timelines 
	* - site/assets/js/jquery.styleTwitter.js - Gebruik deze jQuery plugin om de timeline te stylen, zie bij de plugin voor meer info.
	*
	* Installatie
	* ----------------
	*
	* - Pas de view aan
	* - Style met behulp van jquery.styleTwitter.js
  *
  * @package default
  * @author Jan den Besten
  */

class Twitter extends Module {

  public function __construct() {
    parent::__construct();
  }

	public function index($page) {
    $this->CI->site['module__twitter'] = $this->CI->show('twitter',array('user'=>$this->config('user')),true);
    return $page;
	}


}

?>