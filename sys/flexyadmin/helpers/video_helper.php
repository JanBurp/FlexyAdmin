<?


function get_video_thumb($code,$size='small',$type='youtube') {
	$type=strtolower($type);
	$thumb_url='';
	switch ($type) {
		case 'vimeo':
			$thumb_url =  get_vimeo_info($code,'thumbnail_'.$size);
			break;
		case 'youtube':
		default:
			switch ($size) {
				case 'big':
					$thumb_url='http://img.youtube.com/vi/'.$code.'/0.jpg';
					break;
				case 'medium':
					$thumb_url='http://img.youtube.com/vi/'.$code.'/1.jpg';
					break;
				case 'small':
				default:
					$thumb_url='http://img.youtube.com/vi/'.$code.'/2.jpg';
					break;
			}
			break;
	}
	return $thumb_url;
}


// http://www.soapboxdave.com/2010/04/getting-the-vimeo-thumbnail/
function get_vimeo_info($id,$var='') {
	if (!function_exists('curl_init')) die('CURL is not installed!');
	if (!empty($id)) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://vimeo.com/api/v2/video/$id.php");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$output = unserialize(curl_exec($ch));
		$output = $output[0];
		curl_close($ch);
		if (empty($var))
			return $output;
		else 
			return $output[$var];
	}
	return '';
}


function get_video_code_from_url($url,$type='youtube') {
	$type=strtolower($type);
	$code=$url;
	if (has_string('www.',$url) or has_string('http:',$url)) {
		// is an Url
		$match=array();
		switch ($type) {
			case 'vimeo':
				preg_match('/vimeo.com\/(.*)?/',$url,$match);
				break;
			case 'youtube':
				preg_match('/v=(.*)?/',$url,$match);
			default:
				break;
		}
		if (isset($match[1])) $code=$match[1];
	}
	return $code;
}
