<?
/**
 * FlexyAdmin V1
 *
 * MY_array_helper.php Created on 15-okt-2008
 *
 * @author Jan den Besten
 *
 * adds some functions to the array helper
 *
 */


/**
 * function el($name,$arr,$default=NULL)
 *
 * Shorter version of CI's element() with NULL as default (instead of FALSE) when element doesn't exists
 *
 * @param string 	$name					Name of element
 * @param array 	$arr 					Array
 * @param mixed 	$default=NULL Default value if element not set
 * @return mixed Returns default value if element not set, otherwise the element's data
 */
function el($name,$arr,$default=NULL) {
	return element($name,$arr,$default);
}

/**
 * function array2object($array)
 */
function array2object($array) {
	if (is_array($array)) {
		$obj = new StdClass();
		foreach ($array as $key => $val){
			$obj->$key = $val;
		}
	}
	else { $obj = $array; }
	return $obj;
}

/**
 * function object2array($object)
 */
function object2array($object) {
	if (is_object($object)) {
		foreach ($object as $key => $value) {
			$array[$key] = $value;
		}
	}
	else { $array = $object; }
	return $array;
}

function array2php($array,$tabs=1) {
	$php="array(\n";
	$sub="";
	foreach($array as $key=>$value) {
		if (!empty($sub)) $sub.=",\n";
		$sub.=repeater("\t",$tabs);
		$sub.='"'.$key.'"=>';
		if (is_array($value)) $sub.=array2php($value,$tabs+1);
		else $sub.="'$value'";
	}
	$php.="$sub\n".repeater("\t",$tabs).")";
	return $php;
}


function array2xml($array,$tabs=0) {
	if ($tabs<=0)
		$xml='<?xml version="1.0" encoding="ISO-8859-1"?>'."\n\n";
	else
		$xml="\n";
	$sub="";
	foreach($array as $key=>$value) {
		$sub.=repeater("\t",$tabs);
		$sub.="<$key>";
		if (is_array($value)) {
			$sub.=array2xml($value,$tabs+1);
			$sub.=repeater("\t",$tabs);
		}
		else {
			if (in_string("<&>",$value))
				$sub.="<![CDATA[$value]]>";
			else
				$sub.=$value;
		}
		$sub.="</$key>\n";
		if ($tabs==0) $sub.="\n";
	}
	$xml.="$sub";
	return $xml;
}

// function xml2array($xml) {
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

/**
 * xml2array() will convert the given XML text to an array in the XML structure.
 * Link: http://www.bin-co.com/php/scripts/xml2array/
 * Arguments : $contents - The XML text
 *             $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
 *             $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
 * Examples: $array =  xml2array(file_get_contents('feed.xml'));
 *           $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
 */
function xml2array($contents, $get_attributes=true, $priority = 'tag') {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
       //print "'xml_parser_create()' function not found!";
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
function not_filter_by($a,$p) {
	foreach($a as $k=>$i) {
		if (is_array($i)) {
			if (!strncmp($k,$p,strlen($p))) unset($a[$k]);
		}
		else {
		 if (!strncmp($i,$p,strlen($p))) unset($a[$k]);
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


function combine($k,$v) {
	$CI =& get_instance();
	if ($CI->config->item('PHP_version')==5) {
		$out=array_combine($k,$v);
	}
	elseif (empty($k) or empty($v) or count($k)!=count($v)) {
		$out=false;
	}
	else {
		foreach ($k as $key) {
			$out[$key]=current($v);
			next($v);
		}
	}
	return $out;
}

/**
 * Sort an assoc array by its value, can be used to sort a db return array by another field than its 'id'key
 */
function sort_by($a,$key,$desc=FALSE) {
	if ($desc) {
		$f = "return strnatcmp(\$b['$key'], \$a['$key']);";
		uasort($a, create_function('$a,$b', $f));
	}
	else {
		$f = "return strnatcmp(\$a['$key'], \$b['$key']);";
		uasort($a, create_function('$a,$b', $f));
	}
	return $a;
}


function find_row_by_value($a,$v) {
	$found=array();
	foreach ($a as $id=>$row) {
		if (in_array($v,$row)) $found[$id]=$row;
	}
	if (empty($found)) $found=FALSE;
	return $found;
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

?>