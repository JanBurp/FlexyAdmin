<?php  /** \ingroup libraries
  * Met deze class kun je eenvoudig een html menu maken.
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
  * Aan het a element wordt precies dezelfde class meegegeven als aan het li element.
  * 
  * 
  * Output van het menu aanpassen
  * =============================
  * 
  * Het menu maakt gebruik van 3 views die je zelf kunt aanpassen:
  * 
  *     - views/menu/menu.php - <ul>
  *     - views/menu/item.php - <li><a>
  *     - views/menu/seperaror.php - een <li> zonder link
  * 
  * @author: Jan den Besten
  * @copyright: (c) Jan den Besten
  */
  
class Menu {
  
  private $CI;

  var $settings = array(
    'current'         => '',
    'menu_table'      => '',
    'fields'          => array(
      'uri'         => 'uri',
      'url'         => '',
      'title'       => 'str_title',
      'class'       => 'str_class',
      'visible'     => 'b_visible',
      'clickable'   => 'b_clickable',
      'parent'      => 'self_parent',
      'bool_class'  => '',
      'extra'       => '',
    ),
    'multilang_title' => FALSE,
    'framework'       => 'default',             // 'default', 'bootstrap'
    'attributes'      => array('class'=>''),
    'full_uris'       => false,
    'ordered_titles'  => '',   // NUMBERS geeft 1,2,3,4 etc., ALFA geeft A,B,C,D etc, ROMAN geeft I,II,III,IV etc.
    'view_path'       => 'menu'
  );
  
  private $styles = array(
    'default' => array(
      'current' => 'current',
      'active'  => 'active',
      'first'   => 'first',
      'last'    => 'last',
      'has_sub' => 'has_sub',
      'is_sub'  => 'sub'
    ),
    'bootstrap' => array(
      'current' => 'active',
      'active'  => 'active active-branch',
      'first'   => 'first',
      'last'    => 'last',
      'has_sub' => 'dropdown-menu',
      'is_sub'  => 'sub'
    )
    
    
  );
  
  private $field_set_methods = array();

  /**
   * HTML output
   */
	var $render;
  
  /**
   * Interne representatie van een menu
   */
	var $menu;

  /**
   * @author Jan den Besten
   */
	public function __construct($settings=array()) {
		$this->CI=& get_instance();
    $this->field_set_methods=array_keys($this->settings['fields']);
    foreach ($this->field_set_methods as $key => $value) {
      $this->field_set_methods[$key]='set_'.$value.'_field';
    }
    if ($settings) $this->initialize($settings);
    $current=$this->CI->uri->uri_string();
    $this->set_current($current);
	}

  /**
   * Initialiseer (override defaults)
   * 
   * Geef een array met alle instellingen, hieronder de defaults:
   * 
   *      'current'       => '',                   // Geef hier de huidige url als die afwijkt van standaard
   *      'menu_table'    => '',                   // De db-tabel waar het menu uit wordt gehaald
   *      'fields'        => array(                // Diverse tabel velden die omgezet worden in het menu
   *        'uri'          => 'uri',               // - uri
   *        'url'          => '',                  // - url (voor een vaste link, ipv een uri met parts)
   *        'title'        => 'str_title',         // - naam van het menu-item
   *        'class'        => 'str_class',         // - deze class wordt toegevoegd aan het menu-item
   *        'visible'      => 'b_visible',         // - of dit menu-item wordt getoond of niet
   *        'clickable'    => 'b_clickable',       // - of dit menu-item aanklikbaar is of niet
   *        'parent'       => 'self_parent',       // - parent id
   *        'bool_class'   => '',                  // - als dit veld TRUE is dan wordt de veldnaam toegevoegd als een class aan het menu-item
   *        'extra'        => '',                  
   *      ),
   *      'multilang_title' => FALSE,              // Als TRUE, dat wordt aan 'fields'.'title' een language code die bekend is in $this->site['language'] toegevoegd (str_title wordt dan str_title_nl bv)
   *      'framework'     => 'default',            // 'default', 'bootstrap'
   *      'view_path'     => 'menu'                // pad waar de menu views in staan
   *      'attributes'    => array('class'=>''),   // extra attributen de items
   *      'full_uris'     => false,                // of de uri's full uris zijn
   *      'ordered_titles'=> '',                   // NUMBERS geeft 1,2,3,4 etc., ALFA geeft A,B,C,D etc, ROMAN geeft I,II,III,IV etc. voor de menu-items
   *
   * @param array $settings[]
   * @return this
   * @author Jan den Besten
   */
  public function initialize($settings=array()) {
    foreach ($settings as $name => $value) {
      $this->set($name,$value);
    }
    // Multilang?
    if ($this->settings['multilang_title'] and isset($this->CI->site['language'])) {
      $this->settings['fields']['title'] .= '_'.$this->CI->site['language'];
    }
    return $this;
  }

