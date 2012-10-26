<?

/**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">URL_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * @link http://codeigniter.com/user_guide/helpers/url_helper.html
 */


/**
  * Geeft pad naar assets map van de site en voegt meegegeven map toe
  *
  * @param string $s['']
  * @return string
  * @author Jan den Besten
  */
function assets($s="") {
  static $assets;
  if (empty($assets)) {
  	$CI =& get_instance();
  	$assets=$CI->config->item('ASSETS').$s;
  }
  return $assets;
}

/**
 * Geeft pad naar assets map van admin
 *
 * @param string $s[''] 
 * @return string
 * @author Jan den Besten
 */
function admin_assets($s="") {
  static $assets;
  if (empty($assets)) {
  	$CI =& get_instance();
  	$assets=$CI->config->item('ADMINASSETS').$s;
  }
  return $assets;
}

/**
 * Remove assets path from path
 *
 * @param string $path 
 * @return string $path
 * @author Jan den Besten
 */
function remove_assets($path) {
  return str_replace(assets(),'',$path);
}

/**
 * Add assets path to path
 *
 * @param string $path 
 * @return string $path
 * @author Jan den Besten
 */
function add_assets($path) {
  return assets().remove_assets($path);
}


/**
 * Maakt van gegeven parameters een site_url()
 *
 * @param string,string,string,...
 * @return string
 * @author Jan den Besten
 */
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

/**
 * Maakt van gegeven parameters een uri
 *
 * @param string,string,string,...
 * @return string
 * @author Jan den Besten
 */
function api_uri() {
	$aParams=func_get_args();
	if (substr($aParams[0],0,3)=='API') {
		$CI =& get_instance();
		$uri=$CI->config->item($aParams[0]);
	}
	else {
		$uri=$aParams[0];
	}
	unset($aParams[0]);
	if (count($aParams)>0) {
		foreach ($aParams as $p) $uri.="/$p";
	}
	return reduce_double_slashes($uri);
}

/**
 * Zelfde als rawurlencode()
 *
 * @param string $l 
 * @return string
 * @author Jan den Besten
 * @depricated
 * @ignore
 */
function linkencode($l) {
	return rawurlencode($l);
}

/**
 * Zelfde als  rawurldecode()
 *
 * @param string $l 
 * @return string
 * @author Jan den Besten
 * @depricated
 * @ignore
 */
function linkdecode($l) {
	return rawurldecode($l);
}

/**
 * Encode een pad zodat het geschikt is voor een uri
 *
 * @param string $p 
 * @param string $isPath[TRUE] 
 * @return string
 * @author Jan den Besten
 */
function pathencode($p,$isPath=TRUE) {
	$p=str_replace("/","___",$p);
	if ($isPath) $p=linkencode($p);
	return $p;
}

/**
 * Decode een pad (uit een uri)
 *
 * @param string $p 
 * @param string $isPath[TRUE]
 * @return string
 * @author Jan den Besten
 */
function pathdecode($p,$isPath=TRUE) {
	$p=str_replace("___","/",$p);
	if ($isPath) $p=linkdecode($p);
	return $p;
}

