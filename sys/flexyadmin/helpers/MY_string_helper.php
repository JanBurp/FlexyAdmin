<?php 
/**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/string_helper.html" target="_blank">String_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * @link http://codeigniter.com/user_guide/helpers/string_helper.html
 */



 /**
  * Geeft de string terug met een nummer erachter dat bij elke aanroep wordt opgehoogd
  *
  * @param string $string['']
  * @param string $start[0] 
  * @return string
  * @author Jan den Besten
  */
function count_string($string='',$start=0) {
 static $strings=array();
 if (!isset($strings[$string])) {
   $strings[$string]=$start;
 }
 $count=$string.$strings[$string];
 $strings[$string]++;
 return $count;
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
 * @param mixed $in te zoeken string, of array van strings
 * @param string $string string waarin gezocht wordt
 * @param bool $case_sensitive[TRUE]
 * @return bool TRUE als (één van de) string(s) is gevonden
 * @author Jan den Besten
 */
function has_string($in,$string,$case_sensitive=TRUE) {
  if (!is_array($in)) $in=array($in);
  $has=FALSE;
  foreach ($in as $s) {
    if ($case_sensitive) {
      if (strpos($string,$s)!==FALSE) {
        $has=TRUE;
        break;
      }
    }
    else {
      if (strripos($string,$s)!==FALSE) {
        $has=TRUE;
        break;
      }
    }
  }
  // trace_(array('in'=>$in,'str'=>$string,'has'=>$has));
	return $has;
}

/**
 * Als explode() maar voegt aan elke value een prefix toe
 *
 * @param string $split 
 * @param array $fields 
 * @param string $pre 
 * @return array
 * @author Jan den Besten
 */
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

/**
 * Keert een string om
 *
 * @param string $s 
 * @return string
 * @author Jan den Besten
 */
function str_reverse($s) {
	$o="";
	for ($c=strlen($s); $c>=0; $c--) {
		$o.=substr($s,$c,1);
	}
	return $o;
}

/**
 * Verwijderd eerste karakter van een string
 *
 * @param string $s 
 * @return string
 * @author Jan den Besten
 */
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
 * Standaard is het scheidingskarakter een underscore '_'.
 * 
 * Voorbeeld:
 * 
 *      echo get_prefix( 'str_example' );
 *      echo get_prefix( 'tbl_example.id', '.' );
 * 
 * Geeft als resultaat:
 * 
 *      str
 *      tbl_example
 * 
 */
function get_prefix($s,$split="_") {
	$pk=PRIMARY_KEY;
	$i=strpos($s,$split);
	if ($i) $out=substr($s,0,$i);
	elseif ($s==$pk) $out=$pk; else $out="";
	return $out;
}

/**
 * Geeft suffix van een string
 *
 * @param string $s 
 * @param string $split['_'] 
 * @return string
 * @author Jan den Besten
 */
function get_suffix($s,$split="_") {
	$e=explode($split,$s);
	return $e[count($e)-1];
}
/**
 * Zelfde als get_suffix()
 *
 * @param string $s 
 * @param string $split 
 * @return string
 * @author Jan den Besten
 * @depricated
 * @ignore
 */
function get_postfix($s,$split="_") {
	return get_suffix($s,$split);
}

/**
 * Verwijderd een prefix van een string
 *
 * @param string $s 
 * @param string $split['_']
 * @return string
 * @author Jan den Besten
 */
function remove_prefix($s,$split="_") {
	$i=strpos($s,$split);
	if ($i) $out=substr($s,$i+strlen($split)); else $out=$s;
	return $out;
}

/**
 * Verwijderd de suffix van een string
 *
 * @param string $s 
 * @param string $split['_']
 * @return string
 * @author Jan den Besten
 */
function remove_suffix($s,$split="_") {
	$e=explode($split,$s);
	$e=array_slice($e,0,count($e)-1);
	if (!empty($e))
		return implode($split,$e);
	else
		return $s;
}

/**
 * Zelfde als remove_suffix()
 *
 * @param string $s 
 * @param string $split 
 * @return string
 * @author Jan den Besten
 * @depricated
 * @ignore
 */
function remove_postfix($s,$split="_") {
	return remove_suffix($s,$split);
}

/**
 * Vervangt html tag door een andere html tag
 *
 * @param string $sTag Te zoeken tag
 * @param string $sReplace Nieuwe tag
 * @param string $sSource Tekst
 * @return string
 * @author Jan den Besten
 */
function replace_html($sTag,$sReplace,$sSource) {
	return preg_replace("/(<\/?(".$sTag."|".strtoupper($sTag).")\s\/?>)/",$sReplace,$sSource);
}

/**
 * Verwijderd niet ASCII tekens
 *
 * @param string $s 
 * @return string
 * @author Jan den Besten
 */
function strip_nonascii($s) {
	$s=preg_replace('/[^(\x20-\x7F)\x0A]*/','', $s);
	return $s;
}

/**
 * Vervangt linefeeds
 *
 * @param string $s
 * @param string $r[' '] vervangen door 
 * @return string
 * @author Jan den Besten
 */
function replace_linefeeds($s,$r=' ') {
	$s=preg_replace('/\n+/',' ', $s);
	return $s;
}

/**
 * Maakt een schone string
 * 
 * - &amp; wordt vervangen door &
 * - & wordt vervangen door 'en'
 * - Alle niet letters, en cijfers en _- worden verwijderd
 * - Meer dan één - wordt vervangen door één -
 *
 * @param string $s 
 * @param string $c[0] Als groter dan 0 dan wordt de string ingekort tot deze lengte
 * @return string
 * @author Jan den Besten
 */
function clean_string($s,$c=0) {
  $s=str_replace('&amp;','&',$s);
  $s=str_replace('&','en',$s);
  $s=convert_accented_characters($s);
  $s=preg_replace("/[^A-Za-z0-9_-]/","",$s);
  $s=preg_replace(array("/-+/","/ +/"),array('-',' '),$s);
  if ($c>0) $s=substr($s,0,$c);
	return $s;
}

/**
 * Maak een veilig string, te gebruiken in uri's
 * 
 * - Verwijderd HTML tags
 * - Maakt lowercase
 * - Verwijderd spaties aan begin & eind
 * - Vervangt spaties door '_'
 * - Verwijderd quotes
 *
 * @param string $s 
 * @param string $c[0] Maximale lengte
 * @return string
 * @author Jan den Besten
 */
function safe_string($s,$c=0) {
	$s=strip_tags($s);
	$s=strtolower($s);
	$s=trim($s);
	$s=preg_replace("/\s/","_",$s);
	$s=str_replace(array('"',"'","`",'.',',','(',')'),'',$s);
	if ($c>0) $s=substr($s,0,$c);
	$e=explode('_',$s);
	if (!empty($e) and count($e)>1) {
		$s=$e;
		array_pop($s);
		$s=implode('_',$s);
	}
	return $s;
}



/**
 * Vervangt alle 'gevaarlijke' quotes (' en ") in een string met ongevaarlijke ` quotes
 *
 * @param string $s
 * @return string $s
 * @author Jan den Besten
 */
function safe_quotes($s) {
  $s=str_replace("'",'`',$s);
  $s=str_replace('"','``',$s);
  return $s;
}


/**
 * Als strip_tags() maar vervangt eerst alle <br /> en &nbsp; door normale spaties
 *
 * @param string $s 
 * @param string $c[0] Maximale lengte
 * @return string
 * @author Jan den Besten
 */
function strip_string($s,$c=0) {
	$srch	=array("<br />","&nbsp;");
	$rplc=array(" "," ");
	$s=str_replace($srch,$rplc,$s);
	$s=strip_tags($s);
	$s=trim($s);
	if ($c>0) $s=character_limiter($s,$c);
	return $s;
}

/**
 * Maakt mooie naam
 * 
 * - Vervang _ door spatie
 * - Begin met een hoofdletter
 *
 * @param string $s 
 * @return string
 * @author Jan den Besten
 */
function nice_string($s) {
	return ucfirst(str_replace("_"," ",$s));
}

/**
 * Hexadecimale waarde wordt een string
 *
 * @param string $hexstr 
 * @return string
 * @author Jan den Besten
 */
function hex2str($hexstr) {
	if (substr($hexstr,0,2)=="0x") $hexstr=substr($hexstr,2);
  $hexstr = str_replace(' ','',$hexstr);
  $hexstr = str_replace('\x','',$hexstr);
  $retstr = pack('H*',$hexstr);
  return $retstr;
}

/**
 * String naar hex string
 *
 * @param string $string 
 * @return string
 * @author Jan den Besten
 */
function str2hex($string) {
  $hexstr = unpack('H*',$string);
	$hexstr=array_shift($hexstr);
	if (!empty($hexstr))
  	return "0x".$hexstr;
	else
		return "";
}

/**
 * Maakt een header tekst van een lange tekst
 * 
 * @param string $txt 
 * @param string $len[50] Maximale lengte
 * @param string $type['WORDS] CHARS|WORDS|LINES
 * @param string $strip_tags['&lt;br/&gt;&lt;strong&gt;&lt;italic&gt;&lt;em&gt;&lt;b&gt;&lt;a&gt;&lt;p&gt;']
 * @return string
 * @author Jan den Besten
 */
function intro_string($txt,$len=50,$type='WORDS',$strip_tags='<br/><strong><italic><em><b><a><p>') {
	// first check if there's an intro set by class: intro
	$matches=array();
	preg_match_all('/<([\w]+)([^>]*)class=\"([^\"]*)intro([^\"]*)\"[^>]*>(.*)<\/\1>/uiUsm',$txt,$matches);
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

/**
 * Voeg tekst toe voor (bepaalde) laatste HTML tag
 *
 * @param string $txt 
 * @param string $more Toe te voegen tekst
 * @param string $tag['&lt;/p&gt;']
 * @return string
 * @author Jan den Besten
 */
function add_before_last_tag($txt,$more,$tag='</p>') {
	$stag=str_replace('/','\/',$tag);
	$rtxt=preg_replace('/(.*)'.$stag.'\z/','$1'.$more.$tag,$txt);
	if ($rtxt==$txt) {
		$txt.=$tag;
		$rtxt=preg_replace('/(.*)'.$stag.'\z/','$1'.$more.$tag,$txt);
	}
	return $rtxt;
}

/**
 * Geeft een string van een maximale lengte
 *
 * @param string $txt 
 * @param string $len[100]
 * @param string $type[LINES] Waar op wordt gesplitst [CHARS|WORDS|LINES]
 * @param string $closetags[FALSE]
 * @param string $strip_tags['']
 * @return string
 * @author Jan den Besten
 */
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
      $out='';
      foreach ($lines as $line) {
        $out.=$line.'. ';
        if (strlen($out)>$len) break;
      }
      $out=trim($out);
			break;
	}
	if ($closetags) {
		$out=restore_tags($out);
	}
	return $out;
}

