<?
/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2008, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Flexy Data Class Model
 *
 * This Model returns data in array format.
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */


class Flexy_data extends Model {

	var $table;
	var	$select;
	var $where;
	var $like;
	var $join;	//	array ("table"=> , "on" => )
	var $order;
	var $limit;
	var $query; // CI object

	var $foreigns;
	var $joins;
	var $abstracts;
	var $withOptions;

	var $pk;


	function Flexy_data() {
		parent::Model();
		$this->init();
	}

	function init($table="") {
		log_("info","[FD] init table '$table'");
		$this->pk=$this->config->item('PRIMARY_key');
		$this->from($table);
		$this->select();
		$this->where();
		$this->join();
		$this->order_by();
		$this->foreign();
		$this->joins();
		$this->abstracts();
//		$this->set();
	}

	function from($table="") {
		if (empty($table))
			$this->table="";
		elseif (empty($this->table)) {
			$this->table=$table;
		} else {
			$this->table.=",".$table;
		}
	}

	function select($select="*") {
		$select=trim($select);
		if ($select!="*") {
			// put table name for fields, to create a safe query
			if (!empty($select) and $select!="*" and !empty($this->table) ) {
				$fields=explode(",",$select);
				foreach ($fields as $key=>$f) {
					$this->select=add_string($this->select,$this->table.".".$f,",");
				}
			}
		}
		else
			$this->select=$select;
	}

	function _sql($type,$ANDOR,$args) {
		/**
		 * Init sql types
		 */
		$ANDOR=strtoupper(trim($ANDOR));
		$type=strtoupper($type);
		switch($type) {
			case "LIKE":
				$SQL=$this->like;
				break;
			case "WHERE":
			default:
				$SQL=$this->where;
		}

		/**
		 * Check arguments
		 */
		$num_args=count($args);

		/**
		 * reset?
		 */
		if ($num_args==0 or el(0,$args,"")=="") {
			$SQL="";
		}
		/**
		 * arguments: ('field','value')
		 */
		elseif ($num_args>=2) {
			switch($type) {
				case "LIKE" :
					$SQL[]=array("field"=> $args[0], "match"=> $args[1], "andor"=>$ANDOR);
					break;
				case "WHERE":
				default:
					$SQL=add_string($SQL,$args[0]." = '".$args[1]."'", " ".$ANDOR." ");
			}

		}
		/**
		 * One argument: Array
		 */
		elseif (is_array($args[0])) {
			switch($type) {
				case "LIKE" :
					$SQL[]=array_merge($SQL,$args[0]);
					break;
				case "WHERE":
				default:
					foreach($args[0] as $name=>$value) {
						$this->_sql($type,$ANDOR,$name,$value);
					}
			}
		}
		/**
		 * One argument: String, a whole sql string
		 */
		else {
			$SQL=add_string($SQL,$args[0]," ".$ANDOR." ");
		}

		/**
		 * Save new SQL
		 */
		switch($type) {
			case "LIKE":
				$this->like=$SQL;
				break;
			case "WHERE":
			default:
				$this->where=$SQL;
		}
		return $SQL;
	}

	function where() {
		$args=func_get_args();
		return $this->_sql("WHERE","AND",$args);
//		$num_args=func_num_args();
//		$args=func_get_args();
//		/**
//		 * reset?
//		 */
//		if ($num_args==0 or el(0,$args,"")=="") {
//			$this->where="";
//		}
//		/**
//		 * arguments: ('field','value')
//		 */
//		elseif ($num_args==2) {
//			$this->where=add_string($this->where,$args[0]." = '".$args[1]."'", " AND ");
//		}
//		/**
//		 * One argument: Array
//		 */
//		elseif (is_array($args[0])) {
//			foreach($args[0] as $name=>$value) {
//				$this->where($name,$value);
//			}
//		}
//		/**
//		 * One argument: String, a whole where string
//		 */
//		else {
//			$this->where=add_string($this->where,$args[0]," AND ");
//		}
	}

