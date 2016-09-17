<?php 
/** \ingroup helpers
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/html_helper.html" target="_blank">HTML_helper van CodeIgniter</a>.
 * 
 * Aan veel van onderstaande funties kunnen attributen worden meegegeven: $a
 * 
 * - Als $a een string is dan wordt het attribuut class="$a" toegevoegd, met $a als megegeven class dus
 * - Als $a een array is, dan wordt de key->value paren van de array omgezet in attributen en hun waarde
 * 
 * NB Maak bij voorkeur gebruikt van Views, als het niet anders kan, gebruik dan deze functies in je PHP code ipv letterlijke HTML strings
 * 
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 * @file
 */



// ------------------------------------------------------------------------

if ( ! function_exists('br'))
{
	/**
	 * Generates HTML BR tags based on number supplied
	 *
	 * @param	int	$count	Number of times to repeat the tag
	 * @return	string
	 */
	function br($count = 1)
	{
		return str_repeat('<br />', $count);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('nbs'))
{
	/**
	 * Generates non-breaking space entities based on number supplied
	 *
	 * @param	int
	 * @return	string
	 */
	function nbs($num = 1)
	{
		return str_repeat('&nbsp;', $num);
	}
}


/**
 * Maakt een gegeven html tag
 *
 * @param string $tag de tag
 * @param mixed $a
 * @param bool $end als TRUE dan maakt hij ook een close tag
 * @return string
 * @author Jan den Besten
 */
function html($tag,$a=array(),$end=FALSE) {
	if (!is_array($a)) $a=array("class"=>$a);
	$attr=attributes($a);
	$out="<$tag";
  if (!empty($attr)) $out.=' '.$attr;
	if ($end) $out.=" /";
	$out.=">";
	return $out;
}

/**
 * Maak een attributen lijst van array
 *
 * @param array $a 
 * @return string
 * @author Jan den Besten
 */
function attributes($a=array()) {
	$attr="";
  if (!empty($a)) {
  	foreach ($a as $k=>$v ) {
  		$attr.=" ".$k."=\"$v\"";
  	}
  }
  return $attr;
  
}

/**
 * Zelfde als _html()
 *
 * @param string $tag 
 * @return string
 * @author Jan den Besten
 * @deprecated
 */
function end_html($tag) {
	return _html($tag);
}

/**
 * Maakt een eind tag
 *
 * @param string $tag 
 * @return string
 * @author Jan den Besten
 */
function _html($tag) {
	return "</$tag>";
}

/**
 * &lt;h#&gt; tag
 *
 * @param string $t tekst binnen de header tag
 * @param int $h default=1 header nivo 
 * @param mixed $a  default=array()
 * @return string
 * @author Jan den Besten
 */
function h($t,$h=1,$a=array()) {
	return html("h$h",$a).$t._html("h$h");
}

/**
 * &lt;p&gt; tag
 *
 * @param mixed $a
 * @return string
 * @author Jan den Besten
 */
function p($a=array()) {
	return html("p",$a);
}

/**
 * &lt;/p&gt; tag
 *
 * @return string
 * @author Jan den Besten
 */
function _p() {
	return _html("p");
}

/**
 * &lt;span&gt; tag
 *
 * @param mixed $a 
 * @return string
 * @author Jan den Besten
 */
function span($a=array()) {
	return html("span",$a);
}

/**
 * &lt;/span&gt; tag
 *
 * @return string
 * @author Jan den Besten
 */
function _span() {
	return _html("span");
}

/**
 * &lt;div&gt; tag
 *
 * @param mixed $a 
 * @return string
 * @author Jan den Besten
 */
function div($a=array()) {
	return html("div",$a);
}

/**
 * &lt;/div&gt; tag
 *
 * @return string
 * @author Jan den Besten
 */
function _div() {
	return _html("div");
}

/**
 * Zelfde als _div()
 *
 * @return string
 * @author Jan den Besten
 * @deprecated
 */
function end_div() {
	return _html("div");
}

/**
 * &lt;hr/&gt; tag
 *
 * @param mixed $a 
 * @return string
 * @author Jan den Besten
 */
function hr($a=array()) {
	if (!isset($a['class'])) $a['class']='';
	$a['class'].=' hr';
	return div($a)._div(); // this is better cross browser!
}

/**
 * pre tag
 *
 * @return string
 * @author Jan den Besten
 */
function pre() {
  return '<pre>';
}

/**
 * einde pre tag
 *
 * @return string
 * @author Jan den Besten
 */
function _pre() {
  return '</pre>';
}

/**
 * Maakt een veilig email link
 *
 * @param string $adres Emailadres
 * @param string $text Tekst die in de link moet komen
 * @return string
 * @author Jan den Besten
 * @deprecated
 */
function safe_email($adres,$text) {
  return safe_mailto($adres,$text);
}

/**
 * Maakt een flash object
 *
 * @param string $swf Flashfile
 * @param string $attr 
 * @return string
 * @author Jan den Besten
 */
function flash($swf,$attr="") {
	if (is_array($attr)) {
		$a="";
		foreach($attr as $at=>$v) {
			$a.=" $at=\"$v\"";
		}
		$attr=$a;
	}

	$object=
'<object class="flash" data="'.$swf.'" '.$attr.' classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" >'.
'<param name="allowScriptAccess" value="sameDomain" />'.
'<param name="movie" value="'.$swf.'" />'.
'<param name="quality" value="high" />'.
'<param name="bgcolor" value="#ffffff" />'.
'<embed class="flash" src="'.$swf.'" quality="high" bgcolor="#ffffff" '.$attr.' allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" />'.
'</object>';
	return $object;
}

/**
 * Maakt een div en een a tag aan met class="button"
 *
 * @param string $url
 * @param string $text
 * @param string $class['']
 * @author Jan den Besten
 */
function button($url,$text,$class="") {
	$out="";
	$a=array("class"=>"button ".$class);
	$out.=div($a);
	$out.=anchor($url,$text,$a);
	$out.=end_div();
	return $out;
}

?>