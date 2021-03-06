<?php
/** \ingroup helpers
 * Uitbreiding op [Array_helper van CodeIgniter](http://codeigniter.com/user_guide/helpers/array_helper.html)
 * 
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 * @file
 */


/**
 * Net als element() (van CodeIgniter) maar $name kan een array van keys bevatten en zo een element uit een muldi-dimensionale array teruggeven
 * 
 * @param mixed $name
 * @param array $arr
 * @param mixed $default default=NULL
 * @return mixed
 */
function el($name,$arr,$default=NULL) {
  if (!is_array($name)) $name=array($name);
  foreach ($name as $key) {
    if (!is_array($arr)) break;
    $arr=element($key,$arr,$default);
  }
  if ($arr===null) $arr=$default;
	return $arr;
}


/**
 * Net als el() (van CodeIgniter) maar $default wordt nu ook teruggegeven als het element empty is
 * 
 * @param mixed $name
 * @param array $arr
 * @param mixed $default default=NULL
 * @return mixed
 */
function elm($name,$arr,$default=NULL) {
  if (!is_array($name)) $name=array($name);
  foreach ($name as $key) {
    if (!is_array($arr)) break;
    $arr=element($key,$arr,$default);
  }
  if ($arr===null or empty($arr)) $arr=$default;
	return $arr;
}

/**
 * Zet een item in een (multidimensionele) array
 *
 * @param array $arr array
 * @param mixed $name string of array van strings
 * @param mixed $value 
 * @return void
 * @author Jan den Besten
 */
function set_el( $arr, $name, $value) {
  if (!is_array($name)) $name=array($name);
  do {
    $key=array_shift($name);
    // Zorg ervoor dat de entry iig bestaat
    if (!isset($arr[$key])) $arr[$key] = NULL;
    // Laatste in de rij?
    if (count($name)===0)
      $arr[$key] = $value;
    // Nog niet de laatste
    else
      $arr[$key] = set_el( $arr[$key],$name,$value );
  } while ($key);
  return $arr;
}


/**
 * Maakt een object van een array
 *
 * @param string $array 
 * @param string $recursive default=TRUE
 * @return object
 * @author Jan den Besten
 */
function array2object($array,$recursive=TRUE) {
  if (empty($array)) return $array;
	$obj = new StdClass();
	foreach ($array as $key => $val){
		if (is_array($val)) $val=array2object($val,$recursive);
		$obj->$key = $val;
	}
	return $obj;
}

/**
 * Maakt een array van een object
 *
 * @param string $object 
 * @param string $recursive default=TRUE
 * @return array
 * @author Jan den Besten
 */
function object2array($object,$recursive=TRUE) {
	$array=array();
  if (!empty($object)) {
  	foreach ($object as $key => $value) {
  		if ($recursive and is_object($value)) $value=object2array($value,$recursive);
  		$array[$key] = $value;
  	}
  }
	return $array;
}

/**
 * Maakt van een array een PHP string
 *
 * @param string $array 
 * @param string $tabs default=1
 * @param string $eol  default="\n"
 * @return string
 * @author Jan den Besten
 */
function array2php($array,$tabs=1,$eol="\n") {
	$php="array(".$eol;
	$sub='';
	foreach($array as $key=>$value) {
		if (!empty($sub)) $sub.=",".$eol;
		$sub.=str_repeat("\t",$tabs);
		$sub.="'".$key."'=>";
		if (is_array($value)) $sub.=array2php($value,$tabs+1);
		else $sub.="'$value'";
	}
	$php.="$sub".$eol.str_repeat("\t",$tabs).")";
	return $php;
}

/**
 * Maakt een CSV (Comma Seperated Values) string van een array
 *
 * @param string $array 
 * @param string $eol default="\r\n"
 * @return string
 * @author Jan den Besten
 */
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



