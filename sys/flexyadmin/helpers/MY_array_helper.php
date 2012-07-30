<?
/**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/array_helper.html" target="_blank">Array_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * @link http://codeigniter.com/user_guide/helpers/array_helper.html
 */


/**
 * Shorter version of CI's element() with NULL as default (instead of FALSE) when element doesn't exists
 *
 * @param string $name Name of element
 * @param array $arr
 * @param mixed $default[NULL] Default value if element not set
 * @return mixed Returns default value if element not set, otherwise the element's data
 */
function el($name,$arr,$default=NULL) {
	return element($name,$arr,$default);
}

/**
 * function array2object($array)
 */
function array2object($array,$recursive=TRUE) {
	$obj = new StdClass();
	foreach ($array as $key => $val){
		if (is_array($val)) $val=array2object($val,$recursive);
		$obj->$key = $val;
	}
	return $obj;
}

/**
 * function object2array($object)
 */
function object2array($object,$recursive=TRUE) {
	$array=array();
	foreach ($object as $key => $value) {
		if ($recursive and is_object($value)) $value=object2array($value,$recursive);
		$array[$key] = $value;
	}
	return $array;
}

function array2php($array,$tabs=1) {
	$php="array(\n";
	$sub='';
	foreach($array as $key=>$value) {
		if (!empty($sub)) $sub.=",\n";
		$sub.=repeater("\t",$tabs);
		$sub.="'".$key."'=>";
		if (is_array($value)) $sub.=array2php($value,$tabs+1);
		else $sub.="'$value'";
	}
	$php.="$sub\n".repeater("\t",$tabs).")";
	return $php;
}

function array2csv($array,$eol="\r\n") {
	$csv="";
	$comma=',';
	$firstLine=current($array);
	foreach (array_keys($firstLine) as $key) { $csv.=$key.$comma; }
	$csv=substr($csv,0,strlen($csv)-1).$eol;
	foreach($array as $key=>$row) {
		foreach ($row as $field => $value) {
			// check if needs to be enclosed with ""
			$enclose=preg_match('/[,"\n]/',$value);
			if (!$enclose) $enclose=(trim($value)!=$value);
			if ($enclose) $value='"'.$value.'"';
			$csv.=$value.$comma;
		}
		$csv=substr($csv,0,strlen($csv)-1).$eol;
	}
	return $csv;
}



// http://www.bin-co.com/php/scripts/array2json/
function array2json($arr) {
	if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality.
	$parts = array();
	$is_list = false;

	//Find out if the given array is a numerical array
	$keys = array_keys($arr);
	$max_length = count($arr)-1;
	if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
		$is_list = true;
		for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position
			if($i != $keys[$i]) { //A key fails at position check.
				$is_list = false; //It is an associative array.
				break;
			}
		}
	}

	foreach($arr as $key=>$value) {
		if(is_array($value)) { //Custom handling for arrays
			if($is_list)
				$parts[] = array2json($value); /* :RECURSION: */
			else
				$parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */
			}
		else {
			$str = '';
			if(!$is_list) $str = '"' . $key . '":';
			//Custom handling for multiple data types
			if(is_numeric($value)) $str .= $value; //Numbers
			elseif($value === false) $str .= 'false'; //The booleans
			elseif($value === true) $str .= 'true';
			else $str .= '"' . addslashes($value) . '"'; //All other things
			// :TODO: Is there any more datatype we should be in the lookout for? (Object?)
			$parts[] = $str;
		}
	}
	$json = implode(',',$parts);

	if($is_list) return '[' . $json . ']';//Return numerical JSON
	return '{' . $json . '}';//Return associative JSON
}



function array2xml($array,$keys=NULL,$attr=NULL,$tabs=0) {
	if ($tabs<=0)
		$xml='<?xml version="1.0" encoding="UTF-8"?>'."\n\n";
	else
		$xml="\n";
	$sub="";
	foreach($array as $key=>$value) {
		$sub.=repeater("\t",$tabs);
		if (isset($keys[$tabs])) {
			$at='';
			if (isset($attr[$keys[$tabs]])) {	foreach ($attr[$keys[$tabs]] as $a => $v) {	$at.=' '.$a.'="'.$v.'" ';	}	}
			$sub.="<$keys[$tabs]$at>";
		}
		else
			$sub.="<$key>";
		if (is_array($value)) {
			$sub.=array2xml($value,$keys,$attr,$tabs+1);
			$sub.=repeater("\t",$tabs);
		}
		else {
			if (in_string("<&>",$value))
				$sub.="<![CDATA[$value]]>";
			else
				$sub.=$value;
		}
		if (isset($keys[$tabs]))
			$sub.="</$keys[$tabs]>\n";
		else
			$sub.="</$key>\n";
		if ($tabs==0) $sub.="\n";
	}
	$xml.="$sub";
	return $xml;
}

