<?

/**
 * Maakt een mooie tabel met opties zoals zoeken, sorteren etc.
 *
 * @package default
 * @author Jan den Besten
 */
class Grid Extends CI_Model {

  private $captions=array();
  private $headings=array();
  private $rows=array();
  private $rowId;
  private $order;
  private $search;
  private $currentId;
  private $renderData;
  private $pagin;
	private $type;			// html | files
  private $editable=FALSE;

	/**
	 * @ignore
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
   * @ignore
   */
	public function init() {
		$this->renderExtraClass=array();
		$this->set_captions();
		$this->set_headings();
		$this->rows=array();
		$this->set_type();
		$this->set_order();
		$this->set_search();
		$this->set_current();
		$this->set_pagination();
    return $this;
	}

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
   * @ignore
   */
	public function set_type($type="html") {
		$this->type=$type;
    return $this;
	}

  /**
   * Stelt volgorde in
   *
   * @param string $order['id']
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
	 * @param bool $pagin[FALSE]
	 * @return object $this;
	 * @author Jan den Besten
	 */
  public function set_pagination($pagin=false) {
		if ($pagin) {
			if (!empty($this->currentId))
				$pagin['base_url'].='/current/'.$this->currentId.'/offset';
			else
				$pagin['base_url'].='/offset';
			
			$default=array('auto'=>TRUE,'num_links'=>5,'first_link'=>'&lt;&lt;','last_link'=>'&gt;&gt;' ); //,'total_tag_open'=>'<span class="pager_totals">','total_tag_close'=>'</span>');
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
   * @ignore
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
   * @param string $class['']
   * @return mixed
   */
	public function render($type="", $tableClass="", $extraClass="") {
		if (!empty($type)) $this->set_type($type);

		$table=array();

		if ($this->pagin) {
			$this->pagination->initialize($this->pagin);
			$this->pagin['links']=$this->pagination->create_links();
			$table['pagination']=$this->pagin;
			$extraClass.=' pagination';
		}

		$table["class"]="$tableClass $extraClass";
    if ($this->editable) $table["class"].=' editable';
		$table['order']=implode(':',$this->order);
		$table['search']=$this->search;

		$table["caption"]["class"]="$tableClass $extraClass";
		$table["caption"]["row"]=$this->captions;

		$table["heading"]["class"]="$tableClass $extraClass";
    $firstOrder=remove_suffix($this->order[0],'__');
		foreach($this->headings as $name=>$heading) {
			$orderClass='';
			if ($firstOrder==$name) $orderClass=' headerSortDown';
			if ($firstOrder=='_'.$name) $orderClass=' headerSortUp';
			if ($name=='id') $orderClass.=' edit';
      $prefix=get_prefix($name);
      if ($prefix=='id' and $name!='id') $prefix='id_';
			$table["heading"]["row"][]=array(	"class"	=>"$tableClass $name ".$prefix." $extraClass ".alternator("oddcol","evencol").$orderClass, "cell"	=> $heading );
		}

		$data=$this->rows;
		$alt="";
		if (is_array($data)) {
			foreach($data as $id=>$row) {
				$currClass="";
				if ($this->currentId!=NULL and $id==$this->currentId) $currClass="current ";
				if ($alt=="evenrow") $alt="oddrow"; else $alt="evenrow";
				$tableRowClass="$tableClass id$id $extraClass $currClass $alt";
				$tableRowId=$id;

				$tableCells=array();
				$cn=0;
				foreach($row as $name=>$cell) {
          $cellClass='';
					// if (empty($cell)) $cell="&nbsp;";
					$pre=get_prefix($name);
					if ($pre==$name) $pre="";
          if ($pre=='id' and $pre!=$name) $pre='id_';
          $cell_value=$cell;
          if (is_array($cell)) {
            $cell_value=$cell['value'];
            if (el('editable',$cell)) $cellClass.=' editable';
          }
					$tableCells[]=array(	"class"	=> "$tableClass id$id $name $pre $extraClass $currClass nr$cn $cellClass ".alternator("oddcol","evencol"), 
																"cell"	=> $cell_value );
					$cn++;
				}

				$table["rows"][]=array(	"class"	=> $tableRowClass,
																"id"		=> $tableRowId,
																"row"	=> $tableCells );
			}
		}
		
		log_('info',"grid: rendering");
		$this->renderData=$table;
		return $table;
	}
	
	
  /**
   * Geeft GRID als HTML terug
   *
   * @param string $type 
   * @param string $tableClass 
   * @param string $extraClass 
   * @return string
   * @author Jan den Besten
   */
	public function view($type="", $tableClass="", $extraClass="") {
		if (empty($this->renderData)) $this->render($type, $tableClass, $extraClass);
		$html=$this->load->view("admin/grid",$this->renderData,true);
		return $html;
	}

}

?>
