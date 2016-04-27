<?php
/** \ingroup helpers
 * Handige PHP debug tools
 *
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 * @file
 */


if (!function_exists('xdebug_break')) {
  function xdebug_break() {
    trace_();
  }
}


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
 * @param string $offset default=0 Hoeveel stappen terug worden getoond
 * @param string $limit default=10 Aantal stappen dat wordt getooond
 * @param bool $echo default=TRUE
 * @return string resultaat
 * @author Jan den Besten
 */
function backtrace_($offset=0,$limit=10,$echo=true) {
  // if (ENVIRONMENT=='production') return '';
  // if ($echo) return trace_(NULL,$echo,$offset+1);
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
        // if (!IS_AJAX)
        //   $val['file']='#show#<a href="txmt://open?url=file:///'.$val['file'].'&amp;line='.$val['line'].'">'.$file.' at '.$val['line'].'</a>';
        // else
          $val['file']=$file.' at '.$val['line'];
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
 * @param mixed $a  default=NULL
 * @param bool $echo default=TRUE 
 * @param int $backtraceOffset default=1
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
 * - Lange strings worden getoond als ... (zonder linebreaks)
 * - Als een lege string wordt meegegeven dan wordt backtrace_() aangeroepen
 *
 * @param mixed $a Variabele waar je een dump van wilt
 * @param bool $echo default=TRUE Moet de dump meteen worden getoond?
 * @param int $backtraceOffset default=1
 * @param int $max  default=50
 * @param string $class  Eventuele class
 * @return string Geeft het resulaat (ook nog) als een string
 * @author Jan den Besten
 */
function trace_($a=NULL,$echo=true,$backtraceOffset=1,$max=50,$class='_trace') {
	$CI=&get_instance();
	static $c=0;
  if ($c==0 and !IS_AJAX and !defined('PHPUNIT_TEST')) {
    echo "<style>._trace,.xdebug-var-dump {position:relative;box-sizing:border-box;width:99%;margin:5px .5%;padding:5px 10px;overflow:auto;color:#000;font-family:courier,serif;font-size:10px;line-height:14px;border:solid 1px #696;border-radius:5px;background-color:#DEA;opacity:.9;z-index:99999;} ._trace pre {font-size:10px;border:none;background:transparent;margin:0;padding:2px;}</style>";
  }
  if (IS_AJAX or defined('PHPUNIT_TEST')) {
    $out='';
  }
  else {
    $out='<pre class="'.$class.'">';
  }
  if (defined('PHPUNIT_TEST')) {
   $out.="\e[32m";
  }
  if (!empty($class)) $out.='TRACE ['.$c.']';
  // echo $out;
  ob_start();
  var_dump($a);
  $out.=ob_get_contents();
  // $out=preg_replace("/<font/ui", "<font style=\"\" ", $out); // remove font styling
  $out=preg_replace("/\n*\s*<b>array/ui", " <b>array", $out); // remove array on next line
  $out=preg_replace("/ *<i><font[^>]*>empty<\/font><\/i>\\n/uim", "", $out); // remove "empty"
  ob_end_clean();
  if (IS_AJAX  or defined('PHPUNIT_TEST'))
    $out.="---\n\n";
  else
    $out.='</pre>';
  if ($max>0 and $c>$max) $out='';
	if ($echo and !IS_AJAX) echo $out;
	$c++;
  
  if (IS_AJAX) {
    if (isset($CI->message)) $CI->message->add_ajax("\n".$out);
    echo $out;
  }
	return $out;
}

function trace_sql($sql,$echo=true) {
  $sql = nice_sql($sql);
  return trace_($sql,$echo);
}

function nice_sql($sql) {
  $sql = preg_replace("/(SELECT)\s/uis", "$1\n", $sql,1);
  $sql = str_replace("`, ", "`, \n", $sql);
  $sql = preg_replace("/(FROM)\s/uis", "\n$1 ", $sql,1);
  $sql = substr_replace( $sql,"\nORDER",strrpos($sql,'ORDER'),'5');
  $sql = preg_replace("/(WHERE|SET|LEFT|RIGHT|GROUP)\s/uis", "\n$1 ", $sql);
  return $sql;
}

/**
 * Geeft een trace van een string-waarde
 *
 * @param string $value 
 * @return string
 * @author Jan den Besten
 */
function tr_string($value) {
	$s='';
  if (is_string($value)) {
    $show=preg_match("/^#show#/u", $value);
    // $show=1;
    // echo"<pre>";print_r(array($value,$show));echo"</pre>";
    $s=$value;
    if ($show===1) {
      $s=preg_replace("/^#show#/u", "", $value);
    }
    else {
      $html=($value!=strip_tags($value));
      if ($html) $s=preg_replace('/\s/',' ',htmlentities($value));
      $s=max_length($s,1000,'CHARS');
      if ($s!=$value) $s.=' ...';
      $s=str_replace("\n",'\n',$s);
      $s=str_replace("\r",'\r',$s);
    }
    $s="'".trim($s,"'")."'";
  }
  else
    return $value;
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
			$out[$key]=$value;
	}
	return $out;
}

/**
 * Geeft een trace van een array
 *
 * @param string $array 
 * @param string $return default=FALSE
 * @param string $tabs default=0
 * @param string $brackets  default='()'
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
      if (IS_AJAX  or defined('PHPUNIT_TEST'))
        $thisOut="[".$key."] => ";
      else
        $thisOut="<b>[".$key."]</b> => ";
  		$len=strlen($thisOut)+1;
      $len=2;
  		if (is_array($value)) {
  			$thisOut.=print_ar($value,$return,$tabs+$len);
  		}
  		else {
  			if (is_bool($value)) {
  				if ($value)
  					$thisOut.="TRUE";
  				else
  					$thisOut.="FALSE";
          $thisOut.=$eol;
  			}
  			else {
  				$thisOut.=tr_string($value).$eol;
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
 * @param string $tab default="&nbsp;" tab-string
 * @return string
 * @author Jan den Besten
 */
function tabs($t,$tab=" ") {
  // if (IS_AJAX) $tab="\t";
	return str_repeat($tab,$t);
}

?>
