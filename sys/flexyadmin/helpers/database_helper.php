<?php 
/** \ingroup helpers
 * Functies die handig zijn voor tabel en veldnamen van de database
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 * @file
 **/


/**
 * Test if a value is a TRUE
 *
 * @param string $val 
 * @return void
 * @author Jan den Besten
 */
function is_true_value($val) {
  if (is_bool($val))    return $val;
  if (is_numeric($val)) return $val;
  if (is_array($val))   return !empty($val);
  if (is_string($val)) {
    if (strtolower($val)==='true')  return true;
    if (strtolower($val)==='false') return false;
    if (strtolower($val)==='yes')   return true;
    if (strtolower($val)==='no')    return false;
  }
  return null;
}


/**
 * Test of veld een foreign key is (beginnen met id_)
 *
 * @param string $field
 * @return bool
 */
function is_foreign_key($field) {
	$CI =& get_instance();
	$out=false;
	if (preg_match($CI->config->item('FOREIGN_key_format'), $field)==1) $out=true;
	return $out;
}


/**
 * Test of veld een foreign veld is
 * 
 * Deze veldnamen komen voor in database resultaten waar met `add_foreigns()` foreign data is toegevoegd
 * Deze veldnamen zien er zo uit: `foreign_key__field`
 *
 * @param string $field
 * @return bool
 */
function is_foreign_field($field) {
	$key=get_prefix($field,"__");
	return (is_foreign_key($key));
}

/**
 * Geeft foreign key van gegeven foreign table
 *
 * @param string $table
 * @return string
 */
function foreign_key_from_table($table) {
	return 'id_'.remove_prefix($table);
}


/**
 * Geeft foreign tabel van gegeven foreign key, en checkt of tabel werkelijk bestaat
 *
 * @param string $key Bijvoorbeeld id_links
 * @param bool $give_clean default=FALSE
 * @return string Bijvoorbeeld tbl_links
 */
function foreign_table_from_key($key,$give_clean=false) {
  $s='';
  if ($key) {
  	$CI =& get_instance();
  	$sFid=rtrim($key,"_");
  	$s=$CI->config->item('TABLE_prefix')."_".remove_prefix($sFid);
  	if (!$CI->db->table_exists($s)) {
  		$s=$s."s";
  		if (!$CI->db->table_exists($s)) {
  			$s=$CI->config->item('CFG_table_prefix')."_".remove_prefix($sFid);
  			if (!$CI->db->table_exists($s)) {
  				$s=$s."s";
  			}
  		}
  	}
  	if (strcmp($key,$sFid)!=0 and !$give_clean) $s.="_";		// for self relations add a _
  }
	return $s;
}

/**
 * Geeft many (rechter) tabel van gegeven relatie tabel, en checkt of de tabel bestaat
 *
 * @param string $rel Bijvoorbeeld rel_menu__links
 * @return string Bijvoorbeeld tbl_links
 */
function join_table_from_rel_table($rel) {
	$CI =& get_instance();
	$join=$CI->config->item('TABLE_prefix')."_".remove_prefix($rel,$CI->config->item('REL_table_split'));
	if (!$CI->db->table_exists($join)) $join=$CI->config->item('CFG_table_prefix')."_".remove_prefix($rel,$CI->config->item('REL_table_split'));
	return $join;
}

/**
 * Geeft linker tabel van gegeven relatie tabel en checkt of de tavel bestaat
 *
 * @param string $rel Bijvoorbeeld rel_menu__links
 * @return string Bijvoorbeeld tbl_menu
 */
function table_from_rel_table($rel) {
	$CI =& get_instance();
	$rel=explode("_",$rel);
	$table=$CI->config->item('TABLE_prefix')."_".$rel[1];
	if (!$CI->db->table_exists($table)) $table=$CI->config->item('CFG_table_prefix')."_".$rel[1];
	return $table;
}



/**
 * Geeft linker key van gegeven relatie tabel
 *
 * @param string $rel Bijvoorbeeld rel_menu__links
 * @return string Bijvoorbeeld id_menu
 */
