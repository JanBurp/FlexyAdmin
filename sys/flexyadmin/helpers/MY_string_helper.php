<?

/**
 * Create a Random String
 *
 * Useful for generating passwords or hashes.
 *
 * @access	public
 * @param	string 	type of random string.  Options: alunum, numeric, nozero, unique
 * @param	integer	number of characters
 * @return	string
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


function strsplit($s) {
	$CI =& get_instance();
	if ($CI->config->item('PHP_version')==5) {
		return str_split($s);
	}
	$a=array();
	for ($c=0; $c<strlen($s);$c++) {
		$a[]=$s[$c];
	}
	return $a;
}


/**
	* Find a character in string
	*/ 
function in_string($in,$string) {
	$in=strsplit($in);
	$found=FALSE;
	$c=0;
	while (!$found and $c<count($in)) {
		$res=strpos($string,$in[$c]);
		if ($res) $found=TRUE;
		$c++;
	}
	return $found;
}

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
 * function add_string($string,$add,$split)
 *
 * Adds a string to a string,with a split string if string has content allready
 *
 * @param string 	$string 	String to add to
 * @param string 	$add 			string to add
 * @param	string	$split		a split string,will be added in string has content
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

//
// string remove_first_char( string sName )
//
function remove_first_char($s) {
	if ($s!="") $s=substr($s,1);
	return $s;
}

//
// string get_prefix( string sName )
//
function get_prefix($s,$split="_") {
	$pk=pk();
	$i=strpos($s,$split);
	if ($i) $out=substr($s,0,$i);
	elseif ($s==$pk) $out=$pk; else $out="";
	return $out;
}

function get_postfix($s,$split="_") {
	$e=explode($split,$s);
	return $e[count($e)-1];
}

//
// string remove_prefix( string sName )
//
function remove_prefix($s,$split="_") {
	$i=strpos($s,$split);
	if ($i) $out=substr($s,$i+strlen($split)); else $out=$s;
	return $out;
}

function remove_postfix($s,$split="_") {
	$post=get_postfix($s);
	return str_replace($split.get_postfix($s),"",$s);
}

//
// string replace_html( string sTag,string $sReplace,string $sSource )
//
function replace_html($sTag,$sReplace,$sSource) {
	return preg_replace("/(<\/?(".$sTag."|".strtoupper($sTag).")\s\/?>)/",$sReplace,$sSource);
}

function remove_accent($s) {
	$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
	$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	$s= str_replace($a,$b,$s);
	return $s;
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
	$s=remove_accent($s);
	$s=preg_replace("/[^A-Za-z0-9_-]/","",$s);
	$s=preg_replace(array("/_+/","/-+/","/ +/"),array('_','-',' '),$s);
	if ($c>0) $s=substr($s,0,$c);
	return $s;
}

function safe_string($s,$c=0) {
	$s=strip_tags($s);
	$s=strtolower($s);
	$s=trim($s);
	$s=preg_replace("/\s/","_",$s);
	$s=str_replace(array('"',"'","`",'.'),'',$s);
	if ($c>0) $s=substr($s,0,$c);
	$s=explode('_',$s);
	array_pop($s);
	$s=implode('_',$s);
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

function max_length($txt,$len=100) {
	$lines=explode('.',$txt);
	$line=current($lines);
	$l=0;
	while (strlen($line)<$len and isset($lines[$l+1])) {	$line.=$lines[$l++].'. ';	}
	return $line;
}

function has_alpha($s) {
	return preg_match('/[a-zA-Z]/',$s);
}
function has_digits($s) {
	return preg_match('/\d/',$s);
}

?>