	function or_where() {
		$args=func_get_args();
		return $this->_sql("WHERE","OR",$args);
	}

	function like() {
		$args=func_get_args();
		return $this->_sql("LIKE","AND",$args);
	}

	function or_like() {
		$args=func_get_args();
		return $this->_sql("LIKE","OR",$args);
	}


	function join($jTable="",$jOn="",$jSide="") {
		if (empty($jTable)) {
			$this->join=array();
		} elseif (empty($jOn)) {
			unset($this->join[$jTable]);
		} else {
			$this->join[$jTable]=array("table"=>$jTable,"on"=>$jOn,"side"=>$jSide);
		}
	}

	function order_by($order="") {
		$this->order=$order;
	}

	function limit($numRows="",$offset=0) {
		$this->limit["numrows"]=$numRows;
		$this->limit["offset"]=$offset;
	}


/**
 * Searches for a standard order field in config table.
 * If no explicit order set, decides according to prefixen what order field to take.
 * See flexyadmin_config [FIELDS_standard_order] what fields.
 */

	function _set_standard_order() {
		$order="";
		// find in table info
		$order=$this->cfg->get('CFG_table',$this->table,'str_order_by');
		// or first standard order field
		if (!empty($this->table)) {
			$stdFields=$this->config->item('ORDER_default_fields');
			$fields=$this->db->list_fields($this->table);
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
 * function foreign($foreign=false)
 *
 * Sets foreign tables to join in result.
 *
 * @param bool|array $foreign If set to true, searches for all foreign data. If it is an array only thise foreigns are included
 */

	function foreign($foreign=false) {
		$this->foreigns=$foreign;
	}

/**
 * function abstract($abstract=false)
 *
 * Sets if (or what) abstracts are used for foreign tables
 *
 * @param bool|array
 */

	function abstracts($abstracts=false) {
		$this->abstracts=$abstracts;
	}

/**
 * function foreign_with_abstracts()
 *
 * Sets to include foreigns but only with abstract data
 */
	function foreign_with_abstracts() {
		$this->foreign(true);
		$this->abstracts(true);
	}

/**
 * function with_options($options=false)
 *
 * Sets if options from foreign and join tables has to be included in result array
 *
 * @param bool $options If set to true, searches for all options from foreign and join data.
 */

	function with_options($options=false) {
		$this->withOptions=$options;
	}

/**
 * function joins($joins=false)
 *
 * Sets if join data has to be included in result array
 *
 * @param bool $joins If set to true, searches for join tables and their data
 */

	function joins($joins=false) {
		$this->joins=$joins;
	}


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
		$this->db->select($this->pk);
		$this->db->select($this->get_abstract_field($table));
		$query=$this->db->get($table);
		foreach($query->result_array() as $row) {
			$out[$row[$this->pk]]=$row[$this->config->item('ABSTRACT_field_name')];
		}
		return $out;
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
		$abFields=array();
		/**
		 * First check if abstract fields are set for this table
		 */
		$f=$this->cfg->get('CFG_table',$table,"str_abstract_fields");
		if (isset($f)) $abFields=explode_pre(",",$f,$table.".");

		/**
		 * If not set: Auto abstract fields according to prefixes
		 */
		if (empty($abFields)) {
			$allFields=$this->db->list_fields($table);
			$preTypes=$this->config->item('ABSTRACT_field_pre_types');
			$nr=$this->config->item('ABSTRACT_field_max');
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
			$fieldData=$this->db->field_data($table);
			$types=$this->config->item('ABSTRACT_field_types');
			$nr=$this->config->item('ABSTRACT_field_max');
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
			$allFields=$this->db->list_fields($table);
			$nr=$this->config->item('ABSTRACT_field_max');
			for ($n=0; $n<$nr; $n++) {
				array_push($abFields,$table.".".each($allFields));
			}
		}

		/**
		 * ok, abstract fields are found, now create SQL for it.
		 */
		$out=$this->concat($abFields)." AS ".$asPre.$this->config->item('ABSTRACT_field_name');
		return $out;
	}

	/**
	 * function concat($fields)
	 */
	function concat($fields) {
		//$platform=$this->db->platform();
		// mysql
		$sql="CONCAT_WS('; ',".implode(",",$fields)." )";
		return $sql;
	}


/**
 * Sets primary key.
 * If not set, the standard primary key is used.
 * If set, another associatibe key in output array will be used.
 */
	function primary_key($pk) {
		$this->pk=$pk;
	}


/**
 * function get_foreign_tables([$table])
 *
 * Retrieves foreigntables from the foreign key from table.
 *
 * @param string $table Tablename for which to search, if empty, the current table is used.
 * @return array Foreign table names
 */
	function get_foreign_tables($table="") {
		if (empty($table)) $table=$this->table;
		$out=array();
		// automatic search of all foreign tables
		$fields=$this->db->list_fields($table);
		foreach ($fields as $f) {
			if (is_foreign_key($f)) {
				$out[$f]["key"]=$f;
				$out[$f]["table"]=foreign_table_from_key($f);
			}
		}
		return $out;
	}

/**
 * function get_join_tables([$table])
 *
 * Retrieves jointables from table name.
 *
 * @param string $table Tablename for which to search, if empty, the current table is used.
 * @return array Join table array
 */
	function get_join_tables($table="") {
		if (empty($table)) $table=$this->table;
		$out=array();
		// list all tables with right name
		$like=$this->config->item('REL_table_prefix')."_".remove_prefix($table).$this->config->item('REL_table_split');
		$tables = $this->db->list_tables();
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
		if (count($options)>0) $out=array_merge($out,array("options" => $options));
		return $out;
	}

/**
 * function _add_join_options($out,$joinTables)
 *
 * Adds option arrays to current result array
 * @param array	$out						Current result array
 * @param array	$joinTables			joinTables from which the options wille be added
 * @return array	Resultarray with options
 */
 function _add_join_options($out,$joinTables) {
		$options=array();
		if (isset($joinTables)) {
			foreach ($joinTables as $rel => $jTable) {
				$options[$rel]=$this->get_options($jTable["join"]);
			}
		}
		if (count($options)>0) $out=array_merge($out,array("options" => $options));
		return $out;
	}


/**
 * function _add_field_options($out)
 *
 * Adds option arrays to current result array if options are set in cfg_field
 * @param array	$out						Current result array
 * @return array	Resultarray with options
 */
 function _add_field_options($out) {
		// search options in cfg_field_info for every field, if found, give the options
		$fields=$this->db->list_fields($this->table);
		foreach($fields as $field) {
			$options=$this->cfg->get('CFG_field',$this->table.".".$field,'str_options');
			if (isset($options) and !empty($options))	{
				$options=explode("|",$options);
				if ($this->cfg->get('CFG_field',$this->table.".".$field,'b_multi_options'))
					$out["multi_options"][$field]=combine($options,$options);
				else
					$out["options"][$field]=combine($options,$options);
			}
		}
		return $out;
	}





/**
 * function get($table="")
 *
 * This functions creates a CI query object from database according to settings.
 *
 * @param string $table Tablename, maybe set before
 * @return object Query
 */

	function get($table="",$numRows="",$offset=0) {
		$this->limit($numRows,$offset);
		log_("info","[FD] Get/create query:");
		if (empty($table))
			$table=$this->table;
		else
			$this->from($table);

		/**
		 * set foreign joins if asked for
		 */
		if (!empty($this->foreigns) and $this->foreigns!==false) {
			log_("info","[FD] add joins from foreign tables");
			$foreignTables=$this->get_foreign_tables();
			if (!empty($foreignTables)) {
				// first change select if it is '*' to all fields with tablename in front
				if ($this->select=="*") {
					$fields=$this->db->list_fields($this->table);
					foreach($fields as $key=>$f) {
						$fields[$key]=$f;
					}
				}
				// loop through fields, add them to selectarray and see if it is a foreignfield with known foreigntables
				$selectFields=array();
				foreach($fields as $field) {
					$selectFields[]=$this->table.".".$field;
					// is it a foreign key? Yes: add join and selectfield(s)
					/**
					 * TODO: check if this join allready exists: set by fd->join();
					 */
					if (isset($foreignTables[$field])) {
						$item=$foreignTables[$field];
						$this->db->join($item["table"], $item["table"].".$this->pk = ".$this->table.".".$item["key"], 'left');
						// add abstract or all foreign fields?
						if ($this->abstracts) {
							$abstractField=$this->get_abstract_field($item["table"],$field."__");
							$selectFields[]=$abstractField;
						}
						else {
							$forFields=$this->db->list_fields($item["table"]);
							foreach($forFields as $key=>$f) {
								$selectFields[]= $item["table"].".".$f." AS ".$item["key"]."__".$f;
							}
						}
					}
				}
				// select all fields including foreign fields
				$this->select=implode(",",$selectFields);
			}
		}

		/**
		 * set normal active record query items
		 */
		if (!empty($this->select)) 	$this->db->select($this->select);
		if (!empty($this->where)) 	$this->db->where($this->where);
		if (!empty($this->like))		{
			foreach($this->like as $like)
			{
				if ($like["andor"]=="AND")
					$this->db->like($like["field"],$like["match"]);
				else
					$this->db->or_like($like["field"],$like["match"]);
			}
		}

		if (!empty($this->join)) {
			foreach($this->join as $jTable=>$jOn) {
				if (!empty($jOn["side"]))
					$this->db->join($jTable,$jOn["on"],$jOn["side"]);
				else
					$this->db->join($jTable,$jOn["on"]);
			}
		}
		if (empty($this->order)) 		$this->_set_standard_order();
		if (!empty($this->order)) 	$this->db->order_by($this->order);

		/**
		 * get the query
		 */

		if (isset($this->limit) and !empty($this->limit["numrows"])) {
			$query=$this->db->get($table,$this->limit["numrows"],$this->limit["offset"]);
		}
		else
			$query=$this->db->get($table);
		$this->query=$query;

		return $query;
	}

/**
 * function get_results($table="")
 *
 * This functions retrieves data from database according to settings.
 * Data is put in array
 *
 * @param string $table Tablename, maybe set before
 * @return array Result array ( [id] => array( data ) )
 */

	function get_results($table="",$numRows="",$offset=0) {

		$out=array();
		if (empty($table)) {
			$query=$this->query;
		}
		else {
			$query=$this->get($table,$numRows,$offset);
		}
		log_("info","[FD] Get data from query:");

		/**
		 * Fetch data, and set key
		 */
		foreach($query->result_array() as $row) {
			$out[$row[$this->pk]]=$row;
		}

		/**
		 * add join data if asked for
		 */
		if ($this->joins) {
			$joinTables=$this->get_join_tables();
			if (count($joinTables)>0) {
				foreach($out as $id=>$row) {
					foreach($joinTables as $rel=>$jTable) {
						$out[$id][$rel]=array();
						$rel=$jTable["rel"];
						$join=$jTable["join"];
						if ($this->abstracts) {
							$this->db->select($join.".".pk());
							$this->db->select($this->get_abstract_field($join));
						}
						$this->db->from($rel);
						$this->db->where($jTable["id_this"],$id);
						$this->db->join($join,$join.".".pk()."=".$rel.".".$jTable["id_join"],"left");
						$query=$this->db->get();
						foreach($query->result_array() as $result) {
							$out[$id][$rel][$result[pk()]]=$result;
						}
					}
				}
			}
		}

		/**
		 * add options if asked for
		 */
		if ($this->withOptions and !empty($out)) {
			$out=$this->_add_field_options($out);

			// options of foreigntables
			if (!isset($foreignTables)) {
				$foreignTables=$this->get_foreign_tables();
			}
			$out=$this->_add_foreign_options($out,$foreignTables);

			// options of jointables
			if (!isset($joinTables)) {
				$joinTables=$this->get_join_tables();
			}
			$out=$this->_add_join_options($out,$joinTables);
		}

		log_("info","[FD] data ready");

		$this->init();
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
		if (empty($this->table)) $this->table=$table;
		log_("info","[FD] Get default data:");
		$out=array();
		$id=-1;
		$fields=$this->db->list_fields($table);
		foreach ($fields as $field) {
			$out[$id][$field]=$this->cfg->field_data($this->table,$field,'default');
			if ($out[$id][$field]==NULL) $out[$id][$field]="";
		}

		/**
		 * Add join table defaults if asked for
		 */

		if ($this->joins) {
			$jt=$this->get_join_tables($table);
			if (count($jt)>0) {
				foreach($jt as $rel=>$jTable) {
					$out[$id][$rel]=array();
				}
			}
		}

		/**
		 * add options if asked for
		 */
		if ($this->withOptions) {
			$out=$this->_add_field_options($out);
			// foreign table options
			$ft=$this->get_foreign_tables($table);
			$out=$this->_add_foreign_options($out,$ft);
			// join table options
			if (!isset($jt)) $jt=$this->get_join_tables($table);
			$out=$this->_add_join_options($out,$jt);

			// add options set in cfg_field_info


		}
		log_("info","[FD] default data ready");
		return $out;
	}

}

//
///**
// * function set
// */
//	function set($name="",$value="") {
//		if ($name=="") $this->set=NULL;
//		$this->set[$name]=$value;
//
//	}
//
///**
// * function update($table,$data,$where)
// *
// * Updates the table with given data
// *
// * @param string $table Table
// * @param array $data Dataset to be updated
// * @param string $where		If set this where is used, otherwise the where statement is checked
// */
//
//	function update($table,$data=NULL,$where="") {
//		if (!empty($where)) $this->where($where);
//		/**
//		 * set normal active record query items
//		 */
//		$this->db->where($this->where);
//		/**
//		 * set data set
//		 */
//		if (empty($data)) {
//			$data=$this->set;
//		}
//		/**
//		 * update
//		 */
//		$this->db->update($table,$data);
//	}
//
//}

/*


class Flexy_data extends Model {

	var $aTables	= array(); // tables, info and maybe data
	var $isAdmin	= false;

	function Flexy_data() {
		parent::Model();
		load_library_class("FlexyTable");
	}

	function init($isAdmin=false) {
		$this->isAdmin=$isAdmin;
		$tables=$this->db->list_tables();
		foreach ($tables as $table) {
			$this->_set_table($table);
		}
		foreach ($tables as $table) {
			$this->_analyse_table($table);
		}
		// set some configurations
		$this->config->set_item('language', $this->get_config('CFG_config_language'));
	}

	function _set_table($table) {
		$this->aTables[$table]= new FlexyTable($table);
		$this->table($table)->init($this->isAdmin);
	}

	function _analyse_table($t) {
		$fields=$this->table($t)->get_fields();
		// set ui names etc
		$this->table($t)->set_ui_name($this->get_table_config($t,"CFG_table_ui_name"));
		$this->table($t)->set_order_by($this->get_table_config($t,"CFG_table_order_by"));
		$this->table($t)->set_abstract_fields($this->get_table_config($t,"CFG_table_abstract_fields"));
		// analyse relations
		foreach ($fields as $field) {
			if (!$field->is_primary_key()) {
				if ($field->is_foreign_key()) {
					$this->table($t)->add_has_one(foreign_table($field->get_name()),$field->get_name());
					if ($this->table($t)->is_join()) {
						$rootTable=root_table_from_join_table($t);
						$this->table($rootTable)->add_has_many($t);
					}
				}
			}
		}
		// put in table_cfg if not set
		if ($this->isAdmin and !$this->table($t)->is_config() ) {
			$cfg=$this->get_table_config($t);
			if ($cfg=="") {
				$this->db->insert(
								$this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_table'),
								array($this->config->item('CFG_table_name')=>$t) );
			}
		}
	}

	function get_flexy_tables()										{	return $this->aTables; }
	function get_flexy_table($table)							{ return $this->table($table); }
	function get_flexy_table_names($noConfig=false)	{
		$tables=array_keys($this->aTables);
		if ($noConfig) {
			$cfgs=filter_by($tables,$this->config->item('CFG_table_prefix'));
			$tables=array_diff($tables,$cfgs);
		}
		return $tables;
	}

	function get_config($name) {
		$cfgTable=$this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_config');
		$cfg=$this->table($cfgTable)->get_all($this->config->item($name));
		$cfg=current($cfg);
		if (isset($cfg[$this->config->item($name)]))
			return $cfg[$this->config->item($name)];
		return "";
	}
	function get_table_config($table,$name="") {
		$cfgTable=$this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_table');
		$cfg=$this->table($cfgTable)->get_all();
		if (isset($cfg)) {
			$row=find_row_by_value($cfg,$table);
			if (isset($row[$this->config->item($name)])) {
				return $row[$this->config->item($name)];
			}
			else {
				return $row;
			}
		}
		return "";
	}
	function get_field_config($field,$name="") {
		$cfgField=$this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_field');
		$cfg=$this->table($cfgField)->get_all();
		if (isset($cfg)) {
			$row=find_row_by_value($cfg,$field);
			if (isset($row[$this->config->item($name)])) {
				return $row[$this->config->item($name)];
			}
		}
		return "";
	}
	function get_other_config($cfgTable,$field,$row=false) {
		$cfgField=$this->config->item('CFG_table_prefix')."_".$this->config->item($cfgTable);
		$cfg=$this->table($cfgField)->get_all($this->config->item($field));
		$out="";
		if (isset($cfg)) {
			if ($row)
				$out=current($cfg[$row]);
			else
				$out=current(current($cfg));
		}
		return $out;
	}


	function table($table) {
		return $this->aTables[$table];
	}

	function get($query) {
		// Trace($this->db->last_query());
		$data=$query->result_array();
		// zorg dat id juist is
		$result=array();
		foreach ($data as $r) {$result[$r[$this->config->item('PRIMARY_key')]]=$r;}
		return $result;
	}

	function get_sql($sql) {
		$query=$this->db->query($sql);
		return $this->get($query);
	}

	function _set_table_standard_query($table) {
		$this->db->from($table);
		$this->db->order_by($this->table($table)->get_order_by());
	}

	function get_table($table) {
		$this->_set_table_standard_query($table);
		$query=$this->db->get();
		return $this->get($query);
	}

	function get_table_and_abstracts($table,$select="") {
		return $this->get_table_data($table,$select,true);
	}

	function get_table_and_has_ones($table,$select="")	{
		return $this->get_table_data($table,$select,false);
	}

	function get_table_full($table,$select="")	{
		return $this->get_table_data($table,$select,false,true);
	}

	function get_table_data($table,$select="",$onlyAbstracts=false,$withJoins=false)	{
		$hasMany=false;
		$relHasMany=array();
		$primary_key=$this->config->item('PRIMARY_key');
		$abstract_field=$this->config->item('ABSTRACT_field_name')."_";
		// start standard query
		$this->_set_table_standard_query($table);
		$t=$this->table($table);
		// select fields, and add/replace foreign data/abstract
		if ($select=="")
			$select=explode(",",implode_pre(",",$this->table($table)->get_field_names(),$table."."));
		else {
			// make sure the primary key is there...
			if (strpos($primary_key,$select)===false) $select=$primary_key.",".$select;
			$select=explode(",",$select);
		}
		foreach($t->get_relations() as $r ) {
			// has one relations
			if ($r->has_one()) {
				$ft=rtrim($r->get_foreign(),"_");
				$fk=$r->get_foreign_key();
				// only abstracts?
				if ($onlyAbstracts) {
					$abField=$abstract_field.$fk;
					$abSelect=concat_ws_pre_as($this->config->item('ABSTRACT_field_split'), $this->table($ft)->get_abstract_fields(), $ft.".", $abField);
					// ok, replace select (foreign_key) with abstract field
					$key=array_search($table.".".$fk,$select);
					$select[$key]=$abSelect;
				}
				// no abstract but data
				else {
					$forFields=$this->table($ft)->get_field_names();
					foreach ($forFields as $key=>$field) {
						$forFields[$key]="$ft.$field AS ".$ft."__$field";
					}
					$select=array_merge($select,$forFields);
				}
				// and add a Join to query
				$joinOn=$ft.".".$primary_key." = ".$table.".".$fk;
				$this->db->join($ft, $joinOn,"LEFT");
			}
			if ($r->has_many())	{
				$hasMany=true;
				array_push($relHasMany,$r);
			}
		}
		// add selects to query (with abstracts or has_ones)
		$this->db->select($select);
		$query=$this->db->get();
		$result=$this->get($query);
		// join tables?
		if ($withJoins and $hasMany) {
			// loop through results and add new rows
			foreach ($result as $key => $row) {
				$id=$row[$this->config->item('PRIMARY_key')];
				$joinRows=array();
				foreach($relHasMany as $r) {
					$data=$this->get_join_data($r,$id);
					$joinRows[$r->get_join()]=$data;
				}
				$result[$key]=array_merge($row,$joinRows);
			}
		}
		return $result;
	}

	function get_join_data($rel,$id) {
		if ($rel->has_many()) {
			$joinTable=$rel->get_join();
			$table=$rel->get_table();
			$foreignTable=$rel->get_foreign();
			$foreignKey=foreign_key_from_table($foreignTable);
			if ($foreignTable==$table) // is self relating?
				$foreignKey.="_";
			$this->db->join($foreignTable,$joinTable.".".$foreignKey." = ".$foreignTable.".".$this->config->item('PRIMARY_key'));
			$select=$this->table($foreignTable)->get_field_names();
			$select=implode_pre(",",$select,$foreignTable.".");
			$this->db->select($select);
			$query=$this->db->get_where($joinTable,array($joinTable.".".foreign_key_from_table($table) => $id));
			$data=$query->result_array();
			// zorg dat id juist is
			$result=array();
			foreach ($data as $r) {$result[$r[$this->config->item('PRIMARY_key')]]=$r;}
			return $result;
		}
		return NULL;
	}

	function update($table,$id,$data,$joins=NULL) {
		$pk=$this->config->item('PRIMARY_key');
		unset($data[$pk]);
		foreach ($data as $name=>$value) {
			$this->db->set($name,$value);
		}
		// insert or update?
		if ($id<0) {
			$this->db->insert($table);
		}
		else {
			$this->db->where($pk, $id);
			$this->db->update($table);
		}
		// Joins:
		if (isset($joins)) {
			foreach($joins as $join=>$values) {
				// first delete all old joins
				$root_id=foreign_key_from_table($table);
				$this->delete_where($join,array($root_id=>$id));
				// insert new ones
				$foreignTable=foreign_table_from_join_table($join);
				$foreign_id=foreign_key_from_table($foreignTable);
				foreach ($values as $value) {
					$this->db->set($root_id,$id);
					$this->db->set($foreign_id,$value);
					$this->db->insert($join);
				}
			}
		}
		return true;
	}

	function delete_where($table,$where) {
		if (!is_array($where)) $where=array($where);
		$this->db->where($where);
		$this->db->delete($table);
		return true;
	}

	function delete($table,$id) {
		$this->delete_where($table, array($this->config->item('PRIMARY_key') => $id) );
		// and join relations
		$rels=$this->FlexyData->table($table)->get_relations();
		foreach ($rels as $r) {
			if ($r->has_many()) {
				$join=$r->get_join();
				$root_id=foreign_key_from_table($table);
				$this->delete_where($join,array($root_id => $id));
			}
		}
		return true;
	}

}

*/

?>
