<?

function help($s,$help) {
	$CI =& get_instance();
	$class='';
	if (isset($CI->_add_help)) $class=$CI->_add_help($help);
	return span("help $class").$s._span();
}

?>
