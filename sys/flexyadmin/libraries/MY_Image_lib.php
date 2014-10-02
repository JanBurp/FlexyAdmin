<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Image Manipulation class
 * 
 * Add sharpen parameter for quality improvement with imagemagick
 *
 */
class MY_Image_lib extends CI_Image_lib {

  // Added by JdB, idea DirkKokx
  var $sharpen      = FALSE;


	public function __construct($props = array()) {
    parent::__construct($props);
	}


	/**
	 * Image Process Using ImageMagick
	 *
	 * This function will resize, crop or rotate
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function image_process_imagemagick($action = 'resize')
	{
		//  Do we have a vaild library path?
		if ($this->library_path == '')
		{
			$this->set_error('imglib_libpath_invalid');
			return FALSE;
		}

		if ( ! preg_match("/convert$/i", $this->library_path))
		{
			$this->library_path = rtrim($this->library_path, '/').'/';

			$this->library_path .= 'convert';
		}

		// Execute the command
		$cmd = $this->library_path." -quality ".$this->quality;

		if ($action == 'crop')
		{
			$cmd .= " -crop ".$this->width."x".$this->height."+".$this->x_axis."+".$this->y_axis." \"$this->full_src_path\" \"$this->full_dst_path\" 2>&1";
		}
		elseif ($action == 'rotate')
		{
			switch ($this->rotation_angle)
			{
				case 'hor'	: $angle = '-flop';
					break;
				case 'vrt'	: $angle = '-flip';
					break;
				default		: $angle = '-rotate '.$this->rotation_angle;
					break;
			}

			$cmd .= " ".$angle." \"$this->full_src_path\" \"$this->full_dst_path\" 2>&1";
		}
		else  // Resize editted by JdB (idea DirkKokx) (added -sharpen param)
		{
      $sharpen='';
      if ($this->sharpen) $sharpen=' -sharpen '.$this->sharpen ;
			$cmd .= " -resize  ".$this->width."x".$this->height." $sharpen \"$this->full_src_path\" \"$this->full_dst_path\" 2>&1";
      // strace_($cmd);
		}

		$retval = 1;

		@exec($cmd, $output, $retval);

		//	Did it work?
		if ($retval > 0)
		{
			$this->set_error('imglib_image_process_failed');
			return FALSE;
		}

		// Set the file to 777
		@chmod($this->full_dst_path, FILE_WRITE_MODE);

		return TRUE;
	}
  
}