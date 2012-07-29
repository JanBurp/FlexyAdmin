<?

function _callback_highlight($matches) {
	$code=$matches[1];
	$code=strip_tags($code,'<p><br><a><div><b><strong><em><italic>');
	$code=str_replace('&nbsp;',' ',$code);
	$code=trim($code);
	$code=str_replace(array('&lt;','&gt;'),array('<','>'),$code);
	$code=str_replace(array('<br/>','<br />','<br>'),"\n",$code);
	$code=highlight_code($code);
	return $code;
}

function highlight_code_if_needed($txt) {
	$txt=preg_replace_callback('/<code>(.*?)<\/code>/','_callback_highlight',$txt);
	return $txt;
}


?>