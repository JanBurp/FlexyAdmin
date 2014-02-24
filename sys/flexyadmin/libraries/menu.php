<?
 /**
  * Met deze class kun je eenvoudig een html menu maken.
  *
  * @author Jan den Besten
  *
  * Eenvoudig menu maken
  * ========================
  *
  * Hieronder zie je een voorbeeld om vanuit het niets een menu aan te maken.
  *
  *     $menu = new Menu();
  *     $menu->add( array( 'uri'=>'home', 'name'=>'Home' ) );
  *     $menu->add( array( 'uri'=>'een_pagina', 'name'=>'Een Pagina' ) );
  *     $menu->add_sub( array( 'uri'=>'een_pagina', 'sub'=>array( 'uri'=>'subpagina', 'name'=>'Subpagina' ) );
  *     $menu->add( array( 'uri'=>'links', 'name'=>'Links') );
  *     $menu->add( array( 'uri'=>'contact', 'name'=>'Contact') );
  *     echo $menu->render();
  *
  * Het resultaat in HTML is dan:
  *
  *     <ul>
  *       <li><a href="home">Home</a></li>
  *       <li><a href="een_pagina">Een pagina</a>
  *         <ul>
  *           <li><a href="een_pagina/subpagina">Subpagina</a></li>
  *         </ul>
  *         </li>
  *       <li><a href="links">Links</a></li>
  *       <li><a href="contact">Contact</a></li>
  *     </ul>
  *
  * Menu aanmaken vanuit een menu tabel
  * =======================================
  *
  * Hieronder zie je hoe je een menu aanmaakt vanuit een tabel.
  * Het is vergelijkbaar als de standaard manier die je in de *controller.php* vindt.
  * Behalve dat daar de variabele `$menu` al bestaat in de vorm van `$this->menu`.
  *
  *     $menu = new Menu();
  *     $menu->set_current('home');
  *     $menu->set_menu_from_table('tbl_menu');
  *     echo $menu->render();
  *
  * Heeft hetzelfde resultaat als het voorbeeld hierboven.
  * 
  * Velden van een menu tabel
  * =========================
  * 
  * Als je een menu rechtstreeks vanuit een database tabel aanmaakt zoals hierboven, dan zijn de volgende velden nodig in die tabel:
  * 
  * - `id`
  * - `order` - bepaald de volgorde van een item in het menu
  * - `uri` - de uri van het item
  * - `str_title` - de titel die zichtbaar wordt in het menu
  * 
  * Optionele velden en hun effect:
  * 
  * - `self_parent` - voor menu's met meerdere levels
  * - `str_class` -  CSS class die meegegeven wordt aan het menu-item
  * - `b_visible` - Als TRUE dan wordt het item getoond, anders niet
  * - `b_clickable` - Als TRUE dan wordt het item aanklikbaar, anders bestaat het puur uit tekst
  * 
  * Classes en id's van menu elementen
  * ======================================
  *
  * Bovenstaande HTML voorbeelden zijn sterk vereenvoudigd omdat er nog classes en id's meegegeven worden aan de ul, li en a elementen.
  *
  * ul
  * ------
  *
  * Aan het ul element worden alleen een tweetal classes meegegeven:
  *
  * - `lev#` (waar # staat voor het level, beginnend bij 1)
  * - als het een submenu is (lev2 of hoger) komt de uri van de pagina waar het een submenu van is erbij.
  *
  * In bovenstaand voorbeeld ziet de eerste ul er dus zo uit:
  *
  *     <ul class="lev1">;
  *
  * En de tweede ul (onderdeel van de tweede li):
  *
  *     <ul class="lev1 een_pagina">;
  *
  * li
  * ------
  * 
  * Classes meegegeven aan een li element:
  *
  * - `lev#` (net als bij ul)
  * - `pos#` (positie binnen bovenliggende ul, beginnend bij 1)
  * - `first` alleen als pos1, dus de eerste li binnen de ul
  * - `last` alleen voor de laatste li binnen de ul
  * - `current` als dit de huidige pagina is, dit is er altijd maar één in het hele menu
  * - `active` als de huidige pagina een onderliggende pagina is (dus als class `current` heeft). Dit is de hele tak van li's naar boven toe. Maar altijd maar één tak
  * - de uri van dit menu-item. Dus bijvoorbeeld `een_pagina`.
  *
  * De id die meegegeven wordt aan een li element is alsvolgt samengesteld:
  *
  * - `menu_`
  * - uri van de huidige pagina, met daarachter een '_'
  * - `pos#_lev#`
  *
  * In bovenstaand voorbeeld ziet de eerste li er dus zo uit:
  *
  *     <li id="menu_home_pos1_lev1" class="lev1 pos1 first current home">
  *
  * a
  * ------
  * Aan het a element wordt precies dezelfde id en class meegegeven als aan het li element.
  *
  */
  
