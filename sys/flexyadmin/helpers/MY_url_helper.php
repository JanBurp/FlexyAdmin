<?php 
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
  $url=reduce_double_slashes(site_url($uri));
  $url=str_replace($CI->config->item('url_suffix'),'',$url);
	return $url;
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


