<?
class Html_model Extends Model {

	var $data;
	var $render;
	var $html;
	var $view;

	function Html_model() {
		parent::Model();
		$this->init();
	}

	function init() {
		$this->set_view();
		$this->set_attribute();
		$this->set_title();
		$this->set_data();
		$this->render=array();
		$this->html='';
	}

	function set_view($view='admin/html_view') {
		$this->view=$view;
	}



	function set_attribute($attr='',$value='') {
		$this->render['attributes'][$attr]=$value;
	}
	
	function add_attribute($attr,$value) {
		if (isset($this->render['attributes'][$attr]))
			$value=$this->render['attributes'][$attr].' '.$value;
		$this->set_attribute($attr,$value);
		return $this->render['attributes'][$attr];
	}
	
	function set_class($class='') {
		$this->set_attribute('class',$class);
	}
	
	function add_class($class) {
		return $this->add_attribute('class',$class);
	}
	


	
	function set_title($title='Title') {
		$this->render['title']=$title;
	}




	function set_data($data=NULL) {
		$this->data=$data;
	}

	function render() {
		$this->render_attributes();
		$this->render_data();
		return $this->render;
	}

	function render_attributes() {
		$attributes='';
		if (isset($this->render['attributes'])) {
			foreach ($this->render['attributes'] as $key => $value)
				$attributes.=$key.'="'.$value.'" ';
		}
		$this->render['attributes_render']=$attributes;
		return $attributes;
	}
	
	function render_data() {
		$render=array();
		if (!empty($this->data)) {
			foreach ($this->data as $key => $value) {
				$render['data'][$key]=$value;
			}
		}
		$this->render['data']=$render;
		return $render;
	}
	
	function view($echo=FALSE) {
		$this->render();
		trace_($this->render);
		$html=$this->load->view($this->view,$this->render,!$echo);
		return $html;
	}

}

?>
