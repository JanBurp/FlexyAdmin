<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Uitbreiding op de [Active Record Class](http://codeigniter.com/user_guide/database/active_record.html) van CodeIgniter's [Database Library](http://codeigniter.com/user_guide/database/index.html)
 * 
 * Belangrijkste doelen:
 * 
 * - Resultaten meteen in een array
 * - Eenvoudig samenvoegen van tabellen via foreign keys en relatie tabellen (dus niet meer nodig om zelf joins te schrijven)
 * - Zoeken kan ook samengevoegde data
 * - Automatische bepalen van de volgorde als dat niet wordt meegegeven
 * - Opties en default waarden kunnen meegegeven worden
 * 
 * Aanroepen
 * =========
 * 
 * Omdat dit een uitbreiding is op CodeIgniters [Active Record Class](http://codeigniter.com/user_guide/database/active_record.html) kun je de methods zo aanroepen:
 * 
 *      $res = $this->db->get_results('tbl_links');
 * 
 * En je kunt de aanroepen ook aan elkaar knopen:
 * 
 *      $res = $this->db->select('url_url')->get_results('tbl_links');
 * 
 * Resultaten
 * ==========
 * 
 * Resultaten komen in de vorm van een array terug.
 * Voorbeeld voor een array die `get_row()` teruggeeft:
 * 
 *      array(
 *        [id] => '1',
 *        [uri] => 'home',
 *        [order] => '0',
 *        [self_parent] => '0',
 *        [str_title] => 'Home',
 *        [txt_text] => 'Welkom!'
 *      )
 * 
 * Voorbeeld van een array die `get_result()` teruggeeft:
 * 
 *      array(
 *        [1] => (
 *              [id] => '1',
 *              [str_title] => 'Jan den Besten - webontwerp en geluidsontwerp',
 *              [url_url] => 'http://www.jandenbesten.net'
 *             ),
 *        [2] => (
 *               [id] => '2',
 *               [str_title] => 'FlexyAdmin',
 *               [url_url] => 'http://www.flexyadmin.com'
 *              ),
 *        [3] => (
 *              [id] => '3',
 *              [str_title] => 'Muziek van Jan',
 *              [url_url] => 'http://www.jandegeluidenman.nl',
 *             )
 *        )
 * 
 * 
 * Zoeken op samengevoegde data
 * ============================
 * 
 * Je kunt in je 'where' statement ook zoeken in tabellen die met een relatietabel gekoppeld zijn.
 * Onderstaand voorbeeld geeft:
 * 
 * - rijen van *tbl_menu* en de samengevoegde (many) data van *tbl_links*
 * - waarvoor geld dat *tbl_links.str_title* = 'FlexyAdmin'
 * 
 * Voorbeeld:
 * 
 *      $this->add_many();
 *      $this->db->where( 'rel_menu__links.str_title', 'FlexyAdmin' );
 *      $result = $this->db->get_result( 'tbl_menu' );
 * 
 * Als je op id_ velden zoekt zal in de relatietabel zelf worden gezocht:
 * 
 *      $this->add_many();
 *      $this->db->where( 'rel_menu__links.id_link', 3 );
 *      $result = $this->db->get_result( 'tbl_menu' );
 * 
 * 
 *
 * @package default
 * @author Jan den Besten
 */

class MY_DB_mysql_driver extends CI_DB_mysql_driver {
	
  private $CI;
  
	private $pk;
	private $key;
	private $maxTextLen;
	private $eachResult;
  private $foreignTables;

  /**
   * FALSE als er geen foreigndata meegenomen moet worden. TRUE of een array van tabellen waarvan de foreign data megegenomen moet worden in het resultaat.
   *
   * @var mixed
   */
  public $foreigns;
  private $foreign_trees=FALSE;

  private $abstracts;
  private $asAbstracts;
  
  /**
   * FALSE als er geen relatietabellen moeten worden gekoppeld. TRUE of een array van tabellen die gekoppeld moeten worden in het resultaat.
   *
   * @var mixed
   */
  public $many;

  private $options;
  private $whereUri;
  private $uriAsFullUri;
  private $extraFullField;
  private $orderAsTree;
  private $last_order;
  private $orderByForeign;
  private $orderByMany;
  private $selectFirst;
  private	$selectFirsts;
  private $ar_dont_select;
  private $ar_last_query=FALSE;
  private $ar_last_count=FALSE;
  private $remember_query=TRUE;

  /**
   * @param string $params 
   * @author Jan den Besten
   * @ignore
   */
	public function __construct($params) {
		parent::__construct($params);
    $this->CI=& get_instance();
		$this->reset();
	}

  /**
   * Reset alles tot default en maak huidige query leeg
   *
   * @return object this
   * @author Jan den Besten
   */
	public function reset() {
		$this->primary_key();
		$this->set_key();
		$eachResult=array();
		$savedQuery=array();
		$this->as_abstracts(FALSE);
		$this->add_foreigns(FALSE);
		$this->add_abstracts(FALSE);
		$this->add_many(FALSE);
		$this->add_options(FALSE);
		$this->max_text_len();
		$this->where_uri();
		$this->uri_as_full_uri(FALSE);
		$this->order_as_tree(FALSE);
		$this->order_by_foreign(FALSE);
		$this->order_by_many(FALSE);
		$this->ar_dont_select=array();
		$this->select_first();
    $this->remember_query=TRUE;
    return $this;
	}

	
  /**
   * Repareer alle ar fields
   *
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _repair_ar() {
		// splits ar_where by OR/AND
		$where=implode(' ',$this->ar_where);
		$split=preg_split("/\s(OR|AND)\s/", $where,-1,PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		if ( ! empty($split)) {
			// Make sure, first one is OR
			if ( ! in_array($split[0],array('AND','OR'))) array_unshift($split,'OR');
			// trace_($split);
			$where=array();
			// $where[]=$split[0];
			for ($i=0; $i < count($split); $i+=2) { 
				$andor=$split[$i];
				if (isset($split[$i+1]) and !empty($split[$i+1])) {
					$item=trim($split[$i+1]);
					if (!empty($item)) {
						if ($i>0) $item=$andor.' '.$item;
						$where[]=$item;
					}
				}
			}
			$this->ar_where=$where;
			// trace_($where);
		}
	}

  /**
   * Zet primary key, standaard 'id'
   *
   * @param string $pk['id']
   * @return object this
   * @author Jan den Besten
   */
	public function primary_key($pk="id") {
		$this->pk=$pk;
    return $this;
  }

  /**
   * Zet key van de resultaat arrays.
   *
   * @param string $key['id'] Moet een unieke waarde bevatten, velden die zich ervoor lenen zijn id, uri etc.
   * @return object this
   * @author Jan den Besten
   */
	public function set_key($key="id") {
		$this->key=$key;
    return $this;
	}

  /**
   * Test of het veld bestaat in een bepaalde tabel DEPRICATED!!
   *
   * @param string $table Tabel waar het veld in wordt gecheckt
   * @param string $field Te checken veldnaam
   * @return bool TRUE als veld bestaat, anders FALSE
   * @author Jan den Besten
   * @depricated
   * @ignore
   */
	public function has_field($table,$field) {
    return $this->field_exists($field,$table);
	}
  
	
  
  /**
   * Test of een rij in een tabel bestaat afhankelijk van meegegeven data
   *
   * @param string $table Tabel waarin getest wordt
   * @param array $data data waarop getest wordt array('field'=>'value', ... )
   * @param array $unset_fields['id,'uri'] Velden die niet meegenomen worden in de test (mogen wel in $data staan)
   * @return bool True als row bestaat
   * @author Jan den Besten
   */
   public function has_row($table,$data,$unset_fields=array('id','uri')) {
    foreach ($unset_fields as $field) {
      unset($data[$field]);
    }
		foreach ($data as $field => $value) $this->where($field,$value);
    $row=$this->get_row($table);
		return !empty($row);
  }
  
	
  /**
   * Zoekt eerste veld in een tabel met bepaalde prefix
   *
   * @param string $table Tabel waarin gezocht wordt
   * @param string $pre['str'] Prefix waarop gezocht wordt
   * @return string Gevonden veldnaam, of FALSE als niets gevonden
   * @author Jan den Besten
   */
  public function get_first_field($table,$pre="str") {
		$f=$this->list_fields($table);
		$found=FALSE;
		while (!$found and $i=each($f)) {
			if ($pre==get_prefix($i['value'])) {
				$found=TRUE;
				$field=$i['value'];
			}
		}
		if ($found)
			return $field;
		else
			return FALSE;
	 }

	
   /**
    * Stel ar_order_by aan de hand van meegegeven foreign keys en door te kijken naar de order van die foreign tabellen
    *
    * @param array $order_by_foreign[FALSE]
    * @param string $table Tabel
    * @return void
    * @author Jan den Besten
    * @ignore
    */
	private function _set_order_by_foreign($order_by_foreign=FALSE,$table) {
		if ($order_by_foreign) {
			if (!is_array($order_by_foreign)) $order_by_foreign=array($order_by_foreign);
						foreach ($order_by_foreign as $key => $foreign_order_id) {
				$desc=explode(' ',$foreign_order_id);
				$foreign_order_id=$desc[0];
				// to get good order for SQL, ASC/DESC must be swapped.
				if (isset($desc[1])) $desc=''; else $desc='DESC';
				$foreign_table=foreign_table_from_key($foreign_order_id);
				$abstract_fields=$this->get_abstract_fields_sql($foreign_table);
				$sql="SELECT `id`,$abstract_fields FROM `$foreign_table` ORDER BY `abstract` $desc";
				$query=$this->query($sql);
				$foreign_order_ids=array();
				foreach ($query->result_array() as $row) {$foreign_order_ids[$row['id']]=$row['id'];}
				$query->free_result();
				foreach ($foreign_order_ids as $id => $row) {$this->order_by('('.$table.'.'.$foreign_order_id.' = '.$id.')');}
			}
		}
	}

   /**
    * Stel ar_order_by aan de hand van meegegeven many tabellen
    *
    * @param string $order_by_many 
    * @param string $table 
    * @return void
    * @author Jan den Besten
    * @ignore
    */
	private function _set_order_by_many($order_by_many=FALSE,$table) {
		if ($order_by_many) {
			if (!is_array($order_by_many)) $order_by_many=array($order_by_many);
						foreach ($order_by_many as $rel_table) {
				// trace_($rel_table);
				$desc=explode(' ',$rel_table);
				$rel_table=$desc[0];
        $rel_table=remove_postfix($rel_table,'.');
				// to get good order for SQL, ASC/DESC must be swapped.
				if (isset($desc[1])) $desc='DESC'; else $desc='';
				$foreign_table=join_table_from_rel_table($rel_table);
				$this_key=this_key_from_rel_table($rel_table);
				$join_key=join_key_from_rel_table($rel_table);
				$abstract_fields=$this->get_abstract_fields_sql($foreign_table);
				// order of foreign table
				$sql="SELECT `id`,$abstract_fields FROM `$foreign_table` ORDER BY `abstract` $desc";
				// trace_($sql);
				$query=$this->query($sql);
				$foreign_order_ids=array();
				foreach ($query->result_array() as $row) {$foreign_order_ids[$row['id']]=$row['id'];}
				$query->free_result();
				// trace_($foreign_order_ids);
				// order in relation table
				$sql="SELECT * FROM `$rel_table` ORDER BY";
				foreach ($foreign_order_ids as $id => $row) { $sql.=' ('.$join_key.' = '.$id.'),'; }
				$sql=substr($sql,0,strlen($sql)-1);
				// trace_($sql);
				$query=$this->query($sql);
				// order
				$order_ids=array();
				foreach ($query->result_array() as $row) {
					$order_ids[$row[$this_key]]=$row[$this_key];
				}
				$query->free_result();
				// for right order (of empty fields)
				// if ($desc=='DESC') sort($order_ids);
				foreach ($order_ids as $key=>$value) {$this->order_by('(`'.$table.'`.`id` = '.$value.') ');}
			}
		}
	}

	/**
	 * Sets standard order. First by looking in standard order field in config table.
	 * If no explicit order set, decides according to prefixen what order field to take.
	 * See flexyadmin_config [FIELDS_standard_order] what fields.
	 */
   
   /**
    * Stelt order_by in als deze niet wordt meegegeven
    * 
    * - Eerst wordt gekeken of er order is ingesteld in cfg_table_info
    * - Als dat niet bestaat dan wordt gezocht naar het eerste zinvolle veld wat een volgorde kan bepalen (order,str_,dat_ velden etc)
    *
    * @param string $table 
    * @param string $fallbackOrder['']
    * @param string $tree_possible[TRUE]
    * @param string $set[TRUE]
    * @return void
    * @author Jan den Besten
    * @ignore
    */
	private function _set_standard_order($table,$fallbackOrder="",$tree_possible=TRUE,$set=TRUE) {
		$order="";
		if ($this->orderAsTree and $tree_possible) {
			if ($this->field_exists('self_parent',$table)) $this->order_by("self_parent");
			if ($this->field_exists('order',$table)) $this->order_by("order");
			$order="self_parent";
		}
		else {
			// find in table info
			if (isset($this->CI->cfg)) {
				$order=$this->CI->cfg->get('CFG_table',$table,'str_order_by');
				// or first standard order field
				if (empty($order) and !empty($table)) {
					if (!empty($fallbackOrder))
						$order=$fallbackOrder;
					else {
						$stdFields=$this->CI->config->item('ORDER_default_fields');
						$fields=$this->list_fields($table);
						do {
							$curr=current($stdFields);
							$curr=explode(" ",$curr);
							$testField=trim($curr[0]);
							reset($fields);
							do {
								if (strncmp($testField,current($fields),strlen($testField))==0) {
									$order=current($fields);
									if (isset($curr[1])) $order.=" ".$curr[1];
								}
							} while (empty($order) and next($fields));
						}
						while (empty($order) and next($stdFields));
					}
				}
				$order=trim($order);
				// check if it is not id, add id to it to prefent dubious sort results (if id exists)
				if ($order!=PRIMARY_KEY) {
          if ($this->field_exists(PRIMARY_KEY,$table)) {
            $order=add_string($order,PRIMARY_KEY,',');   
          }
				}
				// trace_($order);
				
				if ($set and $order) $this->order_by($order);
			}
		}
    // if ($set) $this->order=$this->ar_orderby;
    // trace_(array('table'=>$table,'order'=>$order));
		return $order;
	}

  
  /**
   * Stel volgorde in, idem als origineel
   *
   * @param array $orderby 
   * @param string $direction 
   * @return object this
   * @author Jan den Besten
   */
	public function order_by($orderby,$direction='') {
    parent::order_by($orderby,$direction);
    $this->last_order=$this->ar_orderby;
    return $this;
	}

  /**
   * Stel de volgorde in van huidige tabel in vanuit een foriegn_key en de volgorde van het resultaat daarvan
   *
   * @param array $args[FALSE] Array van foreign keys en asc/desc
   * @return object this
   * @author Jan den Besten
   */
	public function order_by_foreign($args=FALSE) {
		$this->order_by_foreign=$args;
    return $this;
	}

  /**
   * Stel de volgorde in van huidige tabel in vanuit een many tabel en de volgorde van het resultaat daarvan
   *
   * @param array $args[FALSE] Array van many tabellen en asc/desc
   * @return object this
   * @author Jan den Besten
   */
   public function order_by_many($args=FALSE) {
		$this->order_by_many=$args;
    return $this;
	}

  /**
   * Stel de volgorde van de tabel in als een boomstructuur
   * 
   * Kan alleen bij tabellen met die de velden _order_ en _self_parent_ hebben.
   *
   * @param bool $orderAsTree[TRUE]
   * @return object this
   * @author Jan den Besten
   */
	public function order_as_tree($orderAsTree=TRUE) {
		$this->orderAsTree=$orderAsTree;
    return $this;
	}

  
  /**
   * Geeft laatst gebruikte volgorde (order_by)
   *
   * @return string
   * @author Jan den Besten
   */
	public function get_last_order() {
		$order=$this->last_order;
		if (is_array($order)) $order=current($order);
    $order=remove_suffix($order,',');
		return str_replace('`','',$order);
	}

  /**
   * Zoekt op uri
   *
   * @param string $uri=''
   * @return object this
   * @author Jan den Besten
   */
	public function where_uri($uri="") {
		$this->whereUri=$uri;
    return $this;
	}
	
  /**
   * Zoekt in met behulp van meegegeven search-set
   * 
   * Search set array ziet er alsvolgt uit:
   * 
   *      array(  "search" => "",                             // Zoekterm
   *              "field"  => "",                             // In welk veld gezocht wordt
   *              "or"     => "[AND|OR]",                     // AND of OR, default OR
   *              "table"  => ''                              // In welke tabel (default huidige tabel)
   *            )
   *
   * @param array $search
   * @param bool $word_boundaries[FALSE] Als True dan worden de zoektermen als volledige woorden gezocht 
   * @param bool $set_sql[TRUE] Als TRUE dan wordt de SQL hiermee opgebouwd 
   * @return mixed als $set_sql=TRUE dan wordt $this teruggegeven, anders de seacrh SQL
   * @author Jan den Besten
   */
	public function search($search,$word_boundaries=FALSE,$set_sql=TRUE) {
		// if $search is one dimensial array, make more dimensonal
		if (isset($search['search'])) $search=array($search);
		$default=array('search'=>'','field'=>'id','or'=>'AND','table'=>'');
		$sql='';
		foreach ($search as $k => $s) {
			if (($s['search']!='') and ($s['field']!='') and ($s['field']==trim($s['field'],'_'))) {
				$s=array_merge($default,$s);
				if (!empty($s['table'])) $s['table'].='.';
				$s['or']=strtoupper($s['or']);
				$sql.=$s['or'].' ';
				
				$fieldPre=get_prefix($s['field']);
				// search in foreign table, with abstract
				if ($fieldPre=='id' and $s['field']!='id') {
					$foreign_search=$s;
					$foreign_search['table']=foreign_table_from_key($s['field']);
					$foreign_search['or']='OR';
					$ab_fields=$this->get_abstract_fields($foreign_search['table']);
					foreach ($ab_fields as $ab_field) {
						$in=array();
						$ab_field=remove_prefix($ab_field,'.');
						$foreign_search['field']=$ab_field;
						$sub_sql="SELECT `id` FROM `".$foreign_search['table']."` WHERE ".$this->search($foreign_search,$word_boundaries,FALSE);
						$sub_query=$this->query($sub_sql);
						$foreign_ids=array();
						foreach ($sub_query->result_array() as $row) {array_push($in,$row['id']);}
						$sub_query->free_result();
						$s['in']=$in;
					}
				}

				// search in many table, with abstract
				if ($fieldPre=='rel') {
					$foreign_search=$s;
					$rel_table=$s['field'];
					$foreign_search['table']=join_table_from_rel_table($rel_table);
					$foreign_search['or']='OR';
					$this_key=this_key_from_rel_table($rel_table);
					$join_key=join_key_from_rel_table($rel_table);
					$ab_fields=$this->get_abstract_fields($foreign_search['table']);
					foreach ($ab_fields as $ab_field) {
						$in=array();
						$ab_field=remove_prefix($ab_field,'.');
						$foreign_search['field']=$ab_field;
						$sub_sql="SELECT `id` FROM `".$foreign_search['table']."` WHERE ".$this->search($foreign_search,$word_boundaries,FALSE);
						$sub_query=$this->query($sub_sql);
						$foreign_ids=array();
						foreach ($sub_query->result_array() as $row) {$foreign_ids[$row['id']]=$row;}
						$sub_query->free_result();
						if ($foreign_ids) {
							// search if any in relation table
							$sub_sql="SELECT * FROM `$rel_table` WHERE ";
							foreach ($foreign_ids as $id => $row) { $sub_sql.=' ('.$join_key.' = '.$row['id'].') OR '; }
							$sub_sql=substr($sub_sql,0,strlen($sub_sql)-3);
							$sub_query=$this->query($sub_sql);
							foreach ($sub_query->result_array() as $row) {array_push($in,$row[$this_key]);}
							$sub_query->free_result();
						}
						$s['in']=$in;
					}
					$s['field']='id';
				}
				
				// set sql depending on search type
				if (isset($s['in'])) {
					// IN ()
					$in="'".implode("','",$s['in'])."'";
					if (!empty($s['in']))
						$sql.=$s['table'].$s['field'].' IN ('.$in.') ';
					else
						$sql.=$s['table'].$s['field'].' IN (-1) '; // empty result
				}
				else {
					// LIKE
          if ($word_boundaries)
            $sql.=$s['table'].$s['field'].' REGEXP \'[[:<:]]'.$s['search'].'[[:>:]]\' ';
          else
            $sql.=$s['table'].$s['field'].' LIKE \'%'.$s['search'].'%\' ';
				}
			}
		}
		$sql='('.substr($sql,3).')';                // remove first AND and put between ()
    $sql=str_replace('AND', ') AND (',$sql);    // Make sure AND works like AND by putting terms between ()
		if ($set_sql) {
		  $this->where($sql,NULL,FALSE);
      return $this;
		}
		return $sql;
	}

	/**
	 * Select eerste veld(en) dat met bepaalde prefix begint
	 *
	 * @param string $pre['']
	 * @return object $this
	 * @author Jan den Besten
	 */
  public function select_first($pre="") {
		if (empty($pre))
			$this->selectFirst=array();
		else
			$this->selectFirst[]=$pre;
    return $this;
	}
	
  /**
   * Geeft veld terug dat met select_first() is gevonden
   *
   * @param string $n[-1] Als groter dan 1 dan is het resultaat een array van velden
   * @return mixed string of array van velden
   * @author Jan den Besten
   */
	public function get_select_first($n=-1) {
		if ($n<0) return $this->selectFirsts;
		else return $this->selectFirsts[$n];
	}

  /**
   * Zelfde als dons_select()
   *
   * @param mixed $dont_select[''] 
   * @return object $this
   * @author Jan den Besten
   */
	public function unselect($dont_select=""){
    return $this->dont_select($dont_select);
	}
  
  /**
   * de-selecteer een veld uit de SELECT lijst
   *
   * @param mixed $dont_select[''] Veldnaam die uit de selectlijst gehaald moet worden, of een string met veldnamen gescheiden door komma's of een array van veldnamen
   * @return object $this
   * @author Jan den Besten
   */
	public function dont_select($dont_select="") {
		if (!empty($dont_select)) {
      if (is_string($dont_select)) {
        $dont_select=str_replace(' ','',$dont_select);
        $dont_select=explode(',',$dont_select);
      }
      foreach ($dont_select as $value) {
  			$this->ar_dont_select[]=$value;
      }
		}
    return $this;
	}

  /**
   * Zorgt ervoor dat alle uri velden in het resultaat volledige uri-paden zijn, dus ook met de uri velden van de parents ervoor.
   *
   * @param bool $fullUri[TRUE]
   * @param string $extraFullField[''] Hier kun je meerdere velden meegeven die hetzelfde worden behandeld, bijvoorbeeld str_title
   * @return object $this
   * @author Jan den Besten
   */
	public function uri_as_full_uri($fullUri=TRUE,$extraFullField='') {
		$this->uriAsFullUri=$fullUri;
		$this->extraFullField=$extraFullField;
    return $this;
	}

   /**
    * Maakt een Query-Object met alle instellingen (foreigns, abstracts, order, full etc.)
    *
    * @param string $table['']
    * @param string $limit[0]
    * @param string $offset[0]
    * @return object CI-Query-object
    * @author Jan den Besten
    * @ignore
    */
	private function _get($table="",$limit=0,$offset=0) {
		log_("info","[DB+] Get/create query:");

		/**
		 * 'select_first' type of field is asked for
		 */
		if (!empty($this->selectFirst)) {
			$this->selectFirsts=array();
			reset($this->selectFirst);
			$fields=$this->list_fields($table);
			$loop=TRUE;
			while ($loop) {
				$preSearch=current($this->selectFirst);
				$field=current($fields);
				$preField=get_prefix($field);
				if ($preSearch==$preField) {
					$this->selectFirsts[]=$field;
					$this->select($field);
					$loop=FALSE;
				}
				$loop=($loop and each($fields)!==false);
			}
		}
		
		/**
			* As abstracts if asked for
			*/
		if ($this->asAbstracts) {
			$this->ar_select=array();
			$this->select(array($this->pk,$this->get_abstract_fields_sql($table)));
		}

		/**
			* Explicit select all fields
			*/
		if (empty($this->ar_select)) {
			$select=$this->list_fields($table);
			foreach($select as $key=>$f) {
				$select[$key]=$f;
			}
		}
		else {
			$select=$this->ar_select;
			$this->ar_select=array();
		}		

		/**
		 * add foreign (joins) if asked for
		 */
		if (!empty($this->foreigns) and $this->foreigns!==false) {
			log_("info","[DB+] add joins from foreign tables");
			if (is_array($this->foreigns)) {
				$foreignTables=$this->foreigns;
			}
			else
				$foreignTables=$this->get_foreign_tables($table);
			if (!empty($foreignTables)) {
				// loop through fields, add them to select array and see if it is a foreignfield with known foreigntables
				$selectFields=array();
				foreach($select as $field) {
					if (strpos($field,".")===FALSE)
						$selectFields[]=$table.".".$field;
					else 
						$selectFields[]=$field;
					
					// is it a foreign key? Yes: add join and selectfield(s)
					/**
					 * TODO: check if this join allready exists: set by db->join();
					 */
					if (isset($foreignTables[$field])) {
						$item=$foreignTables[$field];
						$joinTable=rtrim($item["table"],'_'); // Make sure self relations are possible
						$joinAsTable=$item["table"];
						$this->join($joinTable.' '.$joinAsTable, $joinAsTable.".$this->pk = ".$table.".".$item["key"], 'left');
						// add abstract or all foreign fields?
						if ($this->abstracts) {
              // Test if foreign table has a tree order, if so, it's not a simple question of adding some SQL, data needs to be created...
              if ($this->field_exists('self_parent',$joinAsTable)) {
                $this->foreign_trees[]=$joinAsTable;
              }
              else {
  							$abstractField=$this->get_abstract_fields_sql($joinAsTable,$field."__");
  							$selectFields[]=$abstractField;
              }
						}
						else {
							if (isset($item['fields']) and !empty($item['fields']))
								$forFields=$item['fields'];
							else
								$forFields=$this->list_fields($joinTable);
							foreach($forFields as $key=>$f) {
                if (!has_string('AS',$f)) {
                  $f.=" AS ".$joinAsTable."__".$f;
                }
								$selectFields[]= $joinAsTable.".".$f;
							}
						}
					}
				}
				// select all fields including foreign fields
				$select=$selectFields;
			}
		}
		
		// trace_($this->ar_select);
		// trace_($this->ar_join);
		
		/**
			* Select, but first unselect the dont select fields
			*/
		foreach ($this->ar_dont_select as $dont) {
			foreach ($select as $key => $value) {
				if ($value==$dont or $value=="$table.$dont") unset($select[$key]);
			}
		}
		$this->select($select);

    /**
		 * Set Order
		 */
		if ($this->order_by_foreign) {
			$this->_set_order_by_foreign($this->order_by_foreign,$table);
		}
		elseif ($this->order_by_many) {
			$this->_set_order_by_many($this->order_by_many,$table);
		}
		elseif (empty($this->ar_orderby))
			$this->_set_standard_order($table);
    
    


		/**
		 * if many, find if a where or like part is referring to a many table
		 */
		if ($this->many) {
      // trace_($this->ar_where);
			$manyTables=$this->get_many_tables($table,$this->many);
			$manyWhere=FALSE;
			$manyLike=FALSE;
      // $this->_repair_ar();
      // remove back-ticks
      // foreach ($this->ar_where as $key => $ar_where) {
      //   $this->ar_where[$key]=str_replace('`','',$ar_where);
      // }
      // trace_($this->ar_where);
      // trace_($manyTables);
			foreach($manyTables as $mTable) {
				$jTable=$mTable["join"];
				$relTable=$mTable['rel'];
        // trace_($mTable['join']);
				// WHERE
				$foundKeysArray=array_ereg_search($mTable['rel'], $this->ar_where);
        // trace_($this->ar_where);
        // trace_(!empty($foundKeysArray));
				foreach($foundKeysArray as $key) {
					$manyWhere=TRUE;
					$mWhere=$this->ar_where[$key];
          // trace_($mWhere);
					$AndOr=trim(substr($mWhere,0,3));
					if (!in_array($AndOr,array("AND","OR"))) $AndOr=''; else $AndOr.=' ';
          $mValue=get_suffix($mWhere,'=');
          $mField=remove_suffix($mWhere,'=');
          $mField=trim(str_replace('`','',get_suffix($mField,'.')));
          // trace_($mField);
          $justInRel=($mField==$mTable['id_this'] OR $mField==$mTable['id_join']);
          if ($justInRel) {
            $mWhere = $mField.' = '.$mValue;
  					$sql="SELECT ".$mTable["rel"].".".$mTable["id_this"]." AS id  
  								FROM ".$mTable["rel"]." 
  								WHERE ".$mWhere;
          }
          else {
            $mWhere = ' AND '.$mTable['join'].'.'.$mField.' = '.$mValue;
  					$sql="SELECT ".$mTable["rel"].".".$mTable["id_this"]." AS id  
  								FROM ".$mTable["rel"].",".trim($mTable["join"],'_')." 
  								WHERE ".$mTable["rel"].".".$mTable["id_join"]."=".trim($mTable["join"],'_').".id ".$mWhere;
          }
          // trace_('#SHOW# '.$sql);
					$query=$this->query($sql);
					$manyResults=$query->result_array();
					$query->free_result();
          // trace_($manyResults);
					// replace current where and add new 'WHERE IN' to active record which selects the id where the many field is right
					if (!empty($manyResults)) {
						$whereIn='';
						foreach($manyResults as $r) { $whereIn=add_string($whereIn,$r["id"],',');	}
						// $this->where_in($mTable["this"].".".$this->pk,$whereIn);
						$this->ar_where[$key]=$AndOr.$mTable["this"].".".$this->pk.' IN ('.$whereIn.') ';
					}
					else {
						// if (count($this->ar_where)==0)
							$this->ar_where[$key]='FALSE ';
						// else
						// 	$this->ar_where[$key]=' ';
					}
				}
        $this->_repair_ar();
        // trace_($this->ar_where);
				
				// LIKE
				$foundKeysArray=array_ereg_search($jTable, $this->ar_like);
				foreach($foundKeysArray as $key) {
					$manyLike=TRUE;
					$mLike=$this->ar_like[$key];
					$AndOr=trim(substr($mLike,0,3));
					if (!in_array($AndOr,array("AND","OR"))) $mLike=" AND ".$mLike;
					$sql="SELECT ".$mTable["rel"].".".$mTable["id_this"]." AS id  
								FROM ".$mTable["rel"].",".$mTable["join"]." 
								WHERE ".$mTable["rel"].".".$mTable["id_join"]."=".$mTable["join"].".id ".$mLike;
					$query=$this->query($sql);
					$manyResults=$query->result_array();
					$query->free_result();
					// trace_($manyResults);
					// remove current like and add new 'WHERE IN' to active record which selects the id where the many field is right
					unset($this->ar_like[$key]);
					// add WHERE IN statement
					$whereIn=array();
					if (!empty($manyResults)) {
						foreach($manyResults as $r) {
							$whereIn[]=$r["id"];
						}
						$this->where_in($mTable["this"].".".$this->pk,$whereIn);
					}
				}
			}
		}
		
		
		/**
		*	If TEXT maxlength, replace these in ar_where
		*/
		if ($this->maxTextLen>0) {
      $foundKeysArray=array_ereg_search("txt_", $this->ar_select);
      foreach($foundKeysArray as $key) {
        $field=$this->ar_select[$key];
        if (!has_string('CONCAT',$field))
          $this->ar_select[$key]="SUBSTRING(".$field.",1,".$this->maxTextLen.") AS `".remove_prefix($field,".")."`";
        else {
          $this->ar_select[$key]=preg_replace("/(CONCAT(.*)\\))/uiUsm", "SUBSTRING($1,1,".$this->maxTextLen.")", $this->ar_select[$key]);
        }
      }
		}
		
		//
		// whereUri
		//
		if ($this->whereUri) {
			$uri=$this->whereUri;
			$uriParts=explode('/',$this->whereUri);
			$foundId=$this->get_unique_id_from_fulluri($table,$uriParts);
			if ($foundId>-1)
				$this->where($table.'.id',$foundId);
			else
				$this->where($table.'.id','-1'); // can't be found!
		}


		// Stop caching the query
		// $this->stop_cache();

    // Where statement, later if tree
    if ($this->orderAsTree) {
      $this->last_where=$this->ar_where;
      $this->ar_where=array();
    }
    
		/**
		 * get the query
		 */
		if ($limit>=1)
			$query=$this->get($table,$limit,$offset);
		else
			$query=$this->get($table);
    if ($this->remember_query) $this->ar_last_query=$this->last_query();
    // trace_('#show#'.$this->ar_last_query);
		return $query;
	}

	/**
	 * Zoekt de parent en geeft die terug
	 *
	 * @param string $table
	 * @param array $row data
	 * @param string $extraField[''] 
	 * @param string $full[TRUE]
	 * @return array parent
	 * @author Jan den Besten
	 */
  private function get_parent($table,$row,$extraField='',$full=true) {
    $remember=$this->remember_query;
    $this->remember_query=false;
    $this->where('id',$row['self_parent']);
		$this->select("id,order,uri,self_parent");
		if (!empty($extraField)) $this->select($extraField);
		$parent=$this->get_row($table);
    if ($full and $parent['self_parent']!=0) {
      $parentParent=$this->get_parent($table,$parent,$extraField,$full);
      $parent['uri']=$parentParent['uri'].'/'.$parent['uri'];
      if ($extraField) $parent[$extraField]=$parentParent[$extraField].'&nbsp;/&nbsp;'.$parent[$extraField];
    }
    $this->remember_query=$remember;
    return $parent;
	}

  /**
   * Zoekt id van meegeven full_uri
   *
   * @param string $table
   * @param string $uriParts Het volledige uri 
   * @param int $parent[0] parent id
   * @return int id
   * @author Jan den Besten
   */
	private function get_unique_id_from_fulluri($table,$uriParts,$parent=0) {
		$foundID=-1;
		if (count($uriParts)>1) {
			$part=array_shift($uriParts);
			$sql="SELECT id,self_parent FROM $table WHERE uri='$part' AND self_parent='$parent'";
			$query=$this->query($sql);
			$items=$query->result_array();
			$query->free_result();
			// zoek in gevonden subitem
			$item=current($items);
			$found=$this->get_unique_id_from_fulluri($table,$uriParts,$item['id']);
			// geef gevonden id door
			if (count($uriParts)>0) {
				$foundID=$found;
			}
			else {
				$foundID=$item['id'];
			}
		}
		elseif (count($uriParts)==1) {
			$part=current($uriParts);
			// first check without $parent, for simple menutree's
			if ($parent==0) {
				$sql="SELECT id FROM $table WHERE uri='$part'";
				$query=$this->query($sql);
				$items=$query->result_array();
			}
			if ($parent>0 or (isset($items) and count($items)>1) ) {
				// More than one found, not a simple tree: find with parent
				$sql="SELECT id,self_parent FROM $table WHERE uri='$part' AND self_parent='$parent'";
				$query=$this->query($sql);
				$items=$query->result_array();
			}
			if ($items) {
				$query->free_result();
				$item=current($items);
				$foundID=$item['id'];
			}
		}
		return $foundID;
	}

  /**
   * Controleer of full_uri klopt
   *
   * @param string $table
   * @param string $uriParts Volledige uri 
   * @param string $self_parent
   * @return bool
   * @author Jan den Besten
   * @ignore
   */
	private function _check_fulluri($table,$uriParts,$self_parent) {
		$check=FALSE;
		$part=array_pop($uriParts);
		$sql="SELECT id,self_parent FROM $table WHERE id=$self_parent AND uri='$part'";
		$query=$this->query($sql);
		$row=$query->row_array();
		$query->free_result();
		if (!empty($row)) {
			if ($row['self_parent']==0)
				$check=TRUE;
			else
				$check=$this->_check_fulluri($table,$uriParts,$row['self_parent']);
		}
		return $check;
	}
	
	
	/**
	 * Zelfde als get_result()
	 *
	 * @param string $table
	 * @param string $limit[0] 
	 * @param string $offset[0]
	 * @return array
	 * @author Jan den Besten
	 */
	public function get_results($table,$limit=0,$offset=0) {
		return $this->get_result($table,$limit,$offset);
	}
  
  /**
   * Geeft resultaat van opgebouwde query als array met alle opties meegenomen (order, foreigns, many, abstract, full_uris etc.)
   *
   * @param string $table 
   * @param string $limit[0]
   * @param string $offset[0]
   * @return array
   * @author Jan den Besten
   */
	public function get_result($table,$limit=0,$offset=0) {
		$orderAsTree=$this->orderAsTree;
		$fullUri=$this->uriAsFullUri;
		$extraFullField=$this->extraFullField;
		
		$result=$this->_get_result($table,$limit,$offset);
    
		// order results as tree if asked for
		if ($orderAsTree and !empty($result)) {
			$options=el("options",$result);
			$multiOptions=el("multi_options",$result);
			unset($result["options"]);
			unset($result["multi_options"]);

			$result=$this->_make_tree_result($result);

      // No normal WHERE statement possible with a tree order, but this is a hack to make it possible
      if (!empty($this->last_where)) {
        $this->ar_where=$this->last_where;
        $this->select('id');
        $query=$this->get($table);
        $whereResult=$query->result_array();
        $whereResult=$this->_set_key_to($whereResult,'id');
        foreach ($result as $id => $row) {
          if (!isset($whereResult[$id])) unset($result[$id]);
        }
      }

			if ($options) $result["options"]=$options;
			if ($multiOptions) $result["multi_options"]=$multiOptions;
		}
    
		// Full uris if asked for
		if ($fullUri) {
			$uriField='uri';
			if (is_string($fullUri)) $uriField=$fullUri;
      if (is_array($fullUri)) {
        trace_(array('BUG? :: fullUri=>'=>$fullUri));
      }
			foreach ($result as $key => $row) {
				if (isset($row['self_parent']) and $row["self_parent"]!=0) {
					if (!empty($extraFullField)) $extra=$row[$extraFullField];
          // Get parent
					if ( $this->_test_if_full_path($result,$row) ) {
						$parentUri=$result[$row["self_parent"]][$uriField];
						if (!empty($extraFullField)) {$parentExtra=$result[$row["self_parent"]][$extraFullField];}
					}
					else {
						$parent=$this->get_parent($table,$row,$extraFullField,true);
            $parentUri=$parent['uri'];
						if (!empty($extraFullField)) {$parentExtra=$parent[$extraFullField];}
					}
          // Set
					$result[$key][$uriField]=$parentUri."/".$row['uri'];
					if (!empty($extraFullField)) {
						$result[$key][$extraFullField]=$parentExtra."&nbsp;/&nbsp;".$extra;
					}
				}
        // Although no parent, still need to set another uri field if set
        elseif (is_string($fullUri)) {
          $result[$key][$fullUri]=$result[$key]['uri'];
        }
			}
		}
		return $result;
	}
	
	
  /**
   * Test of rij een onderdeel is van een boom
   *
   * @param array $result gehele resultaat van een query
   * @param string $row te testen item
   * @return bool
   * @author Jan den Besten
   * @ignore
   */
  private function _test_if_full_path($result,$row) {
		$test=FALSE;
		$self_parent=$row['self_parent'];
		while (!$test and $self_parent>0 and isset($result[$self_parent])) {
			$self_parent=$result[$self_parent]['self_parent'];
			$test=($self_parent==0);
		}
		return $test;
	}
	
  /**
   * Maak van een Query resultaat een boom-structuur
   *
   * @param array $result
   * @return array $result
   * @author Jan den Besten
   * @ignore
   */
	private function _make_tree_result($result) {
		$test=current($result);
		if (count($result)>1 and isset($test["self_parent"])) {
			// group by self_parent
			$grouped=array();
			foreach ($result as $id=>$val) {
				$grouped[$val["self_parent"]][$id]=$val;
			}
			// trace_($grouped);		
			// set groups on right place, from root (=0)
			$result=$this->_groups_to_tree($grouped,0);
			// trace_($result);
		}
		return $result;
	}
  
  /**
   * Wordt gebruikt door _make_tree_result()
   *
   * @param string $grouped 
   * @param string $parent 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	private function _groups_to_tree($grouped,$parent) {
		$tree=array();
		if (count($grouped)>1 and isset($grouped[$parent])) {
			foreach($grouped[$parent] as $id=>$val) {
				$tree[$id]=$val;
				if (isset($grouped[$id])) {
					$sub=$this->_groups_to_tree($grouped,$id);
					foreach($sub as $i=>$v) $tree[$i]=$v; // merge $sub with keys in place
				}
			}
		}
		else
			$tree=current($grouped);
		return $tree;
	}
	
  
  /**
   * Veranderd de key van een resultaat array
   *
   * @param array $a 
   * @param string $key 
   * @return array
   * @author Jan den Besten
   * @ignore
   */
	public function _set_key_to($a,$key="") {
		$n=0;
		$out=array();
		$first=current($a);
		if (isset($first[$key])) {
			foreach($a as $row) {
				if (empty($key))
					$out[$n++]=$row;
				else
					$out[$row[$key]]=$row;
			}
		}
		else
			$out=$a;
		return $out;
	}
	
  /**
   * Maakt een resultaat met alle opties, en voegt er eventueel many data en options aantoe
   *
   * @param string $table 
   * @param string $limit[0]
   * @param string $offset[0]
   * @return array
   * @author Jan den Besten
   * @ignore
   */
	private function _get_result($table,$limit=0,$offset=0) {
		// init
		$result=array();
		
		// fetch data
		$query=$this->_get($table,$limit,$offset);
    
		log_("info","[DB+] Get data from query:");
		$res=$query->result_array();
		$query->free_result();
		$result=$this->_set_key_to($res,$this->key);

		/**
		 * add (one to) many data if asked for
		 */
		if ($this->many) {
			$last_order=$this->get_last_order(); // keep last order
			$manyTables=$this->get_many_tables($table,$this->many);
			if (count($manyTables)>0) {
				// loop through all results to add the add_many data
				$manyResult=array();
				foreach($result as $id=>$row) {
					// loop throught all many tables to add the many data
					foreach($manyTables as $rel=>$jTable) {
						$manyResult[$rel]=array();
						$rel=$jTable["rel"];
						$join=rtrim($jTable["join"],'_');
						if ($this->abstracts) {
							$this->select($join.".".PRIMARY_KEY);
							$this->select($this->get_abstract_fields_sql($join));
						}
						$relSelect=$this->many[$rel];
						if (!empty($relSelect)) {
							array_unshift($relSelect,$rel.'.id');
							// trace_($relSelect);
							$this->select($relSelect);
						}
						$this->from($rel);
						$this->where($jTable['rel'].'.'.$jTable["id_this"],$id);
						$this->join($join,$join.".".PRIMARY_KEY."=".$rel.".".$jTable["id_join"],"left");
						$this->order_by($rel.'.id');
						$query=$this->get();
						$resultArray=$query->result_array();
						$query->free_result();
						foreach($resultArray as $res) {
							$manyResult[$rel][$res[PRIMARY_KEY]]=$res;
						}
					}
					// insert many results at right place
          $result=$this->_insert_many_at_set_place($result,$manyResult,$id);
				}
			}
      // $this->order=$last_order;
		}


		/**
			* If where_uri, and more uri's found? Search parent uris for the right one
			* TODO: Misschien blijven er nog meer over? Wat dan?
			*/
		if (!empty($this->whereUri) and count($result)>1) {
			// trace_($this->whereUri);
			// trace_($result);
			foreach ($result as $key=>$item) {
				$parent=$item["self_parent"];
				$parentUri=$this->get_field_where($table,"uri",$this->pk,$parent);
				if (strpos($this->whereUri,$parentUri)===FALSE) unset($result[$key]);
			}
			// trace_($result);
		}

    /**
     * RESET ALL, but keep settings for last addings
     */
		$this->ar_last_count=count($result);
    $options=$this->options;
    $foreign_trees=$this->foreign_trees;
		$this->reset();
    
		/**
		 * add options if asked for
		 */
		if ($options and !empty($result)) {
			$result=$this->_add_field_options($result,$table);
			// options of foreigntables
			$result=$this->_add_foreign_options($result,$this->get_foreign_tables($table),$table);
			// options of many tables
			if (!isset($manyTables)) {
				$manyTables=$this->get_many_tables($table);
			}
			$result=$this->_add_many_options($result,$manyTables);
		}

    /**
     * If foreign data is tree, add these data
     */
    if ($foreign_trees) {
      foreach ($foreign_trees as $foreign_table ) {
        $foreign_key=foreign_key_from_table($foreign_table);
        $foreign_key_abstract=$foreign_key.'__'.$this->CI->config->item('ABSTRACT_field_name');
        $abstract_fields=$this->get_abstract_fields($foreign_table);
        $abstract_field=get_suffix(current($abstract_fields),'.');
        // get all foreign tree data
        $this->select('id,uri,order,self_parent');
        $this->select($abstract_field)->order_as_tree()->uri_as_full_uri(TRUE,$abstract_field);
        $this->remember_query=FALSE;
        $foreign_data=$this->get_results($foreign_table,0,0);
        $this->remember_query=TRUE;
        // put tree abstract in result
        foreach ($result as $id => $row) {
          if (isset($foreign_data[$row[$foreign_key]][$abstract_field]))
            $result[$id][$foreign_key_abstract]=$foreign_data[$row[$foreign_key]][$abstract_field];
          else
            $result[$id][$foreign_key_abstract]='';
        }
      }
    }
    
		log_("info","[DB+] data ready");
		return $result;
	}
	
  /**
   * Plaatst many velden op goede volgorde in resultaat
   *
   * @param array $result 
   * @param array $manyResult 
   * @param int $id 
   * @return array
   * @author Jan den Besten
   * @internal
   * @ignore
   */
  private function _insert_many_at_set_place($result,$manyResult,$id) {
    		foreach ($manyResult as $rel => $relData) {
      $manyOrder='';
			if (isset($this->CI->cfg)) $manyOrder=$this->CI->cfg->get('CFG_table',$rel,'str_form_many_order');
      if (empty($manyOrder)) $manyOrder='last';
			switch ($manyOrder) {
				case 'first':
					// always first: id, uri, order, self_parent
					$firstResult=$result[$id];
					$lastResult=$result[$id];
					unset($lastResult[PRIMARY_KEY]);
					unset($lastResult['uri']);
					unset($lastResult['order']);
					unset($lastResult['self_parent']);
					$firstResult=array_diff_assoc($firstResult,$lastResult);
					$result[$id]=array_merge($firstResult,array($rel=>$relData),$lastResult); // first many fields
					break;
				case 'last':
				default:
					$result[$id]=array_merge($result[$id],array($rel=>$relData)); // normal order
					break;
			}
		}
    return $result;
  }
  
  
  /**
   * Geeft resultaat als PHP array string
   *
   * @param string $table
   * @param string $limit[0] 
   * @param string $offset[0]
   * @return string PHP array
   * @author Jan den Besten
   */
	public function get_result_as_php($table,$limit=0,$offset=0) {
		return array2php($this->get_result($table,$limit,$offset));
	}
	
  /**
   * Geeft resultaat als XML string
   *
   * @param string $table 
   * @param string $limit[0]
   * @param string $offset[0]
   * @return string XML
   * @author Jan den Besten
   */
	public function get_result_as_xml($table,$limit=0,$offset=0) {
		return array2xml($this->get_result($table,$limit,$offset));
	}
	
	/**
	 * Geeft één rij van een resultaat
	 *
	 * @param string $table
	 * @param string $offset[0] 
	 * @return array
	 * @author Jan den Besten
	 */
  public function get_row($table,$offset=0) {
		if ($offset>1)
			$data=$this->get_result($table,1,$offset);
		else
			$data=$this->get_result($table,1);
		return current($data);
	}
	
  /**
   * Geeft waarde van een veld uit een tabel aan de hand van een waarde in een ander veld, als veld niet bestaat: FALSE
   *
   * @param string $table
   * @param string $field 
   * @param string $where veld waarop voorwaarde getest word
   * @param string $what waarde van de voorwaarde
   * @param string $like[FALSE] Als TRUE dan wordt de voorwaarde getest met LIKE ipv WHERE
   * @return mixed
   * @author Jan den Besten
   */
	public function get_field_where($table,$field,$where="",$what="",$like=FALSE) {
		$sql="SELECT `$field` FROM `$table` ";
		if ($where=='' or $what=='')
			$sql.=" LIMIT 1";
		else {
			if ($like)
				$sql.="WHERE `$where` LIKE '$what%'";
			else
				$sql.="WHERE `$where`='$what'";
		}
		$query=$this->query($sql);
		$row=$query->row_array();
		$query->free_result();
		if (isset($row[$field]))
			return $row[$field];
		else
			return FALSE;
	}
  
  /**
   * Geeft waarde van een veld
   *
   * @param string $table 
   * @param string $field 
   * @param string $id[''] id van de rij in de tabel
   * @return mixed
   * @author Jan den Besten
   */
	public function get_field($table,$field,$id="") {
		return $this->get_field_where($table,$field,$this->pk,$id);
	}
  
  
  /**
   * Geeft waarde van een veld en kiest die een willekeurig rij
   *
   * @param string $table 
   * @param string $field
   * @return mixed
   * @author Jan den Besten
   */
	public function get_random_field($table,$field='id') {
		$sql="SELECT `$field` FROM `$table` ORDER BY RAND()";
		$query=$this->query($sql);
		$row=$query->row_array();
		$query->free_result();
		return $row[$field];
	}
	
  /**
   * Maakt resulaat en met elke aanroep van get_each() wordt de volgende rij gegeven
   *
   * @param string $table 
   * @param string $limit[0]
   * @param string $offset[0]
   * @return array
   * @author Jan den Besten
   */
	public function get_each($table="",$limit=0,$offset=0) {
		if (!isset($this->eachResult)) {
			$this->eachResult=$this->get_result($table,$limit,$offset);
		}
		$result=each($this->eachResult);
		if ($result===FALSE) $this->reset();
		return $result;
	}

  /**
   * Geeft aantal rijen van laatste resultaat
   *
   * @return int
   * @author Jan den Besten
   */
	public function last_num_rows() {
		return $this->ar_last_count;
	}

  /**
   * Geeft aantal rijen van laatste resultaat in het geval dat LIMIT op 0 zou staan
   *
   * @return init
   * @author Jan den Besten
   */
	public function last_num_rows_no_limit() {
		if ( ! $this->ar_last_query) {
			return FALSE;
		}
		$sql=$this->ar_last_query;
    
    // Remove sub SELECTS
    $sql = preg_replace("/,?\(SELECT.*\)/uiU", "", $sql);
    
    // Find table
		$match=array();
		$table='';
    $tablePlus='';
		if ( preg_match('/\sFROM\s(.*?)\s/si',$sql,$match) ) {
			$table=trim($match[1],'()`"');
			$tablePlus="`$table`.";
		}

    // Cleanup Query
    $num_rows=0;
    if (!empty($tablePlus)) {
      $sql=preg_replace('/SELECT(.*)?\sFROM\s/si','SELECT '.$tablePlus.'`id` FROM',$sql);
  		$sql=preg_replace('/ORDER BY(.*)?/si','',$sql);
  		$query=$this->query($sql);
  		$num_rows=$query->num_rows();
  		$query->free_result();
    }

		return $num_rows;
	}

  /**
   * Maakt CONCAT sql van velden
   *
   * @param array $fields 
   * @return string
   * @author Jan den Besten
   */
	private function concat($fields) {
		$sql="CONCAT_WS('|',".implode(",",$fields)." )";
		return $sql;
	}

  /**
   * Stel maximum aantal karakters in dat in TEXT velden wordt teruggegeven in een resultaat
   *
   * @param string $max[0] als 0 dan is er geen beperking
   * @return object $this
   * @author Jan den Besten
   */
	public function max_text_len($max=0) {
		$this->maxTextLen=$max;
    return $this;
	}


	/**
	 * Geeft alle foreign tables die naar de gegeven tabel verwijzen
	 *
	 * @param string $table Table
	 * @return array
	 */
	public function get_foreign_tables($table="") {
		$fields=$this->list_fields($table);
		foreach ($fields as $f) {
			if (is_foreign_key($f)) {
				$this->foreignTables[$f]["key"]=$f;
				$this->foreignTables[$f]["table"]=foreign_table_from_key($f);
			}
		}
		return $this->foreignTables;
	}

  
  /**
   * Voegt foreign tabeldata toe aan resultaat
   * 
   * Je kunt ook een array meegeven (ipv TRUE) om een deel van de foreign data te selecteren, bijvoorbeeld:
   * 
   *     // Neem alleen data mee uit de tabel *tbl_links* en daarvan alleen het veld *str_title*
   *     $this->db->add_foreigns( array( 'tbl_links'=>array('str_title','txt_text AS txt_text') ) ); 
   *
   * @param mixed $foreigns[TRUE]
   * @return object $this
   * @author Jan den Besten
   */
	public function add_foreigns($foreigns=true) {
		$this->foreigns=$foreigns;
    $this->foreign_trees=FALSE;
		if (is_array($foreigns)) {
			$this->foreigns=array();
			foreach ($foreigns as $table => $value) {
				$key='id_'.remove_prefix($table);
				$this->foreigns[$key]=array('key'=>$key,'table'=>$table,'fields'=>$value);
				if (substr($key,strlen($key)-1,1)=='s') {
					$key=substr($key,0,strlen($key)-1);
					$this->foreigns[$key]=array('key'=>$key,'table'=>$table,'fields'=>$value);
				}
			}
		}
		$this->foreignTables=array();
    return $this;
	}

  /**
   * Zelfde als add_foreigns() maar dan worden de velden van de foreign data samengevoegd tot een abstract veld
   *
   * @param mixed $foreigns[TRUE]
   * @return object $this
   * @author Jan den Besten
   */
	public function add_foreigns_as_abstracts($foreigns=true) {
		$this->add_foreigns($foreigns);
		$this->add_abstracts();
    return $this;
	}

  /**
   * Zelfde als add_foreigns_as_abstracts() met dit verschil dat alleen als meegegeven argument TRUE is of een array automatisch add_foreigns() wordt aangeroepen
   *
   * @param mixed $abstracts[TRUE] 
   * @return object $this
   * @author Jan den Besten
   */
	public function add_abstracts($abstracts=true) {
		$this->abstracts=$abstracts;
		if ($abstracts!=false) $this->add_foreigns();
    return $this;
	}
	
  /**
   * Resultaat wordt teruggegeven als een abstract
   *
   * @param bool $as[TRUE] 
   * @return object $this
   * @author Jan den Besten
   */
	public function as_abstracts($as=true) {
		$this->asAbstracts=$as;
    return $this;
	}
	
  /**
   * Geeft de abstract velden terug van een tabel
   * 
   * Zal eerst checken in *cfg_table_info* of daar abstract velden zijn ingesteld, anders wordt er iets aanneemlijks gecreeërd.
   *
   * @param string $table
   * @param string $asPre[''] 
   * @return array
   * @author Jan den Besten
   */
	public function get_abstract_fields($table,$asPre='') {
		$cleanTable=rtrim($table,'_'); // make sure self relation is possible
		$abFields=array();
		/**
		 * First check if abstract fields are set for this table
		 */
		$f=$this->CI->cfg->get('CFG_table',$cleanTable,"str_abstract_fields");
		if (isset($f)) $abFields=explode_pre(",",$f,$cleanTable.".");
		/**
		 * If not set: Auto abstract fields according to prefixes
		 */
		if (empty($abFields)) {
			$allFields=$this->list_fields($cleanTable);
			$preTypes=$this->CI->config->item('ABSTRACT_field_pre_types');
			$nr=$this->CI->config->item('ABSTRACT_field_max');
			$loop=true;
			while ($loop) {
				$field=current($allFields);
				$pre=get_prefix($field);
				if (in_array($pre,$preTypes)) {
					array_push($abFields,$table.".".$field);
					$nr--;
				}
				$loop=($nr>0 and each($allFields)!==false);
			}
		}
		/**
		 * If not set: Auto abstract fields according to db types
		 */
		if (empty($abFields)) {
			$fieldData=$this->field_data($cleanTable);
			$types=$this->CI->config->item('ABSTRACT_field_types');
			$nr=$this->CI->config->item('ABSTRACT_field_max');
			$loop=true;
			while ($loop) {
				$fieldInfo=current($fieldData);
				if ($fieldInfo) {
					$type=$fieldInfo->type;
					if (in_array($type,$types)) {
						array_push($abFields,$table.".".$fieldInfo->name);
						$nr--;
					}
				}
				$loop=($nr>0 and each($fieldData)!==false);
			}
		}
		/**
		 * If still nothing set... just get the first fields
		 */
		if (empty($abFields)) {
			$allFields=$this->list_fields($cleanTable);
			$nr=$this->CI->config->item('ABSTRACT_field_max');
			for ($n=0; $n<$nr; $n++) {
				array_push($abFields,$table.".".each($allFields));
			}
		}
    foreach ($abFields as $key => $field) {
      $abFields[$key]=trim($field);
    }
		return $abFields;
	}

	/**
	 * Geeft SQL terug met abstractfields
	 *
	 * @param string $table 
	 * @param string $asPre['']
	 * @return string
	 * @author Jan den Besten
	 */
  public function get_abstract_fields_sql($table,$asPre='') {
		$abFields=$this->get_abstract_fields($table,$asPre);
    // Deep foreigns?
    $cfgDeep=$this->CI->config->item('DEEP_FOREIGNS');
    if ($cfgDeep) {
      foreach ($cfgDeep as $deepKey => $deepInfo) {
        // Is er een deep foreing field? Plaats de abstract daarvan
        if ($nr=in_array_like($deepKey,$abFields)) {
          $deep_field=$abFields[$nr];
          $abFields[$nr]="(SELECT `".$deepInfo['abstract']."` FROM `".$deepInfo['table']."` WHERE ".$deepInfo['table'].".id=".$deep_field.")";
        }
      }
    }
    // Maak de SQL
    $sql=$this->concat($abFields)." AS ".$asPre.$this->CI->config->item('ABSTRACT_field_name');
    return $sql;
	}

  
	/**
	 * Zelfde als get_abstract_fields_sql()
	 *
	 * @param string $table 
	 * @param string $asPre 
	 * @return string
	 * @author Jan den Besten
	 * @depricated
	 * @ignore
	 */
	public function get_abstract_field($table,$asPre="") {
		return $this->get_abstract_fields_sql($table, $asPre);
	}

  /**
   * Geeft alle tables terug met gegeven prefix
   *
   * @param string $prefix['tbl_']
   * @return array
   * @author Jan den Besten
   */
	public function get_tables($prefix='tbl_') {
		$tables = $this->list_tables();
		$tables = filter_by($tables,$prefix);
		return $tables;
	}

   /**
    * Geeft alle tabellen (en meer info) die een many-many relatie hebben met gegeven tabel (dus via een rel_ tabel)
    *
    * @param string $table 
    * @param array $tables[''] hier kun je een voorselectie meegeven
    * @return array
    * @author Jan den Besten
    */
		public function get_many_tables($table,$tables='') {
			$out=array();
						if (empty($tables) or !is_array($tables)) {
				// list all tables with right name
				$like=$this->CI->config->item('REL_table_prefix')."_".remove_prefix($table).$this->CI->config->item('REL_table_split');
				if (isset($this->CI->cfg)) {
					// first table with info (for order)
					$tablesWithInfo=$this->CI->cfg->get('CFG_table');
					$tablesWithInfo=array_keys($tablesWithInfo);
					$tablesWithInfo=filter_by($tablesWithInfo,$like);
					if (!empty($tablesWithInfo)) $tablesWithInfo=array_combine($tablesWithInfo,$tablesWithInfo);
				}
				// add tables with no info
				$tables=$this->list_tables();
				$tables=filter_by($tables,$like);
				if (!empty($tables)) $tables=array_combine($tables,$tables);
				if (isset($tablesWithInfo) and !empty($tablesWithInfo)) $tables=array_merge($tablesWithInfo,$tables);
			}

			foreach ($tables as $rel=>$row) {
				$relFields=$this->list_fields($rel);
				$out[$rel]["this"]=$table;
				$out[$rel]["rel"] =$rel;
				// $join=join_table_from_rel_table($rel);
				$out[$rel]["join"]=foreign_table_from_key($relFields[2]);
				$out[$rel]["id_this"]=$relFields[1];
				$out[$rel]["id_join"]=$relFields[2];
			}
			return $out;
		}

  /**
   * Neem alle many-many data ook mee, dus data van tabellen die met een relatietabel gekoppeld zijn
   * 
   * Je kunt ook een deel van de many-tabellen filteren:
   * 
   *     $this->db->add_many( );   // Neemt van alle tabellen alle velden mee.
   *     $this->db->add_many( array( 'rel_menu__links' ) );   // Neemt van de tabel *tbl_links* alle velden mee.
   *     $this->db->add_many( array( 'rel_menu__links'=>array('uri','str_title') ) ); // Neemt van de tabel *tbl_links* de velden *uri* en *str_title* mee.
   *     $this->db->add_many( array( 'rel_menu__links'=>array('uri','str_title') ) ); // Neemt van de tabel *tbl_links* de velden *uri* en *str_title* mee.
   *
   * @param mixed $many[true]
   * @return object $this
   * @author Jan den Besten
   */
	public function add_many($many=true) {
		if (is_string($many)) $many=array($many);
    // Make sure tables has right format
    if (is_array($many)) {
      $test=current($many);
      if (!is_array($test)) {
        $many=array_combine($many,$many);
        foreach ($many as $key => $value) {
          $many[$key]=array();
        }
      }
    }
		$this->many=$many;
    return $this;
	}
	
  /**
   * Voeg opties (van velden die in een formulier meerkeuze zijn) toe aan het resultaat
   *
   * @param bool $options[true]
   * @return object $this
   * @author Jan den Besten
   */
	public function add_options($options=true) {
		$this->options=$options;
    return $this;
	}

  /**
   * Geeft alle opties van meerkeuze velden uit een gegeven tabel
   *
   * @param string $table
   * @param string $optionsWhere['']
   * @return array
   * @author Jan den Besten
   */
	public function get_options($table,$optionsWhere="") {
			$options=array();
			$cleanTable=rtrim($table,'_');
			$asTree=$this->field_exists('self_parent',$cleanTable);
      
			$this->select($this->pk);
			if ($asTree) $this->select('uri,order,self_parent');
      $abstract_fields=$this->get_abstract_fields($cleanTable);
      foreach ($abstract_fields as $key => $value) {
        $abstract_fields[$key]=trim(remove_prefix($value,'.'));
      }
      // $abstract_field=remove_prefix(current($abstract_field),'.');
      $this->select($abstract_fields);
      $abstract_field=current($abstract_fields);
			if ($asTree) {
        $this->order_as_tree();
        $this->uri_as_full_uri(TRUE,$abstract_field);
			}
			else {
        // $this->_set_standard_order($cleanTable,$this->CI->config->item('ABSTRACT_field_name'));
        $this->_set_standard_order($cleanTable,$abstract_field);
			}
      
			if (!empty($optionsWhere)) $this->ar_where[]=$optionsWhere;
      
      // Hard coded usersgroup options
      if ($table=='cfg_user_groups') $this->where('id >=',$this->CI->user_group_id);
        
      // Get results
      if ($asTree) {
        $res=$this->get_results($cleanTable);
        unset($res['options']);
        unset($res['multi_options']);
        // strace_($res);
      }
      else {
        $query=$this->get($cleanTable);
        $res=$query->result_array();
        $query->free_result();
        // set id as key
        $res=$this->_set_key_to($res,PRIMARY_KEY);
      }
      // Deep foreigns?
      $cfgDeep=$this->CI->config->item('DEEP_FOREIGNS');
			foreach($res as $row) {
        $options[$row[$this->pk]]='';
        foreach ($abstract_fields as $field) {
          $thisOption=$row[$field];
          if ($cfgDeep) {
            if (isset($cfgDeep[$field])) {
              $query=$this->query('SELECT `'.$cfgDeep[$field]['abstract'].'` FROM `'.$cfgDeep[$field]['table'].'` WHERE id='.$row[$field].' LIMIT 1');
              $temp = $query->row();
              if ($temp) $thisOption = $temp->$cfgDeep[$field]['abstract'];
            }
          }
          $options[$row[$this->pk]]=add_string($options[$row[$this->pk]], $thisOption, ' | ');
        }
        // $options[$row[$this->pk]]=$row[$abstract_field];
        // if ($asTree and $row['self_parent']!=0 and isset($options[$row[$this->pk]]) and isset($res[$row['self_parent']])) $options[$row[$this->pk]]=$res[$row['self_parent']][$abstract_field].' / '.$options[$row[$this->pk]];
			}
      return $options;
		}

	/**
	 * Voegt opties toe van foreign tables
	 * 
	 * @param array	$out Huidig resultaat
	 * @param array	$foreignTables de foreigntabellen waarvan de opties moeten worden toegevoegd
	 * @param string $table['']
	 * @return array
	 * @ignore
	 */
	 private function _add_foreign_options($out,$foreignTables,$table='') {
			$options=array();
			if (isset($foreignTables)) {
				foreach ($foreignTables as $key => $forTable) {
					$cleanTable=rtrim($forTable['table'],'_');
					$optionsWhere=$this->CI->cfg->get('CFG_table',$cleanTable,'str_options_where');
					// override options Where with Field Info, if there is any
					if (!empty($table)) {
						$cfgKey=$table.'.'.$forTable['key'];
						$optWhere=$this->CI->cfg->get('CFG_field',$cfgKey,'str_options_where');
						if (!empty($optWhere)) $optionsWhere=$optWhere;
					}
					$options[$key]=$this->get_options($cleanTable,$optionsWhere);
				}
			}
			if (count($options)>0) {
				if (isset($out["options"]))
					$out["options"]=array_merge($out["options"],$options);
				else
					$out["options"]=$options;
			}
			return $out;
		}

	/**
	 * Voegt opties van many-tables toe aan huidige resultaat
	 * 
	 * @param array	$out huidig resultaat
	 * @param array	$joinTables
	 * @return array
	 * @ignore
	 */
	 private function _add_many_options($out,$manyTables) {
			$options=array();
			if (isset($manyTables)) {
				foreach ($manyTables as $rel => $jTable) {
					$optionsWhere=$this->CI->cfg->get('CFG_table',$jTable["rel"],'str_options_where');
					$options[$rel]=$this->get_options($jTable["join"],$optionsWhere);
				}
			}
			if (count($options)>0) {
				if (isset($out["options"]))
					$out["options"]=array_merge($out["options"],$options);
				else
					$out["options"]=$options;
			}
			return $out;
		}

	/**
	 * Voeg opties toe van alle velden in de tabel waarvan opties ingesteld zijn in cfg_field_info
	 *
	 * @param array	$out huidige resultaat
	 * @param string $table
	 * @return array
	 * @ignore
	 */
	 private function _add_field_options($out,$table) {
			// search options in cfg_field_info for every field, if found, give the options
			$fields=$this->list_fields($table);
			foreach($fields as $field) {
        // specifiek veld
				$options=$this->CI->cfg->get('CFG_field',$table.".".$field,'str_options');
        // of generiek veld
        if (empty($options)) $options=$this->CI->cfg->get('CFG_field',"*.".$field,'str_options');
        
				if (isset($options) and !empty($options))	{
					$options=explode("|",$options);
					if ($this->CI->cfg->get('CFG_field',$table.".".$field,'b_multi_options'))
						$out["multi_options"][$field]=array_combine($options,$options);
					else
						$out["options"][$field]=array_combine($options,$options);
				}
			}
			return $out;
		}


	/**
	 * Geeft default waarden terug van velden in een tabel
	 *
	 * @param string $table
	 * @return array
	 */
	public function defaults($table) {
		log_("info","[DB+] Get default data:");
		$out=array();
		$id=-1;
		$fields=$this->list_fields($table);
		foreach ($fields as $field) {
			$out[$id][$field]=$this->CI->cfg->field_data($table,$field,'default');
			if ($out[$id][$field]==NULL) $out[$id][$field]="";
		}

		/**
		 * Add many table defaults if asked for
		 */
		if ($this->many) {
			$jt=$this->get_many_tables($table);
			if (count($jt)>0) {
				foreach($jt as $rel=>$jTable) {
					$out[$id][$rel]=array();
				}
        $out=$this->_insert_many_at_set_place($out,$jt,$id);
			}
		}

		/**
		 * add options if asked for
		 */
		if ($this->options) {
			$out=$this->_add_field_options($out,$table);
			// foreign table options
			$ft=$this->get_foreign_tables($table);
			$out=$this->_add_foreign_options($out,$ft,$table);
			// join table options
			if (!isset($jt)) $jt=$this->get_many_tables($table);
			$out=$this->_add_many_options($out,$jt);
		}
		log_("info","[DB+] default data ready");
		$this->reset();
		return $out;
	}


}
?>
