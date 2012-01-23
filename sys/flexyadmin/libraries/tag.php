<?
class Tag {
	
	var $tag='div';
	var $closetag=true;
	var $atttributes=array();
	var $html='';
	var $view='admin/html/tag';

	public function __construct($tag='div',$closetag=true) {
		$this->set_tag($tag);
		$this->close_tag($closetag);
	}
	
	public function set_tag($tag='div') {
		$this->tag=$tag;
		return $this;
	}

	public function close_tag($closetag=true) {
		$this->closetag=$closetag;
		return $this;
	}
	
	public function add_attributes($attr,$value='') {
		if (!is_array($attr)) $attr=array($attr=>$value);
		$this->atttributes=array_merge($this->atttributes,$attr);
		return $this;
	}
	
	public function add_class($class) {
		if (!isset($this->atttributes['class']))
			$this->atttributes['class']=$class;
		else
			$this->atttributes['class']=add_string($this->atttributes['class'],$class,' ');
		return $this;
	}
	
	public function set_id($id) {
		$this->atttributes['id']=$id;
		return $this;
	}
	
	public function set_view($view='html') {
		$this->view=$view;
		return $this;
	}
	
	public function add_html($html) {
		$this->html.=$html;
		return $this;
	}
	public function add_content($html) {
		return $this->add_html($html);
	}

	
	public function view($show=true) {
		$data=array(
			'tag'					=> $this->tag,
			'closetag'		=> $this->closetag,
			'attributes'	=> implode_attributes($this->atttributes),
			'html'				=> $this->html
		);
		$CI=&get_instance();
		return $CI->load->view($this->view,$data,$show);
	}


}

?>
