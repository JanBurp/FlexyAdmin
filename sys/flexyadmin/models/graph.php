<?
/**
 * Maakt grafieken, zoals bij de statistieken
 *
 * @package default
 * @author Jan den Besten
 */
class Graph Extends CI_Model {

  private $captions=array();
  private $headings=array();
  private $rows=array();
  private $max;
	private $type;			// html | files

  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
		$this->init();
	}

  /**
   * Initialiseren
   *
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	public function init() {
		$this->renderExtraClass=array();
		$this->set_captions();
		$this->set_headings();
		$this->rows=array();
		$this->set_type();
		$this->set_max();
    return $this;
	}

  /**
   * Stelt titel in
   *
   * @param string $caption 
   * @return object $this
   * @author Jan den Besten
   */
	public function set_captions($caption="") {
		$this->captions=NULL;
		$this->captions[]=array("class"=>get_prefix(strip_tags($caption)," "),"cell"=>$caption);
    return $this;
	}

  /**
   * Stelt kopjes in
   *
   * @param array $headings 
   * @return object $this
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
   * Past kop aan
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
   * Type output
   *
   * @param string $type 
   * @return object $this;
   * @author Jan den Besten
   * @ignore
   * @depricated
   */
	public function set_type($type="html") {
		$this->type=$type;
    return $this;
	}

  // public function set_current($currentId=NULL) {
  //   $this->currentId=$currentId;
  // }

  /**
   * Zet de data die getoond moet worden
   *
   * @param string $data 
   * @param string $name 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_data($data=NULL,$name="") {
		if (isset($data) and !empty($data)) {
			$this->rows=$data;
			$this->set_headings(array_keys(current($data)));
		}
		$this->set_captions($name);
    return $this;
	}
	
  /**
   * Stelt maximun in van bar
   *
   * @param string $max 
   * @return object $this;
   * @author Jan den Besten
   */
	public function set_max($max=100) {
		$this->max=$max;
    return $this;
	}

  /**
   * Geeft de HTML output van de grafiek
   *
   * @param string $type['']
   * @param string $class['']
   * @return string
   */
	public function render($type="", $tableClass="", $extraClass="") {
		if (!empty($type)) $this->set_type($type);
		
		$table=array();
		$table["class"]="$tableClass $extraClass";

		$table["caption"]["class"]="$tableClass $extraClass";
		$table["caption"]["row"]=$this->captions;

		$data=$this->rows;
		$max=$this->max;

		$alt="";
		foreach($data as $id=>$row) {
			// $currClass="";
			// if ($this->currentId!=NULL and $id==$this->currentId) $currClass="current ";
			if ($alt=="evenrow") $alt="oddrow"; else $alt="evenrow";
			$tableRowClass="$tableClass $extraClass $alt";
			$tableRowId=$id;

			$tableCells=array();
			$cn=0;
			foreach($row as $name=>$cell) {
				$pre=get_prefix($name);
				if ($pre==$name) $pre="";
				$tableCells[]=array(	"class"				=> "$tableClass $name $extraClass nr$cn ".alternator("oddcol","evencol"),
															"value"				=> $cell,
															"percentage" 	=> round(($cell/$max)*100) );
				$cn++;
			}

			$table["rows"][]=array(	"class"	=> $tableRowClass,
															"row"		=> $tableCells );
		}
		
		log_('info',"graph: rendering");
		return $table;
	}

}

?>
