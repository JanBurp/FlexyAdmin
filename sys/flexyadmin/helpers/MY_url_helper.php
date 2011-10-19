<?

function index_url($s) {
	return index_page()."/".$s;
}

function assets($s="") {
	$CI =& get_instance();
	return $CI->config->item('ASSETS').$s;
}

function admin_assets($s="") {
	$CI =& get_instance();
	return $CI->config->item('ADMINASSETS').$s;
}

function front_uri() {
	$uri=base_url();
	return $uri;
}

function api_url() {
	$CI =& get_instance();
	$aParams=func_get_args();
	$uri=$CI->config->item($aParams[0]);
	unset($aParams[0]);
	if (count($aParams)>0) {
		foreach ($aParams as $p) $uri.="/$p";
	}
	return reduce_double_slashes(site_url($uri));
}

function api_uri() {
	$CI =& get_instance();
	$aParams=func_get_args();
	$uri=$CI->config->item($aParams[0]);
	unset($aParams[0]);
	if (count($aParams)>0) {
		foreach ($aParams as $p) $uri.="/$p";
	}
	return reduce_double_slashes($uri);
}

function linkencode($l) {
	// $l=str_replace(" ","_",$l); // Kan niet als je mappen met _ erin wilt maken
	return rawurlencode($l);
}

function linkdecode($l) {
	// $l=str_replace("_"," ",$l);
	return rawurldecode($l);
}

function pathencode($p,$isPath=TRUE) {
	$p=str_replace("/","___",$p);
	if ($isPath) $p=linkencode($p);
	return $p;
}

function pathdecode($p,$isPath=TRUE) {
	$p=str_replace("___","/",$p);
	if ($isPath) $p=linkdecode($p);
	return $p;
}


function get_path_and_file($name) {
	$explode=explode("/",$name);
	$file=$explode[count($explode)-1];
	array_pop($explode);
	$path=implode("/",$explode);
	return array("path"=>$path,"file"=>$file);
}
