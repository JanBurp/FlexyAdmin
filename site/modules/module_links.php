<?

function _module_links($item) {
	// Always use $CI instead of $this to call CI methods.
	$CI=&get_instance();
	
	$links=$CI->db->get_results('tbl_links');
	$show=$CI->show('links',array('links'=>$links),true);
	$CI->add_content($show);
}

?>