  /**
   * Stelt één setting in
   *
   * @param string $name
   * @param string $value
   * @return this
   * @author Jan den Besten
   */
  public function set($name,$value) {
    if (method_exists($this,'set_'.$name)) {
      $method='set_'.$name;
      return $this->$method($value);
    }
    if (is_array($value))
      $this->settings[$name]=array_merge($this->settings[$name],$value);
    else
      $this->settings[$name]=$value;
    return $this;
  }

  /**
   * This makes it possible to use methods set_property(value) instead of set(property,value)
   *
   * @param string $function
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @internal
   */
  public function __call($function, $args) {
    if (substr($function,0,4)=='set_') {
      $value=current($args);
      $this->set(remove_prefix($function,'_'),$value);
      return;
    }
    trigger_error('Call to undefined method '.__CLASS__.'::'.$function.'()', E_USER_ERROR);
  }


  /**
   * Zet extra velden die binnen de menu tags terechtkomen
   *
   * @param string $extra default=''
   * @param string $startTag default='&lt;p&gt;'
   * @param string $closeTag default='&lt;/p&gt;'
   * @return void
   * @author Jan den Besten
   */
  public function set_extra_field($extra="",$startTag="<p>",$closeTag="</p>"){
    if (empty($extra))
      $this->settings['fields']['extra']=array();
    else
      $this->settings['fields']['extra'][$extra]=array("name"=>$extra,"start"=>$startTag,"close"=>$closeTag);
    return $this;
  }
  
  
  /**
   * Zet boolean veld van een menu tabel
   *
   * Als dit veld bestaat en TRUE is dan zal de naam van het veld aan de css class van het menu-item worden toegevoegd
   *
   * @param string $boolClass['']
     * @author Jan den Besten
   */
  public function add_bool_class_field($boolClass='') {
    $this->settings['fields']['bool_class']=$boolClass;
    return $this;
  }

  /**
   * Stelt menu tabel in
   *
   * @param string $table[''] Als je dit leeglaat dan wordt de standaard menu tabel gekozen (tbl_menu of res_menu_result)
   * @return object this
   * @author Jan den Besten
   */
	public function set_menu_table($table='') {
		if (empty($table)) $table=get_menu_table();
		$this->settings['menu_table']=$table;
		return $this;
	}

