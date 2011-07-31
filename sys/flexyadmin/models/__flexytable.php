<?
require_once(APPPATH."models/flexyhtml.php");


class FlexyTable Extends FlexyHtml {

	var $headings;
	var $nrColumns;
	var $nrRows;

	function set_data($data=NULL) {
		if (!is_array($data)) $data=array('data'=>$data);
		$this->data=$data;
		$headings=current($data);
		$this->nrRows=count($data);
		$this->nrColumns=count($headings);
		if (!empty($headings)) {
			$headings=array_keys($headings);
			$headings=array_combine($headings,$headings);
		}
		$this->set_headings($headings);
	}

	function set_headings($headings=NULL) {
		$this->headings=$headings;
	}


	function render() {
		parent::render();
		$this->render_headings();
		return $this->render;
	}

	function render_headings() {
		$this->render['headings']=$this->headings;
	}

	function render_data() {
		$this->render['data']=$this->data;
		$this->render['nrColumns']=$this->nrColumns;
		$this->render['nrRows']=$this->nrRows;		
		return $this->data;
	}

}

?>
