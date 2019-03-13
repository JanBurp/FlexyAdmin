<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup core
 * Uitbreiding op [URI class van CodeIgniter](http://codeigniter.com/user_guide/libraries/uri.html)
 *
 * @author Jan den Besten
 */
 
class MY_URI extends CI_URI {

	/**
	 * home pagina
	 */
  private $home;
  
  /**
   * uri part van home
   */
	private $homePart;

  /**
   * XDEBUG tekst in uri_query (wordt eruit gefilterd)
   */
	private $xdebug='XDEBUG_SESSION_START';

  /**
   * array van uri delen waarna de uri niet meer bekeken wordt (verwijderd)
   */
	private $remove=array();

  /**
   */
	public function __construct() {
		parent::__construct();
		$this->set_home();
		$this->set_remove();
	}
  
  /**
   * Set uri for testing purposes
   *
   * @param string $str 
   * @return this
   * @author Jan den Besten
   */
  public function set_uri($str) {
    if (defined('PHPUNIT_TEST')) {
      $this->segments=array();
      $this->_set_uri_string($str);
    }
    return $this;
  }

  /**
   * Stel uri van homepage in
   *
   * @param string $home[''] 
   * @param string $p default=1 Part van de uri
   * @return this
   * @author Jan den Besten
   */
	public function set_home($home="",$p=1) {
		$this->home=$home;
		$this->homePart=$p;
    return $this;
	}

  /**
   * Geeft uri van homepage
   *
   * @return string
   * @author Jan den Besten
   */
  public function get_home() {
    return $this->home;
  }
  

  /**
   * Stel hiermee uriparts in waarachter de uri niet wordt 'gezien' voor het laden van een pagina.
   * 
   * Wordt bijvoorbeeld gebruikt door auto-pagination met de standaard remove part 'offset'
   *
   * @param mixed $remove
   * @return void
   * @author Jan den Besten
   */
	public function set_remove($remove="") {
		if (!is_array($remove)) $remove=array($remove);
		$this->remove=$remove;
    return $this;
  }


  /**
   * Voeg een uripart toe waarachter de uri niet wordt 'gezien'
   *
   * @param string $remove 
   * @return void
   * @author Jan den Besten
   */
  public function add_remove($remove='') {
    $this->remove[]=$remove;
    return $this;
  }
  
  /**
   * Geeft alle remove uri-parts
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_remove() {
    return $this->remove;
  }
	
  /**
   * Zet de standaard remove part van pagination in
   *
   * @return string pagination remove part, default: '_offset'
   * @author Jan den Besten
   */
	public function remove_pagination() {
		$CI=&get_instance();
		if ( ! isset($CI->pagination->auto_uripart)) return NULL;
		$parameter=$CI->pagination->auto_uripart;
		$this->add_remove($parameter);
		return $parameter;
	}

  /**
   * Checkt of segment geen XDEBUG segment is
   *
   * @param string $s 
   * @return string
   * @author Jan den Besten
   * @internal
   */
	protected function _segment($s) {
		$s=$this->segment($s);
		if ($s==$this->xdebug) 
			return "";
		return $s;
	}

  /**
   * Geeft uri string zonder alle remove uri parts en alles wat daarachter komt
   *
   * @return void
   * @author Jan den Besten
   */
	protected function _uri_string() {
		$s=$this->uri_string();
		if ($s==$this->xdebug) $s="";
		if ($s=="") $s=$this->home;
		if (!empty($this->remove)) {
			foreach ($this->remove as $remove) {
				if (!empty($remove)) {
					$pos=strpos($s,$remove);
          if (substr($s,$pos-1,1)=='/') $pos-=1;
					if ($pos>0) $s=substr($s,0,$pos);
				}
			}
		}
		return $s;
	}

  /**
   * Test een uripart
   *
   * @param string $is Waarop het uripart wordt getest 
   * @param int $s default=1 Uripart (1 of hoger) 
   * @return bool TRUE als uripart zelfde is als $is
   * @author Jan den Besten
   */
	public function is($is,$s=1) {
		if ($this->_segment($s))
			return ($this->_segment($s)==$is);
		else
			return FALSE;
	}

  /**
   * Test of uri de homepage is
   *
   * @return bool
   * @author Jan den Besten
   */
	public function is_home() {
		$isHome=$this->is($this->home,$this->homePart);
		if (!$isHome) $isHome=($this->total_segments()==0);
		return $isHome;
	}

  /**
   * Test of URI in admin deel (CMS)
   *
   * @return bool
   * @author Jan den Besten
   */
  public function is_admin() {
    return $this->is('admin',1);
  }


  /**
   * Test of uri langer dan $n part(s) is
   *
   * @param string $n default=1
   * @return bool
   * @author Jan den Besten
   */
	public function has_more($n=1) {
		return $this->total_segments()>$n;
	}

  /**
   * Geeft uri part (waarbij de remove parts verwijderd zijn)
   *
   * @param int $s default=0 Part
   * @return string
   * @author Jan den Besten
   */
	public function get($s=0) {
		if ($s==0) {
			$u=$this->_uri_string();
		}
		else {
			$u=$this->_segment($s);
			if (empty($u) and $s==$this->homePart) $u=$this->home;
		}
		if (isset($u[0]) and $u[0]=="/") $u=substr($u,1);
		if (in_array($u,$this->remove)) $u='';
		return $u;
	}

  /**
   * Geeft alle uri-parts tot en met gegeven part (waarbij de remove parts verwijderd zijn)
   *
   * @param int $s default=0 tot part
   * @return string
   * @author Jan den Besten
   */
	public function get_to($s=0) {
		$u=explode('/',$this->_uri_string());
		$u=array_slice($u,0,$s+1);
		$u=implode('/',$u);
		$u=ltrim($u,'/');
		return $u;
	}

  /**
   * Geef all uri-parts vanaf gegeven part (waarbij de remove parts verwijderd zijn)
   *
   * @param string $parameter gegeven uripart (geen nr, maar daadwerkijke uri, bijvoorbeeld 'offset')
   * @param string $include default=FALSE moet gegeven uripart zelf meegenomen worden?
   * @return mixed FALSE als niet gevonden, anders een array van parts
   * @author Jan den Besten
   */
  public function get_from_part($parameter,$include=false) {
		$uri=$this->segment_array();
    $segment=array_search($parameter,$uri);
    if (!$segment) {
      return false;
    }
    if ($include) $segment--;
    $u=array_slice($uri,$segment);
		return $u;
  }

	/**
	 * Geeft laatste uripart
	 *
	 * @return string
	 * @author Jan den Besten
	 */
	public function get_last() {
		$u=explode('/',$this->_uri_string());
		return array_pop($u);
	}

  /**
   * Geeft uri parameter (uri part direct na gegeven part)
   * 
   *      // Stel uri= 'home/blogs/offset/10
   *      $offset = $this->uri->get_parameter('offset'); // Geeft 10
   *
   * @param string $parameter 
   * @param string $default default=FALSE eventueel mee te geven default waarde als er geen parameter (of waarde) gevonden is
   * @return string
   * @author Jan den Besten
   */
	public function get_parameter($parameter,$default=NULL) {
		$uri=$this->segment_array();
		$segment=array_search($parameter,$uri);
		if ( !$segment ) return false;
		return $this->segment($segment+1,$default);
	}
	
  /**
   * Geeft pagination offset. Zelfs als get_parameter() met pagination part meegegeven (standaard 'offset')
   *
   * @return string
   * @author Jan den Besten
   */
	public function get_pagination() {
		$CI=&get_instance();
		if ( ! isset($CI->pagination)) return NULL;
		$parameter=$CI->pagination->auto_uripart;
		return (int) $this->get_parameter($parameter,0);
	}
  

}

?>
