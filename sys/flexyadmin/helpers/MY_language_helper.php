<?

function langp() {
	$args=func_get_args();
	// trace_($args);
	$line=lang($args[0]);
	if (func_num_args()>1) {
		array_shift($args);
		if (count($args)<=1) {
			return str_replace("%s",$args[0],$line);
		}
		else {
			$nr=0;
			foreach ($args as $value) {
				$line=str_replace("%".$nr,$value,$line);
			}
		}
	}
	return $line;
}

?>
