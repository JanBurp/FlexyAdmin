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

function get_file_without_extension($f) {
	$p=strrpos($f,".");
	if ($p===FALSE) return $f;
	return substr($f,0,$p);
}
function add_file_prefix($f,$pre) {
	$p=strrpos($f,"/");
	$path=substr($f,0,$p+1);
	$file=substr($f,$p+1);
	return $path.$pre.$file;
}
function add_file_postfix($f,$post) {
	$p=strrpos($f,"/");
	$path=substr($f,0,$p+1);
	$file=substr($f,$p+1);
	$ext=get_file_extension($file);
	$file=get_file_without_extension($file);
	return $path.$file.$post.'.'.$ext;
}

function clean_file_name($f) {
	$ext=get_file_extension($f);
	if (!empty($ext))
		$name=substr($f,0,strlen($f)-strlen($ext));
	else
		$name=$f;
	// replace spaces with underscores
	$name=str_replace(' ','_',$name);
	$name=clean_string($name);
	if (!empty($ext))	$name="$name.$ext";
	// remove underscores off the beginning
	$name=trim($name,'_');
	// remove more than double underscores, safer for path encode/decode
	$name=preg_replace('/(__(_+))/','__',$name);
	return $name;
}

?>