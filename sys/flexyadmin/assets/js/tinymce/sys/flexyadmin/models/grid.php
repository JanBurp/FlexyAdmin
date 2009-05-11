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

class Grid Extends Model {

	var $captions=array();
	var $headings=array();
	var $rows=array();
	var $rowId;
	var $currentId;

	var $type;			// html | files

	function Grid() {
		parent::Model();
		$this->init();
	}

	function init() {
		$this->renderExtraClass=array();
		$this->set_captions();
		$this->set_headings();
		$this->rows=array();
		$this->set_type();
		$this->set_current();
	}

	function set_captions($caption="") {
		$this->captions=NULL;
		$this->captions[]=array("class"=>get_prefix(strip_tags($caption)," "),"cell"=>$caption);
	}

	function prepend_to_captions($add,$class="") {
		array_unshift($this->captions, array("class"=>$class,"cell"=>$add));
	}

	function append_to_captions($add,$class="") {
		array_push($this->captions, array("class"=>$class,"cell"=>$add));
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

	function set_rows($rows=NULL) {
		$this->rows=$rows;
		$this->rowId=0;
	}

	function next_row_id() {
		return $this->rowId++;
	}

	function add_row($row=NULL,$rowId="") {
		if (!empty($row))	{
			if (empty($rowId))
				$rowId=$this->next_row_id();
			$this->rows[$rowId]=$row;
		}
		return $rowId;
	}

	function set_data($data=NULL,$name="") {
		if (isset($data) and !empty($data)) {
			$this->rows=$data;
			$this->set_headings(array_keys(current($data)));
		}
		$this->set_captions($name);
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

		$table["heading"]["class"]="$tableClass $extraClass";
		foreach($this->headings as $name=>$heading) {
			$table["heading"]["row"][]=array(	"class"	=>"$tableClass $name $extraClass ".alternator("oddcol","evencol"),
																				"cell"	=> $heading );
		}

		$data=$this->rows;
		$alt="";
		foreach($data as $id=>$row) {
			$currClass="";
			if ($this->currentId!=NULL and $id==$this->currentId) $currClass="current ";
			if ($alt=="evenrow") $alt="oddrow"; else $alt="evenrow";
			$tableRowClass="$tableClass id$id $extraClass $currClass $alt";
			$tableRowId=$id;

			$tableCells=array();
			$cn=0;
			foreach($row as $name=>$cell) {
				// if (empty($cell)) $cell="&nbsp;";
				$pre=get_prefix($name);
				if ($pre==$name) $pre="";
				$tableCells[]=array(	"class"	=> "$tableClass id$id $name $pre $extraClass $currClass nr$cn ".alternator("oddcol","evencol"),
															"cell"	=> $cell );
				$cn++;
			}

			$table["rows"][]=array(	"class"	=> $tableRowClass,
															"id"		=> $tableRowId,
															"row"	=> $tableCells );
		}
		
		log_('info',"grid: rendering");
		return $table;
	}

}

?>
