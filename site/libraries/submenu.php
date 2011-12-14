<?

/**
 * Submenu
 *
 * Use this module if you need a submenu.
 * - In views/site.php add somewhere this code:	<div id="submenu"><?=$submenu?></div>
 * - Set the level from which the submenu is renderd in config/submenu.php
 * - In config.php:
 *	- $config['autoload_modules'] = array('submenu');
 *  - $config['site_variables']	= array('submenu');
 *
 * @package default
 * @author Jan den Besten
 */


class Submenu extends Module {


	public function __construct() {
		parent::__construct();
	}

	public function index($page) {
		$level=$this->config('level');
		if ($level>0) {
			$uri=$this->CI->uri->get_to($this->config('level'));
			$submenu=$this->CI->menu->render_branch($uri);
		}
		else {
			$submenu=$this->CI->menu->render();
		}
		$this->CI->site['submenu']=$submenu;
	}


}

?>