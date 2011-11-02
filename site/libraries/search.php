<?

class Search extends Module {

	public function __construct() {
		parent::__construct();
		$this->CI->lang->load('search');
	}

	public function index($item) {
		$search=$this->CI->input->post( lang('search_term') );
		
		if ($search) {
			$fields=array();
			$fields[]=$this->config['title_field'];
			$fields[]=$this->config['text_field'];
			$fields=array_merge($fields,$this->config['extra_fields']);

			$this->CI->db->search( $this->_create_search_array_for_db($search, $fields ) );
			$this->CI->db->uri_as_full_uri(TRUE, $this->config['title_field'] );
			$results=$this->CI->db->get_results( $this->config['table'] );

			if ($results) {
				foreach ($results as $id => $result) {
					// add pre uri
					if (!empty($this->config['pre_uri'])) {
						$results['id']['uri']=$this->config['pre_uri'].'/'.$result['uri'];
					}
					// smaller results messages
					if ($this->config['result_max_length']==0) {
						$results[$id]['txt_text']='';
					}
					elseif (!empty($this->config['result_max_type']) and !empty($result[$this->config['text_field']])) {
						$results[$id]['txt_text']=add_before_last_tag(intro_string($result[$this->config['text_field']], $this->config['result_max_length'], $this->config['result_max_type'],''), $this->config['result_max_ellipses']);
					}
				}

				if ($this->config['order_as_tree']) $results=$this->_order_as_menu($results);
			}

			return $this->CI->show('search_results',array('search'=>$search,'items'=>$results),true);
		}
	}
	
	
	public function form() {
		// set form action uri
		$action=$this->config['result_page_uri'];
		if (empty($action) and !empty($this->config['result_page_where'])) {
			$this->CI->db->uri_as_full_uri();
			$this->CI->db->where($this->config['result_page_where'][0],$this->config['result_page_where'][1]);
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
		if (empty($search)) $search=lang('empty_value');
		$this->CI->site['search_form']=$this->CI->show('search_form',array('action'=>$action, 'value'=>$search),true);
	}
	

	
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
	
	private function _order_as_menu($result) {
		// get full table with tree order, match with search_result
		// $this->CI->db->select('id,order,self_parent');
		$this->CI->db->order_as_tree();
		$tree=$this->CI->db->get_result( $this->config['table']);
		
		foreach ($tree as $id => $row) {
			if (isset($result[$id]))
				$tree[$id]=$result[$id];
			else
				unset($tree[$id]);
		}

		return $tree;
	}

}

?>