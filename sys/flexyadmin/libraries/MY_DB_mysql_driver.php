<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	Extended DB_Mysql Class
*	
* With first some easy methods for retrieving results.
* And some methods to include abstract fields or all fields from foreign tables (joins)
* And some methods to include and where data from one to many relations (rel)
*
* TODO: for other databases, put non driver restricted methods in an include file and include it in every driver
*/

class MY_DB_mysql_driver extends CI_DB_mysql_driver {
	
	var $pk;
	var $key;
	var $maxTextLen;
	var $eachResult;
	var $foreignTables;
	var $foreigns;
	var $abstracts;
	var $asAbstracts;
	var $many;
	var $options;
	var $whereUri;
	var $uriAsFullUri;
	var $orderAsTree;
	var $ar_dont_select;
	var $selectFirst;
	var	$selectFirsts;

	function MY_DB_mysql_driver($params) {
		parent::CI_DB_mysql_driver($params);
		$this->reset();
	}

	function reset() {
		$this->primary_key();
		$this->set_key();
		$eachResult=array();
		$savedQuery=array();
		$this->add_foreigns(FALSE);
		$this->add_abstracts(FALSE);
		$this->add_many(FALSE);
		$this->add_options(FALSE);
		$this->max_text_len();
		$this->where_uri();
		$this->uri_as_full_uri(FALSE);
		$this->order_as_tree(FALSE);
		$this->ar_dont_select=array();
		$this->select_first();
	}

	/**
	 * Sets primary key.
	 * If not set, the standard primary key is used.
	 * If set, another associatibe key in output array will be used.
	 */
	function primary_key($pk="id") {
		$this->pk=$pk;
	}

	function set_key($key="id") {
		$this->key=$key;
	}

	function has_field($table,$field) {
		$f=$this->list_fields($table);
		return (in_array($field,$f));
	}
	