/**
 * Maakt JSON van een array. Is net anders dan de standaard PHP functie json_encode():
 * - Arrays blijven arrays in dezelfde volgorde
 * 
 * Zie http://www.bin-co.com/php/scripts/array2json/
 *
 * @param string $arr
 * @return string JSON
 */
function array2json($arr) {
	$parts = array();
	$is_list = FALSE;

	// Find out if the given array is a numerical array
	$keys = array_keys($arr);
	$max_length = count($arr)-1;
  $is_list=TRUE;
  for($i=0; $i<count($keys); $i++) {
    if(!is_integer($keys[0])) {
      $is_list = FALSE; //It is an associative array.
      break;
    }
  }

	foreach($arr as $key=>$value) {
    $prefix = get_prefix($key);
		if(is_array($value)) { //Custom handling for arrays
			if($is_list)
				$parts[] = array2json($value); /* :RECURSION: */
			else
				$parts[] = '"'.$key.'":'.array2json($value); /* :RECURSION: */
			}
		else {
			$str = '';
			if (!$is_list) $str = '"' . $key . '":';
			
			// Custom handling for multiple data types:
      // Booleans
      if (in_array($prefix,array('b','is','has')) and (is_bool($value) or (is_numeric($value) and $value>=0 and $value<=1) or in_array($value,array('true','false'))) ) {
  			if ($value == false)
          $str.= 'false';
  			else
          $str.= 'true';       
      }
      // Numbers
      elseif( is_numeric($value) and preg_match('/[^\d]/u',$value)===0 and (substr($value,0,1)!=='0' or $value==0)) {
      	$str.= $value;
      }
      // Other booleans
			elseif($value === false) {
				$str.= 'false';
			}
			elseif($value === true) {
				$str.= 'true';
			}
      // Strings - Default
      else {
      	$value = preg_replace('/[\x00-\x09\x0B-\x1F\x7F]/u', '', $value);
      	$str.='"'.addcslashes ($value, '"'."\n\r".chr(92)).'"'; // All other things: escape double quotes, backslash and newlines
      }
			$parts[] = $str;
		}
	}
	$json = implode(',',$parts);

	if($is_list) return '[' . $json . ']'; //Return numerical JSON
	return '{' . $json . '}'; //Return associative JSON
}


/**
 * Maakt een array van gegeven JSON string
 *
 * @param string $json 
 * @return array
 * @author Jan den Besten
 */
function json2array($json) {
  return json_decode($json,true);
}

/**
 * Maakt XML van meegegeven array
 *
 * @param string $array 
 * @param string $keys default=NULL
 * @param string $attr default=NULL
 * @param string $tabs default=0
 * @return string XML
 * @author Jan den Besten
 */
