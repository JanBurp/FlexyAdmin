<?php 
/**
 * With this model you can add method calls in a que which can be run later when asked for in a controller.
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 * @internal
 */

class Queu extends CI_Model {
	
	private $calls;
	
	public function __construct() {
		parent::__construct();
		$this->remove_calls();
	}

	public function add_call($object,$method,$args='',$place='') {
		$call=array('object'=>$object,'method'=>$method,'args'=>$args);
		if (!in_array($call,$this->calls)) {
      switch ($place) {
        case 'top':
          array_unshift($this->calls,$call);
          break;
        default:
          $this->calls[]=$call;
          break;
      }
    }
	}
	
	public function run_calls() {
		$calls=$this->get_calls();
		if (!empty($calls)) {
			foreach ($calls as $call) {
				if (method_exists($call['object'],$call['method'])) {
					$call['object']->$call['method']($call['args']);
				}
			}
		}
		$this->remove_calls();
	}
	
	public function get_calls() {
		return $this->calls;
	}
	
	public function remove_calls() {
		$this->calls=array();
		return $this->calls;
	}




}
	
?>