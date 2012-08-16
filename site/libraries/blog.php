<?

/**
 	* Eenvoudige blog module, kun je ook gebruiken voor niews of andere actualiteiten
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
 	* - Laad het database bestand db/add_simple_blog.sql
 	* - Pas de configuratie aan indien nodig (zie: site/config/blog.php)
 	* - Pas de view (en styling) aan indien nodig
 	*
 	* @author Jan den Besten
 	* @package FlexyAdmin_blog
 	*/

class Blog extends Module {

	public function __construct() {
		parent::__construct();
		if ($this->config('comments')) {
			$this->CI->load->library('comments');
			$this->CI->comments->set_config( $this->config('comments') );
		}
	}

	/**
		* Zorgt ervoor dat de blog wordt getoond.
		*
		* @param string $page 
		* @return void
		* @author Jan den Besten
		*/
	public function index($page) {
		if ( $this->CI->db->table_exists($this->config('table'))) {
			$blogItems=$this->CI->db->get_result( $this->config('table') );
			foreach ($blogItems as $id => $blogItem) {
				// make nice date format
				$blogItems[$id]['niceDate']=strftime('%a %e %b %Y',mysql_to_unix($blogItem[$this->config('field_date')]));
				if ($this->config('comments')) $blogItems[$id]['comments'] = $this->CI->comments->module($blogItem);
			}
			return $this->CI->view('blog',array('items'=>$blogItems),true);
		}
		return FALSE;
	}

}

?>