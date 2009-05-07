<?
/**
 * FlexyAdmin V1
 *
 * debug_helper.php Created on 15-okt-2008
 *
 * @author Jan den Besten
 */


/**
 * Sends message to logfile
 *
 * @param string $type 		Type of message: 'info','error' or 'debug'
 * @param string $message The message to send
 */
function log_($type,$message) {
	log_message($type,"FlexyAdmin: $message");
}

/**
 * function err_($message)
 *
 * Sends message to logfile as error and gives error message
 *
 * @param string $message The message to send
 */

function err_($message) {
	show_error($message);
	log_('error',$message);
}

function backtrace_($return=false) {
	$dbgTrace = debug_backtrace();
	$out=array();
	foreach($dbgTrace as $key => $val) {
		unset($val['object']);
		if (isset($val['args'])) {
			if (count($val['args'])==0) unset($val['args']);
		}
		if (isset($val['file'])) {
			$explode=explode("/",$val['file']);
			$len=count($explode);
			if ($len>5)	$explode=array_slice($explode,$len-4);
			$file=implode('/',$explode);
			if (isset($val['line'])) {
				$val['file']='#show#<a style="color:#000;text-decoration:underline;" href="txmt://open?url=file:///'.$val['file'].'&amp;line='.$val['line'].'">'.$file.' at '.$val['line'].'</a>';
				unset($val['line']);
			}
		}
		if (isset($val['type'])) {
			$val['type']=htmlentities($val['type']);
			if (isset($val['function'])) {
				$val['class->method']=$val['class'].$val['type'].$val['function'];
				unset($val['function']);
				unset($val['type']);
				unset($val['class']);
			}
		}
		$out[$key]=$val;
	}
	if (!$return) $out=trace_($out);
	return $out;
}

function trace_($a=NULL,$return=false) {
	static $c=0;
	if ($a==NULL) {
		$a=backtrace_(true);
		unset($a[0]);
		$type="backtrace";
	}
	else
		$type=gettype($a);
	$out="<pre style=\"font-size:10px;z-index:99999;margin:2px;padding:2px;background-color:#ccc;color:#000;border:solid 1px #000;\"><span style=\"color:#fff;font-weight:bold;\">trace [#$c][$type]:</span>\n";
	if (is_bool($a)) {
		if ($a)
			$out.="'True'";
		else
			$out.="'False'";
	}
	elseif (is_array($a) or is_object($a)) {
		$out.=print_ar(array_($a,true),true);
	}
	else
		$out.=print_r($a,true);
	$c++;
	$out.="</pre>";
	if (!$return) echo $out."<hr/>";
	return $out;
}

function array_($a) {
	$out=array();
	foreach($a as $key=>$value) {
		if (is_array($value)) {
			$out[$key]=array_($value);
		}
		elseif (is_object($value)) {
			$out[$key]="{object}";
		}
		else {
			if ((substr($value,0,6)!="#show#") and (in_string("<>",$value) or (strlen($value)>100)) )
				$out[$key]="<span title=\"".htmlentities($value)."\" style=\"cursor:help;color:#fff;\">[text]</span>";
			else {
				$out[$key]=str_replace("#show#","",$value);
			}
		}
	}
	return $out;
}

function print_ar($array,$return=false,$tabs=0) {
	$out="Array:\n";
	foreach($array as $key=>$value) {
		$out.=repeater("\t",$tabs+1)."[".$key."] => ";
		if (is_array($value))
			$out.=print_ar($value,$return,$tabs+1);
		else
			$out.="'$value'\n";
	}
	if (!$return) echo $out;
	return $out;
}

?>
