<?

/**
	* Laat een Google Map zien
	*
	* Bestanden
	* ----------------
	*
	* - site/config/google_map.php - Hier kun je een een aantal dingen instellen
	* - db/add_google_map.sql - database bestand, altijd nodig!
	* - site/views/google_map.php - De view waarin de map komt
	* - site/views/google_map_popup.php - view waarin de popup gemaakt wordt
	*
	* Installatie
	* ----------------
	*
	* - Laad het database bestand db/add_google_map.sql
	* - Pas de configuratie aan
	* - Pas de view (en styling) aan indien nodig
	*
	* @author Jan den Besten
	* @package FlexyAdmin_comments
	*
	*/
 
 
class Google_map extends Module {

   /**
    * @ignore
    */
	public function __construct() {
		parent::__construct();
		$this->CI->load->library('GMap');
		$this->CI->gmap->GoogleMapAPI();
	}

  /**
  	* Laat een google map zien van normale omvang
  	*
  	* @param string $page 
  	* @return string
  	* @author Jan den Besten
  	*/
	public function index($page) {
    return $this->_showmap($page);
	}
  
  /**
   * Laat een kleine google map zien (in de config kun je de omvang instellen)
   *
   * @param string $page 
   * @return string
   * @author Jan den Besten
   */
  public function small($page) {
    $this->CI->gmap->disableTypeControls();
    $this->CI->gmap->disableScaleControl();
    $this->CI->site['map']=$this->_showmap($page,'small');
  }
  
  /**
   * Laat map zien, kies de omvang
   *
   * @param string $page 
   * @param string $size 'normal' of 'small' (je kunt meerdere aanmaken in de config)
   * @return string
   * @author Jan den Besten
   * @ignore
   */
  private function _showmap($page,$size='normal') {
    $config=$this->config($size);
	
		$this->CI->gmap->setMapType( $config['type'] );
		$this->CI->gmap->setHeight( $config['height'] );
		$this->CI->gmap->setWidth( $config['width'] );
		$this->CI->gmap->setZoomLevel( $config['zoomlevel'] );
		
		if ($this->config('multiple')) {
			$this->CI->gmap->disableZoomEncompass();
			$addresses=$this->CI->db->get_results($this->config('table'));
			foreach ($addresses as $item) {
				$address=$item['str_address'];
				$title=$item['str_title'];
				$html=$this->CI->view('google_map_popup',$item,true);
				$this->CI->gmap->addMarkerByAddress($address,$title,$html);
			}
		}
		else {
			$address=$this->CI->site['str_address'];
			$title=$this->CI->site['str_title'];
			$html=$this->CI->view('google_map_popup',$this->CI->site,true);
			$this->CI->gmap->addMarkerByAddress($address,$title,$html);
		}
		
		$data['headerjs'] 	= $this->CI->gmap->getHeaderJS();
		$data['headermap'] 	= $this->CI->gmap->getMapJS();
		$data['onload'] 		= $this->CI->gmap->printOnLoad();
		$data['map'] 				= $this->CI->gmap->printMap();
		$data['sidebar'] 		= $this->CI->gmap->printSidebar();
		
		return $this->CI->view('google_map', $data,true);
  }


}

?>