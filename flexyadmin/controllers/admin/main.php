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
 * main Controller Class
 *
 * This Controller shows the startscreen
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 * @todo
 * - basic screen
 *
 */

class Main extends AdminController {

	function Main() {
		parent::AdminController();
	}

	function index() {
		$this->_set_content($this->cfg->get('CFG_configurations',"txt_info"));
		if ($this->config->item('LOCAL'))
			$this->_add_content("You are testing this site local!");
		$this->_show_all();
	}

}

?>