  /**
   * Zet menu tabel en laad menu in vanuit die tabel en creert het menu
   *
   * @param string $table default='' Als je dit leeglaat dan wordt de standaard menu tabel gekozen (tbl_menu of res_menu_result)
   * @param string $foreign default=false Eventuele foreign data die meegenomen moet worden in resultaat (zie bij db->add_foreign())
   * @return array het menu als de interne menu array
   * @author Jan den Besten
   */
	public function set_menu_from_table($table="",$foreign=false) {
    if (!empty($table) or empty($this->settings['menu_table'])) $this->set('menu_table',$table);
    $table=$this->settings['menu_table'];
    
    if ($table) {
      
      $this->CI->data->table( $table );
      $fields = $this->CI->data->list_fields();
      
  		// select fields
  		foreach ($fields as $key=>$f) {
  			if (!in_array($f,$this->settings['fields']) and !isset($this->settings['fields']['extra'][$f])) unset($fields[$key]);
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
      $this->CI->data->select( $fields );
  		if ($foreign) $this->CI->data->with('many_to_one',$foreign);
  		if (in_array($this->settings['fields']['parent'],$fields)) {
        $this->CI->data->path('full_uri','uri');
  		}
      $data=$this->CI->data->get_result();
  		return $this->set_menu_from_table_data($data,$foreign);
    }
    
    return array();
	}
  
  /**
   * Geeft alle huidige menu-items
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_items() {
    return $this->menu;
  }
  
	/**
	 * Maakt menu van array uit database resultaat
	 *
	 * @param array $items database resultaat
	 * @param string $foreign default=false TODO
	 * @return array het menu als de interne menu array
	 * @author Jan den Besten
	 */
	public function set_menu_from_table_data($items="",$foreign=false) {
    
		$counter=1;
		$menu=array();
		
		$boolFields=$this->settings['fields'];
		$boolFields=filter_by_key($boolFields,'b_');

		foreach($items as $item) {
			if (!isset($item[$this->settings['fields']["visible"]]) or ($item[$this->settings['fields']["visible"]]) ) {
				$thisItem=array();
				$thisItem["id"]=$item[PRIMARY_KEY];
				$uri=$item[$this->settings['fields']["uri"]];
				$thisItem["uri"]=$uri;
				if (isset($item['full_uri']))	$thisItem["full_uri"]=$item['full_uri'];
				
				if (empty($thisItem['name'])) {
					if (isset($item[$this->settings['fields']["title"]])) {
					  $thisItem['name']=$item[$this->settings['fields']["title"]];
					}
					else {
					  $thisItem['name']=$uri;
					}
				}
        $thisItem['class']='';
				if (isset($item[$this->settings['fields']["class"]])) 	    $thisItem["class"]=str_replace(array('|',',','.','/'),array(' ',' ','_','_'),$item[$this->settings['fields']["class"]]);
				if (isset($item[$this->settings['fields']["bool_class"]]) and ($item[$this->settings['fields']["bool_class"]]))	$thisItem["class"].=' '.$this->settings['fields']["bool_class"];
				if (isset($item[$this->settings['fields']["parent"]])) 	    $parent=$item[$this->settings['fields']["parent"]]; else $parent="";
				if (isset($item[$this->settings['fields']["clickable"]]) && !$item[$this->settings['fields']["clickable"]]) $thisItem["uri"]='';
        
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
		return $menu;
	}


  /**
   * Maakt een menu van een filetree resultaat van read_map()
   *
   * @param string $files 
   * @return array
   * @author Jan den Besten
   */
  public function set_menu_from_filetree($files=array()) {
    $menu=array();
    foreach ($files as $key => $file) {
      $name=remove_suffix($file['name'],'.');
      $uri=trim($name);
      $menu[$name]=array(
        'uri'   => $uri,
        'name'  => $name,
        'class' => get_suffix($key,'.')
      );
      if (isset($file['.'])) {
        $menu[$key]['sub']=$this->set_menu_from_filetree($file['.'],$uri);
      }
    }
    $this->set_menu($menu);
    return $menu;
  }


  /**
   * Zet intern menu array
   *
   * Hiermee kun je in één keer een menu maken door een array mee te geven. (ipv met add etc)
   *
   * @param array $menu array volgens de interne menu representatie
   * @return object this
   * @author Jan den Besten
   */
	public function set_menu($menu=NULL) {
		$this->menu=$menu;
    return $this;
	}

  /**
   * Voeg een menu item toe aan het eind van het huidige menu
   *
   * @param array $item array( "uri"=>uri, "name"=>name, "class"=>class ) 
   * @return object this
   * @author Jan den Besten
   */
	public function add($item) {
    if (empty($item) or $item==='seperator') return $this->add_seperator();
    if ($item==='split') return $this->add_split();
		$this->menu[$item['uri']]=$item;
    return $this;
	}

  /**
   * Voeg één of meerdere menu items toe aan het eind van het huidige menu
   *
   * @param array $items array( "uri"=>uri, "name"=>name, "class"=>class ) 
   * @return object this
   * @author Jan den Besten
   */
  public function add_items($items) {
    foreach ($items as $item) {
      $this->add($item);
    }
    return $this;
  }
  
  /**
   * Voeg seperator toe aan menu
   *
   * @return $this
   * @author Jan den Besten
   */
	public function add_seperator() {
		$this->menu[]='';
    return $this;
	}

  /**
   * Voeg split toe aan Menu (meestal is dat een </ul>)
   *
   * @return $this
   * @author Jan den Besten
   */
	public function add_split() {
		$this->menu[]='split';
    return $this;
	}

  

  /**
   * Voegt een menu item toe aan het begin van het huidige menu
   *
   * @param array $item array( "uri"=>uri, "name"=>name, "class"=>class ) 
   * @return object this
   * @author Jan den Besten
   */
	public function add_to_top($item) {
		$menu=array_merge(array($item['uri']=>$item),$this->menu);
		$this->set_menu($menu);
    return $this;
	}

  /**
   * Voeg een menu item toe na een ander item
   *
   * @param string $item  array( "uri"=>uri, "name"=>name, "class"=>class ) 
   * @param string $after uri van item waarachter het nieuwe item moet komen
   * @return object this
   * @author Jan den Besten
   */
	public function add_after($item,$after) {
		if (array_key_exists($after,$this->menu)) {
				$new=array();
				foreach($this->menu as $k=>$i) {
					$new[$k]=$i;
					if ($k==$after) $new[$item['uri']]=$item;
				}
			$this->menu=$new;
		}
    return $this;
	}

  /**
   * Voegt een menu item toe aan het eind van het huidige menu
   *
   * @param array $item array( "uri"=>uri, "name"=>name, "class"=>class ) 
   * @return object this
   * @author Jan den Besten
   */
	public function add_to_bottom($item) {
		$menu=array_merge($this->menu,array($item['uri']=>$item));
		$this->set_menu($menu);
    return $this;
	}

  /**
   * Voegt submenu toe aan bestaand item
   *
   * @param array $sub  array( "uri"=>uri, sub=>  array( "uri"=>uri, "name"=>name, "class"=>class )  ) 
   * @return object this
   * @author Jan den Besten
   */
	public function add_sub($sub) {
		if (array_key_exists($sub['uri'],$this->menu)) {
			$item=$this->menu[$sub['uri']];
			$this->menu[$sub['uri']]['sub'][$sub['sub']['uri']]=$sub['sub'];
		}
    return $this;
 	}

  /**
   * Verwijderd menu item
   *
   * @param string $uri uri van te verwijderen item
   * @return object this
   * @author Jan den Besten
   */
	public function remove_item($uri) {
		unset($this->menu[$uri]);
    return $this;
	}

  /**
   * Stelt de huidige pagina in
   *
   * @param string $current[''] 
   * @return object this
   * @author Jan den Besten
   */
	public function set_current($current="") {
    // cleanup
		$current=str_replace(index_page(),"",$current);
    $current=trim($current,'/');
		// remove query's in uri
		$current=remove_suffix($current,'?');
		// remove everything after URI_HASH (:)
		if ($this->CI->config->item('URI_HASH','')!="") $current=remove_suffix($current,$this->CI->config->item('URI_HASH'));
    $this->settings['current']=$current;
    return $this;
	}

  /**
   * Rendered een menu aan de hand van meegegeven menu tabel
   *
   * @param string $table
   * @return string HTML output
   * @author Jan den Besten
   */
	public function render_from_table($table) {
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
   * @internal
   */
	private function inUri($in,$uri) {
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
	public function get_home_uri() {
		reset($this->menu);
		$home=current($this->menu);
		return $home['uri'];
	}

  /**
   * Geeft submenu als menu array
   *
   * @param string $branchUri uri van submenu
   * @return array submenu array
   * @author Jan den Besten
   */
  public function get_branch($branchUri) {
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
   * @param string $attr default='' eventueel standaard mee te geven attributen voor menu-items
   * @param string $level default=1 level
   * @param string $preUri default='' een uri die aan de voorkant van alle uri's wordt geplakt
   * @param string $nobranchUri default=FALSE 
   * @return string
   * @author Jan den Besten
   */
	public function render_branch($branchUri,$attr="",$level=1,$preUri="",$nobranchUri=FALSE) {
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
   * @param array $menu default=NULL Je kunt een menu-array meegeven, als dit leeg is wordt de interne menu-array gebruikt
   * @param string $attr default='' eventueel standaard mee te geven attributen voor menu-items
   * @param string $level default=1 level
   * @param string $preUri default='' een uri die aan de voorkant van alle uri's wordt geplakt
   * @param string $max_level default=0 tot welk level gerenderd wordt (bij 0 is er geen limiet)
   * @return string
   * @author Jan den Besten
   */
	public function render($menu=NULL,$attr="",$level=1,$preUri="",$max_level=0) {
    $uri='';
		if (!isset($menu)) $menu=$this->menu;
		if (!is_array($attr)) $attr=array("class"=>$attr);
		if ($level>1) unset($attr["id"]);

    $styles=$this->styles[$this->settings['framework']];
    
    $html='';
		if ($menu) {
  		$pos=1;
      $_pos=count($menu);
			foreach($menu as $uri=>$item) {
        
        // create uri
        if (isset($item['uri'])) $uri=$item['uri'];
        $thisUri=el($this->settings['fields']['url'],$item,$uri);
        if (!el('unique_uri',$item,false) and !empty($thisUri)) $thisUri=$preUri."/".$thisUri;
        $thisUri=trim($thisUri,'/');
				$cleanUri=remove_suffix($thisUri,$this->CI->config->item('URI_HASH'));
        $classUri=str_replace('/','',get_suffix(str_replace(array('?','='),'_',$cleanUri),'/'));
        
        // title
        $title=ascii_to_entities(trim(el('name',$item,$uri),'_'));
        // title as ordered chars
        if ($this->settings['ordered_titles']) {
          switch ($this->ordered_titles) {
            case 'NUMBERS': $title=$pos; break;
            case 'ALFA':    $title=chr($pos+64); break;
            case 'ROMAN':   $title=numberToRoman($pos); break;
          }
        }
        // extra strings in title?
				if (isset($item['extra'])) {
          if (is_string($item['extra'])) {
            $title.=$item['extra'];
          }
          else {
            foreach ($item['extra'] as $extra) {$title.=$item['extra'];}
          }
        }
        
        // heeft submenu?
        $submenu='';
        if (isset($item['sub']) and ($max_level==0 or $level<$max_level)) {
          $submenu=$this->render($item['sub'],$attr,$level+1,$thisUri,$max_level);
        }

        // item, seperator or split
        $view='item';
        if (empty($item) or $item==='seperator') $view='seperator';
        if ($item==='split') $view='split';
        
        // first / last
        $order_style = '';
        if ($pos==1) $order_style.='first ';
        if ($pos==count($menu)) $order_style.='last';
        $order_style=trim($order_style);
        
        // Current
        $current = ($this->settings['current']==$cleanUri?$styles['current']:'').((strpos($submenu,$styles['current'])>0?' '.$styles['active']:''));
        
        // Icon
        $icon = el('icon',$item,'');
        $iconactive = el('iconactive',$item);
        if ($iconactive and $current) {
          $icon = $iconactive;
        }
        
        // render item
        $item_html=$this->CI->load->view($this->settings['view_path'].'/'.$view.'.php',array(
          'title'       => $title,
          'uri'         => $thisUri,
          'full_uri'    => el('full_uri',$item,''),
          'lev'         => $level,
          'pos'         => $pos,
          '_pos'        => $_pos,
					'order'       => $order_style,
					'sub'         => (isset($item['sub']))?$styles['is_sub']:'',
          'current'     => $current,
          'class_uri'   => $classUri,
          'class'       => el('class',$item,' '),
          'attr'        => attributes(el('attr',$item,'')),
          'clickable'   => (!empty($thisUri)),
          'submenu'     => $submenu,
          'icon'        => $icon,
        ),true);

        $html.=$item_html;
				$pos++;
        $_pos--;
			}
		}
    
    $html=$this->CI->load->view($this->settings['view_path'].'/menu.php',array('lev'=>$level,'uri'=>$uri, 'sub'=>($level>1)?$styles['has_sub']:'', 'menu'=>$html, 'framework' => $this->settings['framework']),true);
		return $html;
	}
	
	
  /**
   * Geeft item aan de hand van meegegeven uri (of huidige pagina) als database resultaat
   *
   * Als je het menu aanmaakt vanuit een menu tabel dan kun je dit gebruiken om niet nogmaals de database aan te spreken als je een item uit die menu tabel wilt. Dat kun je dan hiermee doen.
   *
   * @param string $uri[''] Uri van te verkrijgen item, als leeg dan wordt current uri gebruikt
   * @param string $foreigns default=FALSE Moeten foreign tabels ook meegenomen worden
   * @param string $many default=FALSE Idem voor Many relaties
   * @return array database item
   * @author Jan den Besten
   */
	public function get_item($uri='',$foreigns=false,$many=false) {
		$item=array('uri'=>'');
    if (empty($uri)) $uri=$this->settings['current'];
    
    $table = el('menu_table',$this->settings );
    if (empty($table)) $table=get_menu_table();
    $this->CI->data->table( $table );
    $this->CI->data->path('full_uri','uri');
    // Zoek in simpel uri veld, of naar gehele pad?
    if (strpos($uri,'/')!==FALSE) {
      $this->CI->data->where_path( 'full_uri', $uri );
    }
    else {
      $this->CI->data->where( 'uri', $uri );
      if ($this->CI->data->field_exists('self_parent')) $this->CI->data->where( 'self_parent', 0 );
    }
    // Relaties?
    if ($foreigns) $this->data->with('many_to_one');
    if ($many)     $this->data->with('many_to_many');
    // Resultaat
    $items=$this->CI->data->get_result();
    if ($items) {
      $item=current($items);
    }
    else {
      $item=FALSE;
    }
		return $item;
	}
	
  
  /**
   * Geeft uri van eerstvolgende item in submenu
   *
   * @param string $uri uri van submenu
   * @return string uri van eerstvolgend item in submenu of FALSE als deze niet bestaat
   * @author Jan den Besten
   */
  public function get_first_sub_uri($uri='') {
    $sub_uri=FALSE;
		if (empty($uri)) $uri=$this->settings['current'];
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
    if (empty($uri)) $uri=$this->settings['current'];
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
	public function get_prev($uri='') {
		$prev=false;
		if (empty($uri)) $uri=$this->settings['current'];
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
   * @param bool $full default=TRUE als TRUE dan worden full_uri's meegegeven die het hele uri pad representeren
   * @return string
   * @author Jan den Besten
   */
	public function get_prev_uri($uri='',$full=true) {
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
	public function get_next($uri='') {
		$next=false;
		if (empty($uri)) $uri=$this->settings['current'];
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
   * @param string $uri default='' Als leeg dan wordt current gebruikt
   * @param bool $full default=true als TRUE dan worden full_uri's meegegeven
   * @return string
   * @author Jan den Besten
   */
	public function get_next_uri($uri='',$full=true) {
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
	public function get_prev_branch($uri='') {
		$branch=FALSE;
		if (empty($uri)) $uri=$this->settings['current'];
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
   * @param string $full_uri default=TRUE Als TRUE dan worden full_uri's meegegeven
   * @return string
   * @author Jan den Besten
   */
	public function get_prev_branch_uri($uri='',$full_uri=true) {
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
	public function get_next_branch($uri='') {
		$branch=FALSE;
		if (empty($uri)) $uri=$this->settings['current'];
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
   * @param string $full_uri default=TRUE Als TRUE dan worden full_uri's meegegeven
   * @return string
   * @author Jan den Besten
   */
   public function get_next_branch_uri($uri='',$full_uri=true) {
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
	 * @param bool $current default=TRUE
	 * @return array
	 * @author Jan den Besten
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
