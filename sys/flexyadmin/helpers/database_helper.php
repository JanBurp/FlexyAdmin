<?php 
/**
 * Functies die handig zijn voor tabel en veldnamen van de database
 *
 * @author Jan den Besten
 **/

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
 * @return string Bijvoorbeeld tbl_links
 */
function foreign_table_from_key($key,$give_clean=false) {
	$CI =& get_instance();
	$sFid=rtrim($key,"_");
  $s='';
  if (isset($CI->cfg)) $s=$CI->cfg->get('cfg_field_info',$key,'table','');
  if (empty($s)) {
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
  }
	if (strcmp($key,$sFid)!=0 and !$give_clean) $s.="_";		// for self relations add a _
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
 * Geeft menu tabel
 * 
 * - = tbl_menu
 * - = res_menu_result als deze bestaat
 *
 * @return string
 * @author Jan den Besten
 */
 function get_menu_table() {
	static $table='';
	if (empty($table)) {
		$CI =& get_instance();
		$tables=$CI->config->item('MENU_TABLES');
		$next=current($tables);
		if ($next) {
			$table=$next;
			while ( $next and ! $CI->db ->table_exists($table)) {
				$next=next($tables);
				if ($next) $table=$next;
			}
		}
	}
	return $table;
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
  if (!$tables) $tables=$CI->db->list_tables();
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


?>
