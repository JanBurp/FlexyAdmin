<?

/**
 * Maakt een submenu
 *
 * <h2>Bestanden</h2>
 * - site/config/submenu.php - Hier kun je instellen op welk level het submenu begint
 *
 * <h2>Installatie</h2>
 * - Voeg ergens in een view (bv views/site.php) de code &lt;div id=&quot;submenu&quot;&gt;&lt;?=$submenu?&gt;&lt;/div&gt; toe
 * - Stel het level in in config/submenu.php
 * - Laad de module altijd in: <span class="code">$config['autoload_modules']=array('submenu');</span>
 * - Voeg een variabele 'submenu' toe aan $this->site: <span class="code">$config['site_variables']=array('submenu');</span>
 *
 * @package default
 * @author Jan den Besten
 */

class Submenu extends Module {


	public function __construct() {
		parent::__construct();
	}

  /**
   * Hier wordt de module aangeroepen
   *
   * @param string $page
   * @return string 
   * @author Jan den Besten
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