function array2xml($array,$keys=NULL,$attr=NULL,$tabs=0) {
	if ($tabs<=0)
		$xml='<?xml version="1.0" encoding="UTF-8"?>'."\n\n";
	else
		$xml="\n";
	$sub="";
	foreach($array as $key=>$value) {
		$sub.=str_repeat("\t",$tabs);
		if (isset($keys[$tabs])) {
			$at='';
			if (isset($attr[$keys[$tabs]])) {	foreach ($attr[$keys[$tabs]] as $a => $v) {	$at.=' '.$a.'="'.$v.'" ';	}	}
			$sub.="<$keys[$tabs]$at>";
		}
		else
			$sub.="<$key>";
		if (is_array($value)) {
			$sub.=array2xml($value,$keys,$attr,$tabs+1);
			$sub.=str_repeat("\t",$tabs);
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



/**
 * Repareert verkeerde XML (ivm oude r805 code)
 *
 * @param string $xml 
 * @return string
 * @author Jan den Besten
 */
function reformMalformedXML($xml) {
	return preg_replace('/<(\d+)>([^<]*)<(.*?)>(.*?)<\/(\d+)>/s','<$3>$2<$3>$4</$3>',$xml);
}

/**
 * Repareert XML Array Keys (ivm oude r805 code)
 *
 * @param array $a 
 * @param string $rKey 
 * @return array
 * @author Jan den Besten
 */
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
 * Maakt een array van meegegeven XML
 * Zie http://www.bin-co.com/php/scripts/xml2array/
 * 
 * Voorbeeld:
 * 
 *     $array =  xml2array(file_get_contents('feed.xml'));
 *     $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
 * 
 * @param string $contents XML
 * @param bool $get_attributes default=TRUE If this is TRUE the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
 * @param string $priority  default='tag' - Can be 'tag' or 'attribute'. This will change the way the resulting array structure. For 'tag', the tags are given more importance.
 * @return array The parsed XML in an array form.
 */
function xml2array($contents, $get_attributes=true, $priority = 'tag') {
  
//   $xml = simplexml_load_string($contents);
//   $json = json_encode($xml);
//   $array = json_decode($json,TRUE);
//
//   return $array;
// }
//
  
  
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
 * Maakt een array van meegegeven CSV
 * 
 * @param string $csv
 * @param array $fldnames default=NULL array of fields names. Leave this to null to use the first row values as fields names.
 * @param string $sep default=',' string used as a field separator
 * @param string $protect default='"' char used to protect field (generally single or double quote)
 * @param array  $filters default=NULL array of regular expression that row must match to be in the returned result. ie: array('fldname'=>'/pcre_regexp/')
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
 * Geeft alle elementen uit de associatieve array die het meegegeven prefix in hun value hebben
 *
 * @param string $arr Array 
 * @param string $prefix Prefix
 * @return array
 * @author Jan den Besten
 */
function filter_by($arr,$prefix) {
	foreach($arr as $k=>$i) {
		if (is_array($i)) {
			if (strncmp($k,$prefix,strlen($prefix))) unset($arr[$k]);
		}
		else {
		 if (strncmp($i,$prefix,strlen($prefix))) unset($arr[$k]);
		}
	}
	return $arr;
}

/**
 * Geeft alle elementen uit de associatieve array die NIET de meegegeven prefix(en) in hun waarde hebben
 *
 * @param array $a Array
 * @param array $ap Prefix(en) eentje als een string, of meerdere als een array van strings
 * @return array
 * @author Jan den Besten
 */
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

/**
 * Geeft alle elementen uit de associatieve array met de gegeven key. De key kan tegelijkertijd vervangen worden door een nieuwe.
 *
 * @param string $a Array
 * @param string $preKey Key
 * @param string $replaceKey default=FALSE Geef hier eventueel een nieuwe key 
 * @return array
 * @author Jan den Besten
 */
function filter_by_key($a,$preKey,$replaceKey=FALSE) {
	$arr=array();
	$len=strlen($preKey);
	foreach ($a as $key => $value) {
		$newKey=$key;
		if ($replaceKey!==false) $newKey=str_replace($preKey,'',$newKey);
		if (substr($key,0,$len)==$preKey) $arr[$newKey]=$value;
	}
	return $arr;
}

/**
 * Geeft alle elementen waarvan de key de meegegeven prefix hebben
 *
 * @param array $a 
 * @param string $prefix 
 * @return array
 * @author Jan den Besten
 */
function filter_by_prefix( $a, $prefix ) {
	$len=strlen($prefix);
	foreach ($a as $key => $value) {
		if (substr($key,0,$len)!==$prefix) unset($a[$key]);
	}
	return $a;
}


/**
 * Geeft alle elemeten uit de associatieve array die NIET de meegegeven (prefix van de) key(s) hebben.
 *
 * @param string $a 
 * @param mixed $keys string of arrat van strings
 * @return array
 * @author Jan den Besten
 */
function not_filter_by_key($a,$keys) {
	$arr=array();
  if (!is_array($keys)) $keys = array($keys);
	foreach ($a as $arrkey => $value) {
    $found = false;
    foreach ($keys as $key) {
      $len = strlen($key);
      if (substr($arrkey,0,$len)===$key) $found = true;
    }
    if (!$found) $arr[$arrkey] = $value;
	}
	return $arr;
}

/**
 * implode array maar dan van de key in de multidimensionale array
 *
 * @param string $split 
 * @param string $array 
 * @param string $key 
 * @return string
 * @author Jan den Besten
 */
function implode_key($split,$array,$key) {
  $s='';
  foreach ($array as $k => $item) {
    $s=add_string($s,$item[$key],$split);
  }
  return $s;
}

/**
 * Als array_merge() maar waarden worden niet overschreven
 *
 * @param string $a 
 * @param string $b 
 * @return void
 * @author Jan den Besten
 */
function array_merge_strict($a,$b) {
	$m=$a;
	foreach ($b as $key => $value) {
		if (!in_array($value,$m)) $m[]=$value;
	}
	return $m;
}

/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param array $array2
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
function array_merge_recursive_distinct(array &$array1, array &$array2 ) {
  $merged = $array1;
  foreach ( $array2 as $key => &$value ) {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    }
    else {
      $merged [$key] = $value;
    }
  }
  return $merged;
}



/**
 * Vervangt gegeven key in de array in nieuwe key
 *
 * @param string $orig Te zoeken key
 * @param string $new Te vervangen key
 * @param array &$array Array
 * @return array
 * @author Jan den Besten
 */
function array_change_key_name($orig,$new, &$array) {
	foreach ($array as $k => $v)
		$return[($k===$orig) ? $new:$k]=$v;
	return (array)$return;
}


/**
 * Sorteert een array, niet lettend op case
 *
 * @param array &$array 
 * @return void
 * @author Jan den Besten
 * @deprecated
 */
function ignorecase_sort(&$array) {
  for ($i = 0; $i < sizeof($array); $array[$i] = strtolower($array[$i]).$array[$i], $i++);
  sort($array);
  for ($i = 0; $i < sizeof($array); $i++) {
    $a = $array[$i];
    $array[$i] = substr($a, (strlen($a)/2), strlen($a));
  }
}

/**
 * Als ksort() Maar dan niet case gevoelig
 *
 * @param array &$a 
 * @return void
 * @author Jan den Besten
 */
function ignorecase_ksort(&$a) {
	$n=array();
	foreach($a as $k=>$v) {$n[strtolower($k)]=array("key"=>$k,"value"=>$v);}
	ksort($n,SORT_STRING);
	$a=array();
	foreach($n as $k=>$v)	{$a[$v["key"]]=$v["value"];}
}



/**
 * Sorteert een associatieve array met de values van een bepaalde key
 * 
 * Hiermee kun je het een resultaat array van de database opnieuw sorteren (anders dan op de key)
 *
 * @param array $a 
 * @param array $keys array van keys waarvan de values moeten worden gesorteerd
 * @param bool $desc default=FALSE Als TRUE dan wordt de volgorde andersom
 * @param bool $case default=FALSE case-gevoeligheid
 * @param int $max default=0
 * @return array
 * @author Jan den Besten
 */
function sort_by($array,$keys,$desc=FALSE,$case=FALSE,$max=0) {
	if (!is_array($keys)) $keys=array($keys);
	$key = array_shift($keys);
	// DESC
	if ($desc) {
		if ($case) {
			uasort($array, function($a,$b) use($key) {
				return strcasecmp($b[$key],$a[$key]);
			});	
		}
		else {
			uasort($array, function($a,$b) use($key) {
				return strnatcmp($b[$key],$a[$key]);
			});	
		}
	}
	// ASC
	else {
		if ($case) {
			uasort($array, function($a,$b) use($key) {
				return strcasecmp($a[$key],$b[$key]);
			});	
		}
		else {
			uasort($array, function($a,$b) use($key) {
				return strnatcmp($a[$key],$b[$key]);
			});	
		}
	}
	// slice
	if ($max>0) $array=array_slice($array,0,$max);
	// subsort
	if (count($keys)>0) {
		$nr=0;
		array_push($array,array());
		foreach ($array as $k => $val) {
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
						$array=array_merge( array_slice($array,0,$start), $b, array_slice($array,$start+count($b)) );
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
		array_pop($array);
	}
	return $array;
}


/**
 * Sorteert associatieve array op volgorde van meegegeven keys, een eventueel restant komt daarachter
 *
 * @param array $a 
 * @param array $keys 
 * @return array
 * @author Jan den Besten
 */
function sort_keys($a,$keys) {
  $b=array();
  foreach ($keys as $key) {
    if (isset($a[$key])) {
      $b[$key]=$a[$key];
      unset($a[$key]);
    }
  }
  $b=array_merge($b,$a);
  return $b;
}


/**
 * Sorteert array op lengte van de waarden
 *
 * @param array $a 
 * @return array
 * @author Jan den Besten
 */
function array_sort_length($a) {
  usort($a, function($a, $b) {
    return strlen($b) - strlen($a);
  });
  return $a;
}


/**
 * Zoekt in associatieve array eerst gevonden waarde die lijkt op meegegeven waarde
 *
 * @param string $v Te zoeken waarde
 * @param string $a Array
 * @param int $offset [startpunt van te zoeken 'like' string]
 * @return mixed FALSE of gevonden key
 * @author Jan den Besten
 */
function in_array_like($v,$a,$offset=false) {
	$in=false;
	$i=current($a);
	while (!empty($i) and !$in) {
		$pos = strpos($i,$v);
		if ($pos!==false) {
			if ($offset===false or $pos===$offset) {
				$in=key($a);
			}
		}
		$i=next($a);
	}
	return $in;
}

/**
 * Zoekt alle rijen waarbinnen een waarde voorkomt (eventueel in specifieke keys) en geeft deze waarden in een array terug.
 *
 * @param array $a array waarin gezocht wordt
 * @param mixed $v waarde die gezocht wordt
 * @param string $key[''] Eventueel mee te geven key waarin gezoch moet worden
 * @param bool $like default=FALSE als TRUE dan wordt gezocht naar een waarde die erop lijkt ipv precies gelijk is, of een vaste positie
 * @return array
 * 
 * @author Jan den Besten
 */
function find_row_by_value($a,$v,$key='',$like=false) {
	$found=array();
	foreach ($a as $id=>$row) {
		if (empty($key) and is_array($row)) {
			if ($like!==false) {
				if (in_array_like($v,$row,$like)) $found[$id]=$row;
			}
			else {
				if (in_array($v,$row)) $found[$id]=$row;
			}
		}
		elseif (is_array($row)) {
			if ($like===true) {
				if (isset($row[$key]) and has_string($v,$row[$key])) $found[$id]=$row;
			}
			elseif ($like!==false) {
				if (isset($row[$key]) and strpos($row[$key],$v)===$like) $found[$id]=$row;	
			}
			else {
				if (isset($row[$key]) and $row[$key]==$v) $found[$id]=$row;
			}
		}
    else {
			if ($like!==false) {
				if (has_string($v,$row)) $found[$id]=$row;
			}
			elseif ($like!==false) {
				if (isset($row) and strpos($row,$v)===$like) $found[$id]=$row;	
			}
			else {
				if ($row==$v) $found[$id]=$row;
			}
    }
	}
  // if (empty($found)) $found=FALSE;
	return $found;
}

/**
 * Zelfde als array_ereg_search()
 *
 * @param string $val 
 * @param string $array 
 * @return array
 * @author Jan den Besten
 * @deprecated
 */
function array_preg_search($val,$array) {
  return array_ereg_search($val,$array);
}

/**
 * Geeft array terug van keys waar de meegegeven (regex) zoekterm gevonden is
 *
 * @param string $val (regex) zoekterm
 * @param array $array 
 * @param string $key default=FALSE, eventueel kun je een key meegegeven als het een multidimensionele array betreft
 * @return array
 * @author Jan den Besten
 */
function array_ereg_search($val, $array, $key=false) {
	$return = array();
	foreach($array as $i=>$v) {
    if ($key) $v=$v[$key];
  	if (preg_match("/$val/i", $v)) $return[] = $i;
	}
	return $return;
}

/**
 * Zoekt of een waarde van de ene array in de andere array voorkomt
 *
 * @param array $a 
 * @param array $b 
 * @return mixed FALSE als niet gevonden, anders de eerste key
 * @author Jan den Besten
 */
function one_of_array_in_array($a,$b) {
	$in=false;
  $key=false;
	foreach ($a as $k=>$v) {
    $key=array_search($v,$b);
		$in=$in || $key;
    if ($in) break;
	}
  if ($in) $in=$key;
	return $in;
}

/**
 * Geeft array terug met alleen de keys gespecificeerd in de 2e array
 *
 * @param array $a Array
 * @param array $fields Array van keys die meegenomen worden
 * @return array
 * @author Jan den Besten
 */
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

/**
 * Als implode() maar plakt voor elk element een prefix
 *
 * @param string $i Implode karakter
 * @param string $a Array
 * @param string $pre Prefix
 * @return string
 * @author Jan den Besten
 */
function implode_pre($i,$a,$pre) {
	$out=implode($i.$pre,$a);
	$out=$pre.$out;
	return $out;
}

/**
 * Vind de maximale waarde binnen de array (in eventueel meegegeven key)
 *
 * @param array $a 
 * @param string $k default=NULL  
 * @return int
 * @author Jan den Besten
 */
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

/**
 * Geeft laatste element van array
 *
 * @param array $a 
 * @return mixed
 * @author Jan den Besten
 */
function array_last($a) {
	$l=count($a);
	$s=array_slice($a,$l-1,1);
	return current($s);
}

/**
 * Voegt een item toe aan een array, na een array element met gegeven key
 *
 * @param array $a Array
 * @param string $key Key
 * @param mixed $row Toe te voegen item
 * @return array
 * @author Jan den Besten
 */
function array_add_after($a,$key,$row) {
	// normal
	if (!is_assoc($a)) {
		$offset = array_search($key, $a) + 1;
		$row = array($row);
	  return array_merge( array_slice($a, 0, $offset, true), $row, array_slice($a, $offset, NULL, true) );
	}
	// assoc
	$offset = array_search($key, array_keys($a)) + 1;
  $first_part = array_slice($a, 0, $offset, true);
  $last_part  = array_slice($a, $offset, NULL, true);
  $result = array_merge($first_part, $row, $last_part);
	return $result;
}

/**
 * Voegt element toe voor een item met gegeven key
 *
 * @param string $a Array
 * @param string $key Key
 * @param string $row Toe te voegen item
 * @return array
 * @author Jan den Besten
 */
function array_add_before($a,$key,$row) {
	// normal
	if (!is_assoc($a)) {
		$offset = array_search($key, $a);
		$row = array($row);
	  return array_merge( array_slice($a, 0, $offset, true), $row, array_slice($a, $offset, NULL, true) );
	}
	// assoc
	$offset = array_search($key, array_keys($a));
	return array_slice($a, 0, $offset, true)
					+ $row
					+ array_slice($a, $offset, NULL, true);
}



/**
 * Voegt een associatief array element toe aan het begin van een array
 *
 * @param array $arr 
 * @param string $key 
 * @param mixed $val 
 * @return array
 * @author Jan den Besten
 */
function array_unshift_assoc($arr,$key,$val) {
  $arr = array_reverse($arr, true);
  $arr[$key] = $val;
  return array_reverse($arr, true);
}

/**
 * Voegt een associatief array element toe aan het eind van een array
 *
 * @param array $arr 
 * @param string $key 
 * @param mixed $val 
 * @return array
 * @author Jan den Besten
 */
function array_push_assoc($arr,$key,$val) {
  $arr[$key]=$val;
  return $arr;
}


/**
 * Implode associative array
 *
 * @param string $glue 
 * @param array $array 
 * @return string
 * @author Jan den Besten
 */
function implode_assoc($glue,$array) {
  $out='';
  foreach ($array as $key => $value) {
    $out.=$key.' => "'.$value.'"'.$glue;
  }
  $out=substr($out,0,strlen($out)-strlen($glue));
  return $out;
}



/**
 * Maakt van associatieve array een string van attributen
 * 
 *      array( 'class'=>'red', title=>'rood' );
 * 
 * Wordt:
 * 
 *      class="red" title="rood"
 *
 * @param array $array 
 * @return string
 * @author Jan den Besten
 */
function implode_attributes($array) {
	$out='';
	foreach ($array as $key => $value) {
		$out.=$key.'="'.$value.'" ';
	}
	return $out;
}


/**
 * Maakt van een string met attributen een associatieve array met de attributen
 *
 *      class="red" title="rood"
 * 
 * Wordt:
 * 
 *      array( 'class'=>'red', title=>'rood' );
 *
 * @param string $s 
 * @return array
 * @author Jan den Besten
 */
function explode_attributes($s) {
  $attributes=array();
  if (preg_match_all("/\\s?(.*)=\"(.*)\"/uiU", $s,$matches)) {
    if (isset($matches[2])) {
      foreach ($matches[1] as $key => $value) {
        $attributes[trim($value)] = $matches[2][$key];
      }
      return $attributes;
    }
  }
  return false;
}


/**
 * Geeft een array terug met alleen de key/value paren die meegegeven zijn
 *
 * @param array $a 
 * @param arra $keep array van te bewaren keys
 * @return array
 * @author Jan den Besten
 */
function array_keep_keys($a,$keep) {
  if (!is_array($a) or !is_array($keep)) return $a;
  foreach ($a as $key => $value) {
    if (!in_array($key,$keep)) unset($a[$key]);
  }
  return $a;
}

/**
 * Geeft een array zonder de meegegeven key/value paren
 *
 * @param array $a 
 * @param array $unset array van keys die verwijderd worden
 * @param bool $recursive default=FALSE als TRUE dan word de array gezien als een multidimensionale array en kijkt die in elke tak
 * @return array
 * @author Jan den Besten
 */
function array_unset_keys($a,$unset,$recursive=FALSE) {
  foreach ($a as $key => $value) {
    if (in_array($key,$unset)) unset($a[$key]);
    if ($recursive and is_array($value)) $a[$key]=array_unset_keys($value,$unset);
  }
  return $a;
}

/**
 * Hernoemt de meegegeven keys
 *
 * @param array $a 
 * @param array $rename 
 * @param bool $keep_all  default=TRUE
 * @return array
 * @author Jan den Besten
 */
function array_rename_keys($a,$rename=array(),$keep_all=true) {
  $b = array();
  foreach ($a as $key => $value) {
    if (isset($rename[$key])) {
      $b[$rename[$key]]=$a[$key];
      unset($a[$key]);
    }
    else {
      if ($keep_all) $b[$key]=$a[$key];
    }
  }
  return $b;
}


/**
 * Verwijderd dubbele items uit een multidimensionale array (een array met arrays dus)
 *
 * @param string $input De array met mogelijk dubbele items
 * @param array $keys[''] Geef hier eventueel een array van keys waarop gecheckt moet worden (je kunt zo bepalen welke keys wel/niet dubbel mogen zijn) 
 * @return array De array met alle dubbele items verwijderd
 * @author Jan den Besten
 */
function array_unique_multi($input, $keys='') {
  $array=$input;
  if (!empty($keys)) {
    $keys=array_combine($keys,$keys);
    // remove the keys wich are not checked for
    foreach ($array as $id => $row) {
      $array[$id]=array_intersect_key($row,$keys);
    }
  }
  $serialized = array_map('serialize', $array);
  $unique = array_unique($serialized);
  $unique = array_intersect_key($array, $unique);
  if (!empty($keys)) {
    // return array with full keys
    foreach ($unique as $key => $value) {
      $unique[$key]=$input[$key];
    }
  }
  return $unique;
}


/**
 * Berekend het verschil van een multidimensionale array. Gaat er wel vanuit dat de arrays dezelfde keys hebben
 *
 * @param array $a 
 * @param array $b 
 * @return array het vershil
 * @author Jan den Besten
 */
function array_diff_multi($a,$b) {
  $diff = array();
  foreach ($a as $akey => $avalue) {
    if (!isset($b[$akey])) {
      $diff[$akey]=$avalue;
    }
    else {
      if (!is_array($avalue)) {
        if ($avalue!=$b[$akey]) $diff[$akey]=$b[$akey];
      }
      else {
        $diff[$akey]=array_diff_multi($avalue,$b[$akey]);
      }
    }
  }
  return $diff;
}


/**
 * Test of een array associatieve keys heeft 
 *
 * @param array $a
 * @return bool TRUE als de array associatieve keys heeft
 * @author Jan den Besten
 */
function is_assoc($a){
  return (bool)count(array_filter(array_keys($a), 'is_string'));
}

/**
 * Test of een array multidimensionaal is
 *
 * @param string $array 
 * @return bool
 * @author Jan den Besten
 */
function is_multi($array) {
  return (count($array) != count($array, COUNT_RECURSIVE));
}


/**
 * Maakt van een multidimensionale array een 'platte' array door de subarray te vervangen door de 1e waarde ervan
 *
 * @param array $a 
 * @return array
 * @author Jan den Besten
 */
function array_flatten($a) {
  foreach ($a as $key => $value) {
    $value=current($value);
    $a[$key]=$value;
  }
  return $a;
}



/**
 * Set a value in a multidimensional array with an array of keys given
 *
 * @param array &$a 
 * @param array $multikey 
 * @param string $value 
 * @return array
 * @author Jan den Besten
 */
function array_set_multi_key(&$a,$multikey,$value) {
  switch (count($multikey)) {
    case '1': $a[$multikey[0]] = $value; break;
    case '2': $a[$multikey[0]][$multikey[1]] = $value; break;
    case '3': $a[$multikey[0]][$multikey[1]][$multikey[2]] = $value; break;
    case '4': $a[$multikey[0]][$multikey[1]][$multikey[2]][$multikey[3]] = $value; break;
    case '5': $a[$multikey[0]][$multikey[1]][$multikey[2]][$multikey[3]][$multikey[4]] = $value; break;
    case '6': $a[$multikey[0]][$multikey[1]][$multikey[2]][$multikey[3]][$multikey[4]][$multikey[5]] = $value; break;
  }
  return $a;
}



/**
 * Group an array by its keys
 *
 * @param string $a 
 * @param string $split['']
 * @return void
 * @author Jan den Besten
 */
function array_group_by($a,$split='') {
  ksort($a);
  $groups = array();
  foreach ($a as $key=>$val) {
    if ($split) {
      $group=get_prefix($key,$split);
      $key=remove_prefix($key,$split);
      $groups[$group][$key]=$val;
    }
    else {
      $groups[$key][]=$val;  
    }
  }
  return $groups;
}

/**
 * Transform multidimensional array to one dimensional array
 *
 * @param array $a 
 * @param string $field 
 * @return array
 * @author Jan den Besten
 */
function flatten_array_by_field($a,$field) {
  $flatten=array();
  foreach ($a as $key=>$row) {
    $flatten[$key]=$row[$field];
  }
  return $flatten;
}

/**
 * Loopt alle items in de array (recursief) langs en verwijderd empty waarden
 *
 * @param array $a 
 * @return array $a
 * @author Jan den Besten
 */
function array_unset_empty_values($a) {
  foreach ($a as $key => $value) {
    if (is_array($value)) {
      $value=array_unset_empty_values($value);
      $a[$key]=$value;
    }
    if (empty($value)) unset($a[$key]);
  }
  return $a;
}


?>