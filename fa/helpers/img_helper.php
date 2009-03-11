<?
function icon($class="",$s="",$extraClass="") {
	if (empty($s)) $s=$class;
	return "<div class=\"icon $class $extraClass\" title=\"$s\"><span class=\"hide\">$s</span></div>";
}

function popup_img($img,$txt) {
	$atts = array(
              'width'      => '400',
              'height'     => '300',
              'scrollbars' => 'no',
              'status'     => 'no',
              'resizable'  => 'no',
              'screenx'    => '0',
              'screeny'    => '0'
            );
	return anchor_popup(api_url('API_popup_img',pathencode($img)),$txt,$atts);
}


function show_thumb($attr) {
	$a=array();
	if (!is_array($attr)) $a["src"]=$attr; else $a=$attr;
	$ext=get_file_extension($a["src"]);
	if (!isset($a["title"])) $a["title"]=$a["src"];
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

