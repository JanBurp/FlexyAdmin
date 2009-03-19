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

function trace_($a="",$return=false) {
	static $c=0;
	$out="<pre class=\"debug\">trace: ";
	if (is_bool($a)) {
		if ($a)
			$out.="'True'";
		else
			$out.="'False'";
	}
	elseif (empty($a)) {
		$out.="#".$c++;
	}
	else
		$out.=print_r($a,true);
	$out.="</pre>";
	if (!$return) echo $out;
	return $out;
}

function array_($a,$return=false) {
	$out="<pre class=\"debug\">trace: ";
	$out.=print_r($a,true);
	$out.="</pre>";
	if (isset($a)) reset($a);
	if (!$return) echo $out;
	return $out;
}

?>
