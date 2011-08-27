<?

class Links extends Module {

	function module($item) {
		$links=$this->CI->db->get_results('tbl_links');
		return $this->CI->show('links',array('links'=>$links),true);
	}

}

?>