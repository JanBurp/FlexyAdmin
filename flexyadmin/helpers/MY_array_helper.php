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

/**
 * function filter_by($array,$prefix)
 *
 * Filters all (string) elements out that don't have the given prefix
 */
function filter_by($a,$p) {
	foreach($a as $k=>$i) { if (strncmp($i,$p,strlen($p))) unset($a[$k]);	}
	return $a;
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

// sort assoc array by assoc name
function sort_by($a, $s) {
	$f = "return strnatcmp(\$a['$s'], \$b['$s']);";
	usort($a, create_function('$a,$b', $f));
	return $a;
}


function find_row_by_value($a,$v) {
	foreach ($a as $id=>$row) {
		if (in_array($v,$row)) return $row;
	}
	return NULL;
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

?>