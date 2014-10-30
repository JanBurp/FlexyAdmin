<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
	* Plugin_template
	*
	* Gebruik dit als basis voor je eigen plugins.
	* Lees ook [Plugins maken]({Plugins-maken})
	*
	* @author Jan den Besten
	*/
 class Plugin_template extends Plugin {
   
   /**
    * Laat deze altijd staan in je plugin
    *
    * @author Jan den Besten
    * @ignore
    */
   public function __construct() {
     parent::__construct();
   }


	/**
		* _trigger
		*
		* Mochten de triggers voor het activeren van de plugin niet vastliggen, dan kun je met deze method de triggers zelf aanmaken
		* (Zie in de config: $config['trigger'] en $config['trigger_method']).
		* Het resultaat van deze method wordt toegevoegd aan $config['trigger'].
		*
		* @return array
		* @author Jan den Besten
		*/
	public function _trigger() {
		$trigger=array();
		return $trigger;
	}


	/**
		* _admin_logout
		*
		* Standaard method die wordt aangeroepen als de plugin actief moet worden bij uitloggen.
		* De return waarde beinvloed of het uitloggen door mag gaan of dat er een melding wordt getoond.
		*
		* @return string Als leeg (of void) dan kan de loguit procedure verder gaan. Zoniet geef dan een string terug met de melding die getoond moet worden.
		* @author Jan den Besten
		*/
	public function _admin_logout() {
    // return '';
	}


	/**
		* _admin_api
		*
		* Standaard method die wordt aangeroepen als de plugin actief wordt via een URL (of menu-item) in het admin deel.
		* De url is: /admin/plugin/NAAM_PLUGIN/EVENTUELE_PARAMETERS
		*
		* @return string HTML output van de plugin
		* @author Jan den Besten
		*/
	public function _admin_api($args=NULL) {
    $this->add_message('Use this template as a base for your plugins.');
    return $this->view();
	}


	/**
		* _after_update()
		*
		* Dit wordt aangeroepen als aan de bij triggers ingestelde voorwaarden wordt voldaan.
		* Je kunt de data van het huidige record dat net door de gebruiker is aangepast met deze method aanpassen en teruggeven.
		* De data kun je vinden in $this->newData en moet je aangepast teruggeven.
		*
		* @return array met aangepaste data (van $this->newData), of een error string: er wordt een melding getoond, en de waarden blijven hetzelfde als de ingevoerde waarden.
		* @author Jan den Besten
		*/
	public function _after_update() {
		return $this->newData;
	}


	/**
		* _after_delete()
		*
		* Dit wordt aangeroepen als aan de bij triggers ingestelde voorwaarden wordt voldaan.
		* Je kunt met de return waarde beinvloeden of het item daadwerkelijk verwijderd mag worden.
		*
		* @return bool FALSE als delete kan doorgaan
		* @author Jan den Besten
		*/
	public function _after_delete() {
		return false;
	}


}

?>