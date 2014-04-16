<?php /**
 * Only for internal use
 *
 * @author Jan den Besten
 * @package default
 * @ignore
 **/

 /**
  * help
  *
  * @param string $s 
  * @param string $help 
  * @return void
  * @author Jan den Besten
  * @ignore
  * @internal
  */
function help($s,$help) {
	$CI =& get_instance();
	$class='';
	if (method_exists($CI,'_add_help')) $class=$CI->_add_help($help);
	return span("help $class").$s._span();
}

?>
