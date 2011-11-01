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
 *	array("uri"=>uri, "name"=>name, "class"=>class, "sub"=>array())
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
	var $itemAttr;
	var $currentAsActive;
	
	var $changeModules;
	
	var $menuTable;
	
	var $tmpMenuStart;
	var $tmpMenuEnd;
	var $tmpItemStart;
	var $tmpItemEnd;
	var $itemControls;

	function __construct() {
		$this->init();
	}

	function init() {
		$this->set_templates();
		$this->set_menu_table();
		$this->set_current();
		$this->set_uri_field();
		$this->set_title_field();
		$this->set_class_field();
		$this->set_visible_field();
		$this->set_clickable_field();
		$this->set_parent_field();
		$this->set_extra_field();
		$this->set_attributes();
		$this->add_controls();
		$this->set_current_class_active(false);
		$this->register_change_module();
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
	
	function remove_extra_fields($fields='',$menu='',$level=0) {
		$this->set_extra_field();
		if (empty($menu)) {$menu=$this->menu;}
		foreach ($menu as $uri => $item) {
			unset($menu[$uri]['extra']);
			if (isset($item['sub']) and !empty($item['sub'])) {$menu[$uri]['sub']=$this->remove_extra_fields($fields,$item['sub'],$level+1);}
		}
		if ($level==0) $this->menu=$menu;
		return $menu;
	}
	function set_class_field($class="str_class") {
		$this->fields["class"]=$class;
	}
	function add_bool_class_field($boolClass='') {
		$this->fields[$boolClass]=$boolClass;
	}
	function set_current_class_active($currentAsActive=true) {
		$this->currentAsActive=$currentAsActive;
	}
	function set_visible_field($visible="b_visible") {
		$this->fields["visible"]=$visible;
	}
	function set_clickable_field($clickable="b_clickable") {
		$this->fields["clickable"]=$clickable;
	}
	function set_parent_field($parent="self_parent") {
		$this->fields["parent"]=$parent;
	}

	function set_attributes($attr="") {
		if (!is_array($attr)) $attr=array("class"=>$attr);
		$this->attr=$attr;
	}

	function set_item_attributes($attr="") {
		if (!is_array($attr)) $attr=array("class"=>$attr);
		$this->itemAttr=$attr;
	}

	function register_change_module($module=false) {
		if ($module)
			$this->changeModules[]=$module;
		else
			$this->changeModules=NULL;
	}

	function set_menu_table($table='') {
		if (empty($table)) $table = get_menu_table();
		$this->menuTable=$table;
		return $table;
	}

	function set_menu_from_table($table="",$foreign=false) {
		$table=$this->set_menu_table($table);
		$CI =& get_instance();
		// select fields
		$fields=$CI->db->list_fields($table);
		foreach ($fields as $key=>$f) {
			if (!in_array($f,$this->fields) and !isset($this->extraFields[$f])) unset($fields[$key]);
		}
		if (is_array($foreign)) {
			foreach ($foreign as $t => $ff) {
				$fields[]='id_'.remove_prefix($t);
				foreach ($ff as $f) {
					$fields[]=$t.'.'.$f;
				}
			}
		}
		// get data from table
		$CI->db->select(PRIMARY_KEY);
		$CI->db->select($fields);
		if ($foreign) $CI->db->add_foreigns($foreign);
		if (in_array("self_parent",$fields)) {
			$CI->db->uri_as_full_uri('full_uri');	
			$CI->db->order_as_tree();	
		}
		$data=$CI->db->get_result($table);
		return $this->set_menu_from_table_data($data,$foreign);
	}
	
	function get_table() {
		return $this->table;
	}
	
	
	function set_menu_from_table_data($items="",$foreign=false) {
		$counter=1;
		$CI =& get_instance();

		$menu=array();
		
		$boolFields=$this->fields;
		$boolFields=filter_by_key($boolFields,'b_');

		foreach($items as $item) {
			if (!isset($item[$this->fields["visible"]]) or ($item[$this->fields["visible"]]) ) {
				$thisItem=array();
				$thisItem["id"]=$item[PRIMARY_KEY];
				$uri=$item[$this->fields["uri"]];
				$thisItem["uri"]=$uri;
				if (isset($item['full_uri']))	$thisItem["full_uri"]=$item['full_uri'];
				
				if (empty($thisItem['name'])) {
					if (isset($item[$this->fields["title"]]))
						$thisItem['name']=$item[$this->fields["title"]];
					else
						$thisItem['name']=$uri;
				}
				if (isset($item[$this->fields["class"]])) 	$thisItem["class"]=str_replace('|',' ',$item[$this->fields["class"]]);
				if (isset($item[$this->fields["parent"]])) 	$parent=$item[$this->fields["parent"]]; else $parent="";
				if (isset($item[$this->fields["clickable"]]) && !$item[$this->fields["clickable"]]) $thisItem["uri"]='';
				// classbooleans
				if (!empty($boolFields)) {
					foreach ($boolFields as $boolField) {
						if (isset($item[$boolField]) && $item[$boolField]) $thisItem["class"]=' '.$boolField;
					}
				}
				
				if (!empty($this->extraFields)) {
					foreach ($this->extraFields as $extraName => $extra) {
						if (isset($item[$extraName])) {
							$thisItem["extra"][$extraName]=$extra["start"].$item[$extraName].$extra["close"];
						}
					}
				}
				$menu[$parent][$uri]=$thisItem;
			}
		}
		
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
		// trace_($menu);
		return $menu;
	}

	function set_menu($menu=NULL) {
		$this->menu=$menu;
	}

	function add($item) {
		$this->menu[$item['uri']]=$item;
	}

	function add_to_top($item) {
		$menu=array_merge(array($item['uri']=>$item),$this->menu);
		$this->set_menu($menu);
	}

	function add_after($item,$after) {
		if (array_key_exists($after,$this->menu)) {
				$new=array();
				foreach($this->menu as $k=>$i) {
					$new[$k]=$i;
					if ($k==$after) $new[$item['uri']]=$item;
				}
			$this->menu=$new;
			return TRUE;
		}
		else return FALSE;
	}

	function add_to_bottom($item) {
		$menu=array_merge($this->menu,array($item['uri']=>$item));
		$this->set_menu($menu);
	}

	function add_sub($sub) {
		if (array_key_exists($sub['uri'],$this->menu)) {
			$this->menu[$sub['uri']]["sub"]=$sub;
			return TRUE;
		}
		return FALSE;
 	}

	function remove_item($uri) {
		unset($this->menu[$uri]);
	}

	function set_current($current="") {
		$current=str_replace(index_page(),"",$current);
		if (substr($current,0,1)=="/") $current=substr($current,1);
		// remove query's
		$current=explode('?',$current);
		$current=current($current);
		// remove everything after :
		if (strpos($current,':')>0) $current=get_prefix($current,':');
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
		// remove query's from $in
		$in=explode('?',$in);
		$in=current($in);
		//
		$in=explode("/",$in);
		$uri=explode("/",$uri);
		// if same TRUE
		if ($in==$uri) return TRUE;
		// if in longer then uri, impossible active, FALSE
		if (count($uri)<count($in)) return FALSE;
		// ok, possible active branch, first set uri as long as in, then check if same
		$uri=array_slice($uri,0,count($in));
		if ($in==$uri) return TRUE;
		return FALSE;
	}

	function get_home_uri() {
		reset($this->menu);
		$home=current($this->menu);
		return $home['uri'];
	}

	function add_controls($controls="") {
		$this->itemControls=$controls;
	}

	
	function render_branch($branchUri,$attr="",$level=1,$preUri="",$nobranchUri=FALSE) {
		$out='';
		if ($nobranchUri)
			$preUri=ltrim($preUri.'/');
		else
			$preUri=ltrim(add_string($preUri,$branchUri,'/'),'/');
		$branchUri=ltrim($branchUri,'/');
		$uris=explode('/',$branchUri);
		$branch=$this->menu;
		while (count($uris)>0 and $branch) {
			$uri=array_shift($uris);
			if (isset($branch[$uri])) {
				$branch=$branch[$uri];
				if ($branch) {
					if (isset($branch['sub']))
						$branch=$branch['sub'];
					else
						$branch=false;
				}
			}
			else $branch=false;
		}
		if ($branch) {
			$out=$this->render($branch,$attr,$level,$preUri);
		}
		return $out;
	}

	function render($menu=NULL,$attr="",$level=1,$preUri="") {
		if (empty($attr)) $attr=$this->attr;
		if (!is_array($attr)) $attr=array("class"=>$attr);
		if (empty($attr["class"])) $attr["class"]="";
		if ($level>1) unset($attr["id"]);
		$ULattr=$attr;
		$ULattr['class']=trim("lev$level ".$ULattr['class']);

		$branch=array();
		$out=$this->tmp($this->tmpMenuStart,$ULattr); // <ul .. >
		if (!isset($menu)) $menu=$this->menu;

		$pos=1;
		if ($menu) {
			foreach($menu as $uri=>$item) {
				// Change item before rendering, if some other classes has request so
				$item=$this->_change_item($item);
				
				$itemOut='';
				if (isset($item['name']))	$name=$item['name']; else $name='';
				if (empty($item)) {
					// seperator
					$itemOut.=$this->tmp($this->tmpItemStart,array("class"=>"seperator pos$pos lev$level"));
					$itemOut.=$this->tmp($this->tmpItemEnd);
					$out.=$itemOut;
					$pos++;
				}
				if (isset($item[$this->urlField])) {
					$thisUri=$item[$this->urlField];
					if (!empty($preUri) and !empty($thisUri) and $this->urlField=="uri" and !(isset($item['unique_uri']) and $item['unique_uri'])) $thisUri=$preUri."/".$thisUri;
					$link='';
					if (!empty($thisUri))	$link=trim($this->tmp($this->tmpUrl,$thisUri),'/');
					// set class
					$cName=get_suffix($item[$this->urlField],'/');
					$first=($pos==1)?' first':'';
					$last=($pos==count($menu))?' last':'';
					$sub=(isset($item['sub']))?' sub':'';
					// trace_(array('current'=>$this->current,'link'=>$link));
					if (strpos($link,':')>0)
						$checklink=get_prefix($link,':');
					else
						$checklink=$link;
					$current='';
					if ($this->current==$checklink) {
						$current=' current';
						if ($this->currentAsActive) $current.=' active';
					}
					$class="lev$level pos$pos $first$last$sub ".$attr['class']." $cName$current";
					if (isset($this->itemAttr['class']) and !empty($this->itemAttr['class'])) $class.=' '.$this->itemAttr['class'];
					if (isset($item['class']) and !empty($item['class'])) $class.=' '.$item['class'];
					
					$itemAttr['class']=trim($class);
					// set id
					$itemAttr['id']="menu_$cName"."_pos$pos"."_lev$level";

					// render item/subitem
					$itemOut.=$this->tmp($this->tmpItemStart,array("class"=>$itemAttr["class"],'id'=>$itemAttr['id']));  // <li ... >
					if (isset($item["uri"])) {
						if (isset($item['name']))
							$showName=ascii_to_entities($item['name']);
						else
							$showName=trim(ascii_to_entities($name),'_');
						// trace_($showName);
						$pre=get_prefix($showName,"__");
						if (!empty($pre)) $showName=$pre;
						if (isset($item["help"])) $showName=help($showName,$item["help"]);
						if (isset($item['extra'])) {foreach ($item['extra'] as $extra) {$showName.=$extra;}	}
						// extra attributes set?
						$extraAttr=array();
						$extraAttr=$item;
						unset($extraAttr['class'],$extraAttr['uri'],$extraAttr['id'],$extraAttr['sub'],$extraAttr['unique_uri']);
						$itemAttr=array_merge($itemAttr,$extraAttr);
						// if (isset($item['target'])) $itemAttr['target']=$item['target'];
						if (isset($itemAttr['title'])) $itemAttr['title']=strip_tags($itemAttr['title']);
						// trace_($showName);
						if (empty($link)) {
							$itemAttr['class'].=' nonClickable';
							$itemOut.=span($itemAttr).$showName._span();
						}
						else {
							$itemOut.=anchor($link, $showName, $itemAttr);
						}
					}
					if (isset($item["sub"])) {
						$subOut=$this->render($item["sub"],"$cName",$level+1,$thisUri);
						// check if needs to add active class
						if (strpos($subOut,'current')>0) {
							$itemOut=preg_replace("/<li([^>]*)class=\"([^\"]*)\"/","<li$1class=\"$2 active\"",$itemOut);
							$itemOut=preg_replace("/<a([^>]*)class=\"([^\"]*)\"/","<a$1class=\"$2 active\"",$itemOut);
						}
						$itemOut.=$subOut;
					}
					$out.=$itemOut.$this->tmp($this->tmpItemEnd);
					$pos++;
				}
			}
		}
		$out.=$this->tmp($this->tmpMenuEnd); // </ul>
		return $out;
	}
	
	// This function checks if other classes needs to change something...
	function _change_item($item) {
		if ($this->changeModules) {
			foreach ($this->changeModules as $key => $module) {
				if (method_exists($module,'change_menu_item')) {
					$give_item=$item;
					$item=$module->change_menu_item($item);
				}
			}
		}
		return $item;
	}
	
	function get_item($uri='',$foreigns=false,$many=false) {
		if (empty($uri)) $uri=$this->current;
		$CI =& get_instance();
		$CI->db->where_uri($uri);
		if ($foreigns) $CI->db->add_foreigns();
		if ($many) $CI->db->add_many();
		$item=$CI->db->get_row($this->menuTable);
		return $item;
	}
	
	
	function get_prev($uri='') {
		$prev=false;
		if (empty($uri)) $uri=$this->current;
		$submenu=$this->_get_submenu($uri);
		if ($submenu) {
			$thisUri=get_suffix($uri,'/');
			$prev_uri=false;
			foreach ($submenu as $key=>$value) {
				if ($key==$thisUri) break;
				$prev_uri=$key;
			}
			if ($prev_uri) {
				$prev=$submenu[$prev_uri];
				$prev['full_uri']=remove_suffix($uri,'/').'/'.$prev_uri;
			}
		}
		return $prev;
	}
	function get_prev_uri($uri='',$full=true) {
		$prev=$this->get_prev($uri);
		if ($prev) {
			if ($full)
				return $prev['full_uri'];
			else
				return $prev['uri'];
		}
		return false;
	}

	function get_next($uri='') {
		$next=false;
		if (empty($uri)) $uri=$this->current;
		$submenu=$this->_get_submenu($uri);
		if ($submenu) {
			arsort($submenu);
			$thisUri=get_suffix($uri,'/');
			$next_uri=false;
			foreach ($submenu as $key=>$value) {
				if ($key==$thisUri) break;
				$next_uri=$key;
			}
			if ($next_uri) {
				$next=$submenu[$next_uri];
				$next['full_uri']=remove_suffix($uri,'/').'/'.$next_uri;	
			}
		}
		return $next;
	}
	function get_next_uri($uri='',$full=true) {
		$next=$this->get_next($uri);
		if ($next) {
			if ($full)
				return $next['full_uri'];
			else
				return $next['uri'];
		}
		return false;
	}


	function get_prev_branch($uri='') {
		$branch=FALSE;
		if (empty($uri)) $uri=$this->current;
		$ParentUri=remove_suffix($uri,'/');
		if ($ParentUri) {
			$ParentMenu=$this->_get_submenu($ParentUri);
			$ParentShortUri=get_suffix($ParentUri,'/');
			end($ParentMenu);
			do {
				if (isset($current)) prev($ParentMenu);
				$current=current($ParentMenu);
			} while ($current and $current['uri']!=$ParentShortUri );
			if ($current) {
				$prev=prev($ParentMenu);
				if ($prev) {
					$branch=$prev['sub'];
					$branch=end($branch);
					$branch['full_uri']=remove_suffix($ParentUri,'/').'/'.$prev['uri'].'/'.$branch['uri'];
				}
			}
		}
		return $branch;
	}
	function get_prev_branch_uri($uri='',$full_uri=true) {
		$prev=$this->get_prev_branch($uri);
		if ($prev) {
			if ($full_uri)
				return $prev['full_uri'];
			else
				return $prev['uri'];
		}
		return $prev;
	}


	function get_next_branch($uri='') {
		$branch=FALSE;
		if (empty($uri)) $uri=$this->current;
		$ParentUri=remove_suffix($uri,'/');
		if ($ParentUri) {
			$ParentMenu=$this->_get_submenu($ParentUri);
			$ParentShortUri=get_suffix($ParentUri,'/');
			do {
				$current=each($ParentMenu);
			} while ($current and $current['key']!=$ParentShortUri );
			if ($current) {
				$next=each($ParentMenu);
				if ($next) {
					$branch=$next['value']['sub'];
					$branch=current($branch);
					$branch['full_uri']=remove_suffix($ParentUri,'/').'/'.$next['value']['uri'].'/'.$branch['uri'];
				}
			}
		}
		return $branch;
	}
	function get_next_branch_uri($uri='',$full_uri=true) {
		$next=$this->get_next_branch($uri);
		if ($next) {
			if ($full_uri)
				return $next['full_uri'];
			else
				return $next['uri'];
		}
		return $next;
	}

	
	function _get_submenu($uri) {
		$parts=explode('/',$uri);
		array_pop($parts);
		$submenu=$this->menu;
		foreach ($parts as $part) {
			if (isset($submenu[$part]['sub']))
				$submenu=$submenu[$part]['sub'];
			else
				$submenu=false;
		}
		return $submenu;
	}

}

?>