<?php 
/** \ingroup models
 * Maakt een mooie tabel met opties zoals zoeken, sorteren etc.
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */
class Grid extends CI_Model {

  private $captions=array();
  private $headings=array();
  private $rows=array();
  private $rowId;
  private $order;
  private $search;
  private $searchfields;
  private $currentId;
  private $renderData;
  private $pagin;
	private $type;			// html | files
  private $editable=FALSE;

	/**
	 * __construct
	 */
  public function __construct() {
		parent::__construct();
		$this->init();
	}

  /**
   * Initialiseer
   *
   * @return object $this;
   * @author Jan den Besten
   */
	public function init() {
		$this->renderExtraClass=array();
		$this->set_captions();
		$this->set_headings();
		$this->rows=array();
		$this->set_type();
		$this->set_order();
		$this->set_search();
		$this->set_searchfields();
		$this->set_current();
		$this->set_pagination();
    return $this;
	}

  /**
   * Stelt in of de grid editable is of niet
   *
   * @param bool $editable 
   * @return void
   * @author Jan den Besten
   */
  public function set_editable($editable=true) {
    $this->editable=$editable;
  }

  /**
   * Stel koppen in
   *
   * @param array $caption 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_captions($caption="") {
		$this->captions=NULL;
		$this->captions[]=array("class"=>get_prefix(strip_tags($caption)," "),"cell"=>$caption);
    return $this;
	}

  /**
   * Voeg een kop aan het begin toe
   *
   * @param array $add toe te voegen cel
   * @param string $class
   * @return object $this;
   * @author Jan den Besten
   **/
  public function prepend_to_captions($add,$class="") {
		array_unshift($this->captions, array("class"=>$class,"cell"=>$add));
    return $this;
	}

  /**
   * Voeg een kop aan het eind toe
   *
   * @param array $add toe te voegen cel
   * @param string $class
   * @return object $this;
   * @author Jan den Besten
   **/
	public function append_to_captions($add,$class="") {
		array_push($this->captions, array("class"=>$class,"cell"=>$add));
    return $this;
	}

  /**
   * Stel de koppen in
   *
   * @param array $headings 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_headings($headings=NULL) {
		if (isset($headings) and !empty($headings)) {
			foreach($headings as $name=>$heading) {
				if (is_numeric($name)) $name=$heading;
				$this->set_heading($name,$heading);
			}
		}
    return $this;
	}

  /**
   * Pas heading aan
   *
   * @param string $name 
   * @param string $heading 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_heading($name,$heading) {
		$this->headings[$name]=$heading;
    return $this;
	}

  /**
   * Zet output type
   *
   * @param string $type 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_type($type="html") {
		$this->type=$type;
    return $this;
	}

  /**
   * Stelt volgorde in
   *
   * @param string $order default='id'
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_order($order='id') {
		$orderArr=explode(',',$order);
		foreach ($orderArr as $key=>$order) {
			$post=get_suffix($order,' ');
			if ($post=='DESC') $order='_'.$order;
			$order=str_replace(array(' DESC',' ASC'),'',$order);
			$orderArr[$key]=trim($order);
		}
		$this->order=$orderArr;
    return $this;
	}
	
  /**
   * Stel zoekterm in
   *
   * @param string $search['']
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_search($search='') {
		$this->search=$search;
    return $this;
	}

  /**
   * Velden waarin uitgebreid zoeken mogelijk is
   *
   * @param array $fields 
   * @return $this
   * @author Jan den Besten
   */
	public function set_searchfields( $searchfields = array()) {
		$this->searchfields=$searchfields;
    return $this;
	}


  /**
   * Stel huidig item in
   *
   * @param string $currentId 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_current($currentId=NULL) {
		$this->currentId=$currentId;
    return $this;
	}

	/**
	 * Zet pagination
	 *
	 * @param bool $pagin default=FALSE
	 * @return object $this;
	 * @author Jan den Besten
	 */
  public function set_pagination($pagin=false) {
		if ($pagin) {
			if (!empty($this->currentId))
				$pagin['base_url'].='/current/'.$this->currentId.'/offset';
			else
				$pagin['base_url'].='/offset';
			
			$default=array('auto'=>TRUE,'num_links'=>5,'first_link'=>'&lt;&lt;','last_link'=>'&gt;&gt;','total_tag_open'=>'<span class="pagination_total">','total_tag_close'=>'</span>');
			$pagin=array_merge($default,$pagin);
		}
		$this->pagin=$pagin;
    return $this;
	}

  /**
   * Stel rijen in
   *
   * @param array $rows 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_rows($rows=NULL) {
		$this->rows=$rows;
		$this->rowId=0;
    return $this;
	}

  /**
   * Geeft volgende row
   *
   * @return int
   * @author Jan den Besten
   */
	public function next_row_id() {
		return $this->rowId++;
	}

  /**
   * Voegt rij toe
   *
   * @param array $row 
   * @param int $rowId 
   * @return int $rowId
   * @author Jan den Besten
   */
	public function add_row($row=NULL,$rowId="") {
		if (!empty($row))	{
			if (empty($rowId))
				$rowId=$this->next_row_id();
			$this->rows[$rowId]=$row;
		}
		return $rowId;
	}

  /**
   * Stelt de grid data in
   *
   * @param array $data 
   * @param string $name Titel
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_data($data=NULL,$name="") {
		if (isset($data) and !empty($data)) {
			$this->rows=$data;
			if (is_array($data)) $this->set_headings(array_keys(current($data)));
		}
		$this->set_captions($name);
    return $this;
	}

  /**
   * Geeft de gegenereeerde Grid als Array data
   *
   * @param string $type['']
   * @param string $table['']
   * @param string $class['']
   * @return mixed
   */
	public function render($type="", $table="", $class="") {
		if (!empty($type)) $this->set_type($type);
    
    $current_ids=explode('_',$this->currentId);

		$renderData=array();
    $renderData['title']=current($this->captions)['cell'];

		if ($this->pagin) {
			$this->pagination->initialize($this->pagin);
			$this->pagin['links']=$this->pagination->create_links();
			$renderData['pagination']=$this->pagin;
			$class.=' pagination';
		}

		$renderData["class"]="$table $class";
    if ($this->editable) $renderData["class"].=' editable';
		$renderData['order']=implode(':',$this->order);
		$renderData['search']=$this->search;
		$renderData['searchfields']=$this->searchfields;
    
		$renderData["caption"]["class"]="$table $class";
		$renderData["caption"]["row"]=$this->captions;

		$renderData["headers"]=array();
    $firstOrder=remove_suffix($this->order[0],'.');
		foreach($this->headings as $name=>$heading) {
			$renderData["headers"][]=$name;
		}

		$data=$this->rows;
    $renderData['data']=$data;
		
		log_('info',"grid: rendering");
		$this->renderData=$renderData;
		return $this->renderData;
	}
	
	
  /**
   * Geeft GRID als HTML terug
   *
   * @param string $type 
   * @param string $table 
   * @param string $class 
   * @return string
   * @author Jan den Besten
   */
	public function view($type="", $table="", $class="") {
		if (empty($this->renderData)) $this->render($type, $table, $class);
		$html=$this->load->view("admin/grid",$this->renderData,true);
		return $html;
	}

}

?>
