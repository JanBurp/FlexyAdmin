<?

/**
	* Maakt een submenu
	*
	* Bestanden
	* ----------------
	*
	* - site/config/submenu.php - Hier kun je instellen op welk level het submenu begint
	*
	* Installatie
	* ----------------
	*
	* - Voeg ergens in een view (bv views/site.php) de code `<div id="submenu"><?=$submenu?></div>` toe
	* - Stel het level in in config/submenu.php
	* - Laad de module altijd in: `$config['autoload_modules']=array('submenu');`
	* - Voeg een variabele 'submenu' toe aan `$this->site: $config['site_variables']=array('submenu');`
	*
	* @package default
	* @author Jan den Besten
	*/

class Submenu extends Module {

  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
	}

  /**
  	* Hier wordt de module aangeroepen
  	*
  	* @param string $page
  	* @return string 
  	* @author Jan den Besten
  	* @ignore
  	*/
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