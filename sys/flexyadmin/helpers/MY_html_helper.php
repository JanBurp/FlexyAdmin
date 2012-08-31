<?

/**
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
 * @link http://codeigniter.com/user_guide/helpers/html_helper.html
 */

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
 * Zelfde als _html()
 *
 * @param string $tag 
 * @return string
 * @author Jan den Besten
 * @depricated
 * @ignore
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
 * @param int $h[1] header nivo 
 * @param mixed $a
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
 * @depricated
 * @ignore
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
 * Maakt een veilig email link
 *
 * @param string $adres Emailadres
 * @param string $text Tekst die in de link moet komen
 * @return string
 * @author Jan den Besten
 * @depricated
 * @ignore
 */
function safe_email($adres,$text) {
	$adres=explode("@",$adres);
	return '<script language="JavaScript" type="text/javascript">email("'.$adres[0].'","'.$adres[1].'","'.$text.'");</script>';
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
 * @ignore
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