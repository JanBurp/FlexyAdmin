<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Create video thumbs with ffmpeg
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Plugin_create_video_thumbs extends Plugin {

  public function __construct() {
    parent::__construct();
  }

	public function _admin_api($args=NULL) {
    if ( !$this->CI->flexy_auth->allowed_to_use_cms()) return false;

		if (isset($args)) {
			if (isset($args[0])) {
				$folder = $args[0];
        $path = $this->CI->config->item('ASSETSFOLDER').$folder;

        $videos = scan_map($path);
        foreach ($videos as $video) {
          $this->create_thumb($video,$path);
        }

        $this->add_message("<p>Video thumbs created in <b>$folder</b>.</p>");
			}
		}
    return $this->show_messages();
	}

  private function create_thumb($video,$path) {
    $ffmpeg = 'ffmpeg';

    $name = get_suffix($video,"/");

    // $video  = dirname(__FILE__) . '/demo.mpg';
    $image  = $path . '/'.$name.'.jpg';

    // default time to get the image
    $second = 10;
    // // get the duration and a random place within that
    // $cmd = "$ffmpeg -i $video 2>&1";
    // if (preg_match('/Duration: ((\d+):(\d+):(\d+))/s', `$cmd`, $time)) {
    //     $total = ($time[2] * 3600) + ($time[3] * 60) + $time[4];
    //     $second = rand(1, ($total - 1));
    // }

    // get the screenshot
    $cmd = "$ffmpeg -i $video -deinterlace -an -ss $second -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $image 2>&1";
    $return = `$cmd`;

    $this->add_message("$name -> $image<br>");
  }
	
}

?>
