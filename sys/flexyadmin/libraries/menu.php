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
	var $urlField;
	var $fields;
	var $extraFields;
	var	$attr;
	
	var $menuTable;
	
	var $tmpMenuStart;
	var $tmpMenuEnd;
	var $tmpItemStart;
	var $tmpItemEnd;
	var $itemControls;

	function Menu() {
		$this->init();
	}

	function init() {
		$this->set_templates();
		$this->set_current();
		$this->set_uri_field();
		$this->set_title_field();
		$this->set_class_field();
		$this->set_visible_field();
		$this->set_parent_field();
		$this->set_extra_field();
		$this->set_attributes();
		$this->add_controls();
	}

	function set_uri_field($uri="uri") {
		$this->fields["uri"]=$uri;
	}
	function set_title_field($title="str_title") {
		$this->fields["title"]=$title;
	}
	function set_extra_field($extra="",$startTag="<p>",$closeTag="</p>"){
		if (empty($extra))
			$this->extraFields=array();
		else
			$this->extraFields[$extra]=array("name"=>$extra,"start"=>$startTag,"close"=>$closeTag);
	}
	function set_class_field($class="str_class") {
		$this->fields["class"]=$class;
	}
	function set_visible_field($visible="b_visible") {
		$this->fields["visible"]=$visible;
	}
	function set_parent_field($parent="self_parent") {
		$this->fields["parent"]=$parent;
	}

	function set_attributes($attr="") {
		if (!is_array($attr)) $attr=array("class"=>$attr);
		$this->attr=$attr;
	}

	function set_menu_from_table($table="") {
		$counter=1;
		$CI =& get_instance();
		if (empty($table)) {
			$table=$CI->cfg->get('CFG_configurations',"str_menu_table");
		}
		// select fields
		$fields=$CI->db->list_fields($table);
		foreach ($fields as $key=>$f) {
			if (!in_array($f,$this->fields) and !isset($this->extraFields[$f])) unset($fields[$key]);
		}
		// get data form menu_table
		$CI->db->select(pk());
		$CI->db->select($fields);
		if (in_array("self_parent",$fields)) $CI->db->order_as_tree();
		$items=$CI->db->get_result($table);
		$menu=array();
		foreach($items as $item) {
			if (!isset($item[$this->fields["visible"]]) or ($item[$this->fields["visible"]]) ) {
				$thisItem=array();
				$thisItem["id"]=$item[pk()];
				if (isset($item[$this->fields["uri"]]))			$thisItem["uri"]=$item[$this->fields["uri"]];	else $thisItem["uri"]=$item[$this->fields["title"]];
				if (isset($item[$this->fields["class"]])) 	$thisItem["class"]=str_replace('|',' ',$item[$this->fields["class"]]);
				if (isset($item[$this->fields["parent"]])) 	$parent=$item[$this->fields["parent"]]; else $parent="";
				
				if (!empty($this->extraFields)) {
					foreach ($this->extraFields as $extraName => $extra) {
						if (isset($item[$extraName])) {
							$thisItem["extra"][]=$extra["start"].$item[$extraName].$extra["close"];
						}
					}
				}
				
				if (isset($menu[$parent][$item[$this->fields["title"]]]))
					$menu[$parent][$item[$this->fields["title"]]."__".$counter++]=$thisItem;
				else
					$menu[$parent][$item[$this->fields["title"]]]=$thisItem;
				
			}
		}
		// trace_($menu);
		// Set submenus on right place in array
		$item=end($menu);
		while ($item) {
			$id=key($menu);
			foreach($item as $name=>$value) {
				$sub_id=$value["id"];
				if (isset($menu[$sub_id])) {
					$menu[$id][$name]["sub"]=$menu[$sub_id];
					unset($menu[$sub_id]);
				}
			}
			$item=prev($menu);
		}
		// trace_($menu);
		// set first
		reset($menu);
		$menu=current($menu);
		$this->set_menu($menu);
		return $menu;
	}

	function set_menu($menu=NULL) {
		$this->menu=$menu;
	}

	function add($name,$item) {
		$this->menu[$name]=$item;
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
		$current=str_replace(index_page(),"",$current);
		if (substr($current,0,1)=="/") $current=substr($current,1);
		$this->current=$current;
	}

	function set_current_name($current="") {
		if (isset($this->menu[$current]["uri"])) {
			$this->set_current("/".$this->menu[$current]["uri"]);
			return $this->current;
		}
		else
			return false;
	}

	// function set_uri_template($tmpUri="%s") {
	// 	$this->set_url_template($tmpUri);
	// }
	function set_url_template($tmpUrl="%s") {
		$this->tmpUrl=$tmpUrl;
	}
	function set_url_field($urlField="uri") {
		$this->urlField=$urlField;
	}
	function set_templates() {
		$this->set_menu_templates();
		$this->set_item_templates();
		$this->set_url_template();
		$this->set_url_field();
	}
	function set_menu_templates($start="<ul %s>",$end="</ul>") {
		$this->tmpMenuStart=$start;
		$this->tmpMenuEnd=$end;
	}
	function set_item_templates($start="<li %s>",$end="</li>") {
		$this->tmpItemStart=$start;
		$this->tmpItemEnd=$end;
	}
	
	function tmp($tmp,$attr="") {
		if (!empty($attr)) {
			if (is_string($attr)) {
				return str_replace("%s",$attr,$tmp);
			}
			else {
				$a="";	
				foreach ($attr as $key => $value) $a.=$key.'="'.$value.'" ';
				return str_replace("%s",$a,$tmp);
			}
		}
		return str_replace("%s","",$tmp);
	}

	function render_from_table($table) {
		$menu=$this->set_menu_from_table($table);
		$this->set_menu($menu);
		return $this->render($menu);
	}

	function inUri($in,$uri) {
		$in=explode("/",$in);
		$uri=explode("/",$uri);
		// if same TRUE
		if ($in==$uri) return TRUE;
		// if in longer then uri, impossible active, FALSE
		if (count($uri)<count($in)) return FALSE;
		// ok, possible active branch, first set in as long as uri, then check if same
		$uri=array_slice($uri,0,count($in));
		if ($in==$uri) return TRUE;
		
		// $active=FALSE;
		// while (!$active and (count($in)>0) ) {
		// 	$active=$in[count($in)-1]==$uri[count($uri)-1];
		// 	array_pop($in);
		// 	array_pop($uri);
		// }
		return FALSE;
	}

	function add_controls($controls="") {
		$this->itemControls=$controls;
	}

	function render($menu=NULL,$attr="",$level=1,$preUri="") {
		if (empty($attr)) $attr=$this->attr;
		if (!is_array($attr)) $attr=array("class"=>$attr);
		if (empty($attr["class"])) $attr["class"]="";
		$attr["class"].=" lev$level";
		if ($level>1) unset($attr["id"]);
		$branch=array();
		$out=$this->tmp($this->tmpMenuStart,$attr);
		if (!isset($menu)) $menu=$this->menu;
		// trace_($menu);
		$pos=1;
		foreach($menu as $name=>$item) {
			$thisUri=$item[$this->urlField];
			if (!empty($preUri) and $this->urlField=="uri") $thisUri=$preUri."/".$thisUri;
			// set class
			$cName=strtolower(str_replace(" ","_",$name));
			$link=$this->tmp($this->tmpUrl,$thisUri);
			// trace_($link);
			$itemAttr=array();
			$itemAttr['class']=$attr['class'];
			$itemAttr["class"]="$cName pos$pos lev$level";
			if ($pos==1)																$itemAttr["class"].=" first";
			if ($pos==count($menu))											$itemAttr["class"].=" last";
			if (isset($item["class"]))									$itemAttr["class"].=" ".$item["class"];
			if ($this->current==$link) 									$itemAttr["class"].=" current";
			if ($this->inUri($link,$this->current))			$itemAttr["class"].=" active";
			$out.=$this->tmp($this->tmpItemStart,array("class"=>$itemAttr["class"]));
			// render item or submenu
			if (isset($item["uri"])) {
				$showName=ascii_to_entities($name);
				$pre=get_prefix($showName,"__");
				if (!empty($pre)) $showName=$pre;
				if (isset($item["help"])) $showName=help($showName,$item["help"]);
				if (isset($item['extra'])) {
					foreach ($item['extra'] as $extra) {
						$showName.=$extra;
					}
				}
				// extra attributes set?
				$extraAttr=array();
				$extraAttr=$item;
				unset($extraAttr['class']);
				unset($extraAttr['uri']);
				unset($extraAttr['id']);
				$itemAttr=array_merge($itemAttr,$extraAttr);
				// trace_($itemAttr);
				$out.=anchor($link, $showName, $itemAttr);
			}
			if (isset($item["sub"]))
				$out.=$this->render($item["sub"],"$cName",$level+1,$thisUri);
			$out.=$this->tmp($this->tmpItemEnd);
			$pos++;
		}
		$out.=$this->tmp($this->tmpMenuEnd);
		return $out;
	}


}

?>