/**
 * Vind eerste HTML tag en geeft array met allerlei informatie
 *
 * @param string $txt Tekst waarin gezocht moet worden
 * @param string $tag HTML tag die gezocht wordt
 * @param mixed $max_pos[FALSE] Maximaal aantal karakters waar de tag gevonden moet worden vanaf het begin van de tekst. Als FALSE dan is er geen begrenzing.
 * @return array ( 'tag'=> '', 'inner' => '', ['pos'=> ''] )
 * @author Jan den Besten
 */
function find_first_tag($txt,$tag,$max_pos=FALSE) {
  $match=FALSE;
  if (preg_match("/<".$tag."(.*)?>(.*)?<\\/".$tag.">/uiU", $txt,$match)) {
    $tag=$match[0];
    if (isset($match[2])) {
      $inner=$match[2];
      if ($max_pos) {
        $pos=strpos($txt,$tag);
        if ($pos>$max_pos) {
          $match=FALSE;
        }
      }
    }
  };
  if ($match) {
    $match=array('tag'=>$tag,'inner'=>$inner);
    if (isset($pos)) $match['pos']=$pos;
  }
  return $match;
}

/**
 * Zorgt ervoor dat alle tags gesloten worden in de tekst
 *
 * @param string $input 
 * @return string
 * @author Jan den Besten
 */
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

/**
 * Test of string heeft letters
 *
 * @param string $s 
 * @return bool
 * @author Jan den Besten
 */
function has_alpha($s) {
	return preg_match('/[a-zA-Z]/',$s);
}

/**
 * Test of string getallen heeft
 *
 * @param string $s 
 * @return bool
 * @author Jan den Besten
 */
function has_digits($s) {
	return preg_match('/\d/',$s);
}


/**
 * Geeft een romeins cijfer terug
 *
 * @param int $num Het getal wat omgezet moet worden in romeinse cijfers
 * @return string Het romeinse cijfer
 * @author Jan den Besten
 */
function numberToRoman($num) {
  // Make sure that we only use the integer portion of the value
  $n = intval($num);
  $result = '';
 
  // Declare a lookup array that we will use to traverse the number:
  $lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
  'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
  'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
 
  foreach ($lookup as $roman => $value) {
    // Determine the number of matches
    $matches = intval($n / $value);
    // Store that many characters
    $result .= str_repeat($roman, $matches);
    // Substract that from the number
    $n = $n % $value;
  }
 
  // The Roman numeral should be built, return it
  return $result;
}

?>