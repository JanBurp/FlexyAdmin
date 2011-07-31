<?
class FlexyHtml Extends CI_Model {

	var $name;
	var $parent;

	var $attributes;
	var $title;
	var $data;
	var $render;
	var $html;
	var $view;

	function __construct() {
		parent::__construct();
		$this->init();
	}

	function init() {
		// Set name of this Class, for default values
		$this->name=$this->_make_class_name(get_class($this));
		$this->parent=$this->_make_class_name(get_parent_class($this));
		// Set default values
		$this->set_view('flexyobjects/'.$this->name.'_view');
		$this->set_attribute();
		$this->set_class($this->parent.' '.$this->name);
		$this->set_title();
		$this->set_data();
		// empty render & html
		$this->render=array();
		$this->html='';
	}
	
	function _make_class_name($name) {
		$name=strtolower($name);
		if ($name=='model') {
			return '';
		}
		$name='flexy'.ucfirst(str_replace('flexy','',$name));
		return $name;
	}
	

/**
 * Set and Add methods
 */

	function set_view($view='') {
		$this->view=$view;
	}

	function set_attribute($attr='',$value='') {
		if (empty($attr) and empty($value))
			$this->attributes=array();
		else
			$this->attributes[$attr]=$value;
	}
	
	function add_attribute($attr='',$value='') {
		if (isset($this->attributes[$attr]))
			$value=$this->attributes[$attr].' '.$value;
		$this->set_attribute($attr,$value);
		return $this->attributes[$attr];
	}
	
	function set_class($class='') {
		$this->set_attribute('class',$class);
	}
	
	function add_class($class) {
		return $this->add_attribute('class',$class);
	}
	
	function set_title($title='') {
		$this->title=$title;
	}

	function set_data($data=NULL) {
		if (!is_array($data)) $data=array($data);
		$this->data=$data;
	}


/**
 * Render methods
 */

	function render() {
		$this->render_attributes();
		$this->render_title();
		$this->render_data();
		return $this->render;
	}

	function render_attributes() {
		$attributes='';
		if (isset($this->attributes)) {
			foreach ($this->attributes as $key => $value)
				$attributes.=$key.'="'.$value.'" ';
		}
		$this->render['attributes']=$attributes;
		return $attributes;
	}
	
	function render_title() {
		$this->render['title']=$this->title;
		return $this->title;
	}
	
	function render_data() {
		$render=array();
		if (!empty($this->data)) {
			foreach ($this->data as $key => $value) {
				$render[$key]=$value;
			}
		}
		$this->render['data']=$render;
		return $render;
	}
	
	
/**
 * View methods
 */
	
	function view($echo=FALSE) {
		$this->render();
		$html=$this->load->view($this->view,$this->render,!$echo);

		unset($this->render['data']);
		strace_($this->render);
		return $html;
	}

}

?>
