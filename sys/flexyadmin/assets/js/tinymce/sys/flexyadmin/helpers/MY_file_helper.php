<?
/**
 * FlexyAdmin V1
 *
 * MY_file_helper.php
 *
 * @author Jan den Besten
 *
 * adds some functions to the file helper
 *
 */


function get_file_extension($f) {
	$p=strrpos($f,".");
	if ($p===FALSE) return "";
	return substr($f,$p+1);
}


?>