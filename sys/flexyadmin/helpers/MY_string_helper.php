<?


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
 * Adds a string to a string, with a split string if string has content allready
 *
 * @param string 	$string 	String to add to
 * @param string 	$add 			string to add
 * @param	string	$split		a split string, will be added in string has content
 * @return string result string after adding
 */
function add_string($s,$add,$split="|") {
	if (!empty($add)) {
		if (strpos($s,$add)===false) {
			if ($s!="")
				$s.=$split.$add;
			else
			 $s=$add;
		}
	}
	return $s;
}

function str_reverse($s) {
	$a=strsplit($s);
	$a=array_reverse($a);
	$o="";
	foreach($a as $c) $o.=$c;
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
	$r=remove_prefix($s,$split);
	if (strpos($r,$split)===false) return $r;
	return get_postfix($r,$split);
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
// string replace_html( string sTag, string $sReplace, string $sSource )
//
function replace_html($sTag,$sReplace,$sSource) {
	return preg_replace("/(<\/?(".$sTag."|".strtoupper($sTag).")\s\/?>)/",$sReplace,$sSource);
}

function clean_string($s) {
	$a='ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	$b='aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	$s=utf8_decode($s);
	$s=strtr($s,utf8_decode($a),$b);
	$s=ereg_replace("[^A-Za-z0-9_-]", "", $s);
	return utf8_encode($s);
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

?>
