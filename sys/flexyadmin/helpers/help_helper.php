<?

function help($s,$help) {
	$CI =& get_instance();
	$class=$CI->_add_help($help);
	return span("help $class").$s._span();
}

?>