class Menu {

  // BUSY
  // private $settings = array(
  //   'current'           => '',
  //   'uri_field'         => 'uri',
  //   'title_field'       => 'str_title',
  //   'class_field'       => 'str_class',
  //   'bool_class_fields' => array(),
  //   'visible_field'     => 'b_visible',
  //   'clickable_field'   => 'b_clickable',
  //   'parent_field'      => 'self_parent',
  //   'extra_field'       => '',
  //   'attributes'        => array('class'=>''),
  //   'menu_table'        => ''
  //   'menu_templates'    => array('<ul %s>','</ul>'),
  //   'item_templates'    => array('<li %s>','</li>'),
  //   'url_template'      => '%s'
  // )
  

  /**
   * HTML output
   *
   * @var string
   */
	var $render;
  
  /**
   * Interne representatie van een menu
   *
   * @var array
   */
	var $menu;
  
  /**
   * url van huidige pagina
   *
   * @var string
   */
	var $current;

  private  $tmpUrl;
	private  $urlField;
  private  $createUriTree=TRUE;
	private  $fields;
  private  $ordered_titles=FALSE;
	private  $extraFields;
  private  $attr;
	private  $itemAttr;
	private  $currentAsActive;
  private  $nested=TRUE;
	
	private  $changeModules;
  
	/**
	 * De tabel uit de database die gebruikt wordt.
	 *
	 * @var string
	 */
	var $menuTable;
  
	private $tmpMenuStart;
	private $tmpMenuEnd;
	private $tmpItemStart;
	private $tmpItemEnd;
	private $itemControls;


  /**
   * @author Jan den Besten
   * @ignore
   */
	public function __construct() {
		$this->init();
	}

  // BUSY
  // /**
  //  * Initialiseer (override defaults)
  //  *
  //  * @param array $settings[]
  //  * @return this
  //  * @author Jan den Besten
  //  */
  // public function initialize($settings=array()) {
  //   foreach ($settings as $name => $value) {
  //     $this->set($name,$value);
  //   }
  //   return $this;
  // }
  // 
  // /**
  //  * Stelt één setting in
  //  *
  //  * @param string $name 
  //  * @param string $value 
  //  * @return this
  //  * @author Jan den Besten
  //  */
  // public function set($name,$value) {
  //   if (method_exists($this,'set_'.$name))
  //     $this->'set_'.$name($value);
  //   else
  //     $this->settings[$name]=$value;
  //   return $this;
  // }
  

  /**
   * Init
   *
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	public function init() {
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

  /**
   * Zet uri veld van menu tabel
   *
   * @param string $uri['uri']
   * @return void
   * @author Jan den Besten
   */
	public function set_uri_field($uri="uri") {
		$this->fields["uri"]=$uri;
	}
  
  /**
   * Bepaalt of de uri's in het menu fulluris moeten worden (dus de tree volgend), of 'as is'.
   *
   * @param string $createUriTree[TRUE]
   * @return void
   * @author Jan den Besten
   */
  public function set_create_uri_tree($createUriTree=TRUE) {
    $this->createUriTree=$createUriTree;
  }
  
