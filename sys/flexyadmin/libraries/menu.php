<?
/**
 * FlexyAdmin V1
 *
 * frontend_menu.php Created on 9-dec-2008
 *
 * @author Jan den Besten
 */


/**
 * Class Menu
 *
 *
 *	array ( "name" => array("class"=>class, "uri"=>uri),
 *					"name" => array("class"=>class, "sub"=>array(
 *																											)
 *					)
 *
 */

class Menu {

	var $render;
	var $menu;
	var $current;

	var $tmpUrl;
	
	var $menuTable;
	var $menuTableNameField;
	

	var $tmpMenuStart;
	var $tmpMenuEnd;
	var $tmpItemStart;
	var $tmpItemEnd;

	function Menu() {
		$this->init();
	}

	function init() {
		$this->set_templates();
		$this->set_current();
		$this->set_menu_name_field();
	}

	function set_menu_from_table($table="") {
		$CI =& get_instance();
		if (empty($table)) {
			$table=$CI->cfg->get('CFG_configurations',"str_menu_table");
		}
		$CI->fd->order_by("order");
		$items=$CI->fd->get_results($table);
		$menu=array();
		foreach($items as $item) {
			if (!isset($item["b_visible"]) or ($item["b_visible"]) ) {
				$thisItem=array();
				if (isset($item["uri"])) $thisItem["uri"]=$item["uri"];	else $thisItem["uri"]=$item[$this->menuTableNameField];
				if (isset($item["str_class"])) $thisItem["class"]=$item["str_class"];
				$menu[$item[$this->menuTableNameField]]=$thisItem;
			}
		}
		$this->set_menu($menu);
		return $menu;
	}

	function set_menu_name_field($nameField="str_title") {
		$this->menuTableNameField=$nameField;
	}

	function set_menu($menu=NULL) {
		$this->menu=$menu;
	}

	function add_to_top($name,$item) {
		$menu=array_merge(array($name=>$item),$this->menu);
		$this->set_menu($menu);
	}

	function add_after($name,$item,$after) {
		if (array_key_exists($after,$this->menu)) {
				$new=array();
				foreach($this->menu as $k=>$i) {
					$new[$k]=$i;
					if ($k==$after) $new[$name]=$item;
				}
			$this->menu=$new;
			return TRUE;
		}
		else return FALSE;
	}

	function add_to_bottom($name,$item) {
		$menu=array_merge($this->menu,array($name=>$item));
		$this->set_menu($menu);
	}

	function add_sub($name,$sub) {
		if (array_key_exists($name,$this->menu)) {
			$this->menu[$name]["sub"]=$sub;
			return TRUE;
		}
		return FALSE;
 	}

	function remove_item($name) {
		unset($this->menu[$name]);
	}

	function set_current($current="") {
		$this->current=$current;
	}

	function set_current_name($current="") {
		if (isset($this->menu[$current]["uri"])) {
			$this->set_current($this->menu[$current]["uri"]);
			return $this->current;
		}
		else
			return false;
	}

	function set_url_template($tmpUrl="%s") {
		$this->tmpUrl=$tmpUrl;
	}

	function set_templates() {
		$this->set_menu_templates();
		$this->set_item_templates();
		$this->set_url_template();
	}

	function set_menu_templates($start="<ul class=\"menu %s\">",$end="</ul>") {
		$this->tmpMenuStart=$start;
		$this->tmpMenuEnd=$end;
	}

	function set_item_templates($start="<li class=\"%s\">",$end="</li>") {
		$this->tmpItemStart=$start;
		$this->tmpItemEnd=$end;
	}

	function tmp($tmp,$class="") {
		return str_replace("%s",$class,$tmp);
	}


	function render_from_table($table) {
		$menu=$this->set_menu_from_table($table);
		$this->set_menu($menu);
		return $this->render($menu);
	}

	function render($menu=NULL,$class="",$level=1) {
		$branch=array();
		$out=$this->tmp($this->tmpMenuStart);
		if (!isset($menu)) $menu=$this->menu;
		// trace_($menu);
		$pos=1;
		foreach($menu as $name=>$item) {
			// set class
			$class=strtolower(str_replace(" ","_",$name))." pos$pos lev$level";
			if ($pos==1) $class.=" first";
			if ($pos==count($menu)) $class.=" last";
			if (isset($item["class"])) $class.=" ".$item["class"];
			if (isset($item["uri"]) and $this->current==$item["uri"]) $class.=" current";
			$out.=$this->tmp($this->tmpItemStart,$class);
			// render item or submenu
			if (isset($item["uri"]))
				$out.=anchor($this->tmp($this->tmpUrl,$item["uri"]), ascii_to_entities($name), array("class"=>$class));
			if (isset($item["sub"]))
				$out.=$this->render($item["sub"],$class,$level+1);
			$out.=$this->tmp($this->tmpItemEnd);
			$pos++;
		}
		$out.=$this->tmp($this->tmpMenuEnd);
		return $out;
	}


}

?>
