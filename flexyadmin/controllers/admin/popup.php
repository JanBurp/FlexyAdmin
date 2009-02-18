<?
require_once(APPPATH."controllers/admin/MY_Controller.php");

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2008, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Popup Controller Class
 *
 * This Controller shows a popup
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Popup extends BasicController {

	function Popup() {
		parent::BasicController();
	}

	function index() {
		$this->_set_content("POPUP");
	}


/**
 * This controls the image popup view
 *
 * @param string $img Img name
 */

	function img($img) {
		$img=site_url(pathdecode($img,TRUE));
		$this->load->view('admin/popup', array("img"=>$img));
	}

}

?>
