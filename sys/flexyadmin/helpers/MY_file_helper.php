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


function copy_file($source,$dest) {
	$content=@file_get_contents($source);
	if ($content === FALSE) {
		$status=false;
	}
	else {
		$copy=fopen($dest, "w");
		fwrite($copy, $content);
		fclose($copy);
		$status=true;
	}
	return $status;
}


function get_file_extension($f) {
	$p=strrpos($f,".");
	if ($p===FALSE) return "";
	return strtolower(substr($f,$p+1));
}

function get_file_without_extension($f) {
	$p=strrpos($f,".");
	if ($p===FALSE) return $f;
	return substr($f,0,$p);
}
function add_file_prefix($f,$pre) {
	$p=strrpos($f,"/");
	if ($p===false) {
		$path='';
		$file=$f;
	}
	else {
		$path=substr($f,0,$p+1);
		$file=substr($f,$p+1);
	}
	return $path.$pre.$file;
}
function add_file_suffix($f,$post) {
	$p=strrpos($f,"/");
	if ($p===false) {
		$path='';
		$file=$f;
	}
	else {
		$path=substr($f,0,$p+1);
		$file=substr($f,$p+1);
	}
	$ext=get_file_extension($file);
	$file=get_file_without_extension($file);
	return $path.$file.$post.'.'.$ext;
}
function add_file_presuffix($f,$pre='',$post='') {
	$p=strrpos($f,"/");
	if ($p===false) {
		$path='';
		$file=$f;
	}
	else {
		$path=substr($f,0,$p+1);
		$file=substr($f,$p+1);
	}
	$ext=get_file_extension($file);
	$file=get_file_without_extension($file);
	return $path.$pre.$file.$post.'.'.$ext;
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

function file_types_are_images($types) {
	$CI=&get_instance();
	return file_types_in_array($types,$CI->config->item('FILE_types_img'));
}

function file_types_are_flash($types) {
	$CI=&get_instance();
	return file_types_in_array($types,$CI->config->item('FILE_types_flash'));
}

function file_types_in_array($types,$array) {
	$in = TRUE;
	if (!empty($types)) {
		if (is_string($types)) $types=explode(',',$types);
		foreach ($types as $type) {
			$in = $in OR in_array($type,$array);
		}
	}
	else return FALSE;
	return $in;
}


?>