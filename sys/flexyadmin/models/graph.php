<?
/**
 * FlexyAdmin V1
 *
 * grid.php Created on 21-okt-2008
 *
 * @author Jan den Besten
 */


/**
 * Class Grid (model)
 *
 * Handles grid rendering
 *
 */

class Graph Extends Model {

	var $captions=array();
	var $headings=array();
	var $rows=array();
	var $max;

	var $type;			// html | files

	function Graph() {
		parent::Model();
		$this->init();
	}

	function init() {
		$this->renderExtraClass=array();
		$this->set_captions();
		$this->set_headings();
		$this->rows=array();
		$this->set_type();
		$this->set_max();
	}

	function set_captions($caption="") {
		$this->captions=NULL;
		$this->captions[]=array("class"=>get_prefix(strip_tags($caption)," "),"cell"=>$caption);
	}

	function set_headings($headings=NULL) {
		if (isset($headings) and !empty($headings)) {
			foreach($headings as $name=>$heading) {
				if (is_numeric($name)) $name=$heading;
				$this->set_heading($name,$heading);
			}
		}
	}

	function set_heading($name,$heading) {
		$this->headings[$name]=$heading;
	}

	function set_type($type="html") {
		$this->type=$type;
	}

	function set_current($currentId=NULL) {
		$this->currentId=$currentId;
	}

	function set_data($data=NULL,$name="") {
		if (isset($data) and !empty($data)) {
			$this->rows=$data;
			$this->set_headings(array_keys(current($data)));
		}
		$this->set_captions($name);
	}
	
	function set_max($max=100) {
		$this->max=$max;
	}

/**
 * function render()
 *
 * Returns grid output (a table) according to template
 *
 * @param string $type html or other format
 * @param string $class extra attributes such as class
 * @return string	grid output
 */

	function render($type="", $tableClass="", $extraClass="") {
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
