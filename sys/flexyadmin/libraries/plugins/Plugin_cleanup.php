<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Verwijderd onnodige bestanden (assets/_tmp bijvoorbeeld)
 *
 * @author Jan den Besten
 * @internal
 */

class Plugin_cleanup extends Plugin {

	public function __construct() {
		parent::__construct();
	}

	public function _admin_logout() {
 		$this->_cleanup();
	}
	
	public function _admin_api($args=NULL) {
		$this->_cleanup();
		// $this->CI->assets->delete_unused_files();
    return $this->show_messages();
	}


	private function _cleanup() {

		// _tmp keep for 3 months
		$tmp_dir = $this->CI->config->item('ASSETSFOLDER').'_tmp';
		if (file_exists($tmp_dir)) {
			$duetime = time() - 3 * TIME_MONTH;
			$files = scan_map($tmp_dir);
			if ($files) {
				foreach($files as $file) {
					$stamp = filemtime($file);
					if ($stamp<$duetime) {
						@unlink($file);
					}
				}
			}
		}
	}
	
}

?>