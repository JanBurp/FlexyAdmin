<?php 
/** \ingroup helpers
 * Aantal handige functies voor het omgaan met afbeeldingen
 *
 *
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 * @file
 **/

/**
 * Geeft HTML voor een icon
 *
 * @author Jan den Besten
 */
function icon($class="",$s="",$extraClass="",$a="") {
	if (empty($s)) $s=$class;
	return "<div class=\"icon $class $extraClass\" title=\"$s\" $a><span class=\"hide\">$s</span></div>";
}

/**
 * Geeft alle images (src) terug uit een (html) tekst
 *
 * @param string $text 
 * @return array
 * @author Jan den Besten
 */
function get_images_from_text($text) {
  $images=NULL;
  if(preg_match("/<img(.*)src=\"([^\"]*)\"/ui", $text, $matches)) {
    $images=$matches[2];
    if (!is_array($images)) $images=array($images);
  }
  return $images;
}

/**
 * Geeft thumb
 *
 * @param mixed $attr 
 * @return string
 * @author Jan den Besten
 */
function show_thumb($attr) {
	$a=array();
	if (!is_array($attr)) $a["src"]=$attr; else $a=$attr;
  $map=explode('/',$a['src']);
  $filename=array_pop($map);
  $map=array_pop($map);
	$ext=get_file_extension($filename);
  
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
				if (!isset($a["longdesc"])) $a["longdesc"]='file/serve/'.$map.'/'.$filename;
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
 * @param string $file het afbeeldingsbestand
 * @return mixed FALSE als niet bestaat of fout, anders =getimagesize($i)
 * @author Jan den Besten
 */
function get_img_size($file) {
  $CI=&get_instance();
  $CI->load->model('mediatable');
  return $CI->mediatable->get_img_size($file);
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
 * @param string $file afbeelding
 * @return string = 'landscape', 'portrait' of 'unknown'
 * @author Jan den Besten
 */
function portrait_or_landscape($file) {
  $CI=&get_instance();
  $CI->load->model('mediatable');
  return $CI->mediatable->portrait_or_landscape($file);
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