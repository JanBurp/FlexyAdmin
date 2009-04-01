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
	var $many;
	var $options;

	function MY_DB_mysql_driver($params) {
		parent::CI_DB_mysql_driver($params);
		$this->reset();
	}

	function reset() {
		$this->primary_key();
		$eachResult=array();
		$this->add_foreigns(FALSE);
		$this->add_abstracts(FALSE);
		$this->add_many(FALSE);
		$this->add_options(FALSE);
		$this->max_text_len();
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
		if (!empty($table)) {
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
		$this->order_by($order);
		return $order;
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
				// loop through fields, add them to select array and see if it is a foreignfield with known foreigntables
				$selectFields=array();
				foreach($fields as $field) {
					$selectFields[]=$table.".".$field;
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
			}
		}
		/**
		 * set standard order if not set
		 */
		if (empty($this->ar_order))
			$this->_set_standard_order($table);

		/**
		 * if many, find if a where part is referring to a many table
		 */
		if ($this->many) {
			$manyTables=$this->get_many_tables($table);
			foreach($manyTables as $mTable) {
				$jTable=$mTable["join"];
				$foundKeysArray=array_ereg_search($jTable, $this->ar_where);
				foreach($foundKeysArray as $key) {
					$mWhere=$this->ar_where[$key];
					$sql="SELECT ".$mTable["rel"].".".$mTable["id_this"]." AS id  
								FROM ".$mTable["rel"].",".$mTable["join"]." 
								WHERE ".$mTable["rel"].".".$mTable["id_join"]."=".$mTable["join"].".id ".$mWhere;
					$query=$this->query($sql);
					$manyResults=$query->result_array();
					// remove current where and add new where's to active record which selects the id where the many field is right
					unset($this->ar_where[$key]);
					foreach($manyResults as $r) {
						$this->where($mTable["this"].".".$this->pk,$r["id"]);
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
			// trace_($this->ar_select);			
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
		// init
		$result=array();
		// fetch data
		$query=$this->_get($table,$limit,$offset);
		log_("info","[DB+] Get data from query:");
		// set key
		foreach($query->result_array() as $row) {
			$result[$row[$this->pk]]=$row;
		}

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
		return $result;
	}
		
	function get_row($table,$limit=0,$offset=0) {
		if ($limit>1)
			$data=$this->get_result($table,$limit,$offset);
		else
			$data=$this->get_result($table);
		return current($data);
	}
	
	function get_each($table="",$limit=0,$offset=0) {
		if (!isset($this->eachResult)) {
			$this->eachResult=$this->get_result($table,$limit,$offset);
		}
		return each($this->eachResult);
	}


	/**
	 * function concat($fields)
	 */
	function concat($fields) {
		$sql="CONCAT_WS('; ',".implode(",",$fields)." )";
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

	function where_many() {
		
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
		return $out;
	}


}
?>