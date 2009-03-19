<?

function assets($s="") {
	$CI =& get_instance();
	return base_url().$CI->config->item('ASSETS').$s;
}

function admin_assets($s="") {
	return base_url()."fa/assets/".$s;;
}

function front_uri() {
	//$uri=str_replace(uri_segments(),"",current_url());
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
	$l=str_replace(" ","_",$l);
	return rawurlencode($l);
}

function linkdecode($l) {
	$l=str_replace("_"," ",$l);
	return rawurldecode($l);
}

function pathencode($p) {
	$p=str_replace("/","__",$p);
	return linkencode($p);
}

function pathdecode($p,$isPath=FALSE) {
	$p=str_replace("__","/",$p);
	if (!$isPath) $p=linkdecode($p);
	return $p;
}


/*
// string load_class( string sClass)

function load_library_class($sClass) {
	require_once("system/application/libraries/$sClass.php");
}

function load_controller_class($sClass="MY_Controller") {
	require_once("system/application/controllers/$sClass.php");
}


*/