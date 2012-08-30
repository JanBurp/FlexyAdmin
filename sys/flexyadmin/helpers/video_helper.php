<?

/**
 * Een aantal handige functies om met YouTube en Vimeo filmpjes om te gaan
 *
 * @author Jan den Besten
 **/

 /**
  * Haalt url van de thumbnail van een video op
  *
  * @param string $code De YouTube of Vimeo code voor de video
  * @param string $size['small'] de maat [small|medium|big]
  * @param string $type['youtube'] 'youtube' of 'vimeo'
  * @return string Url van de thumb
  * @author Jan den Besten
  */
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


/**
 * Geeft info over vimeo video (o.a. thumb)
 *
 * @author Jan den Besten
 * @link http://www.soapboxdave.com/2010/04/getting-the-vimeo-thumbnail/
 */
function get_vimeo_info($id,$var='') {
	if (!function_exists('curl_init')) die('CURL is not installed!');
	if (!empty($id)) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://vimeo.com/api/v2/video/$id.php");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$curl=curl_exec($ch);
		$output = unserialize($curl);
		if (isset($output[0])) {
			$output = $output[0];
			curl_close($ch);
			if (empty($var))
				return $output;
			else 
				return $output[$var];
		}
	}
	return '';
}

/**
 * Haalt code van een video uit de link
 * 
 * - Stel je hebt de volgende youtube link: http://www.youtube.com/watch?v=ICFELTZcON0
 * - Dan geeft deze functie als resultaat: ICFELTZcON0
 *
 * @param string $url
 * @param string $type['youtube'] 'youtube' of 'vimeo' 
 * @return string
 * @author Jan den Besten
 */
function get_video_code_from_url($url,$type='youtube') {
	$type=strtolower($type);
	$code=$url;
	if (has_string('www.',$url) or has_string('http',$url)) {
		// is an Url
		$match=array();
		switch ($type) {
			case 'vimeo':
				preg_match('/vimeo.com\/([0-9a-z_-]+)/i',$url,$match);
				break;
			case 'youtube':
				preg_match('/v=([0-9a-z_-]+)/i',$url,$match);
			default:
				break;
		}
		if (isset($match[1])) $code=$match[1];
	}
	return $code;
}
