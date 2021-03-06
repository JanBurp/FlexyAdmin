<?php
/** \ingroup helpers
 * Een aantal handige functies om met YouTube en Vimeo filmpjes om te gaan
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 * @file
 **/

function embed_video($code) {
	$default = array(
		'platform' => 'youtube',
		'code'		 => $code,
		'ratio'		 => '16:9',
	);
	if (is_string($code) and substr($code,0,1)=='{') {
		$code = json2array($code);
	}
	if (is_array($code)) {
		$code = array_merge($default,$code);
	}
	else {
		$code = $default;
	}

	// $code['img'] = get_video_thumb($code['code'],'big',$code['platform']);

	$ratio = str_replace(':','by',$code['ratio']);
	switch ($code['platform']) {
		case 'vimeo':
			// $html = '<div class="video embed-responsive embed-responsive-'.$ratio.'"><iframe class="video-player embed-responsive-item" type="text/html" src="https://www.youtube-nocookie.com/embed/'.$code['code'].'?vq=hd720&showinfo=0" frameborder="0" allowfullscreen></iframe></div>';
			break;
		case 'youtube':
		default:
			$html = '<div class="video embed-responsive embed-responsive-'.$ratio.'"><iframe class="video-player embed-responsive-item" type="text/html" src="https://www.youtube-nocookie.com/embed/'.$code['code'].'?vq=hd720&showinfo=0" frameborder="0" allowfullscreen></iframe></div>';
			break;
	}
	return $html;
}


 /**
  * Haalt url van de thumbnail van een video op
  *
  * @param string $code De YouTube of Vimeo code voor de video
  * @param string $size default='small' de maat [small|medium|big]
  * @param string $type default='youtube' 'youtube' of 'vimeo'
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
					$thumb_url='https://i.ytimg.com/vi/'.$code.'/mqdefault.jpg';
					break;
			}
			break;
	}
	return $thumb_url;
}


/**
 * Geeft info over vimeo video (o.a. thumb)
 *
 * Zie http://www.soapboxdave.com/2010/04/getting-the-vimeo-thumbnail/
 *
 * @author Jan den Besten
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
 * Geeft volledige video info als array
 *
 * @author Jan den Besten
 */
function get_video_array($code) {
	if (is_array($code)) {
		$default = $code;
	}
	else {
		$default = array(
			'platform' => 'youtube',
			'code'		 => $code,
			'ratio'		 => '16:9',
		);
	}
	if (!is_array($code) and substr($code,0,1)=='{') {
		$code = json2array($code);
		$code = array_merge($default,$code);
	}
	else {
		$code = $default;
	}
	return $code;
}


/**
 * Haalt code van een video uit de link
 *
 * - Stel je hebt de volgende youtube link: http://www.youtube.com/watch?v=ICFELTZcON0
 * - Dan geeft deze functie als resultaat: ICFELTZcON0
 *
 * @param string $url
 * @param string $type default='youtube' 'youtube' of 'vimeo'
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
        if (has_string('youtube.',$url))
  				preg_match('/v=([0-9a-z_-]+)/i',$url,$match);
        else
          $match[1]=get_suffix($url,'/');
			default:
				break;
		}
		if (isset($match[1])) $code=$match[1];
	}
	return $code;
}


function find_embedded_video($html) {
	if (preg_match('/<iframe.*src="(.*)".*>(<\/iframe>)*?/uU', $html,$match)) {
		return array(
			'iframe' => $match[0],
			'src'		 => $match[1],
		);
	}
	return false;
}

