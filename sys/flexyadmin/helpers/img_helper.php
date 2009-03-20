<?
function icon($class="",$s="",$extraClass="",$a="") {
	if (empty($s)) $s=$class;
	return "<div class=\"icon $class $extraClass\" title=\"$s\" $a><span class=\"hide\">$s</span></div>";
}

function popup_img($img) {
	$imgSize=getimagesize($this->map."/".$name);
	$a=array("src"=>$img, "class"=>"zoom", "zwidth"=>$imgSize[0], "zheight"=>$imgSize[1] );
	return img($a);
}

function show_thumb($attr) {
	$a=array();
	if (!is_array($attr)) $a["src"]=$attr; else $a=$attr;
	$ext=get_file_extension($a["src"]);
	$imgSize=getimagesize($a["src"]);
	$a["zwidth"]=$imgSize[0];
	$a["zheight"]=$imgSize[1];
	if (!isset($a["title"])) $a["title"]=$a["src"];
	if (!isset($a["class"])) $a["class"]="zoom"; else $a["class"].=" zoom";
	if ($ext=="swf") {
		$src=$a["src"];
		unset($a["src"]);
		return flash($src,$a);
	}
	else {
		if (!isset($a["alt"])) $a["alt"]=$a["src"];
		return img($a);
	}
}

