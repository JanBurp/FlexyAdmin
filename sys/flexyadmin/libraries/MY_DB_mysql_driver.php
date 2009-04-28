<?

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
	var $maxTextLen;
	var $eachResult;
	var $foreignTables;
	var $foreigns;
	var $abstracts;
	var $asAbstracts;
	var $many;
	var $options;
	var $whereUri;
	var $orderAsTree;

	function MY_DB_mysql_driver($params) {
		parent::CI_DB_mysql_driver($params);
		$this->reset();
	}

	function reset() {
		$this->primary_key();
		$eachResult=array();
		$savedQuery=array();
		$this->add_foreigns(FALSE);
		$this->add_abstracts(FALSE);
		$this->add_many(FALSE);
		$this->add_options(FALSE);
		$this->max_text_len();
		$this->where_uri();
		// $this->order_as_tree(FALSE);
	}

	/**
	 * Sets primary key.
	 * If not set, the standard primary key is used.
	 * If set, another associatibe key in output array will be used.
	 */
	function primary_key($pk="id") {
		$this->pk=$pk;
	}

	/**
	 * Searches for a standard order field in config table.
	 * If no explicit order set, decides according to prefixen what order field to take.
	 * See flexyadmin_config [FIELDS_standard_order] what fields.
	 */
	function _set_standard_order($table) {
		$order="";
		$CI=& get_instance();
		// find in table info
		$order=$CI->cfg->get('CFG_table',$table,'str_order_by');
		// or first standard order field
		if (empty($order) and !empty($table)) {
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
			// check if it is a tree
			if ($order=="order" and in_array("self_parent",$fields)) {
				$this->order_as_tree();
			}
		}
		if ($this->orderAsTree) {
			$this->order_by("self_parent");
			$this->order_by("order");
		}
		else
			$this->order_by($order);
		return $order;
	}

	function where_uri($uri="") {
		$this->whereUri=$uri;
		if (!empty($uri)) {
			$lastPart=get_postfix($uri,"/");
			$this->like("uri",$lastPart,"before");
		}
	}

	function order_as_tree($orderAsTree=TRUE) {
		$this->orderAsTree=$orderAsTree;
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
			* As abstracts if asked for
			*/
		if ($this->asAbstracts) {
			$this->ar_select=array();
			$this->select(array($this->pk,$this->get_abstract_field($table)));
		}

		/**
		 * add foreign (joins) if asked for
		 */
		if (!empty($this->foreigns) and $this->foreigns!==false) {
			log_("info","[DB+] add joins from foreign tables");
			$foreignTables=$this->get_foreign_tables($table);
			if (!empty($foreignTables)) {
				// change select to all fields (if no select was set) // with tablename in front
				if (empty($this->ar_select)) {
					$fields=$this->list_fields($table);
					foreach($fields as $key=>$f) {
						$fields[$key]=$f;
					}
				}
				else {
					$fields=$this->ar_select;
					$this->ar_select=array();
					// trace_($this->ar_select);
					// trace_($fields);
				}
				// loop through fields, add them to select array and see if it is a foreignfield with known foreigntables
				$selectFields=array();
				foreach($fields as $field) {
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
						$this->join($item["table"], $item["table"].".$this->pk = ".$table.".".$item["key"], 'left');
						// add abstract or all foreign fields?
						if ($this->abstracts) {
							$abstractField=$this->get_abstract_field($item["table"],$field."__");
							$selectFields[]=$abstractField;
						}
						else {
							$forFields=$this->list_fields($item["table"]);
							foreach($forFields as $key=>$f) {
								$selectFields[]= $item["table"].".".$f." AS ".$item["table"]."__".$f;
							}
						}
					}
				}
				// select all fields including foreign fields
				$this->select(implode(",",$selectFields));
				// trace_($this->ar_select);
			}
		}
		/**
		 * set standard order if not set
		 */
		if (empty($this->ar_orderby)) $this->_set_standard_order($table);

		/**
		 * if many, find if a where part is referring to a many table
		 */
		if ($this->many) {
			$manyTables=$this->get_many_tables($table);
			foreach($manyTables as $mTable) {
				$jTable=$mTable["join"];
				// trace_($this->ar_where);
				$foundKeysArray=array_ereg_search($jTable, $this->ar_where);
				foreach($foundKeysArray as $key) {
					$mWhere=$this->ar_where[$key];
					$AndOr=trim(substr($mWhere,0,3));
					if (!in_array($AndOr,array("AND","OR"))) $mWhere=" AND ".$mWhere;
					$sql="SELECT ".$mTable["rel"].".".$mTable["id_this"]." AS id  
								FROM ".$mTable["rel"].",".$mTable["join"]." 
								WHERE ".$mTable["rel"].".".$mTable["id_join"]."=".$mTable["join"].".id ".$mWhere;
					// trace_($sql);
					$query=$this->query($sql);
					$manyResults=$query->result_array();
					// trace_($manyResults);
					// remove current where and add new 'WHERE IN' to active record which selects the id where the many field is right
					unset($this->ar_where[$key]);
					// add WHERE IN statement
					if (!empty($manyResults)) {
						foreach($manyResults as $r) {
							$whereIn[]=$r["id"];
						}

					}
					if (!empty($whereIn))
						$this->where_in($mTable["this"].".".$this->pk,$whereIn);
					else
						$this->where($table.".".$this->pk,"-1"); // make sure no result is returned...
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
		
		/**
		 * get the query
		 */
		if ($limit>1)
			$query=$this->get($table,$limit,$offset);
		else
			$query=$this->get($table);
		return $query;
	}
			
	
	function get_results($table,$limit=0,$offset=0) {
		return $this->get_result($table,$limit,$offset);
	}
	function get_result($table,$limit=0,$offset=0) {
		$result=$this->_get_result($table,$limit,$offset);
		if ($this->orderAsTree and !empty($result)) {
			$options=el("options",$result);
			$multiOptions=el("multi_options",$result);
			unset($result["options"]);
			unset($result["multi_options"]);
			$result=$this->_make_tree_result($result);
			if ($options) $result["options"]=$options;
			if ($multiOptions) $result["multi_options"]=$multiOptions;
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
	
	function _set_key_to($a,$key) {
		$out=array();
		$first=current($a);
		if (isset($first[$key])) {
			foreach($a as $row) {
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
		$result=$this->_set_key_to($res,$this->pk);

		/**
		 * add (one to) many data if asked for
		 */
		if ($this->many) {
			$manyTables=$this->get_many_tables($table);
			if (count($manyTables)>0) {
				foreach($result as $id=>$row) {
					foreach($manyTables as $rel=>$jTable) {
						$result[$id][$rel]=array();
						$rel=$jTable["rel"];
						$join=$jTable["join"];
						if ($this->abstracts) {
							$this->select($join.".".pk());
							$this->select($this->get_abstract_field($join));
						}
						$this->from($rel);
						$this->where($jTable["id_this"],$id);
						$this->join($join,$join.".".pk()."=".$rel.".".$jTable["id_join"],"left");
						$query=$this->get();
						foreach($query->result_array() as $res) {
							$result[$id][$rel][$res[pk()]]=$res;
						}
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
		
	function get_row($table,$limit=0,$offset=0) {
		if ($limit>1)
			$data=$this->get_result($table,$limit,$offset);
		else
			$data=$this->get_result($table);
		return current($data);
	}
	
	function get_field_where($table,$field,$where,$what="") {
		$sql="SELECT `$field` FROM `$table` WHERE `$where`='$what'";
		$query=$this->query($sql);
		$row=$query->row_array();
		return $row[$field];
	}
	function get_field($table,$field,$id) {
		return $this->get_field_where($table,$field,$this->pk,$id);
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
			$CI=& get_instance();
			$abFields=array();
			/**
			 * First check if abstract fields are set for this table
			 */
			$f=$CI->cfg->get('CFG_table',$table,"str_abstract_fields");
			if (isset($f)) $abFields=explode_pre(",",$f,$table.".");

			/**
			 * If not set: Auto abstract fields according to prefixes
			 */
			if (empty($abFields)) {
				$allFields=$this->list_fields($table);
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
				$fieldData=$this->field_data($table);
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
				$allFields=$this->list_fields($table);
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

	/**
	 * function get_many_tables([$table])
	 *
	 * Retrieves manytables from table name.
	 *
	 * @param string $table Tablename for which to search, if empty, the current table is used.
	 * @return array Join table array
	 */
		function get_many_tables($table) {
			$out=array();
			$CI=& get_instance();
			// list all tables with right name
			$like=$CI->config->item('REL_table_prefix')."_".remove_prefix($table).$CI->config->item('REL_table_split');
			$tables = $this->list_tables();
			$tables = filter_by($tables,$like);
			foreach ($tables as $rel) {
				$out[$rel]["this"]=$table;
				$out[$rel]["rel"] =$rel;
				$join=join_table_from_rel_table($rel);
				$out[$rel]["join"]=$join;
				$out[$rel]["id_this"]="id_".remove_prefix($table);
				$out[$rel]["id_join"]="id_".remove_prefix($join);
			}
			return $out;
		}

	function add_many($many=true) {
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
		function get_options($table) {
			$out=array();
			$CI=&get_instance();
			$this->select($this->pk);
			$this->select($this->get_abstract_field($table));
			$this->order_by($CI->config->item('ABSTRACT_field_name'));
			$query=$this->get($table);
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
				foreach ($foreignTables as $key => $forTable) {
					$options[$key]=$this->get_options($forTable["table"]);
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