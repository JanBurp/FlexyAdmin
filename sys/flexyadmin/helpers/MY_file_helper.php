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

function clean_file_name($f) {
	$ext=get_file_extension($f);
	if (!empty($ext))
		$name=substr($f,0,strlen($f)-strlen($ext));
	else
		$name=$f;
	$name=clean_string($name);
	if (!empty($ext))
		$name="$name.$ext";
	return $name;
}

?>