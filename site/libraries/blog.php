<?

class Blog extends Module {

	function __construct() {
		parent::__construct();
		$this->load_config('blog');
		if ($this->config['comments']) {
			$this->CI->load->library('comments');
			$this->CI->comments->set_config( $this->config['comments'] );
		}
	}

	function module($item) {
		$one=(!empty($item['txt_text']));
		$blogItems=$this->CI->db->get_result( $this->config['table'] );
		foreach ($blogItems as $id => $blogItem) {
			// make nice date format
			$blogItems[$id]['niceDate']=strftime('%a %e %b %Y',mysql_to_unix($blogItem[$this->config['field_date']]));
			if ($this->config['comments']) $blogItems[$id]['comments'] = $this->CI->comments->module($blogItem);
		}
		return $this->CI->view('blog',array('items'=>$blogItems),true);
	}

}



?>