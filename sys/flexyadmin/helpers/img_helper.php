<?

/**
 * Aantal handige functies voor het omgaan met afbeeldingen
 *
 * @author Jan den Besten
 **/

/**
 * Geeft HTML voor een icon
 *
 * @author Jan den Besten
 * @ignore
 */
function icon($class="",$s="",$extraClass="",$a="") {
	if (empty($s)) $s=$class;
	return "<div class=\"icon $class $extraClass\" title=\"$s\" $a><span class=\"hide\">$s</span></div>";
}

/**
 * Geeft thumb
 *
 * @param mixed $attr 
 * @return string
 * @author Jan den Besten
 * @ignore
 */
function show_thumb($attr) {
	$a=array();
	if (!is_array($attr)) $a["src"]=$attr; else $a=$attr;
	$ext=get_file_extension($a["src"]);
	$CI=& get_instance();
	$img_types=$CI->config->item('FILE_types_img');
	$flash_types=$CI->config->item('FILE_types_flash');
	if (in_array($ext,$img_types) or in_array($ext,$flash_types)) {
		$imgSize=get_img_size($a["src"]);
		if ($imgSize) {
			$a["zwidth"]=$imgSize[0];
			$a["zheight"]=$imgSize[1];
			if (!isset($a["alt"]))		$a["alt"]=$a["src"];
			if (!isset($a["class"]))	$a["class"]="zoom"; else $a["class"].=" zoom";
			if ($ext=="swf") {
				$src=$a["src"];
				unset($a["src"]);
				return flash($src,$a);
			}
			else {
				if (!isset($a["alt"])) $a["alt"]=$a["src"];
				if (!isset($a["longdesc"])) $a["longdesc"]=$a["src"];
				$cachedThumb=$CI->config->item('THUMBCACHE').pathencode($a['src']);
				if (file_exists($cachedThumb)) $a['src']=$cachedThumb;
				return img($a);
			}
		}
	}
	$path=explode('/',$a['src']);
	return $path[count($path)-1];
}


/**
 * Geeft omvang van een afbeelding, checkt eerst of bestand wel bestaat en handeld eventuele foutmeldingen
 *
 * @param string $i het afbeeldingsbestand
 * @return mixed FALSE als niet bestaat of fout, anders =getimagesize($i)
 * @author Jan den Besten
 */
function get_img_size($i) {
	$size=FALSE;
	if (file_exists($i) and is_file($i)) {
		$errorReporting=error_reporting(E_ALL);
		error_reporting($errorReporting - E_WARNING - E_NOTICE);
		$size=getimagesize($i);
		error_reporting($errorReporting);
  }
	return $size;
}

/**
 * Test of een afbeelding breder is dan een bepaalde waarde
 *
 * @param string $i Afbeeldingsbestand
 * @param int $w Breedte waarop wordt getest
 * @return bool
 * @author Jan den Besten
 */
function is_wider_than($i,$w) {
	$s=get_img_size($i);
	return $s[0]>$w;
}

/**
 * Test of een afbeelding liggen of staand is
 *
 * @param string $i afbeelding
 * @return string = 'landscape', 'portrait' of 'unknown'
 * @author Jan den Besten
 */
function portrait_or_landscape($i) {
	$c='';
	$s=get_img_size($i);
	if ($s) {
		if ($s[0]>$s[1])
			$c='landscape';
		else
			$c='portrait';
	}
	else
		$c='unknown';
	return $c;
}

/**
 * Zoekt title bij afbeelding, of als niet gevonden, maakt het zelf
 *
 * @param string $file Complete pad naar bestand (eventueel zonder hele asset map)
 * @return string
 * @author Jan den Besten
 */
function get_img_title($file) {
  $CI=&get_instance();
  $CI->load->model('mediatable');
  return $CI->mediatable->get_img_title($file);
}