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

function trace_($a=NULL,$return=false) {
	static $c=0;
	$type=gettype($a);
	$out="<pre style=\"font-size:10px;z-index:1000;margin:2px;padding:2px;background-color:#ccc;color:#000;border:solid 1px #000;\"><span style=\"color:#fff;font-weight:bold;\">trace [#$c][$type]:</span>\n";
	if (is_bool($a)) {
		if ($a)
			$out.="'True'";
		else
			$out.="'False'";
	}
	elseif (is_array($a) or is_object($a)) {
		$out.=print_r(array_($a,true),true);
	}
	else
		$out.=print_r($a,true);
	$c++;
	$out.="</pre>";
	if (!$return) echo $out;
	return $out;
}

function array_($a) {
	$out=array();
	foreach($a as $key=>$value) {
		if (is_array($value)) {
			$out[$key]=array_($value);
		}
		else {
			if (in_string("<>&",$value) or (strlen($value)>100))
				$out[$key]="<span title=\"".htmlentities($value)."\" style=\"cursor:help;color:#fff;\">[text]</span>";
			else
				$out[$key]=$value;
		}
	}
	return $out;
}

?>
