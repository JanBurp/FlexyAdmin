<?php 

/** \ingroup helpers
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">URL_helper van CodeIgniter</a>.
 *
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 * @file
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
 * Site URL
 * Zoals de standaard CodeIgniter versie, met deze aanpassingen:
 * - plakt automatisch 'lang=' in de query als dit is ingesteld
 *
 * @param	string	$uri
 * @param	string	$protocol
 * @return	string
 */
function site_url($uri = '', $protocol = NULL) {
  $CI=&get_instance();
  $url=$CI->config->site_url($uri, $protocol);
  if (isset($CI->site['language'])) $url.='?lang='.$CI->site['language'];
  return $url;
}


/**
 * Maakt van gegeven parameters een site_url()
 *
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
  $url=reduce_double_slashes(site_url($uri));
  $url=str_replace($CI->config->item('url_suffix'),'',$url);
	return $url;
}

/**
 * Maakt van gegeven parameters een uri
 *
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
 * @deprecated
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
 * @deprecated
 */
function linkdecode($l) {
	return rawurldecode($l);
}

/**
 * Encode een pad zodat het geschikt is voor een uri
 *
 * @param string $p 
 * @param string $isPath default=TRUE 
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
 * @param string $isPath default=TRUE
 * @return string
 * @author Jan den Besten
 */
function pathdecode($p,$isPath=TRUE) {
	$p=str_replace("___","/",$p);
	if ($isPath) $p=linkdecode($p);
	return $p;
}

/**
 * Finds emailaddresses in string
 *
 * @param string $s
 * @param bool $one default=FALSE set to TRUE if yo need just one email address
 * @return mixed FALSE if no email addres is found, string when on is found, an array of strings when more are found
 * @author Jan den Besten
 */
function get_emails($s,$one=FALSE) {
  $emails = false;
  if (preg_match_all("/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/ui", $s, $matches)) {
    if (count($matches[0])==1) {
      $emails=current($matches[0]);
    }
    else {
      $emails=array();
      foreach($matches[0] as $email) array_push ($emails, strtolower($email));
    }
  }
  if ($one and is_array($emails)) $emails=current($emails);
  return $emails;
}


/**
 * Test an url
 *
 * @param string $url
 * @return bool
 * @author Jan den Besten
 */
function test_url($url) {
  // Email: don't test
  if (substr($url,0,7)=='mailto:') {
    $email=remove_prefix($url,':');
  	$CI =& get_instance();
    return $CI->form_validation->valid_email($email);
  }
  
  // local => add site_url()
  if (substr($url,0,4)!=='http') {
    $url=site_url($url);
  }

  // http(s)
  if (substr($url,0,4)=='http') {
    $handle = curl_init($url);
    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($handle);
    /* Check for 404 (file not found). */
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if($httpCode != 200) {
      return FALSE;
    }
    // trace_([$url,$httpCode,$response]);
    curl_close($handle);
    return TRUE;
  }
  
  return FALSE;
}


/**
 * Maakt leesbare string van een url (haalt http:// etc eraf)
 *
 * @param string $s 
 * @return string
 * @author Jan den Besten
 */
function url_string($s) {
  return trim(str_replace(array('http://','https://','mailto:','tel:'),'',$s),'/');
}



/**
 * Encoded Mailto Link
 *
 * Create a spam-protected mailto link written in Javascript
 *
 * @access	public
 * @param	string	the email address
 * @param	string	the link title
 * @param	mixed	any attributes
 * @return	string
 */
if ( ! function_exists('safe_mailto'))
{
	function safe_mailto($email, $title = '', $attributes = '')
	{
		$title = (string) $title;

		if ($title == "")
		{
			$title = $email;
		}

		for ($i = 0; $i < 16; $i++)
		{
			$x[] = substr('<a href="mailto:', $i, 1);
		}

		for ($i = 0; $i < strlen($email); $i++)
		{
			$x[] = "|".ord(substr($email, $i, 1));
		}

		$x[] = '"';

		if ($attributes != '')
		{
			if (is_array($attributes))
			{
				foreach ($attributes as $key => $val)
				{
					$x[] =  ' '.$key.'="';
					for ($i = 0; $i < strlen($val); $i++)
					{
						$x[] = "|".ord(substr($val, $i, 1));
					}
					$x[] = '"';
				}
			}
			else
			{
				for ($i = 0; $i < strlen($attributes); $i++)
				{
					$x[] = substr($attributes, $i, 1);
				}
			}
		}

		$x[] = '>';

		$temp = array();
		for ($i = 0; $i < strlen($title); $i++)
		{
			$ordinal = ord($title[$i]);

			if ($ordinal < 128)
			{
				$x[] = "|".$ordinal;
			}
			else
			{
				if (count($temp) == 0)
				{
					$count = ($ordinal < 224) ? 2 : 3;
				}

				$temp[] = $ordinal;
				if (count($temp) == $count)
				{
					$number = ($count == 3) ? (($temp['0'] % 16) * 4096) + (($temp['1'] % 64) * 64) + ($temp['2'] % 64) : (($temp['0'] % 32) * 64) + ($temp['1'] % 64);
					$x[] = "|".$number;
					$count = 1;
					$temp = array();
				}
			}
		}

		$x[] = '<'; $x[] = '/'; $x[] = 'a'; $x[] = '>';

		$x = array_reverse($x);
		ob_start();

?><script type="text/javascript">//<![CDATA[
var l=new Array();<?php
$i = 0;foreach ($x as $val){ ?>l[<?php echo $i++; ?>]='<?php echo $val; ?>';<?php } ?>for (var i = l.length-1; i >= 0; i=i-1){if (l[i].substring(0, 1) == '|') document.write("&#"+unescape(l[i].substring(1))+";");else document.write(unescape(l[i]));}
//]]></script><?php

		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}
