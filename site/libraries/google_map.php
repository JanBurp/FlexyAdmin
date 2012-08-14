<?

/**
 * Laat een Google Map zien
 *
 * <h2>Bestanden</h2>
 * - site/config/google_map.php - Hier kun je een een aantal dingen instellen
 * - db/add_google_map.sql - database bestand, altijd nodig!
 * - site/views/google_map.php - De view waarin de map komt
 * - site/views/google_map_popup.php - view waarin de popup gemaakt wordt
 *
 * <h2>Installatie</h2>
 * - Laad het database bestand db/add_google_map.sql
 * - Pas de configuratie aan
 * - Pas de view (en styling) aan indien nodig
 *
 * @author Jan den Besten
 * @package FlexyAdmin_comments
 *
 */
 
 class Google_map extends Module {


	public function __construct() {
		parent::__construct();
		$this->CI->load->library('GMap');
	}

  /**
   * Module
   *
   * @param string $page 
   * @return void
   * @author Jan den Besten
   */
	public function index($page) {
		$this->CI->gmap->GoogleMapAPI();
	
		$this->CI->gmap->setMapType( $this->config('type') );
		$this->CI->gmap->setHeight( $this->config('height') );
		$this->CI->gmap->setWidth( $this->config('width') );
		$this->CI->gmap->setZoomLevel( $this->config('zoomlevel') );
		
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