function this_key_from_rel_table($rel) {
	$CI =& get_instance();
	$key="id_".remove_prefix(get_prefix($rel,$CI->config->item('REL_table_split')));
	return $key;
}

/**
 * Geeft many (rechter) key van gegeven relatie tabel
 *
 * @param string $rel Bijvoorbeeld rel_menu__links
 * @return string Bijvoorbeeld id_links
 */
function join_key_from_rel_table($rel) {
	$CI =& get_instance();
	$key="id_".remove_prefix($rel,$CI->config->item('REL_table_split'));
	return $key;
}


/**
 * Test of tabel een tabel is die aangepast mag worden (geen log_ of res_)
 *
 * @param string $table 
 * @return bool
 * @author Jan den Besten
 */
function is_editable_table($table) {
  $pre=get_prefix($table);
  return !in_array($pre,array('res','log'));
}


/**
 * Geeft een array van veldnamen (table.field) met als input veldnamen waarin wildcards zijn verwerkt
 *
 * @param mixed $wildfields 
 * @param mixed $tables[''] Eventuele selectie van tabellen
 * @return array
 * @author Jan den Besten
 */
function get_fields_from_input($wildfields,$tables='') {
  $CI =& get_instance();
  if (!is_array($wildfields)) $wildfields=explode('|',$wildfields);
  if (!$tables) $tables=$CI->data->list_tables();
  if (!is_array($tables)) $tables=explode('|',$tables);
  $fields=array();
  foreach ($wildfields as $field) {
    $table=get_prefix($field,'.');
    $field=remove_prefix($field,'.');
    if ($table=='*') {
      foreach ($tables as $table) {
        $fields[]=$table.'.'.$field;
      }
    }
    else $fields[]=$table.'.'.$field;
  }
  // trace_($wildfields);
  // trace_($tables);
  // trace_($fields);
  return $fields;
}


/**
 * Geeft de uri van een pagina met de gevraagde module
 *
 * @param string $module 
 * @param bool $full_uri default=true
 * @param string $table  default=''
 * @return string uri
 * @author Jan den Besten
 */
function find_module_uri($module,$full_uri=true,$table='') {
  $CI=&get_instance();
  $CI->data->table('tbl_menu');
  if ($full_uri) $full_uri = $CI->data->field_exists('self_parent');
  
	$CI->data->select('id,uri');
  if ($table) $CI->data->where('str_table',$table);
	if ($full_uri) {
		$CI->data->select('order,self_parent');
		$CI->data->tree('uri');
	}
	if ( get_prefix($CI->config->item('module_field'))==='id' ) {
		// Modules from foreign table
		$foreign_key=$CI->config->item('module_field');
		$foreign_field='str_'.get_suffix($CI->config->item('module_field'));
		$foreign_table=foreign_table_from_key($foreign_key);
		$CI->data->with('many_to_one');
		$like_field = $foreign_table.'__'.$foreign_field;
	}
	else {
		// Modules direct from field
		$like_field = $CI->config->item('module_field');
	}
  
	$CI->data->like( $CI->config->item('module_field'), $module );
  if ($full_uri) {
    $CI->data->order_by('order');
  }
  else {
    $CI->data->order_by('id');
  }
	$items = $CI->data->cache()->get_result();
  
  // oeps er zijn er meer.... pak dan degene die het hoogts in de menustructuur zit en het eerst voorkomt op dat nivo
  if (count($items)>1) {
    foreach ($items as $id => $item) {
      $items[$id]['_level']=substr_count($item['uri'],'/');
    }
    $items=sort_by($items,array('_level','order'));
  }
  reset($items);
  $item=current($items);
	return $item['uri'];
}

/**
 * Maakt van een database result opties die gebruikt kunnen worden in een form
 *
 * @param array $result 
 * @param mixed $fields ['*'] 
 * @return array
 * @author Jan den Besten
 */
function make_options_from_result($result,$fields='*') {
  $options=array();
  foreach ($result as $key => $row) {
    $id=$key;
    if (isset($row['id'])) $id=$row['id'];
    unset($row['id']);
    if ($fields!=='*') $row=array_keep_keys($row,$fields);
    $options[$id]=implode('|',$row);
  }
  return $options;
}



?>