// function xml2arraySimple($xml) {
// 	$xmlary = array();
// 	$xmlArray=array();
// 	$reels = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*)<\/\s*\\1\s*>)/s';
// 	$reattrs = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';
// 	preg_match_all($reels, $xml, $elements);
// 	foreach ($elements[1] as $ie => $xx) {
// 		$key=$elements[1][$ie];
// 		// TODO: xml attributes in xml2array
// 		// if ($attributes = trim($elements[2][$ie])) {
// 		// 	preg_match_all($reattrs, $attributes, $att);
// 		// 	foreach ($att[1] as $ia => $xx)	$xmlary[$ie]["attributes"][$att[1][$ia]] = $att[2][$ia];
// 		// }
// 		// $cdend = strpos($elements[3][$ie], "<");
// 		// if ($cdend > 0) {
// 		// 	$xmlary[$ie]["text"] = substr($elements[3][$ie], 0, $cdend - 1);
// 		// }
// 		
// 		if (preg_match($reels, $elements[3][$ie]))
// 			$value=xml2array($elements[3][$ie]);
// 		elseif (isset($elements[3][$ie])) {
// 			$value=$elements[3][$ie];
// 			$value=str_replace(array('<![CDATA[',']]>'),'',$value);
// 		}
// 		else
// 			$value='';
// 		$xmlArray[$key]=$value;
// 	}
// 
// 	return $xmlArray;
// }


// These function are used to reform a malformed XML (before r804)

function reformMalformedXML($xml) {
	return preg_replace('/<(\d+)>([^<]*)<(.*?)>(.*?)<\/(\d+)>/s','<$3>$2<$3>$4</$3>',$xml);
}
function reformXmlArrayKey($a,$rKey) {
	$r=$a;
	if (isset($a[$rKey]) and !empty($a[$rKey])) {
		$a=$a[$rKey];
		$r=array();
		$c=current($a);
		if (is_array($c)) {
			foreach ($a as $key => $value) {
				if (isset($value[$rKey])) {
          $newKey=$value[$rKey];
  				if (is_string($newKey) and !has_alpha($newKey))
  					$r[$value[$rKey]] = $value;
  				else
  					$r[] = $value;
				}
			}
		}
		else {
			$r[$a[$rKey]]=$a;
		}
	}
	return $r;
}


/**
 * 
 * @link: http://www.bin-co.com/php/scripts/xml2array/
 * @param string contents - The XML text
 * @param bool get_attributes. If this is TRUE the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
 * @param string priority - Can be 'tag' or 'attribute'. This will change the way the resulting array structure. For 'tag', the tags are given more importance.
 * @return array The parsed XML in an array form. Use print_r() to see the resulting array structure.
 *
 * xml2array() will convert the given XML text to an array in the XML structure.<br/>
 * <code>
 * $array =  xml2array(file_get_contents('feed.xml'));<br/>
 * $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
 * </code>
 */
function xml2array($contents, $get_attributes=true, $priority = 'tag') {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
       print "'xml_parser_create()' function not found!";
       return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if(!$xml_values) return; //Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();
        
        if(isset($value)) {
            if($priority == 'tag') $result = $value;
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if(isset($attributes) and $get_attributes) {
            foreach($attributes as $attr => $val) {
                if($priority == 'tag') $attributes_data[$attr] = $val;
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                $repeated_tag_index[$tag.'_'.$level] = 1;

                $current = &$current[$tag];

            } else { //There was another element with the same tag name

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    $repeated_tag_index[$tag.'_'.$level]++;
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2;
                    
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        unset($current[$tag.'_attr']);
                    }

                }
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                $current = &$current[$tag][$last_item_index];
            }

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

            } else { //If taken, put all things inside a list(array)
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    
                    if($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag.'_'.$level]++;

                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $get_attributes) {
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }
                        
                        if($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                }
            }

        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }
    
    return($xml_array);
}



