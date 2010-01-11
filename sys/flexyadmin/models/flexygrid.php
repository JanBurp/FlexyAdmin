<?
require_once(APPPATH."models/flexytable.php");


class FlexyGrid Extends FlexyTable {

	var $pagination;
	var $order;
	var $buttons;

	function init() {
		parent::init();
		$this->set_pagination_length();
		$this->set_pagination_url();
		$this->set_pagination_page();
		$this->set_order();
	}

	function set_pagination_length($length=0) {
		$this->pagination['length']=$length;
	}

	function set_pagination_url($url='') {
		$this->pagination['url']=$url;
	}
	
	function set_pagination_page($page=1) {
		$this->pagination['page']=$page;
	}
	
	function set_order($column='',$direction='') {
		if ($direction=='down' and substr($column,1,1)!='_') $column='_'.$column;
		$this->order=$column;
	}

	function render() {
		parent::render();
		$this->render_pagination();
		$this->render['url']=$this->pagination['url'];
		$this->render['page']=$this->pagination['page'];
		$this->render['order']=$this->order;
		return $this->render;
	}
	
	function render_attributes() {
		if ($this->pagination['length']>0) $this->add_class('pagination');
		parent::render_attributes();
	}

	function render_pagination() {
		$this->render['pagination']=NULL;
		if ($this->pagination['length']>0 and $this->render['totalRows']!=$this->render['nrRows']) {
			$this->add_class('pagination');
			$pages=$this->render['totalRows'] / $this->pagination['length'];
			for ($p=1; $p < $pages; $p++) { 
				$this->render['pagination']['pages'][$p]=$p;
			}
			$this->render['pagination']['prev']=$this->pagination['page']-1;
			if ($this->render['pagination']['prev']<1) $this->render['pagination']['prev']='';
			$this->render['pagination']['next']=$this->pagination['page']+1;
			if ($this->render['pagination']['next']>$pages) $this->render['pagination']['next']='';
			// pre render
			$render=$this->_url($this->render['pagination']['prev'],'&lt&lt').nbs();
			foreach ($this->render['pagination']['pages'] as $page) {
				$render.= $this->_url($page).'|';
			}
			$render=substr($render,0,strlen($render)-1);
			$render.=nbs().$this->_url($this->render['pagination']['next'],'&gt&gt');
			$this->render['pagination']['render']=$render;
		}
	}
	
	function _url($page,$link='') {
		if (empty($page)) return '';
		if (empty($link)) $link=$page;
		$attr=NULL;
		if ($page==$this->pagination['page']) $attr=array('class'=>'current');
		return anchor($this->pagination['url'].'/page/'.$page.'/order/'.$this->order,$link,$attr);
	}



	function render_data() {
		parent::render_data();
		if ($this->pagination['length']>0) {
			$data=array_slice($this->data,$this->pagination['page']*$this->pagination['length'],$this->pagination['length']);
		}
		$this->render['totalRows']=$this->render['nrRows'];
		$this->render['nrRows']=count($data);
		$this->render['data']=$data;
		return $data;
	}

}

?>
