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


function show_thumb($media,$ext="") {
	if (empty($ext)) $ext=get_file_extension($media);
	if ($ext=="swf") return flash($media,array("title"=>$media));
	else 						 return img(array("src"=>$media,"alt"=>"","title"=>$media));
}

