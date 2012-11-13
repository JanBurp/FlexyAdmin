<?

/**
	* Zoeken
	*
	* Bestanden
	* ----------------
	*
	* - site/config/search.php - Hier kun je een een aantal dingen instellen
	* - site/views/search_*.php - De views
	* - site/language/##/search_lang.php - Taalbestanden
	*
	* Installatie
	* ----------------
	*
	* - Pas de configuratie aan indien nodig (zie: site/config/search.php)
	* - Pas de view (en styling) aan indien nodig
	* - Maak je eigen taalbestand en/of wijzig de bestaande
	*
	* Meest gebruikte manier
	* ----------------
	*
	* - Laad op elke pagine de module search.form: <span class="code">$config['autoload_modules'] = array('search.form');</span>
	* - Maak een plekje in de hoofdview waar je het zoekformulier toont: &lt;?=$search_form?&gt;
	* - Maak een pagina aan waar het zoekresultaat getoond moet worden en koppel aan deze pagina de module search.
	*
	* @author Jan den Besten
	* @package FlexyAdmin_comments
	*
	*/


class Search extends Module {

  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
	}

  /**
  	* Hier wordt de module standaard aangeroepen. Genereerd een zoekpagina met meegegeven zoekterm (POST)
  	*
  	* @param string $page
  	* @return string 
  	* @author Jan den Besten
  	* @ignore
  	*/
	public function index($page) {
		$search=$this->CI->input->post( lang('search_term') );
		
		if ($search) {
			$fields=array();
			$fields[]=$this->config('title_field');
			$fields[]=$this->config('text_field');
			$fields=array_merge($fields,$this->config('extra_fields'));

			$this->CI->db->search( $this->_create_search_array_for_db($search, $fields ) );
			if ($this->config('show_full_title'))
				$this->CI->db->uri_as_full_uri(TRUE, $this->config('title_field') );
			elseif ($this->config('order_as_tree'))
				$this->CI->db->uri_as_full_uri();
      elseif ($this->config('order_by'))
        $this->CI->db->order_by($this->config('order_by'));
			$results=$this->CI->db->get_results( $this->config('table') );
			
			// remove double (uri)
			$results=$this->CI->db->_set_key_to($results,'uri');
			

			if ($results) {
				foreach ($results as $id => $result) {
					// add pre uri
					if ($this->config('pre_uri')) {
						$results[$id]['uri']=$this->config('pre_uri').'/'.$result['uri'];
					}
					// smaller results messages
					if ($this->config('result_max_length')==0) {
						$results[$id]['txt_text']='';
					}
					elseif ($this->config('result_max_type') and $result[$this->config('text_field')]) {
						$results[$id]['txt_text']=add_before_last_tag(intro_string($result[$this->config('text_field')], $this->config('result_max_length'), $this->config('result_max_type'),''), $this->config('result_max_ellipses'));
					}
				}

				if ($this->config('order_as_tree')) $results=$this->_order_as_menu($results);
				if ($this->config('group_result_by_uris')) $results=$this->_group_by_uri($results,$this->config('group_result_by_uris'));
			}

			return $this->CI->show('search_results',array('search'=>$search,'items'=>$results,'config'=>$this->config),true);
		}
	}
	
	/**
		* Voegt zoekformulier toe aan $site in $site['search_form']
		*
		* @return void
		* @author Jan den Besten
		*/
	public function form() {
		// set form action uri
		$action=$this->config('result_page_uri');
		if (empty($action) and $this->config('result_page_where')) {
			$this->CI->db->uri_as_full_uri();
			$where=$this->config['result_page_where'];
			$this->CI->db->where($where[0],$where[1]);
			$page=$this->CI->db->get_result( get_menu_table() );
			if ($page) {
				$page=reset($page);
				$action=$page['uri'];
			}
		}
		if (empty($action)) {
			$action=$this->CI->site['uri'];
		}
		
		// set search term
		$search=$this->CI->input->post( lang('search_term') );
		if (empty($search)) $search=lang('search_empty_value');
		$this->CI->site['search_form']=$this->CI->show('search_form',array('action'=>$action, 'value'=>$search),true);
	}
	

	/**
		* Maakt een zoekarray aan
		*
		* @param mixed $term 
		* @param mixed $fields 
		* @return array
		* @author Jan den Besten
  	* @ignore
		*/
	private function _create_search_array_for_db($term,$fields) {
		if (!is_array($term)) $term=array($term);
		if (!is_array($fields)) $fields=array($fields);
		
		$search=array();
		foreach ($term as $t) {
			foreach ($fields as $field) {
				$search[]=array('search'=>$t,'field'=>$field,'or'=>'or');
			}
		}
		return $search;
	}
	
  /**
  	* Sorteert zoekresulaat als een menu
  	*
  	* @param array $result 
  	* @return array
  	* @author Jan den Besten
  	* @ignore
  	*/
	private function _order_as_menu($result) {
		// get full table with tree order, match with search_result
		$this->CI->db->select('id,order,self_parent');
		$this->CI->db->order_as_tree();
		$tree=$this->CI->db->get_result( $this->config('table'));
		
		foreach ($tree as $id => $row) {
			if (isset($result[$id]))
				$tree[$id]=$result[$id];
			else
				unset($tree[$id]);
		}

		return $tree;
	}

  /**
  	* Groupeerd zoekresulaat
  	*
  	* @param array $result 
  	* @param array $uris 
  	* @return array
  	* @author Jan den Besten
  	* @ignore
  	*/
	private function _group_by_uri($result,$uris) {
		foreach ($uris as $key => $uri) {
			$uris[$key]=array('uri'=>$uri);
			$uris[$key]['title']=$this->CI->db->get_field_where(get_menu_table(),$this->config('title_field'),'uri',$uri,TRUE);
			$uris[$key]['result']=find_row_by_value($result,$uri,'uri',true);
		}
		return $uris;
	}


}

?>