  /**
   * Zet titel veld van menu tabel
   *
   * @param string $title['str_title]
   * @return void
   * @author Jan den Besten
   */
	public function set_title_field($title="str_title") {
		$this->fields["title"]=$title;
	}

  /**
   * Titels van menu worden nummers, of andere tekens op volgorde
   * 
   *   - NUMBERS geeft 1,2,3,4 etc.
   *   - ALFA geeft A,B,C,D etc.
   *   - ROMAN geeft I,II,III,IV etc.
   *
   * @param string $order[FALSE] Mogelijke waarden: FALSE|'NUMBERS'|'ALFA'|'ROMAN' 
   * @return void
   * @author Jan den Besten
   */
  public function set_title_field_as_order($order=FALSE) {
    $this->ordered_titles=$order;
  }
  
  
  /**
   * Zet extra velden die binnen de menu tags terechtkomen
   *
   * @param string $extra[''] 
   * @param string $startTag['&lt;p&gt;'] 
   * @param string $closeTag['&lt;/p&gt;']
   * @return void
   * @author Jan den Besten
   */
	function set_extra_field($extra="",$startTag="<p>",$closeTag="</p>"){
		if (empty($extra))
			$this->extraFields=array();
		else
			$this->extraFields[$extra]=array("name"=>$extra,"start"=>$startTag,"close"=>$closeTag);
	}
  
  /**
   * Maak een genest menu, of niet.
   *
   * @param bool $nested 
   * @return object self
   * @author Jan den Besten
   */
  public function as_nested($nested=TRUE) {
    $this->nested=$nested;
    return $this;
  }
	
  /**
   * TODO
   *
   * @param string $fields 
   * @param string $menu 
   * @param string $level 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function remove_extra_fields($fields='',$menu='',$level=0) {
		$this->set_extra_field();
		if (empty($menu)) $menu=$this->menu;
		foreach ($menu as $uri => $item) {
			unset($menu[$uri]['extra']);
			if (isset($item['sub']) and !empty($item['sub'])) $menu[$uri]['sub']=$this->remove_extra_fields($fields,$item['sub'],$level+1);
		}
		if ($level==0) $this->menu=$menu;
		return $menu;
	}
  
  /**
   * Zet class veld
   *
   * Als dit veld bestaat in de menutabel, dan wordt de inhoud van dit veld toegevoegd aan de css class van het menu-item.
   * Zo kun je menu-items eenvoudige classes meegeven die invloed hebben op het uiterlijk. Bijvoorbeeld een andere kleur.
   *
   * @param string $class['str_class']
   * @author Jan den Besten
   */
	function set_class_field($class="str_class") {
		$this->fields["class"]=$class;
	}
  
  /**
   * Zet boolean veld van een menu tabel
   *
   * Als dit veld bestaat en TRUE is dan zal de naam van het veld aan de css class van het menu-item worden toegevoegd
   *
   * @param string $boolClass['']
   * @package default
   * @author Jan den Besten
   */
  function add_bool_class_field($boolClass='') {
		$this->fields[$boolClass]=$boolClass;
  }
  
  /**
   * TODO
   *
   * @package default
   * @author Jan den Besten
   * @ignore
   */
	function set_current_class_active($currentAsActive=true) {
		$this->currentAsActive=$currentAsActive;
	}
  
  /**
   * Zet visble veld
   *
   * Dit veld bepaald of een menu-item wordt getoond of niet
   *
   * @param string $visible['b_visible'] 
   * @return void
   * @author Jan den Besten
   */
	function set_visible_field($visible="b_visible") {
		$this->fields["visible"]=$visible;
	}
  
  /**
   * Zet clickable veld
   *
   * Dit veld bepaald of een menu-item aanklikbaar is of niet
   *
   * @param string $clickable['b_clickable']
   * @return void
   * @author Jan den Besten
   */
	function set_clickable_field($clickable="b_clickable") {
		$this->fields["clickable"]=$clickable;
	}
  
