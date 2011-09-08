<?

class Links extends Module {

	public function index($item) {
		if ( $this->CI->db->table_exists('tbl_links')) {
			$links=$this->CI->db->get_results('tbl_links');
			return $this->CI->view('links',array('links'=>$links),true);
		}
		return FALSE;
	}

}

?>