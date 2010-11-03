<?

function _module_example_file($item) {
	// Always use $CI instead of $this to call CI methods.
	$CI=&get_instance();
	
	$CI->add_content('<h2>FILE MODULE EXAMPLE</h2>');
}

?>