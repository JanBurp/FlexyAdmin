<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Met deze class kun je een wizard maken
 * 
 * Een wizard is een aantal stappen die een gebruiker moet nemen, je ziet het veel bij het uitchecken van een webshop.
 * 
 * Een Wizard moet aangemaakt worden door een array mee te geven aan de construct (of init):
 * 
 *     array(
 *       [title] => 'Titel van de Wizard',
 *       [object] => '{object}',                 // Object waar de methods die de Wizard moet aanroepen in staan  
 *       [uri_segment] => '3',                   // Uri segment waar de wizard uri begint
 *       [steps] => (                            // Array met alle stappen van de Wizard
 *          [stap_1] => (                        // Naam en uri van de stap (geen spaties!)
 *             [label] =>  'Stap 1',             // Titel van de stap (wordt getoond)
 *             [method] => 'first_step'          // Method die deze stap aanroept
 *           ),
 *           [laatste_stap] => (
 *             [label] => 'Bijna klaar',
 *             [method] => 'final'
 *           )
 *       )
 *     )
 *
 * @author Jan den Besten
 */
class Wizard {
  
  private $CI;

  /**
   * Stappen van de wizard
   */
  private $steps=array();
  
  /**
   * Huidige stap
   */
  private $step=false;
  
  /**
   * Uri segment waar de step begint
   */
  private $uri_segment=3;
  
  /**
   * undocumented variable
   */
  private $object=null;
  
  /**
   * Titel van de Wizard
   */
  private $title='Wizard';
  
  /**
   * Data
   */
  private $data=array();


  /**
   * Construct & init
   *
   * @param array $config 
   * @author Jan den Besten
   */
	public function __construct($config=array()) {
    $this->CI=@get_instance();
    $this->CI->load->library('session');
    $this->initialize($config);
	}
  
  /**
   * Initialiseer de wizard
   *
   * @param array $config Instellingen van de wizard
   * @return void
   * @author Jan den Besten
   */
  public function initialize($config=array()) {
    foreach ($config as $key => $value) {
      $this->$key=$value;
    }
  }

  /**
   * Render huidige step
   *
   * @return string
   * @author Jan den Besten
   */
  public function render() {
    $this->get_step();
    $out = h($this->title,1);
    $out .= '<div class="btn-group" role="group">';
    $link = true;
    $class = 'btn-warning';
    foreach ($this->steps as $key=>$s) {
			if ($this->step==$key) {
        $class = 'btn-primary';
        $link  = false;
      }
      if ($link) {
        $out .= '<a href="'.$this->get_step_uri($key).'" class="btn '.$class.'">'.$s['label'].'</a>';
      }
      else {
        $out .= '<button class="btn '.$class.'">'.$s['label'].'</button>';
        $class = 'btn-secondary';
      }
		}
    $out .= '</div>';
    return $out;
  }

  /**
   * Geeft huidige step
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_step() {
    $step=$this->CI->uri->get($this->uri_segment);
    if (!isset($this->steps[$step])) {
      reset($this->steps);
      $step=key($this->steps);
    } 
    $this->step=$step;
    return $this->step;
  }

  /**
   * Geeft uri van (mee te geven) step
   *
   * @param string $extra[''] eventuele argumenten (extra uri-parts)
   * @return string
   * @author Jan den Besten
   */
  public function get_step_uri($step='',$extra='') {
    $uri=explode('/',uri_string());
    $uri=array_slice($uri,0,$this->uri_segment-1);
    $uri=implode('/',$uri);
    $uri=$uri.'/'.$step;
    if ($extra) $uri.='/'.$extra;
    return $uri;
  }


  
  /**
   * Geeft volgende step
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_next_step() {
    $steps=$this->steps;
    reset($steps);
    do {
      $step=each($steps);
    }
    while ($step['key']!=$this->step);
    $step=each($steps);
    return $step['key'];
  }
  
  /**
   * Geeft uri van volgende step
   *
   * @param string $extra[''] eventuele argumenten (extra uri-parts)
   * @return string
   * @author Jan den Besten
   */
  public function get_next_step_uri($extra='') {
    $step=$this->get_next_step();
    $uri=explode('/',uri_string());
    $uri=array_slice($uri,0,$this->uri_segment-1);
    $uri=implode('/',$uri);
    $uri=$uri.'/'.$step;
    if ($extra) $uri.='/'.$extra;
    return $uri;
  }
  
  /**
   * Roept huidige step aan
   *
   * @param string $args Argumenten die mee worden geven aan de step method
   * @return mixed return van de step method
   * @author Jan den Besten
   */
  public function call_step($args) {
    $step=$this->get_step();
    if ($step) {
      $method=$this->steps[$step]['method'];
      if (method_exists($this->object,$method)) {
        return $this->object->$method($args);
      }
    }
    return false;
  }



  /**
   * Saves data from step to step
   *
   * @param string $data 
   * @return void
   * @author Jan den Besten
   */
  public function save_data($data) {
    $this->CI->session->set_userdata('wizard',$data);
    return $this;
  }
  
  /**
   * Gets data from earlier steps
   *
   * @return $data array
   * @author Jan den Besten
   */
  public function get_data() {
    return $this->data=$this->CI->session->userdata('wizard');
  }

  /**
   * Gets data from earlier steps
   *
   * @return $data array
   * @author Jan den Besten
   */
  public function unset_data() {
    return $this->data=$this->CI->session->unset_userdata('wizard');
  }

	
}

?>