	function get_first_field($table,$pre="str") {
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
	 * Searches for a standard order field in config table.
	 * If no explicit order set, decides according to prefixen what order field to take.
	 * See flexyadmin_config [FIELDS_standard_order] what fields.
	 */
	function _set_standard_order($table,$fallbackOrder="") {
		$order="";
		if ($this->orderAsTree) {
			$this->order_by("self_parent");
			$this->order_by("order");
			$order="self_parent";
		}
		else {
			$CI=& get_instance();
			// find in table info
			if (isset($CI->cfg)) {
				$order=$CI->cfg->get('CFG_table',$table,'str_order_by');
				// or first standard order field
				if (empty($order) and !empty($table)) {
					if (!empty($fallbackOrder))
						$order=$fallbackOrder;
					else {
						$stdFields=$CI->config->item('ORDER_default_fields');
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
				$this->order_by($order);
			}
		}
		return $order;
	}

	function where_uri($uri="") {
		$this->whereUri=$uri;
		if (!empty($uri)) {
			$lastPart=get_postfix($uri,"/");
			$this->like("uri",$lastPart,"before");
		}
	}
	
	/**
	*	array("search"=>"", "field"=>"", "or"=>"and/or" )
	*/
	function search($search) {
		foreach ($search as $k => $s) {
			if (!empty($s['search']) and !empty($s['field'])) {
				if (isset($s["or"]) and $s["or"]=="or")
					$this->or_like($s["field"],$s["search"]);
				else
					$this->like($s["field"],$s["search"]);
			}
		}
	}

	function select_first($pre="") {
		if (empty($pre))
			$this->selectFirst=array();
		else
			$this->selectFirst[]=$pre;
	}
	
	function get_select_first($n=-1) {
		if ($n<0) return $this->selectFirsts;
		else return $this->selectFirsts[$n];
	}

	function dont_select($dont_select="") {
		if (!empty($dont_select)) {
			$this->ar_dont_select[]=$dont_select;
		}
	}

	function order_as_tree($orderAsTree=TRUE) {
		$this->orderAsTree=$orderAsTree;
	}

	function uri_as_full_uri($fullUri=TRUE) {
		$this->uriAsFullUri=$fullUri;
	}

	/**
	*	Some function to get nice results
	*/


	/**
	 * function _get()
	 * Creates query (with foreigns and abstracts)
	 *
	 * @param string $table Tablename, maybe set before
	 * @return object Query
	 */
	function _get($table="",$limit=0,$offset=0) {
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
			$this->select(array($this->pk,$this->get_abstract_field($table)));
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
							$abstractField=$this->get_abstract_field($joinAsTable,$field."__");
							$selectFields[]=$abstractField;
						}
						else {
							if (isset($item['fields']) and !empty($item['fields']))
								$forFields=$item['fields'];
							else
								$forFields=$this->list_fields($joinTable);
							foreach($forFields as $key=>$f) {
								$selectFields[]= $joinAsTable.".".$f." AS ".$joinAsTable."__".$f;
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
		 * set standard order if not set
		 */
		if (empty($this->ar_orderby)) $this->_set_standard_order($table);

		/**
		 * if many, find if a where or like part is referring to a many table
		 */
		if ($this->many) {
			$manyTables=$this->get_many_tables($table,$this->many);
			$manyWhere=FALSE;
			$manyLike=FALSE;
			// trace_($this->ar_where);
			// trace_($manyTables);
			foreach($manyTables as $mTable) {
				$jTable=$mTable["join"];
				// WHERE
				$foundKeysArray=array_ereg_search($jTable, $this->ar_where);
				// trace_($foundKeysArray);
				foreach($foundKeysArray as $key) {
					$manyWhere=TRUE;
					$mWhere=$this->ar_where[$key];
					$AndOr=trim(substr($mWhere,0,3));
					if (!in_array($AndOr,array("AND","OR"))) $mWhere=" AND ".$mWhere;
					$sql="SELECT ".$mTable["rel"].".".$mTable["id_this"]." AS id  
								FROM ".$mTable["rel"].",".$mTable["join"]." 
								WHERE ".$mTable["rel"].".".$mTable["id_join"]."=".$mTable["join"].".id ".$mWhere;
					$query=$this->query($sql);
					$manyResults=$query->result_array();
					// remove current where and add new 'WHERE IN' to active record which selects the id where the many field is right
					unset($this->ar_where[$key]);
					// add WHERE IN statement
					$whereIn=array();
					if (!empty($manyResults)) {
						foreach($manyResults as $r) {
							$whereIn[]=$r["id"];
						}
						$this->where_in($mTable["this"].".".$this->pk,$whereIn);
					}
				}
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
				$this->ar_select[$key]="SUBSTRING(".$field.",1,".$this->maxTextLen.") AS `".remove_prefix($field,".")."`";
			}
		}
		
		//
		// if $whereUri, add tablename in front of it
		//
		if ($this->whereUri) {
			foreach ($this->ar_like as $key => $value) {
				$new=str_replace('`uri`','`'.$table.'`.`uri`',$value);
				if ($new!=$value) $this->ar_like[$key]=$new;
			}
		}
		
		/**
		 * get the query
		 */
		if ($limit>=1)
			$query=$this->get($table,$limit,$offset);
		else
			$query=$this->get($table);
		// trace_($this->last_query());
		return $query;
	}
	
	function get_parent_uri($table,$uri="") {
		if (!empty($uri))	$id=$this->get_field_where($table,"id","uri",$uri);
		$this->order_as_tree();
		$this->uri_as_full_uri();
		$this->select("id,order,uri,self_parent");
		$result=$this->get_result($table);
		if (!empty($uri))
			return $result[$id];
		else
			return $result;
	}
	
	function get_results($table,$limit=0,$offset=0) {
		return $this->get_result($table,$limit,$offset);
	}
	function get_result($table,$limit=0,$offset=0) {
		$orderAsTree=$this->orderAsTree;
		$fullUri=$this->uriAsFullUri;
		
		$result=$this->_get_result($table,$limit,$offset);
		// order results if asked for
		if ($orderAsTree and !empty($result)) {
			$options=el("options",$result);
			$multiOptions=el("multi_options",$result);
			unset($result["options"]);
			unset($result["multi_options"]);
			$result=$this->_make_tree_result($result);
			if ($options) $result["options"]=$options;
			if ($multiOptions) $result["multi_options"]=$multiOptions;
		}
		
		/**
		 * Full uris if asked for
		 */
		if ($fullUri) {
			foreach ($result as $key => $row) {
				if ($row["self_parent"]!=0) {
					$uri=$row["uri"];
					if (isset($result[$row["self_parent"]])) {
						$parentUri=$result[$row["self_parent"]]["uri"];
						$result[$key]["uri"]=$parentUri."/".$uri;
					}
					else {
						$parentUri=$this->get_parent_uri($table,$uri);
						$result[$key]["uri"]=$parentUri."/".$uri;
					}
				}
			}
		}
		return $result;
	}
	
	function _make_tree_result($result) {
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
	function _groups_to_tree($grouped,$parent) {
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
	
	function _set_key_to($a,$key="") {
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
	
	function _get_result($table,$limit=0,$offset=0) {
		// init
		$result=array();
		// fetch data
		$query=$this->_get($table,$limit,$offset);
		log_("info","[DB+] Get data from query:");
		$res=$query->result_array();
		$result=$this->_set_key_to($res,$this->key);

		/**
		 * add (one to) many data if asked for
		 */
		if ($this->many) {
			$manyTables=$this->get_many_tables($table,$this->many);
			if (count($manyTables)>0) {
				// loop through all results to add the add_many data
				foreach($result as $id=>$row) {
					// loop throught all many tabels to add the many data
					foreach($manyTables as $rel=>$jTable) {
						$result[$id][$rel]=array();
						$rel=$jTable["rel"];
						$join=rtrim($jTable["join"],'_');
						if ($this->abstracts) {
							$this->select($join.".".pk());
							$this->select($this->get_abstract_field($join));
						}
						$this->from($rel);
						$this->where($jTable["id_this"],$id);
						$this->join($join,$join.".".pk()."=".$rel.".".$jTable["id_join"],"left");
						$this->order_by($rel.'.id');
						$query=$this->get();
						$resultArray=$query->result_array();
						// if (!empty($resultArray)) {
						// 	trace_($jTable);
						// 	trace_($resultArray);
						// }
						foreach($resultArray as $res) {
							$result[$id][$rel][$res[pk()]]=$res;
						}
						// trace_($this->last_query());
					}
				}
			}
		}

		/**
			* If where_uri, and more uri's found? Search parent uris for the right one
			* TODO: Misschien blijven er nog meer over? Wat dat?
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
		 * add options if asked for
		 */
		if ($this->options and !empty($result)) {
			$result=$this->_add_field_options($result,$table);
			// options of foreigntables
			$result=$this->_add_foreign_options($result,$this->get_foreign_tables($table));
			// options of many tables
			if (!isset($manyTables)) {
				$manyTables=$this->get_many_tables($table);
			}
			$result=$this->_add_many_options($result,$manyTables);
		}
		log_("info","[DB+] data ready");
		$this->reset();
		return $result;
	}
	
	function get_result_as_php($table,$limit=0,$offset=0) {
		return array2php($this->get_result($table,$limit,$offset));
	}
	
	function get_result_as_xml($table,$limit=0,$offset=0) {
		return array2xml($this->get_result($table,$limit,$offset));
	}
		
	function get_row($table,$offset=0) {
		if ($offset>1)
			$data=$this->get_result($table,1,$offset);
		else
			$data=$this->get_result($table,1);
		return current($data);
	}
	
	function get_field_where($table,$field,$where="",$what="") {
		if (empty($where) or empty($what))
			$sql="SELECT `$field` FROM `$table` LIMIT 1";
		else
			$sql="SELECT `$field` FROM `$table` WHERE `$where`='$what'";
		$query=$this->query($sql);
		$row=$query->row_array();
		if (isset($row[$field]))
			return $row[$field];
		else
			return FALSE;
	}
	function get_field($table,$field,$id="") {
		return $this->get_field_where($table,$field,$this->pk,$id);
	}
	function get_random_field($table,$field='id') {
		$sql="SELECT `$field` FROM `$table` ORDER BY RAND()";
		$query=$this->query($sql);
		$row=$query->row_array();
		return $row[$field];
	}
	
	function get_each($table="",$limit=0,$offset=0) {
		if (!isset($this->eachResult)) {
			$this->eachResult=$this->get_result($table,$limit,$offset);
		}
		$result=each($this->eachResult);
		if ($result===FALSE) $this->reset();
		return $result;
	}


	/**
	 * function concat($fields)
	 */
	function concat($fields) {
		$sql="CONCAT_WS(';',".implode(",",$fields)." )";
		return $sql;
	}

	function max_text_len($max=0) {
		$this->maxTextLen=$max;
	}


/**
	*	Some functions to include foreign data
	*/

	/**
	 * function get_foreign_tables([$table])
	 *
	 * Retrieves foreigntables from the foreign key from table.
	 *
	 * @param string $table Tablename for which to search, if empty, the current table is used.
	 * @return array Foreign table names
	 */
	function get_foreign_tables($table="") {
		$fields=$this->list_fields($table);
		foreach ($fields as $f) {
			if (is_foreign_key($f)) {
				$this->foreignTables[$f]["key"]=$f;
				$this->foreignTables[$f]["table"]=foreign_table_from_key($f);
			}
		}
		return $this->foreignTables;
	}

	function add_foreigns($foreigns=true) {
		$this->foreigns=$foreigns;
		if (is_array($foreigns)) {
			$this->foreigns=array();
			foreach ($foreigns as $table => $value) {
				$key='id_'.remove_prefix($table);
				$this->foreigns[$key]=array('key'=>$key,'table'=>$table,'fields'=>$value);
			}
		}
		$this->foreignTables=array();
	}

	function add_foreigns_as_abstracts($foreigns=true) {
		$this->add_foreigns();
		$this->add_abstracts();
	}

	function add_abstracts($abstracts=true) {
		$this->abstracts=$abstracts;
		if ($abstracts!=false) $this->add_foreigns();
	}
	
	function as_abstracts($as=true) {
		$this->asAbstracts=$as;
	}
	
	/**
	 * function get_abstract_field($table)
	 *
	 * Gets select field SQL for an abstract from a table
	 *
	 * @param string $table
	 * @return string Result SQL
	 */
		function get_abstract_field($table,$asPre="") {
			$cleanTable=rtrim($table,'_'); // make sure self relation is possible
			$CI=& get_instance();
			$abFields=array();
			/**
			 * First check if abstract fields are set for this table
			 */
			$f=$CI->cfg->get('CFG_table',$cleanTable,"str_abstract_fields");
			if (isset($f)) $abFields=explode_pre(",",$f,$cleanTable.".");

			/**
			 * If not set: Auto abstract fields according to prefixes
			 */
			if (empty($abFields)) {
				$allFields=$this->list_fields($cleanTable);
				$preTypes=$CI->config->item('ABSTRACT_field_pre_types');
				$nr=$CI->config->item('ABSTRACT_field_max');
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
				$types=$CI->config->item('ABSTRACT_field_types');
				$nr=$CI->config->item('ABSTRACT_field_max');
				$loop=true;
				while ($loop) {
					$fieldInfo=current($fieldData);
					$type=$fieldInfo->type;
					if (in_array($type,$types)) {
						array_push($abFields,$table.".".$fieldInfo->name);
						$nr--;
					}
					$loop=($nr>0 and each($fieldData)!==false);
				}
			}
			/**
			 * If still nothing set... just get the first fields
			 */
			if (empty($abFields)) {
				$allFields=$this->list_fields($cleanTable);
				$nr=$CI->config->item('ABSTRACT_field_max');
				for ($n=0; $n<$nr; $n++) {
					array_push($abFields,$table.".".each($allFields));
				}
			}

			/**
			 * ok, abstract fields are found, now create SQL for it.
			 */
			$out=$this->concat($abFields)." AS ".$asPre.$CI->config->item('ABSTRACT_field_name');
			return $out;
		}


/**
	*	Some functions to include and filter one to many tables
	*/

	function get_tables() {
		$tables = $this->list_tables();
		$tables = filter_by($tables,"tbl_");
		return $tables;
	}

	/**
	 * function get_many_tables([$table])
	 *
	 * Retrieves manytables from table name.
	 *
	 * @param string $table Tablename for which to search, if empty, the current table is used.
	 * @return array Join table array
	 */
		function get_many_tables($table,$tables='') {
			$out=array();
			$CI=& get_instance();
			if (empty($tables) or !is_array($tables)) {
				// list all tables with right name
				$like=$CI->config->item('REL_table_prefix')."_".remove_prefix($table).$CI->config->item('REL_table_split');
				$tables = $this->list_tables();
				$tables = filter_by($tables,$like);
			}
			foreach ($tables as $rel) {
				$relFields=$this->list_fields($rel);
				$out[$rel]["this"]=$table;
				$out[$rel]["rel"] =$rel;
				// $join=join_table_from_rel_table($rel);
				$out[$rel]["join"]=foreign_table_from_key($relFields[2]);
				$out[$rel]["id_this"]=$relFields[1];
				$out[$rel]["id_join"]=$relFields[2];
			}
			// trace_($out);
			return $out;
		}

	function add_many($many=true) {
		if (is_string($many)) $many=array($many);
		$this->many=$many;
	}
	
	function add_options($options=true) {
		$this->options=$options;
	}

/**
	*	Some functions to include options (for forms etc)
	*/

	/**
	 * function get_options($table)
	 *
	 * Gets options from given table
	 *
	 * @param string $table
	 * @return array Result array with all options id=>abstract;
	 */
		function get_options($table,$optionsWhere="") {
			$out=array();
			$cleanTable=rtrim($table,'_');
			$CI=&get_instance();
			$this->select($this->pk);
			$this->select($this->get_abstract_field($cleanTable));
			$this->_set_standard_order($cleanTable,$CI->config->item('ABSTRACT_field_name'));
			if (!empty($optionsWhere)) {
				$this->ar_where[]=$optionsWhere;
			}
			$query=$this->get($cleanTable);
			foreach($query->result_array() as $row) {
				$out[$row[$this->pk]]=$row[$CI->config->item('ABSTRACT_field_name')];
			}
			return $out;
		}

	/**
	 * function _add_foreign_options($out,$foreignTables)
	 *
	 * Adds option arrays to current result array
	 * @param array	$out						Current result array
	 * @param array	$foreignTables	Tables from the options to be added
	 * @return array	Resultarray with options
	 */
	 function _add_foreign_options($out,$foreignTables) {
			$options=array();
			if (isset($foreignTables)) {
				$CI=&get_instance();
				foreach ($foreignTables as $key => $forTable) {
					$cleanTable=rtrim($forTable['table'],'_');
					$optionsWhere=$CI->cfg->get('CFG_table',$cleanTable,'str_options_where');
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
	 * function _add_many_options($out,$joinTables)
	 *
	 * Adds option arrays to current result array
	 * @param array	$out						Current result array
	 * @param array	$joinTables			joinTables from which the options wille be added
	 * @return array	Resultarray with options
	 */
	 function _add_many_options($out,$manyTables) {
			$options=array();
			if (isset($manyTables)) {
				foreach ($manyTables as $rel => $jTable) {
					$options[$rel]=$this->get_options($jTable["join"]);
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
	 * function _add_field_options($out)
	 *
	 * Adds option arrays to current result array if options are set in cfg_field
	 * @param array	$out						Current result array
	 * @return array	Resultarray with options
	 */
	 function _add_field_options($out,$table) {
			$CI=& get_instance();
			// search options in cfg_field_info for every field, if found, give the options
			$fields=$this->list_fields($table);
			foreach($fields as $field) {
				$options=$CI->cfg->get('CFG_field',$table.".".$field,'str_options');
				if (isset($options) and !empty($options))	{
					$options=explode("|",$options);
					if ($CI->cfg->get('CFG_field',$table.".".$field,'b_multi_options'))
						$out["multi_options"][$field]=combine($options,$options);
					else
						$out["options"][$field]=combine($options,$options);
				}
			}
			return $out;
		}



	/**
	 * function defaults($table="")
	 *
	 * This functions gives a default data set back
	 *
	 * @param string $table Tablename, maybe set before
	 * @return array Result array ( [-1] => array( data ) )
	 */
	function defaults($table) {
		$CI=& get_instance();
		log_("info","[DB+] Get default data:");
		$out=array();
		$id=-1;
		$fields=$this->list_fields($table);
		foreach ($fields as $field) {
			$out[$id][$field]=$CI->cfg->field_data($table,$field,'default');
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
			}
		}

		/**
		 * add options if asked for
		 */
		if ($this->options) {
			$out=$this->_add_field_options($out,$table);
			// foreign table options
			$ft=$this->get_foreign_tables($table);
			$out=$this->_add_foreign_options($out,$ft);
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