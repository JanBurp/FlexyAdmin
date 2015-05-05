<?php

/**
 * Only for internal use
 *
 * $Author$
 * $Date$
 * $Revision$
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
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
