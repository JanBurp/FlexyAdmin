<?

/**
 	* Eenvoudige blog module, kun je ook gebruiken voor niews of andere actualiteiten
 	* NB Maakt gebruik van een samengesteld menu
 	*
 	* Bestanden
	* ----------------
	*
 	* - site/config/blog.php - Hier kun je een een aantal dingen instellen
 	* - db/add_simple_blog.sql - database bestand met de benodigde tabel
 	* - site/views/blog.php - De view waarin de blog geplaatst worden
 	*
 	* Installatie
	* ----------------
	*
	* - Als er nog geen samengesteld menu is geladen, laad dan het database bestand db/add_auto_menu.sql
	* - Voeg aan 'cfg_auto_menu' een regel toe met de volgende gegevens: Type => from submenu table | Table => tbl_blog | Parent Where => str_module = "blog"
	* - Maak een menu item aan met de module 'blog'
 	* - Laad het database bestand db/add_simple_blog.sql
 	* - Pas de configuratie aan indien nodig (zie: site/config/blog.php)
 	* - Pas de view (en styling) aan indien nodig
 	*
 	* @author Jan den Besten
 	* @package FlexyAdmin_blog
 	*/

class Blog extends Module {

  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
		if ($this->config('comments')) {
			$this->CI->load->library('comments');
			$this->CI->comments->set_config( $this->config('comments') );
		}
    if ($this->config('pagination')) {
      $this->CI->load->library('pagination');
    }
	}

	/**
		* Toont blog items
		*
		* @param string $page 
		* @return void
		* @author Jan den Besten
    * @ignore
		*/
	public function index($page) {
    $pagination_links='';
    if ($this->config('pagination')) {
      $offset     = $this->CI->uri->get_pagination();
      $per_page   = $this->config('pagination');
    }
    
    $items=$this->_get_items($per_page,$offset);

    // Pagination settings
    if ($per_page) {
      $config['total_rows'] = $this->CI->db->last_num_rows_no_limit();
      $config['per_page'] = $per_page;
      $this->CI->pagination->initialize($config);
      $this->CI->pagination->auto();
      $pagination_links = $this->CI->pagination->create_links();
    }
    
		return $this->CI->view('blog',array('items'=>$items,'read_more'=>$this->config('read_more'),'pagination'=>$pagination_links),true);
	}
  
  /**
   * Laat alleen de laatste paar zien
   *
   * @param string $page 
   * @return string
   * @author Jan den Besten
   */
  public function latest($page) {
    $items=$this->_get_items($this->config('latest_items'));
    return $this->CI->view('blog_latest',array('items'=>$items,'read_more'=>$this->config('read_more')),true);
  }
  
  
  /**
   * Haalt de items op en bewerkt de data nog even
   *
   * @param string $limit 
   * @param string $offset 
   * @return array
   * @author Jan den Besten
   */
  private function _get_items($limit=0,$offset=0) {
    // Haal data op
    $this->CI->db->uri_as_full_uri();
    $this->CI->db->where('str_table',$this->config('table'));
		$items=$this->CI->db->get_result( get_menu_table(), $limit, $offset );
    
    // Bewerk data
		foreach ($items as $id => $item) {
			// make nice date format
			$items[$id]['niceDate']=strftime($this->config('date_format'),mysql_to_unix($item[$this->config('field_date')]));
      // intro?
      if ($this->config('intro_length') > 0) {
        $intro=$item[$this->config('field_text')];
        $intro=intro_string($intro,$this->config('intro_length'),'LINES','');
        $items[$id]['read_more_url']=$item['uri'];
        $items[$id][$this->config('field_text')]=$intro;
      }
			if ($this->config('comments')) $items[$id]['comments'] = $this->CI->comments->module($item);
		}
    return $items;
  }
  
  

}

?>