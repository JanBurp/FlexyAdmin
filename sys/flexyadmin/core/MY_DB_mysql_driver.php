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
	var $extraFullField;
	var $orderAsTree;
	var $order;
	var $orderByForeign;
	var $orderByMany;
	var $ar_dont_select;
	var $ar_last_query=FALSE;
	var $ar_last_count=FALSE;
	var $selectFirst;
	var	$selectFirsts;

	function __construct($params) {
		parent::__construct($params);
		$this->reset();
	}

	function reset() {
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
	}

	// Repairs active record
	function _repair_ar() {
		// splits ar_where by OR/AND
		$where=implode($this->ar_where);
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
			// trace_($this->ar_where);
		}
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
	 * Sets ar_order_by by the order of the given foreign keys, by looking into the order of the foreign tables
	 */
	function _set_order_by_foreign($order_by_foreign=FALSE,$table) {
		if ($order_by_foreign) {
			if (!is_array($order_by_foreign)) $order_by_foreign=array($order_by_foreign);
			$CI=& get_instance();
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
	 * Sets ar_order_by by the order of the given relation tables keys, by looking at the first found data
	 */
	function _set_order_by_many($order_by_many=FALSE,$table) {
		if ($order_by_many) {
			if (!is_array($order_by_many)) $order_by_many=array($order_by_many);
			$CI=& get_instance();
			foreach ($order_by_many as $rel_table) {
				// trace_($rel_table);
				$desc=explode(' ',$rel_table);
				$rel_table=$desc[0];
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
	function _set_standard_order($table,$fallbackOrder="",$tree_possible=TRUE,$set=TRUE) {
		$order="";
		if ($this->orderAsTree and $tree_possible) {
			if ($this->field_exists('self_parent',$table)) $this->order_by("self_parent");
			if ($this->field_exists('order',$table)) $this->order_by("order");
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
				$order=trim($order);
				// check if it is not id, add id to it to prefent dubious sort results
				if ($order!=PRIMARY_KEY) $order.=','.PRIMARY_KEY;
				// trace_($order);
				
				if ($set) $this->order_by($order);
			}
		}
		if ($set) $this->order=$this->ar_orderby;
		return $order;
	}

	function order_by($args) {
		parent::order_by($args);
		$this->order=$args;
	}

	function order_by_foreign($args=FALSE) {
		$this->order_by_foreign=$args;
	}

	function order_by_many($args=FALSE) {
		$this->order_by_many=$args;
	}

	function order_as_tree($orderAsTree=TRUE) {
		$this->orderAsTree=$orderAsTree;
	}

	function get_last_order() {
		$order=$this->order;
		if (is_array($order)) $order=current($order);
		return str_replace('`','',$order);
	}

	function where_uri($uri="") {
		$this->whereUri=$uri;
	}
	
	/**
	*	array("search"=>"", "field"=>"", "or"=>"AND/OR", "in"=>array(val1,val2,val3), "table"=>'' )
	*/
	function search($search,$set_sql=TRUE) {
		// if $search is one dimensial array, make more dimensonal
		if (isset($search['search'])) $search=array($search);
		// trace_($search);
		$default=array('search'=>'','field'=>'id','or'=>'AND','table'=>'');
		$sql='';
		foreach ($search as $k => $s) {
			if (($s['search']!='') and ($s['field']!='')) {
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
						$sub_sql="SELECT `id` FROM `".$foreign_search['table']."` WHERE ".$this->search($foreign_search,FALSE);
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
						$sub_sql="SELECT `id` FROM `".$foreign_search['table']."` WHERE ".$this->search($foreign_search,FALSE);
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
					$sql.=$s['table'].$s['field'].' LIKE \'%'.$s['search'].'%\' ';
				}
			}
		}
		$sql=substr($sql,3); // remove first AND
		// trace_($sql);
		if ($set_sql) $this->where($sql,NULL,FALSE);
		return $sql;
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

	function unselect($dont_select=""){
		$this->dont_select($dont_select);
	}
	function dont_select($dont_select="") {
		if (!empty($dont_select)) {
			$this->ar_dont_select[]=$dont_select;
		}
	}

	function uri_as_full_uri($fullUri=TRUE,$extraFullField='') {
		$this->uriAsFullUri=$fullUri;
		$this->extraFullField=$extraFullField;
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
							$abstractField=$this->get_abstract_fields_sql($joinAsTable,$field."__");
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
			$manyTables=$this->get_many_tables($table,$this->many);
			$manyWhere=FALSE;
			$manyLike=FALSE;
			// $this->_repair_ar();
			// trace_($this->ar_where);
			// trace_($manyTables);
			foreach($manyTables as $mTable) {
				$jTable=$mTable["join"];
				$relTable=$mTable['rel'];
				// trace_($mTable);
				// WHERE
				$foundKeysArray=array_ereg_search($mTable['rel'], $this->ar_where);
				// trace_($this->ar_where);
				// trace_($foundKeysArray);
				foreach($foundKeysArray as $key) {
					$manyWhere=TRUE;
					$mWhere=$this->ar_where[$key];
					// trace_($mWhere);
					$AndOr=trim(substr($mWhere,0,3));
					if (!in_array($AndOr,array("AND","OR")))
						$AndOr='';
					else
						$AndOr.=' ';
					$mWhere=' AND '.str_replace(array('AND','OR'),'',$mWhere);
					// trace_($AndOr);
					// trace_($mWhere);
					$sql="SELECT ".$mTable["rel"].".".$mTable["id_this"]." AS id  
								FROM ".$mTable["rel"].",".trim($mTable["join"],'_')." 
								WHERE ".$mTable["rel"].".".$mTable["id_join"]."=".trim($mTable["join"],'_').".id ".$mWhere;
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
							// $this->ar_where[$key]=' ';
					}
				}
				$this->_repair_ar();
				
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
				$this->ar_select[$key]="SUBSTRING(".$field.",1,".$this->maxTextLen.") AS `".remove_prefix($field,".")."`";
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
		
		/**
		 * get the query
		 */
		if ($limit>=1)
			$query=$this->get($table,$limit,$offset);
		else
			$query=$this->get($table);
		$this->ar_last_query=$this->last_query();
		// trace_('#show#'.$this->ar_last_query);
		return $query;
	}

	
	function get_parent($table,$uri="",$field='') {
		if (!empty($uri))	$id=$this->get_field_where($table,"id","uri",$uri);
		$this->order_as_tree();
		$this->uri_as_full_uri(TRUE,$field);
		$this->select("id,order,uri,self_parent");
		if (!empty($field)) $this->select($field);
		$result=$this->get_result($table);
		if (!empty($uri))
			return $result[$id];
		else
			return $result;
	}

	function get_unique_id_from_fulluri($table,$uriParts,$parent=0) {
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

	function _check_fulluri($table,$uriParts,$self_parent) {
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
	
	
	
	function get_results($table,$limit=0,$offset=0) {
		return $this->get_result($table,$limit,$offset);
	}
	function get_result($table,$limit=0,$offset=0) {
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
			if ($options) $result["options"]=$options;
			if ($multiOptions) $result["multi_options"]=$multiOptions;
		}
		
		// Full uris if asked for
		if ($fullUri) {
			foreach ($result as $key => $row) {
				if ($row["self_parent"]!=0) {
					$uri=$row["uri"];
					if (!empty($extraFullField)) $extra=$row[$extraFullField];
					if ( $this->_test_if_full_path($result,$row) ) {
						$parentUri=$result[$row["self_parent"]]["uri"];
						$result[$key]["uri"]=$parentUri."/".$uri;
						if (!empty($extraFullField)) {
							$parentExtra=$result[$row["self_parent"]][$extraFullField];
							$result[$key][$extraFullField]=$parentExtra." / ".$extra;
						}
					}
					else {
						$parent=$this->get_parent($table,$uri,$extraFullField);
						$result[$key]["uri"]=$parent['uri'];
						if (!empty($extraFullField)) {
							$result[$key][$extraFullField]=$parent[$extraFullField];
						}
					}
				}
			}
		}
		return $result;
	}
	
	function _test_if_full_path($result,$row) {
		$test=FALSE;
		$self_parent=$row['self_parent'];
		while (!$test and $self_parent>0 and isset($result[$self_parent])) {
			$self_parent=$result[$self_parent]['self_parent'];
			$test=($self_parent==0);
		}
		return $test;
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
				$CI=& get_instance();
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
					foreach ($manyResult as $rel => $relData) {
						$manyOrder='last';
						if (isset($CI->cfg)) $manyOrder=$CI->cfg->get('CFG_table',$rel,'str_form_many_order');
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
				}
			}
			$this->order=$last_order;
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
		 * add options if asked for
		 */
		if ($this->options and !empty($result)) {
			$result=$this->_add_field_options($result,$table);
			// options of foreigntables
			$result=$this->_add_foreign_options($result,$this->get_foreign_tables($table),$table);
			// options of many tables
			if (!isset($manyTables)) {
				$manyTables=$this->get_many_tables($table);
			}
			$result=$this->_add_many_options($result,$manyTables);
		}
		log_("info","[DB+] data ready");
		
		$this->ar_last_count=count($result);
		
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
		$query->free_result();
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
		$query->free_result();
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

	function last_num_rows() {
		return $this->ar_last_count;
	}

	function last_num_rows_no_limit() {
		if ( ! $this->ar_last_query) {
			return FALSE;
		}
		$sql=$this->ar_last_query;

		$match=array();
		$table='';
		if ( preg_match('/FROM\s(.*?)\s/si',$sql,$match) ) {
			$table=trim($match[1],'()`"');
			$tablePlus="`$table`.";
		}
		
		$sql=preg_replace('/SELECT(.*)?FROM/si','SELECT '.$tablePlus.'`id` FROM',$sql);
		$sql=preg_replace('/ORDER BY(.*)?/si','',$sql);

		$query=$this->query($sql);
		$num_rows=$query->num_rows();
		$query->free_result();
		return $num_rows;
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
				if (substr($key,strlen($key)-1,1)=='s') {
					$key=substr($key,0,strlen($key)-1);
					$this->foreigns[$key]=array('key'=>$key,'table'=>$table,'fields'=>$value);
				}
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
		* Functions for getting the abstract fieds / sql for a table
	 */
	function get_abstract_fields($table,$asPre='') {
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
		return $abFields;
	}

	function get_abstract_fields_sql($table,$asPre='') {
		$abFields=$this->get_abstract_fields($table,$asPre);
		$CI=& get_instance();
		return $this->concat($abFields)." AS ".$asPre.$CI->config->item('ABSTRACT_field_name');
	}

	// This one is for old sites, same as get_abstract_fields_sql()
	function get_abstract_field($table,$asPre="") {
		return $this->get_abstract_fields_sql($table, $asPre);
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
				if (isset($CI->cfg)) {
					// first table with info (for order)
					$tablesWithInfo=$CI->cfg->get('CFG_table');
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
			$options=array();
			$cleanTable=rtrim($table,'_');
			$CI=&get_instance();
			$asTree=$this->has_field($cleanTable,'self_parent');
			$this->select($this->pk);
			if ($asTree) $this->select('uri,order,self_parent');
			$this->select($this->get_abstract_fields_sql($cleanTable));
			if ($asTree) {
				$this->order_as_tree();
			}
			else {
				$this->_set_standard_order($cleanTable,$CI->config->item('ABSTRACT_field_name'));
			}
			if (!empty($optionsWhere)) {
				$this->ar_where[]=$optionsWhere;
			}
			// $res=$this->get_results($cleanTable);
			$query=$this->get($cleanTable);
			$res=$query->result_array();
			$query->free_result();
			// set id as key
			$res=$this->_set_key_to($res,PRIMARY_KEY);
			foreach($res as $row) {
				$options[$row[$this->pk]]=$row[$CI->config->item('ABSTRACT_field_name')];
				if ($asTree and $row['self_parent']!=0) $options[$row[$this->pk]]=$res[$row['self_parent']][$CI->config->item('ABSTRACT_field_name')].' / '.$options[$row[$this->pk]];
			}
			return $options;
		}

	/**
	 * function _add_foreign_options($out,$foreignTables)
	 *
	 * Adds option arrays to current result array
	 * @param array	$out						Current result array
	 * @param array	$foreignTables	Tables from the options to be added
	 * @return array	Resultarray with options
	 */
	 function _add_foreign_options($out,$foreignTables,$table='') {
			$options=array();
			if (isset($foreignTables)) {
				$CI=&get_instance();
				foreach ($foreignTables as $key => $forTable) {
					$cleanTable=rtrim($forTable['table'],'_');
					$optionsWhere=$CI->cfg->get('CFG_table',$cleanTable,'str_options_where');
					// override options Where with Field Info, if there is any
					if (!empty($table)) {
						$cfgKey=$table.'.'.$forTable['key'];
						$optWhere=$CI->cfg->get('CFG_field',$cfgKey,'str_options_where');
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
				$CI=&get_instance();
				foreach ($manyTables as $rel => $jTable) {
					$optionsWhere=$CI->cfg->get('CFG_table',$jTable["rel"],'str_options_where');
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
						$out["multi_options"][$field]=array_combine($options,$options);
					else
						$out["options"][$field]=array_combine($options,$options);
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