  /**
   * Zet parent veld
   *
   * @param string $parent['self_parent']
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function set_parent_field($parent="self_parent") {
		$this->fields["parent"]=$parent;
	}

  /**
   * Zet attributen of class
   *
   * Hiermee kun je een vast attribuut aan alle menu-items meegeven:
   *
   * - Als je een array meegeeft zijn de keys de attribuut namen en de values de waarden van die attributen.
   * - Als je een string meegeeft wordt de waarde van de string aan het class attribuut meegegeven. Zo kun je dus alle menu-items van eenzelfde css class voorzien
   *
   * @param array $attr[''] 
   * @return void
   * @author Jan den Besten
   */
	function set_attributes($attr="") {
		if (!is_array($attr)) $attr=array("class"=>$attr);
		$this->attr=$attr;
	}

  /**
   * TODO
   *
   * @param string $attr 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function set_item_attributes($attr="") {
		if (!is_array($attr)) $attr=array("class"=>$attr);
		$this->itemAttr=$attr;
	}

  /**
   * TODO
   *
   * @param string $module 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function register_change_module($module=false) {
		if ($module)
			$this->changeModules[]=$module;
		else
			$this->changeModules=NULL;
	}

  /**
   * Zet menu tabel
   *
   * @param string $table[''] Als je dit leeglaat dan wordt de standaard menu tabel gekozen (tbl_menu of res_menu_result)
   * @return void
   * @author Jan den Besten
   */
	function set_menu_table($table='') {
		if (empty($table)) $table = get_menu_table();
		$this->menuTable=$table;
		return $table;
	}

  /**
   * Zet menu tabel en laad menu in vanuit die tabel en creert het menu
   *
   * @param string $table[''] Als je dit leeglaat dan wordt de standaard menu tabel gekozen (tbl_menu of res_menu_result)
   * @param string $foreign[false] Eventuele foreign data die meegenomen moet worden in resultaat (zie bij db->add_foreign())
   * @return array het menu als de interne menu array
   * @author Jan den Besten
   */
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
	
  /**
   * Geeft huidige menu tabel
   *
   * @return string
   * @author Jan den Besten
   */
	function get_table() {
		return $this->table;
	}
	
  /**
   * Geeft alle huidige menu-items
   *
   * @return array
   * @author Jan den Besten
   */
  function get_items() {
    return $this->menu;
  }
  
	/**
	 * Maakt menu van array uit database resultaat
	 *
	 * @param array $items database resultaat
	 * @param string $foreign[false] TODO
	 * @return array het menu als de interne menu array
	 * @author Jan den Besten
	 */
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
					if (isset($item[$this->fields["title"]])) {
					  $thisItem['name']=$item[$this->fields["title"]];
					}
					else {
					  $thisItem['name']=$uri;
					}
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
		