/**
* @param string $cvs
* @param array  $fldnames array of fields names. Leave this to null to use the first row values as fields names.
* @param string $sep string used as a field separator (default ';')
* @param string $protect char used to protect field (generally single or double quote)
* @param array  $filters array of regular expression that row must match to be in the returned result. ie: array('fldname'=>'/pcre_regexp/')
* @return array
*/
function csv2array($csv,$fldnames=null,$sep=',',$protect='"',$filters=null){
	$csv=explode("\n",$csv);
	# use the first line as fields names
	if( is_null($fldnames)) {
		$fldnames = array_shift($csv);
		$fldnames = explode($sep,$fldnames);
		$fldnames = array_map('trim',$fldnames);
		if($protect) {
			foreach($fldnames as $k=>$v) $fldnames[$k] = preg_replace(array("/(?<!\\\\)$protect/","!\\\\($protect)!"),'\\1',$v);
		}            
	}
	elseif (is_string($fldnames)) {
		$fldnames = explode($sep,$fldnames);
		$fldnames = array_map('trim',$fldnames);
	}

	$i=0;
	foreach($csv as $row){
		if($protect) {
			$row = preg_replace(array("/(?<!\\\\)$protect/","!\\\\($protect)!"),'\\1',$row);
		}
		$row = explode($sep,trim($row));

		foreach($row as $fldnb=>$fldval) $res[$i][(isset($fldnames[$fldnb])?$fldnames[$fldnb]:$fldnb)] = $fldval;

		if (is_array($filters)) {
			foreach($filters as $k=>$exp){
				if(! preg_match($exp,$res[$i][$k]) )  unset($res[$i]);
			}
		}
		$i++;
	}
	return $res;
}





/**
 * function filter_by($array,$prefix)
 *
 * Filters all (string) elements out that don't have the given prefix
 */
function filter_by($a,$p) {
	foreach($a as $k=>$i) {
		if (is_array($i)) {
			if (strncmp($k,$p,strlen($p))) unset($a[$k]);
		}
		else {
		 if (strncmp($i,$p,strlen($p))) unset($a[$k]);
		}
	}
	return $a;
}
function not_filter_by($a,$ap) {
	if (!is_array($ap)) $ap=array($ap);
	foreach ($ap as $p) {
		foreach($a as $k=>$i) {
			if (is_array($i)) {
				if (!strncmp($k,$p,strlen($p))) unset($a[$k]);
			}
			else {
			 if (!strncmp($i,$p,strlen($p))) unset($a[$k]);
			}
		}
	}
	return $a;
}

function filter_by_key($a,$preKey,$replaceKey=FALSE) {
	$arr=array();
	$len=strlen($preKey);
	foreach ($a as $key => $value) {
		$newKey=$key;
		if ($replaceKey) $newKey=str_replace($preKey,'',$newKey);
		if (substr($key,0,$len)==$preKey) $arr[$newKey]=$value;
	}
	return $arr;
}

function array_merge_strict($a,$b) {
	$m=$a;
	foreach ($b as $key => $value) {
		if (!in_array($value,$m)) $m[]=$value;
	}
	return $m;
}

function array_change_key_name($orig,$new, &$array) {
	foreach ($array as $k => $v)
		$return[($k===$orig) ? $new:$k]=$v;
	return (array)$return;
}


/**
 * function ignorecase_sort(&$array)
 *
 * Sorts an array, case ignoring case
 */
function ignorecase_sort(&$array) {
  for ($i = 0; $i < sizeof($array); $array[$i] = strtolower($array[$i]).$array[$i], $i++);
  sort($array);
  for ($i = 0; $i < sizeof($array); $i++) {
    $a = $array[$i];
    $array[$i] = substr($a, (strlen($a)/2), strlen($a));
  }
}

function ignorecase_ksort(&$a) {
	$n=array();
	foreach($a as $k=>$v) {$n[strtolower($k)]=array("key"=>$k,"value"=>$v);}
	ksort($n,SORT_STRING);
	$a=array();
	foreach($n as $k=>$v)	{$a[$v["key"]]=$v["value"];}
}


/**
 * Sort an assoc array by its value, can be used to sort a db return array by another field than its 'id'key
 */
