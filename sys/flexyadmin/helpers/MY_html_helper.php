<?

/**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/html_helper.html" target="_blank">HTML_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * @link http://codeigniter.com/user_guide/helpers/html_helper.html
 */

/**
 * Maakt een html tag
 *
 * @param string $tag de tag
 * @param mixed $a Als $a een string is dan wordt het de class attribuut van de tag, als $a een array is dan is het een key->value paar van attributen 
 * @param bool $end als TRUE dan maakt hij ook een close tag
 * @return void
 * @author Jan den Besten
 */
function html($tag,$a=array(),$end=FALSE) {
	if (!is_array($a)) $a=array("class"=>$a);
	$attr="";
	foreach ($a as $k=>$v ) {
		$attr.=" ".$k."=\"$v\"";
	}
	$out="<$tag $attr";
	if ($end) $out.=" /";
	$out.=">";
	return $out;
}
/**
 * Maakt een eind tag
 *
 * @param string $tag 
 * @return void
 * @author Jan den Besten
 */
function end_html($tag) {
	return _html($tag);
}
/**
 * Maakt een eind tag
 *
 * @param string $tag 
 * @return void
 * @author Jan den Besten
 */
function _html($tag) {
	return "</$tag>";
}

/**
 * header tag
 *
 * @param string $t tekst binnen de header tag
 * @param int $h[1] header nivo 
 * @param mixed $a attributen (of class)
 * @return void
 * @author Jan den Besten
 */
function h($t,$h=1,$a=array()) {
	return html("h$h",$a).$t._html("h$h");
}

/**
 * p tag
 *
 * @param mixed $a attributen (of class)
 * @return void
 * @author Jan den Besten
 */
function p($a=array()) {
	return html("p",$a);
}
/**
 * einde van p tag
 *
 * @return void
 * @author Jan den Besten
 */
function _p() {
	return _html("p");
}

function span($a=array()) {
	return html("span",$a);
}
function _span() {
	return _html("span");
}

function div($a=array()) {
	return html("div",$a);
}
function _div() {
	return _html("div");
}
function end_div() {
	return _html("div");
}

function hr($a=array()) {
	if (!isset($a['class'])) $a['class']='';
	$a['class'].=' hr';
	return div($a)._div(); // this is better cross browser!
}

function safe_email($adres,$text) {
	$adres=explode("@",$adres);
	return '<script language="JavaScript" type="text/javascript">email("'.$adres[0].'","'.$adres[1].'","'.$text.'");</script>';
}

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


function button($url,$text,$class="") {
	$out="";
	$a=array("class"=>"button ".$class);
	$out.=div($a);
	$out.=anchor($url,$text,$a);
	$out.=end_div();
	return $out;
}

?>