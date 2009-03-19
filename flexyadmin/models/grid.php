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

	var $caption;
	var $headings=array();
	var $rows=array();
	var $rowId;
	var $currentId;

	var $type;			// html | files

	var $tmpStart;
	var $tmpEnd;
	var $tmpCaptionStart;
	var $tmpCaptionEnd;
	var $tmpHeadingsStart;
	var $tmpHeadingsEnd;
	var $tmpHeadStart;
	var $tmpHeadEnd;
	var $tmpRowStart;
	var $tmpRowEnd;
	var $tmpCellStart;
	var $tmpCellEnd;

	function Grid() {
		parent::Model();
		$this->init();
	}

	function init() {
		$this->renderExtraClass=array();
		$this->set_caption();
		$this->set_headings();
		$this->rows=array();
		$this->set_type();
		$this->set_current();
	}

	function set_caption($caption="") {
		$this->caption=div().$caption._div();
	}

	function prepend_to_caption($add,$class="") {
		$this->caption=div($class).$add._div().$this->caption;
	}

	function append_to_caption($add,$class="") {
		$this->caption.=div($class).$add._div();
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
		$func="set_".$type."_templates";
		$this->$func();
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
		$this->set_caption($name);
	}

/**
 * HTML template functions
 */
	function set_html_templates() {
		$this->set_html_grid_templates();
		$this->set_html_caption_templates();
		$this->set_html_headings_templates();
		$this->set_html_head_templates();
		$this->set_html_row_templates();
		$this->set_html_cell_templates();
	}

	function set_html_grid_templates($start="<table class=\"%s\">",$end="</tbody></table>") {
		$this->tmpStart=$start;
		$this->tmpEnd=$end;
	}

	function set_html_caption_templates($start="<caption class=\"caption %s\">",$end="</caption>") {
		$this->tmpCaptionStart=$start;
		$this->tmpCaptionEnd=$end;
	}

	function set_html_headings_templates($start="<thead class=\"heading %s\"><tr class=\"heading %s\">",$end="</tr></thead><tbody>") {
		$this->tmpHeadingsStart=$start;
		$this->tmpHeadingsEnd=$end;
	}

	function set_html_head_templates($start="<th class=\"%s\">",$end="</th>") {
		$this->tmpHeadStart=$start;
		$this->tmpHeadEnd=$end;
	}

	function set_html_row_templates($start="<tr class=\"%s\" id=\"%d\">",$end="</tr>") {
		$this->tmpRowStart=$start;
		$this->tmpRowEnd=$end;
	}

	function set_html_cell_templates($start="<td class=\"%s\" rowid=\"%d\">",$end="</td>") {
		$this->tmpCellStart=$start;
		$this->tmpCellEnd=$end;
	}

	function tmp($tmp,$class="",$id="") {
		$tmp=str_replace("%s",$class,$tmp);
		if (!empty($id)) $tmp=str_replace("%d",$id,$tmp);
		return $tmp;
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
		$out=$this->tmp($this->tmpStart,"$tableClass $extraClass");

		if (!empty($this->caption))
			$out.=$this->tmp($this->tmpCaptionStart,"$tableClass $extraClass") . $this->caption . $this->tmp($this->tmpCaptionEnd);

		if (!empty($this->headings)) {
			$out.=$this->tmp($this->tmpHeadingsStart,"$tableClass $extraClass");
			foreach($this->headings as $name=>$heading) {
				$out.=$this->tmp($this->tmpHeadStart,"$tableClass $name $extraClass ".alternator("oddcol","evencol")) . $heading . $this->tmp($this->tmpHeadEnd);
			}
			$out.=$this->tmp($this->tmpHeadingsEnd);
		}

		$data=$this->rows;
		$alt="";
		foreach($data as $id=>$row) {
			$currClass="";
			if ($id==$this->currentId) {
				$currClass="current ";
			}
			if ($alt=="evenrow") $alt="oddrow"; else $alt="evenrow";
			$out.=$this->tmp($this->tmpRowStart,"$tableClass id$id $extraClass $currClass $alt",$id);
			$cn=0;
			foreach($row as $name=>$cell) {
				$pre=get_prefix($name);
				if ($pre==$name) $pre="";
				$out.=$this->tmp($this->tmpCellStart,"$tableClass id$id $name $pre $extraClass $currClass nr$cn ".alternator("oddcol","evencol")) . $cell . $this->tmp($this->tmpCellEnd);
				$cn++;
			}
			$out.=$this->tmp($this->tmpRowEnd);
		}

		$out.=$this->tmp($this->tmpEnd);
		log_('info',"grid: rendering");
		return $out;
	}

}

?>
