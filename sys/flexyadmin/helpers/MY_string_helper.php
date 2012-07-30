<?

/**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/string_helper.html" target="_blank">String_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * @link http://codeigniter.com/user_guide/helpers/string_helper.html
 */


/**
 * Create a Random String
 *
 * Useful for generating passwords or hashes.
 *
 * @param string type['alnum] of random string.  Options: alnum, numeric, nozero, unique
 * @param integer number[8] of characters
 * @return string
 */
if ( ! function_exists('random_string')) {	
	function random_string($type = 'alnum', $len = 8)	{					
		switch($type)		{
			case 'alnum'	:
			case 'alfa'		:
			case 'numeric':
			case 'nozero'	:
					switch ($type)	{
						case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';	break;
						case 'alfa'		:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';	break;
						case 'numeric':	$pool = '0123456789';	break;
						case 'nozero'	:	$pool = '123456789';	break;
					}
					$str = '';
					for ($i=0; $i < $len; $i++)	{
						$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
					}
					return $str;
			  break;
			case 'unique' : return md5(uniqid(mt_rand()));
			  break;
		}
	}
}


/**
 * Zoekt een karakter in een string
 *
 * @param string $in te zoeken karakter
 * @param string $string string waarin gezocht wordt
 * @return bool TRUE als karakter is gevonden
 * @author Jan den Besten
 */
function in_string($in,$string) {
	$in=str_split($in);
	$found=FALSE;
	$c=0;
	while (!$found and $c<count($in)) {
		$res=strpos($string,$in[$c]);
		if ($res) $found=TRUE;
		$c++;
	}
	return $found;
}

/**
 * Zoekt een string in een string
 *
 * @param string $in te zoeken string
 * @param string $string string waarin gezocht wordt
 * @return bool TRUE als string is gevonden
 * @author Jan den Besten
 */
function has_string($in,$string) {
	return strpos($string,$in)!==false;
}


function explode_pre($split,$fields,$pre) {
	$fields=explode($split,$fields);
	foreach($fields as $key=>$field) {
		$fields[$key]=$pre.$field;
	}
	return $fields;
}

/**
 * Adds a string to a string, with a split string if string has content allready
 *
 * @param string $string String to add to
 * @param string $add string to add
 * @param string $split a split string,will be added in string has content
 * @return string result string after adding
 */
function add_string($s,$add,$split="|") {
	if (empty($s))
		$s=$add;
	else
		$s=$s.$split.$add;
	return $s;
}


function str_reverse($s) {
	$o="";
	for ($c=strlen($s); $c>=0; $c--) {
		$o.=substr($s,$c,1);
	}
	return $o;
}

function remove_first_char($s) {
	if ($s!="") $s=substr($s,1);
	return $s;
}

/**
 * Pakt eerste deel van een string
 *
 * @param string $s 
 * @param string $split['_']
 * @return string
 * @author Jan den Besten
 *
 * Pakt het eerste deel van een string nadat hij de string heeft verdeeld in delen aan de hand van een scheidingskarakter.
 * Standaard is het scheidingskarakter een underscore '_'.<br/>
 * <br/>
 * Voorbeeld:<br/>
 * <code>echo get_prefix( 'str_example' );<br/>
 * echo get_prefix( 'tbl_example.id', '.' );</code>
 * Geeft als resultaat:
 * <code>str<br/>
 * tbl_example</code>
 */
function get_prefix($s,$split="_") {
	$pk=PRIMARY_KEY;
	$i=strpos($s,$split);
	if ($i) $out=substr($s,0,$i);
	elseif ($s==$pk) $out=$pk; else $out="";
	return $out;
}

function get_suffix($s,$split="_") {
	$e=explode($split,$s);
	return $e[count($e)-1];
}
function get_postfix($s,$split="_") {
	return get_suffix($s,$split);
}

function remove_prefix($s,$split="_") {
	$i=strpos($s,$split);
	if ($i) $out=substr($s,$i+strlen($split)); else $out=$s;
	return $out;
}

function remove_suffix($s,$split="_") {
	$e=explode($split,$s);
	$e=array_slice($e,0,count($e)-1);
	if (!empty($e))
		return implode($split,$e);
	else
		return $s;
}
function remove_postfix($s,$split="_") {
	return remove_suffix($s,$split);
}

function replace_html($sTag,$sReplace,$sSource) {
	return preg_replace("/(<\/?(".$sTag."|".strtoupper($sTag).")\s\/?>)/",$sReplace,$sSource);
}

function strip_nonascii($s) {
	$s=preg_replace('/[^(\x20-\x7F)\x0A]*/','', $s);
	return $s;
}

function replace_linefeeds($s,$r=' ') {
	$s=preg_replace('/\n+/',' ', $s);
	return $s;
}

