<?
/**
 * FlexyAdmin V1
 *
 * @author Jan den Besten
 */


/**
 * Class Tree (model)
 *
 * Handles grid rendering
 *
 */

class Tree Extends CI_Model {

	var $captions=array();
	var $headings=array();
	var $tree;
	var $currentId;

	function __construct() {
		parent::__construct();
		$this->init();
	}

	function init() {
		$this->renderExtraClass=array();
		$this->set_captions();
		$this->tree="";
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

	function set_tree($html,$caption) {
		$this->tree=$html;
		$this->set_captions($caption);
	}

	function set_current($currentId=NULL) {
		$this->currentId=$currentId;
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

	function render($tableClass="", $extraClass="") {
		$table=array();
		$table["class"]="$tableClass $extraClass";
		$table["caption"]["class"]="$tableClass $extraClass";
		$table["caption"]["row"]=$this->captions;
		$table["heading"]["class"]="$tableClass $extraClass";
		foreach($this->headings as $name=>$heading) {
			$table["heading"]["row"][]=array(	"class"	=>"$tableClass $name $extraClass ".alternator("oddcol","evencol"),
																				"cell"	=> $heading );
		}

		$table["tree"]=$this->tree;
		
		log_('info',"Tree: rendering");
		return $table;
	}

}

?>
