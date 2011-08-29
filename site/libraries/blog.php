<?

/*

Blog

*/

class Blog extends Module {

	function __construct() {
		parent::__construct();
		$this->CI->load->library('comments');
	}

	function module($item) {
		$one=(!empty($item['txt_text']));
		// get categorie and select blog for it
		$catUri=$this->CI->uri->get(1);
		if ($one)
			$this->CI->db->where('tbl_blog.id',$item['int_id']);
		elseif ($catUri!='home')
			$this->CI->db->where('tbl_blog_categories.uri',$catUri);
		$this->CI->db->where('dat_date <= NOW()');
		$this->CI->db->add_foreigns();
		$blogItems=$this->CI->db->get_result('tbl_blog');
		foreach ($blogItems as $id => $blogItem) {
			$blogItems[$id]['comments'] = $this->CI->comments->module($blogItem);
		}
		return $this->CI->show('blog',array('items'=>$blogItems),true);
	}

}



?>