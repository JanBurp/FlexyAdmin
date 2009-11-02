<?
function icon($class="",$s="",$extraClass="",$a="") {
	if (empty($s)) $s=$class;
	return "<div class=\"icon $class $extraClass\" title=\"$s\" $a><span class=\"hide\">$s</span></div>";
}

function popup_img($img) {
	$imgSize=get_img_size($this->map."/".$name);
	if ($imgSize)
		$a=array("src"=>$img, "class"=>"zoom", "zwidth"=>$imgSize[0], "zheight"=>$imgSize[1] );
	else
		$a=NULL;
	return img($a);
}

function show_thumb($attr) {
	$a=array();
	if (!is_array($attr)) $a["src"]=$attr; else $a=$attr;
	$ext=get_file_extension($a["src"]);
	$imgSize=get_img_size($a["src"]);
	if ($imgSize) {
		$a["zwidth"]=$imgSize[0];
		$a["zheight"]=$imgSize[1];
		if (!isset($a["alt"]))		$a["alt"]=$a["src"];
		if (!isset($a["class"]))	$a["class"]="zoom"; else $a["class"].=" zoom";
		if ($ext=="swf") {
			$src=$a["src"];
			unset($a["src"]);
			return flash($src,$a);
		}
		else {
			if (!isset($a["alt"])) $a["alt"]=$a["src"];
			$CI=& get_instance();
			$cachedThumb=$CI->config->item('THUMBCACHE').pathencode($a['src']);
			if (file_exists($cachedThumb)) $a['src']=$cachedThumb;
			return img($a);
		}
	}
	return '';
}

function get_img_size($i) {
	$size=FALSE;
	if (file_exists($i)) $size=getimagesize($i);
	return $size;
}