function sort_by($a,$keys,$desc=FALSE,$case=FALSE,$max=0) {
	if (!is_array($keys)) $keys=array($keys);
	if ($case) $comparefunction='strcasecmp'; else $comparefunction='strnatcmp';
	$key=array_shift($keys);
	if ($desc) {
		$f = "return $comparefunction(\$b['$key'], \$a['$key']);";
		uasort($a, create_function('$a,$b', $f));
	}
	else {
		$f = "return $comparefunction(\$a['$key'], \$b['$key']);";
		uasort($a, create_function('$a,$b', $f));
	}
	// slice
	if ($max>0) $a=array_slice($a,0,$max);
	// subsort
	if (count($keys)>0) {
		$nr=0;
		array_push($a,array());
		foreach ($a as $k => $val) {
			if (isset($v)) {
				if (!empty($val) and $val[$key]==$v) {
					// add to sub
					$b[$k]=$val;
				}
				else {
					// end of sub, start subsort
					if (count($b)>1) {
						// subsort
						$b=sort_by($b,$keys,FALSE,$case); // $desc standard
						$a=array_merge( array_slice($a,0,$start), $b, array_slice($a,$start+count($b)) );
					}
					unset($b);
					unset($v);
				}
			}
			if (!isset($v) and !empty($val))	{
				$v=$val[$key];
				$b=array();
				$b[$k]=$val;
				$start=$nr;
			}
			$nr++;
		}
		array_pop($a);
	}
	return $a;
}

function in_array_like($v,$a) {
	$in=false;
	$i=each($a);
	while (!empty($i['value']) and !$in) {
		if (strpos($i['value'],$v)!==false) $in=$i['key'];
		$i=each($a);
	}
	return $in;
}

function find_row_by_value($a,$v,$key='',$like=false) {
	$found=array();
	foreach ($a as $id=>$row) {
		if (empty($key)) {
			if ($like) {
				if (in_array_like($v,$row)) $found[$id]=$row;
			}
			else {
				if (in_array($v,$row)) $found[$id]=$row;
			}
		}
		else {
			if ($like) {
				if (isset($row[$key]) and has_string($v,$row[$key])) $found[$id]=$row;
			}
			else {
				if (isset($row[$key]) and $row[$key]==$v) $found[$id]=$row;
			}
		}
	}
	if (empty($found)) $found=FALSE;
	return $found;
}

function array_preg_search($val,$array) {
  return array_ereg_search($val,$array);
}
function array_ereg_search($val, $array) {
	$i = 0;
	$return = array();
	foreach($array as $v) {
  	if(preg_match("/$val/i", $v)) $return[] = $i;
	  $i++;
	}
	return $return;
}

function one_of_array_in_array($a,$b) {
	$in=false;
	foreach ($a as $k=>$v) {
		$in=$in || in_array($v,$b);
	}
	return $in;
}

function select_fields($a,$fields) {
	if (!is_array($fields)) { $fields=array($fields); }
	$out=array();
	foreach ($a as $id=>$row) {
		foreach ($fields as $f) {
			if (isset($row[$f])) $out[$id][$f]=$row[$f];
		}
	}
	return $out;
}

function implode_pre($i,$a,$pre) {
	$out=implode($i.$pre,$a);
	$out=$pre.$out;
	return $out;
}

function find_max($a,$k=NULL) {
	$max=NULL;
	foreach ($a as $key => $value) {
		if (is_array($value)) {
			if (isset($k))
				$val=$value[$k];
			else
				$val=current($value);
			if (!isset($max)) $max=$val;
			if ($val>$max) $max=$val;
		}
	}
	return $max;
}

function array_last($a) {
	$l=count($a);
	$s=array_slice($a,$l-1,1);
	return current($s);
}


function array_add_after($a,$key,$row) {
	if (!is_array($row)) $row=array($row);
	$firstslice=array();
	$k='';
	reset($a);
	$item=each($a);
	while ( $item and $item['key']!=$key ) {
		$firstslice[$item['key']]=$item['value'];
		array_shift($a);
		$item=each($a);
	}
	$firstslice[$item['key']]=$item['value'];
	array_shift($a);
	$item=each($a);
	return array_merge($firstslice,$row,$a);
}

function array_add_before($a,$key,$row) {
	if (!is_array($row)) $row=array($row);
	$firstslice=array();
	$k='';
	reset($a);
	$item=each($a);
	while ( $item and $item['key']!=$key ) {
		$firstslice[$item['key']]=$item['value'];
		array_shift($a);
		$item=each($a);
	}
	// $firstslice[$item['key']]=$item['value'];
	// array_shift($a);
	// $item=each($a);
	return array_merge($firstslice,$row,$a);
}

function implode_attributes($array) {
	$out='';
	foreach ($array as $key => $value) {
		$out.=$key.'="'.$value.'" ';
	}
	return $out;
}


function array_keep_keys($a,$keep) {
  foreach ($a as $key => $value) {
    if (!in_array($key,$keep)) unset($a[$key]);
  }
  return $a;
}

function array_unset_keys($a,$unset) {
  foreach ($a as $key => $value) {
    if (in_array($key,$unset)) unset($a[$key]);
  }
  return $a;
}


?>