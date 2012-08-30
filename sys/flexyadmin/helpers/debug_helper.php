<?
/**
 * Handige PHP debug tools
 * @author Jan den Besten
 */


/**
 * Voegt regel aan logbestand toe
 * 
 * Gebruikt CodeIgniters `log_message()` en voegt aan begin van $message: 'FlexyAdmin: '
 *
 * @param string $type type: 'info','error' or 'debug'
 * @param string $message
 */
function log_($type,$message) {
	log_message($type,"FlexyAdmin: $message");
}

/**
 * Laat foutmelding zien en stuur de foutmelding naar de logfile
 *
 * @param string $message
 */
 function err_($message) {
	show_error($message);
	log_('error',$message);
}

/**
 * Geeft een backtrace van laatste functie/method aanroepen (zie PHP's debug_backtrace())
 *
 * @param string $offset[0] Hoeveel stappen terug worden getoond
 * @param string $limit[10] Aantal stappen dat wordt getooond
 * @param bool $echo[TRUE]
 * @return string resultaat
 * @author Jan den Besten
 */
function backtrace_($offset=0,$limit=10,$echo=true) {
	if ($echo) return trace_(NULL,$echo,$offset+1);
	$dbgTrace = debug_backtrace();
	if ($offset>0) $dbgTrace=array_slice($dbgTrace,$offset,$limit);
	$out=array();
	foreach($dbgTrace as $key => $val) {
		unset($val['object']);
		if (isset($val['args'])) {
			if (count($val['args'])==0)	unset($val['args']);
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
				if (isset($args)) $val['class->method'].="(".$args.")";
				else $val['class->method'].="()";
				unset($val['function']);
				unset($val['type']);
				unset($val['class']);
			}
		}
		$out[$key]=$val;
	}
	if ($echo) trace_($out,$echo);
	return $out;
}

/**
 * Conditionele trace_()
 * 
 * @param bool $condition de conditie die bepaald of de trace getoond wordt (bij TRUE)
 * @param mixed $a 
 * @param bool $echo[TRUE] 
 * @param int $backtraceOffset[1]
 * @return string
 * @author Jan den Besten
 */
function trace_if($condition,$a=NULL,$echo=true,$backtraceOffset=1) {
	if ($condition) return trace_($a,$echo,$backtraceOffset);
}

/**
 * Als trace_() maar dan wordt het resultaat in een sessie variabel gestopt
 * 
 * - Werkt alleen als de session class is geladen.
 * - De gebruikte sessie variabele is 'trace'
 * - In het ADMIN deel wordt de eerstvolgende keer dat een pagina getoond wordt de sessie uitgelezen en getoond
 *
 * @param mixed $a
 * @return void
 * @author Jan den Besten
 */
function strace_($a=NULL) {
	$CI=&get_instance();
	$trace=trace_($a,false);
	$all=$CI->session->userdata('trace');
	$all.=$trace;
	$CI->session->set_userdata('trace',$all);
}

/**
 * Geeft een mooie dump van meegegeven variabele
 * 
 * - Als het kan worden achterhaald geeft het het type variabele
 * - Array's worden genest getoond
 * - Lange strings worden getoond als ... (met een hover wordt hele tekst getoond)
 * - Als een lege string wordt meegegeven dan wordt backtrace_() aangeroepen
 *
 * @param mixed $a Variabele waar je een dump van wilt
 * @param bool $echo[TRUE] Moet de dump meteen worden getoond?
 * @param int $backtraceOffset[1]
 * @return string Geeft het resulaat (ook nog) als een string
 * @author Jan den Besten
 */
function trace_($a=NULL,$echo=true,$backtraceOffset=1) {
	static $c=0;
	$show="Trace";
	if (!isset($a)) {
		$a=backtrace_($backtraceOffset,10,false);
		$show="Variable is empty, do a Backtrace";
		// $show="ERROR: Variable doesn't exist or is NULL.";
		$type="";
	}
	else {
		$type="[".gettype($a)."]";
	}
	$out="<div class=\"FlexyAdminTrace\" style=\"position:relative;font-family:courier,serif;font-size:10px;z-index:99999;margin:2px;padding:5px;background-color:#efe;color:#000;border:solid 1px #666;opacity:.8;\"><span style=\"font-weight:bold;color:#696;\">$show #$c $type:</span>\n";
	if (is_bool($a)) {
		if ($a) $out.="'True'";
		else		$out.="'False'";
	}
	elseif (is_array($a) or is_object($a))
		$out.=print_ar(array_($a,true),true);
	else
		$out.=print_r(tr_string($a),true);
	$c++;
	$out.="</div>";
	if ($echo) echo $out."<br/>";
	return $out;
}

/**
 * Geeft een trace van een string-waarde
 *
 * @param string $value 
 * @return string
 * @author Jan den Besten
 */
function tr_string($value) {
	$s="";
	$value=(string) $value;
	$html=in_string("<>",$value);
	if ((strtoupper(substr($value,0,6))!="#SHOW#") and ($html or (strlen($value)>200)) ) {
		$s.=strip_tags(substr($value,0,80));
		$s.=" <span title=\"".htmlentities($value)."\" style=\"cursor:help;color:#696;\">...</span>";
	}
	else
		$s=str_replace("#show#","",$value);
	return $s;
}

/**
 * Geeft deel van een array trace
 *
 * @param string $a array
 * @return string
 * @author Jan den Besten
 */
function array_($a) {
	$out=array();
	foreach($a as $key=>$value) {
		if (is_array($value))
			$out[$key]=array_($value);
		elseif (is_object($value))
			$out[$key]="{object}";
		else
			$out[$key]=tr_string($value);
	}
	return $out;
}

/**
 * Geeft een trace van een array
 *
 * @param string $array 
 * @param string $return[FALSE]
 * @param string $tabs[0]
 * @return string
 * @author Jan den Besten
 */
function print_ar($array,$return=false,$tabs=0) {
	$out="";
	$out.=" (<br/>";
	foreach($array as $key=>$value) {
		$out.=tabs($tabs);
		$thisOut="[".$key."] => ";
		$len=strlen($thisOut)+1;
		if (is_array($value))
			$thisOut.=print_ar($value,$return,$tabs+$len);
		else {
			if (is_bool($value)) {
				if ($value)
					$thisOut.="'True'";
				else
					$thisOut.="'False'";
			}
			else
				$thisOut.="'$value'<br/>";
		}
		$out.=$thisOut;
		if ($tabs==0) $out.="<br/>";
	}
	$out.=tabs($tabs-1).')<br/>';
	if (!$return) echo $out;
	return $out;
}

/**
 * Geeft aantal tab karakters terug
 *
 * @param string $t aantal
 * @param string $tab[&nbsp;] tab-string
 * @return string
 * @author Jan den Besten
 */
function tabs($t,$tab="&nbsp;") {
	return repeater($tab,$t);
}

?>
