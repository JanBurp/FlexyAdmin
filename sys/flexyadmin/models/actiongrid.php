<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Maakt een grid aan die ajax_acties aanroept.
 * Acties bestaan uit een array van acties en elke actie bestaat een array met de volgende velden:
 * - action_url : url van aan ajax aanroep (inclusief query url)
 * - title      : titel die in het grid zichtbaar is
 *
 * @package default
 * @author Jan den Besten
 */

class Actiongrid extends CI_Model {

  var $actions=array();

	public function __construct() {
		parent::__construct();
		$this->load->model("grid");
	}

  /**
   * Voeg één actie toe (aan bestaande acties)
   *
   * @param string $action
   * @return $this
   * @author Jan den Besten
   */
  public function add_action($action) {
    $this->add_actions(array($action));
    return $this;
  }
  
  /**
   * Voeg acties toe (aan bestaande acties)
   *
   * @param string $actions
   * @return $this
   * @author Jan den Besten
   */
  public function add_actions($actions) {
    $this->actions=array_merge($this->actions,$actions);
    return $this;
  }
  
  public function view() {
		$grid=new grid();
    $griddata=array();
    foreach ($this->actions as $key => $action) {
			$griddata[$key]=array(
        'id'        =>icon('no'),
        '_message'  =>'',
        'uri'       =>$action['action_url'],
      );
      unset($action['action_url']);
      $griddata[$key]=array_merge($griddata[$key],$action);
    }
    
		$grid->set_data($griddata,'ACTIONGRID');
		$renderData=$grid->render("html",'ACTIONGRID',"grid actionGrid home");
		$render=$this->load->view("admin/grid",$renderData,true);
    return $render;
  }

}
