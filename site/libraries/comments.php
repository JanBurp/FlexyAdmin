<?

/**
	* Voegt comments toe aan pagina's
	*
	* Bestanden
	* ----------------
	*
	* - site/config/comments.php - Hier kun je diverse dingen instellen (uitleg staat erbij)
	* - db/add_comments.sql - database bestand met de benodigde tabel (eventueel ook aan te passen)
	* - site/views/comments.php - De view waarin de comments en het formulier geplaatst worden
	* - site/language/##/comments_lang.php - Taalbestanden
	*
	* Installatie
	* ----------------
	*
	* - Laad het database bestand db/add_comments.sql
	* - Pas de configuratie aan indien nodig (zie: site/config/comments.php)
	* - Pas de view (en styling) aan indien nodig
	* - Maak je eigen taalbestand en/of wijzig de bestaande
	*
	* @author Jan den Besten
	* @package FlexyAdmin_comments
	*
	*/
 class Comments extends Module {

  private $foreign_table;
	
  /**
   * @ignore
   */
   public function __construct() {
		parent::__construct();
    $this->CI->load->library('forms');
    $this->CI->forms->initialize('comments',$this->config['form']);
    $this->CI->load->model('formaction');
    $this->CI->load->model('formaction_comments');
    $this->CI->formaction_comments->initialize($this->config);
    $this->foreign_table=foreign_table_from_key( $this->config('key_id') );
	}
  

  /**
   * Zet id standaard op 'id', maar als er een samengesteld menu is (res_menu_result) verander de id dan in de id van de originele tabel
   *
   * @param string $page 
   * @return int
   * @author Jan den Besten
   */
  private function _set_id($page) {
    $id=$page['id'];
		if (isset($page['int_id']) and isset($page['str_table']) and $page['str_table']==$this->foreign_table) {
			$id=$page['int_id'];
		}
    return $id;
  }
  
  /**
  	* Geef comments terug
  	*
  	* @param string $page
  	* @return string 
  	* @author Jan den Besten
  	* @ignore
  	*/
	public function index($page) {
    $id=$this->_set_id($page);
    // Genereer formulier, en geef extra settings mee aan formaction
    $formHtml=$this->CI->forms->comments(array_merge($this->config,array('id'=>$id)) );
		// Get comments
    $comments=$this->CI->formaction_comments->get_comments($id);
		// Show all
		return $this->CI->view('comments',array('form'=>$formHtml,'items'=>$comments),true);
	}


  /**
  	* Telt aantal comments
  	*
  	* @param string $id
  	* @return string 
  	* @author Jan den Besten
  	* @ignore
  	*/
	public function count($page) {
    $id=$this->_set_id($page);
		// Count comments
    $count=$this->CI->formaction_comments->count_comments($id);
		return $this->CI->view('comments_count',array('count'=>$count,'text'=>lang('comments_count')),true);
	}


}



?>