function clean_string($s,$c=0) {
	$s=str_replace('&amp;','&',$s);
	$s=str_replace('&','en',$s);
	$s=convert_accented_characters($s);
	$s=preg_replace("/[^A-Za-z0-9_-]/","",$s);
	$s=preg_replace(array("/-+/","/ +/"),array('-',' '),$s);
	if ($c>0) $s=substr($s,0,$c);
	return $s;
}

function safe_string($s,$c=0) {
	$s=strip_tags($s);
	$s=strtolower($s);
	$s=trim($s);
	$s=preg_replace("/\s/","_",$s);
	$s=str_replace(array('"',"'","`",'.','(',')'),'',$s);
	if ($c>0) $s=substr($s,0,$c);
	$e=explode('_',$s);
	if (!empty($e) and count($e)>1) {
		$s=$e;
		array_pop($s);
		$s=implode('_',$s);
	}
	return $s;
}

function strip_string($s,$c=0) {
	$srch	=array("<br />","&nbsp;");
	$rplc=array(" "," ");
	$s=str_replace($srch,$rplc,$s);
	$s=strip_tags($s);
	$s=trim($s);
	if ($c>0) $s=character_limiter($s,$c);
	return $s;
}

function nice_string($s) {
	return ucfirst(str_replace("_"," ",$s));
}

function hex2str($hexstr) {
	if (substr($hexstr,0,2)=="0x") $hexstr=substr($hexstr,2);
  $hexstr = str_replace(' ','',$hexstr);
  $hexstr = str_replace('\x','',$hexstr);
  $retstr = pack('H*',$hexstr);
  return $retstr;
}

function str2hex($string) {
  $hexstr = unpack('H*',$string);
	$hexstr=array_shift($hexstr);
	if (!empty($hexstr))
  	return "0x".$hexstr;
	else
		return "";
}

function intro_string($txt,$len=50,$type='WORDS',$strip_tags='<br/><strong><italic><em><b><a><p>') {
	// first check if there's an intro set by class: intro
	$matches=array();
	preg_match_all('/<(.*?)class="(.*?)intro(.*?)"(.*?)>(.*?)<\/(.*?)>/is',$txt,$matches);
	$intro='';
	if (isset($matches[0]) and !empty($matches[0])) {
		foreach ($matches[0] as $match) {
			$intro.=$match;
		}
		$intro=str_replace('&nbsp;',' ',strip_tags($intro,$strip_tags));
	}
	// no intro class found, pick an intro by length
	if ($intro=='') {
		$intro=max_length(str_replace('&nbsp;',' ',strip_tags($txt,$strip_tags)),$len,$type,true,$strip_tags);
	}
	// make sure all tags are closed
	$intro=restore_tags($intro);
	return $intro;
}

function add_before_last_tag($txt,$more,$tag='</p>') {
	$stag=str_replace('/','\/',$tag);
	$rtxt=preg_replace('/(.*)'.$stag.'\z/','$1'.$more.$tag,$txt);
	if ($rtxt==$txt) {
		$txt.=$tag;
		$rtxt=preg_replace('/(.*)'.$stag.'\z/','$1'.$more.$tag,$txt);
	}
	return $rtxt;
}

function max_length($txt,$len=100,$type='LINES',$closetags=false,$strip_tags='') {
	$out='';
	switch ($type) {
		case 'CHARS':
			$out=substr(strip_tags($txt,$strip_tags),0,$len);
			break;
		case 'WORDS':
				$words=explode(' ',$txt);
				$line='';
				$w=0;
				while (strlen($line)<$len and isset($words[$w+1])) { $line.=$words[$w++].' ';	}
				$out=$line;
			break;
		case 'LINES':
		default;
				$lines=explode('. ',$txt);
				$lines=array_slice($lines,0,$len);
				$out=implode('. ',$lines);
			break;
	}
	if ($closetags) {
		$out=restore_tags($out);
	}
	return $out;
}

function restore_tags($input) {
	$opened = array();
	// loop through opened and closed tags in order
	if(preg_match_all("/<(\/?[a-z]+)>?/i", $input, $matches)) {
		foreach($matches[1] as $tag) {
			if(preg_match("/^[a-z]+$/i", $tag, $regs)) {
				// a tag has been opened
				if(strtolower($regs[0]) != 'br') $opened[] = $regs[0];
			} elseif(preg_match("/^\/([a-z]+)$/i", $tag, $regs)) {
				// a tag has been closed
				unset($opened[array_pop(array_keys($opened, $regs[1]))]);
			}
		}
	}
	// close tags that are still open
	if($opened) {
		$tagstoclose = array_reverse($opened);
		foreach($tagstoclose as $tag) $input .= "</$tag>";
	}
	return $input;
}

function has_alpha($s) {
	return preg_match('/[a-zA-Z]/',$s);
}
function has_digits($s) {
	return preg_match('/\d/',$s);
}

?>