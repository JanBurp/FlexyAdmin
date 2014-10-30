<?php /**
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
  if (ENVIRONMENT=='production') return '';
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
        if (!IS_AJAX)
          $val['file']='#show#<a href="txmt://open?url=file:///'.$val['file'].'&amp;line='.$val['line'].'">'.$file.' at '.$val['line'].'</a>';
        else
          $val['file']='#show#'.$val['file'].'&amp;line='.$val['line'].'">'.$file.' at '.$val['line'];
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
function trace_($a=NULL,$echo=true,$backtraceOffset=1,$max=50) {
	$CI=&get_instance();
	static $c=0;
  if ($c==0 and !IS_AJAX and !$CI->config->item('IS_ADMIN')) {
    echo "<style>._trace {position:relative;margin:2px;padding:5px;overflow:auto;overflow-x:hidden;color:#000;font-family:courier,serif;font-size:10px;line-height:14px;border:solid 1px #666;background-color:#efe;opacity:.8;z-index:99999;}._trace a {color:#000;font-family:courier,serif;font-size:10px;line-height:14px;text-decoration:underline;}</style>";
  }
  if (IS_AJAX)
    $out='';
  else
    $out='<pre class="_trace">';
  if ($c>=$max) {
    if ($c==$max) $out.="TOO MANY TRACES, MAYBE A LOOP BUG...";
  }
  else {
  	$show="";
  	if (!isset($a)) {
  		$a=backtrace_($backtraceOffset,10,false);
  		$show="NULL -> BACKTRACE ";
  		$type="";
  	}
  	else {
  		$type="[".gettype($a).(is_array($a)?'-'.count($a):'')."]";
  	}
    $out.="TRACE $show#$c$type:";
  	if (is_bool($a)) {
  		if ($a) $out.="'True'";
  		else		$out.="'False'";
  	}
  	elseif (is_array($a) or is_object($a))
  		$out.=print_ar(array_($a,true),true,2);//strlen($show.$type)+3);
  	else
  		$out.=print_r(tr_string($a),true);
  }
  if (IS_AJAX)
    $out.="\n";
  else
    $out.='</pre>';
  if ($c>$max) $out='';
	if ($echo) echo $out;
	$c++;
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
	$html=($value!=strip_tags($value));
  $s=$value;
  $show=has_string('#show#',$value);
  if ($html and !$show) $s=preg_replace('/\s/',' ',htmlentities($value));
  if (!$show) {
    $s=max_length($s,100,'CHARS');
    if ($s!=$value) $s.=' ...';
  }
  if ($show) $s=str_replace('#show#','',$s);
  $s="'".$s."'";
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
		elseif (is_object($value)) {
        $size=count($value);
  			$out[$key]="{OBJECT[$size]}";
      }
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
function print_ar($array,$return=false,$tabs=0,$brackets="()") {
  $eol="\n";
  $bl=substr($brackets,0,1);
  $br=substr($brackets,1,1);
  if (empty($array)) {
    $out=$bl.'EMPTY ARRAY'.$br.$eol;
  }
  else {
  	$out=$bl.$eol;
  	foreach($array as $key=>$value) {
  		$out.=tabs($tabs);
  		$thisOut="[".$key."] => ";
  		$len=strlen($thisOut)+1;
      $len=2;
  		if (is_array($value)) {
  			$thisOut.=print_ar($value,$return,$tabs+$len);
  		}
  		else {
  			if (is_bool($value)) {
  				if ($value)
  					$thisOut.="'True'";
  				else
  					$thisOut.="'False'";
  			}
  			else {
  				$thisOut.="$value".$eol;
  			}
  		}
  		$out.=$thisOut;
  	}
    $out.=tabs($tabs-1).$br.$eol;
  }
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
function tabs($t,$tab=" ") {
  // if (IS_AJAX) $tab="\t";
	return repeater($tab,$t);
}

?>
