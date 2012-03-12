<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wizard {

  var $steps=array();
  var $step=false;
  var $uri_segment=3;
  var $object=null;
  var $title='Wizard';


	public function __construct($config=array()) {
    $this->initialize($config);
	}
  
  public function initialize($config=array()) {
    foreach ($config as $key => $value) {
      $this->$key=$value;
    }
  }

  public function render() {
    $this->get_step();
    $out='';
		foreach ($this->steps as $key=>$s) {
      $thisOut=$s['label'];
			if ($this->step==$key) $thisOut='<strong>'.$thisOut.'</strong>';
      $out=add_string($out,$thisOut,'|');
		}
		$out=h($this->title,1).p().$out._p();
    return $out;
  }

  public function get_step() {
    $uri=explode('/',uri_string());
    $step=element($this->uri_segment,$uri);
    if (!isset($this->steps[$step])) {
      reset($this->steps);
      $step=key($this->steps);
    } 
    $this->step=$step;
    return $this->step;
  }
  
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
  
  public function get_next_step_uri($extra='') {
    $step=$this->get_next_step();
    $uri=explode('/',uri_string());
    $uri=array_slice($uri,0,$this->uri_segment);
    $uri=implode('/',$uri);
    $uri=$uri.'/'.$step;
    if ($extra) $uri.='/'.$extra;
    return $uri;
  }
  
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



	
}

?>
