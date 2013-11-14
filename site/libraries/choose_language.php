<?
 

/**
 * Eenvoudige module om van taal te switchen.
 * - Werkt alleen voor twee-talige sites
 * - Probeert naar de huidge pagina in de andere taal te gaan, anders naar de startpagina 
 *
 * @package default
 * @author Jan den Besten
 */ 

class Choose_language extends Module {

  public function __construct() {
    parent::__construct();
  }

	public function index($page) {
    // Welke nieuwe taal?
    $languages=$this->CI->site['languages'];
    $current_lang=$this->CI->site['language'];
    $key=array_search($current_lang,$languages);
    unset($languages[$key]);
    $new_language=current($languages);
    // Wat was de vorige pagina? Ga daarheen met nieuwe taal
    $new_uri=$new_language;
    if ($ref=isset($_SERVER['HTTP_REFERER'])) {
      $ref=$_SERVER['HTTP_REFERER'];
      if (has_string(site_url(),$ref)) {
        $uri=get_suffix($ref,'/');
        $this->CI->db->select('id,order,self_parent,uri');
        $this->CI->db->where('str_lang',$new_language)->where('uri',$uri);
        $this->CI->db->uri_as_full_uri();
        $new_page=$this->CI->db->get_row(get_menu_table());
        if ($new_page) $new_uri=$new_page['uri'];
      }
    }
    redirect($new_uri);
		return $page;
	}

}

?>