		// set first
		reset($menu);
		$menu=current($menu);
		$this->set_menu($menu);
		// trace_($menu);
		return $menu;
	}

  /**
   * Zet interne menu array
   *
   * Hiermee kun je in één keer een menu maken door een array mee te geven. (ipv met add etc)
   *
   * @param array $menu array volgens de interne menu representatie
   * @return void
   * @author Jan den Besten
   */
	function set_menu($menu=NULL) {
		$this->menu=$menu;
	}

  /**
   * Voeg een menu item toe aan het eind van het huidige menu
   *
   * @param array $item array( "uri"=>uri, "name"=>name, "class"=>class ) 
   * @return void
   * @author Jan den Besten
   */
	function add($item) {
		$this->menu[$item['uri']]=$item;
	}

  /**
   * Voegt een menu item toe aan het begin van het huidige menu
   *
   * @param array $item array( "uri"=>uri, "name"=>name, "class"=>class ) 
   * @return void
   * @author Jan den Besten
   */
	function add_to_top($item) {
		$menu=array_merge(array($item['uri']=>$item),$this->menu);
		$this->set_menu($menu);
	}

  /**
   * Voeg een menu item toe na een ander item
   *
   * @param string $item  array( "uri"=>uri, "name"=>name, "class"=>class ) 
   * @param string $after uri van item waarachter het nieuwe item moet komen
   * @return void
   * @author Jan den Besten
   */
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

  /**
   * Voegt een menu item toe aan het eind van het huidige menu
   *
   * @param array $item array( "uri"=>uri, "name"=>name, "class"=>class ) 
   * @return void
   * @author Jan den Besten
   */
	function add_to_bottom($item) {
		$menu=array_merge($this->menu,array($item['uri']=>$item));
		$this->set_menu($menu);
	}

  /**
   * Voegt submenu toe aan bestaand item
   *
   * @param array $sub  array( "uri"=>uri, sub=>  array( "uri"=>uri, "name"=>name, "class"=>class )  ) 
   * @return bool TRUE als gelukt is, FALSE als niet gelukt (niet bestaand)
   * @author Jan den Besten
   */
	function add_sub($sub) {
		if (array_key_exists($sub['uri'],$this->menu)) {
			$item=$this->menu[$sub['uri']];
			$this->menu[$sub['uri']]['sub'][$sub['sub']['uri']]=$sub['sub'];
			return TRUE;
		}
		return FALSE;
 	}

  /**
   * Verwijderd menu item
   *
   * @param string $uri uri van te verwijderen item
   * @return void
   * @author Jan den Besten
   */
	function remove_item($uri) {
		unset($this->menu[$uri]);
	}

  /**
   * Stelt de huidige pagina in
   *
   * @param string $current[''] 
   * @return void
   * @author Jan den Besten
   */
	public function set_current($current="") {
    $CI =& get_instance();
		$current=str_replace(index_page(),"",$current);
		if (substr($current,0,1)=="/") $current=substr($current,1);
		// remove query's
		$current=explode('?',$current);
		$current=current($current);
		// remove everything after :
		if (strpos($current,$CI->config->item('URI_HASH'))>0) $current=get_prefix($current,$CI->config->item('URI_HASH'));
		$this->current=$current;
	}

  /**
   * TODO
   *
   * @param string $current 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function set_current_name($current="") {
		if (isset($this->menu[$current]["uri"])) {
			$this->set_current("/".$this->menu[$current]["uri"]);
			return $this->current;
		}
		else
			return false;
	}

  /**
   * TODO
   *
   * @param string $tmpUrl 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function set_url_template($tmpUrl="%s") {
		$this->tmpUrl=$tmpUrl;
	}

  /**
   * TODO
   *
   * @param string $tmpUrl 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function set_url_field($urlField="uri") {
		$this->urlField=$urlField;
	}

  /**
   * Init alle templates
   *
   * @return void
   * @author Jan den Besten
   * @ignore
   * @internal
   */
	private function set_templates() {
		$this->set_menu_templates();
		$this->set_item_templates();
		$this->set_url_template();
		$this->set_url_field();
	}

  /**
   * Zet de templates voor het menu en submenus
   *
   * Hiermee kun je de standaard gebruikte HTML tags aanpassen
   * Standaard worden hier de `<ul>` en `</ul>` tags gebruikt
   *
   * @param string $start['&lt;ul&gt;']
   * @param string $end['&lt;/ul&gt;']
   * @return void
   * @author Jan den Besten
   */
	function set_menu_templates($start="<ul %s>",$end="</ul>") {
		$this->tmpMenuStart=$start;
		$this->tmpMenuEnd=$end;
	}

  /**
   * Zet de templates voor de menu item's
   *
   * Hiermee kun je de standaard gebruikte HTML tags en plek van de attributen aanpassen
   * Standaard worden hier de `<li>` en `</li>` tags gebruikt
   * %s wordt vervangen door de attributen (class, id etc.)
   *
   * @param string $start['&lt;li %s&gt;']
   * @param string $end['&lt;/li&gt;']
   * @return void
   * @author Jan den Besten
   */
	function set_item_templates($start="<li %s>",$end="</li>") {
		$this->tmpItemStart=$start;
		$this->tmpItemEnd=$end;
	}
	
  /**
   * tmp
   *
   * @param string $tmp 
   * @param string $attr['']
   * @return string
   * @author Jan den Besten
   * @ignore
   * @internal
   */
	private function tmp($tmp,$attr="") {
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

  /**
   * Rendered een menu aan de hand van meegegeven menu tabel
   *
   * @param string $table
   * @return string HTML output
   * @author Jan den Besten
   */
	function render_from_table($table) {
		$menu=$this->set_menu_from_table($table);
		$this->set_menu($menu);
		return $this->render($menu);
	}

  /**
   * Verwijderd query-uri's uit uri
   *
   * @param string $in 
   * @param string $uri 
   * @return bool
   * @author Jan den Besten
   * @ignore
   * @internal
   */
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

  /**
   * Geeft home uri
   *
   * @return string
   * @author Jan den Besten
   */
	function get_home_uri() {
		reset($this->menu);
		$home=current($this->menu);
		return $home['uri'];
	}

  /**
   * TODO
   *
   * @param string $controls['']
   * @return void
   * @author Jan den Besten
   * @ignore
   * @internal
   */
	function add_controls($controls="") {
		$this->itemControls=$controls;
	}

	
  /**
   * Geeft submenu als menu array
   *
   * @param string $branchUri uri van submenu
   * @return array submenu array
   * @author Jan den Besten
   */
  function get_branch($branchUri) {
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
    return $branch;
  }
  
  /**
   * Renderer een submenu
   *
   * @param string $branchUri uri van het submenu
   * @param string $attr[''] eventueel standaard mee te geven attributen voor menu-items
   * @param string $level[1] level
   * @param string $preUri[''] een uri die aan de voorkant van alle uri's wordt geplakt
   * @param string $nobranchUri[FALSE] 
   * @return string
   * @author Jan den Besten
   */
	function render_branch($branchUri,$attr="",$level=1,$preUri="",$nobranchUri=FALSE) {
		$out='';
		if ($nobranchUri)
			$preUri=ltrim($preUri.'/');
		else
			$preUri=ltrim(add_string($preUri,$branchUri,'/'),'/');
    $branch=$this->get_branch($branchUri,$preUri,$nobranchUri);
		if ($branch) {
			$out=$this->render($branch,$attr,$level,$preUri);
		}
		return $out;
	}

  /**
   * Rendered het menu als HTML
   *
   * Er moet al eerder een menu aangemaakt zijn of van een menu-tabel zijn ingeladen
   *
   * @param array $menu[NULL] Je kunt een menu-array meegeven, als dit leeg is wordt de interne menu-array gebruikt
   * @param string $attr[''] eventueel standaard mee te geven attributen voor menu-items
   * @param string $level[1] level
   * @param string $preUri[''] een uri die aan de voorkant van alle uri's wordt geplakt
   * @param string $max_level[0] tot welk level gerenderd wordt (bij 0 is er geen limiet)
   * @return string
   * @author Jan den Besten
   */
	function render($menu=NULL,$attr="",$level=1,$preUri="",$max_level=0) {
    $CI =& get_instance();
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
        if (empty($item['name'])) $item['name']=$uri;
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
					if (strpos($link,$CI->config->item('URI_HASH'))>0)
						$checklink=get_prefix($link,$CI->config->item('URI_HASH'));
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
						if (isset($item['name'])) {
						  $showName=ascii_to_entities($item['name']);
						}
						else {
              if (empty($name)) $name=$uri;
							$showName=trim(ascii_to_entities($name),'_');
            }
            
            if ($this->ordered_titles) {
              switch ($this->ordered_titles) {
                case 'NUMBERS': $showName=$pos; break;
                case 'ALFA': $showName=chr($pos+64); break;
                case 'ROMAN': $showName=numberToRoman($pos); break;
              }
            }
              
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
						if (empty($link)) {
							$itemAttr['class'].=' nonClickable';
							$itemOut.=span($itemAttr).$showName._span();
						}
						else {
							$itemOut.=anchor($link, $showName, $itemAttr);
						}
					}

          $subOut='';
					if (isset($item["sub"]) and ($max_level==0 or ($level+1)<$max_level) ) {
            if ($this->createUriTree)
              $subOut=$this->render($item["sub"],"$cName",$level+1,$thisUri);
            else
              $subOut=$this->render($item["sub"],"$cName",$level+1);
						// check if needs to add active class
						if (strpos($subOut,'current')>0) {
              $itemOut=preg_replace("/class=\"([^\"]*)\"/","class=\"$1 active\"",$itemOut);
						}
					}
          if ($this->nested)
            $out.=$itemOut.$subOut.$this->tmp($this->tmpItemEnd);
          else
					  $out.=$itemOut.$this->tmp($this->tmpItemEnd).$subOut;
					$pos++;
				}
			}
		}
		$out.=$this->tmp($this->tmpMenuEnd); // </ul>
		return $out;
	}
	
  /**
   * 	This function checks if other classes needs to change something...
   *
   * @param string $item 
   * @return string
   * @author Jan den Besten
   * @ignore
   * @internal
   */
	private function _change_item($item) {
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
	
  /**
   * Geeft item aan de hand van meegegeven uri (of huidige pagina) als database resultaat
   *
   * Als je het menu aanmaakt vanuit een menu tabel dan kun je dit gebruiken om niet nogmaals de database aan te spreken als je een item uit die menu tabel wilt. Dat kun je dan hiermee doen.
   *
   * @param string $uri[''] Uri van te verkrijgen item, als leeg dan wordt current uri gebruikt
   * @param string $foreigns[FALSE] Moeten foreign tabels ook meegenomen worden
   * @param string $many[FALSE] Idem voor Many relaties
   * @return array database item
   * @author Jan den Besten
   */
	function get_item($uri='',$foreigns=false,$many=false) {
		if (empty($uri)) $uri=$this->current;
		$CI =& get_instance();
		$CI->db->where_uri($uri);
		if ($foreigns) $CI->db->add_foreigns();
		if ($many) $CI->db->add_many();
		$item=$CI->db->get_row($this->menuTable);
		return $item;
	}
	
  
  /**
   * Geeft uri van eerstvolgende item in submenu
   *
   * @param string $uri uri van submenu
   * @return string uri van eerstvolgend item in submenu of FALSE als deze niet bestaat
   * @author Jan den Besten
   */
  function get_first_sub_uri($uri='') {
    $sub_uri=FALSE;
		if (empty($uri)) $uri=$this->current;
		$submenu=$this->_get_submenu($uri,false);
		if ($submenu) {
      $sub=current($submenu);
      if ($sub) $sub_uri=$sub['full_uri'];
		}
		return $sub_uri;
  }
	

  /**
   * Geeft item één tak hoger in menu, of false als al op hoogste niveau
   * Als niets wordt meegegeven wordt uitgegaan van huidige pagina.
   *
   * @param string $uri[''] Als leeg, dan wordt huidige pagina gebruikt
   * @return array Menu-item
   * @author Jan den Besten
   */
  public function get_up($uri='') {
    $up=false;
    if (empty($uri)) $uri=$this->current;
    $up_uri=remove_suffix($uri,'/');
    if ($up_uri==$uri) $up_uri=false;
    if ($up_uri) {
      $up=$this->get_item($up_uri);
    }
    return $up;
  }
  
  
  
  /**
   * Geeft vorig menu-item op hetzelfde nivo
   *
   * @param string $uri[''] van huidige item, als leeg is dan wordt current gebruikt
   * @return array
   * @author Jan den Besten
   */
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
				$prev['full_uri']=ltrim(remove_suffix('/'.$uri,'/').'/'.$prev_uri,'/');
			}
		}
		return $prev;
	}
  
  /**
   * Geeft uri van vorige pagina op hetzelfde nivo
   *
   * Hiermee kun je de uri krijgen van de vorige pagina op hetzelfde nivo. Standaard krijg je alleen het uri part van het huidige nivo.
   * Als eerste argument geef je de huidige (volledige) uri mee. Als je deze leeglaat en je hebt al eerder *set_current()* aangeroepen, dan wordt de huidige uri gebruikt.
   * Als het 2e argument TRUE is zal de hele uri (met alle nivo's) teruggegeven worden.
   * Als er geen vorige uri bestaat zal het resultaat FALSE zijn.
   *
   *     $menu->set_current('een_pagina');
   *     echo $menu->get_prev_uri();
   *     echo $menu->get_prev_uri('een_pagina/tweede_sub_pagina', TRUE);
   *     echo $menu->get_prev_uri('links');
   *     echo $menu->get_prev_uri('home');
   *
   * Geeft als resultaat:
   *
   *     home
   *     een_pagina/eerste_sub_pagina
   *     een_pagina
   *     FALSE
   *
   * @param string $uri[''] 
   * @param bool $full[TRUE] als TRUE dan worden full_uri's meegegeven die het hele uri pad representeren
   * @return string
   * @author Jan den Besten
   */
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

  /**
   * Geeft volgend menu-item op hetzelfde nivo
   *
   * @param string $uri[''] van huidige item, als leeg is dan wordt current gebruikt
   * @return array
   * @author Jan den Besten
   */
	function get_next($uri='') {
		$next=false;
		if (empty($uri)) $uri=$this->current;
		$submenu=$this->_get_submenu($uri);
		if ($submenu) {
 			$submenu=array_reverse($submenu,true);
			$thisUri=get_suffix($uri,'/');
			$next_uri=false;
			foreach ($submenu as $key=>$value) {
				if ($key==$thisUri) break;
				$next_uri=$key;
			}
			if ($next_uri) {
				$next=$submenu[$next_uri];
				$next['full_uri']=ltrim(remove_suffix('/'.$uri,'/').'/'.$next_uri,'/');;	
			}
		}
		return $next;
	}
  
  /**
   * Geeft uri van volgende pagina op hetzelfde nivo
   *
   * @param string $uri[''] Als leeg dan wordt current gebruikt
   * @param bool $full[true] als TRUE dan worden full_uri's meegegeven
   * @return string
   * @author Jan den Besten
   */
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

  /**
   * Geeft vorig submenu
   *
   * @param string $uri[''] Als leeg dan wordt current gebruikt 
   * @return array
   * @author Jan den Besten
   */
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
  
  /**
   * Geeft uri van vorig submenu
   *
   * @param string $uri[''] Als leeg dan wordt current gebruikt 
   * @param string $full_uri[TRUE] Als TRUE dan worden full_uri's meegegeven
   * @return string
   * @author Jan den Besten
   */
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

  /**
   * Geeft volgend submenu
   *
   * @param string $uri[''] Als leeg dan wordt current gebruikt 
   * @return array
   * @author Jan den Besten
   */
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
  
  /**
   * Geeft uri van volgend submenu
   *
   * @param string $uri[''] Als leeg dan wordt current gebruikt 
   * @param string $full_uri[TRUE] Als TRUE dan worden full_uri's meegegeven
   * @return string
   * @author Jan den Besten
   */
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

	/**
	 * Pakt submenu
	 *
	 * @param string $uri 
	 * @param bool $current[TRUE]
	 * @return array
	 * @author Jan den Besten
   * @ignore
   * @internal
	 */
	private function _get_submenu($uri,$current=TRUE) {
		$parts=explode('/',$uri);
		if ($current) array_pop($parts);
		$submenu=$this->menu;
		foreach ($parts as $part) {
			if (isset($submenu[$part]['sub']))
				$submenu=$submenu[$part]['sub'];
			else
				$submenu=false;
		}
		return $submenu;
	}
  
  
  /**
   * Geeft home item terug
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_home() {
    $home=current($this->menu);
    return $home;
  }
  

}

?>
