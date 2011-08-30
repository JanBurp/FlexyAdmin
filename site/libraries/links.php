<?

class Links extends Module {

	function module($item) {
		$links=$this->CI->db->get_results('tbl_links');
		return $this->CI->view('links',array('links'=>$links),true);
	}

}

?>