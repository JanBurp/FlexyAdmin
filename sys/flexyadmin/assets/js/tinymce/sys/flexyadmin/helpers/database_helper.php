<?
/**
 * FlexyAdmin V1
 *
 * database_helper.php Created on 15-okt-2008
 *
 * @author Jan den Besten
 */


/**
 * function table_name($table)
 *
 * @param string $table Tablename to create
 * @return string Returns name of the table, with prefix for FlexyAdmin if set
 * @todo Set and get Prefix for all tables.
 *
 */
function table_name($table) {
	return $table;
}


/**
 * function get_primary_key()
 * function pk()
 *
 * Gets primary key from config
 */
function get_primary_key() {
	return pk();
}
function pk() {
	$CI =& get_instance();
	return $CI->config->item('PRIMARY_key');
}


/**
 * function is_foreign_key($field)
 *
 * Checks if this field is a foreign key, according to its name and how the name should be (see flexyadmin_config)
 *
 * @param string $field The fieldname to check
 * @return bool true if this field is a foreign key
 */
function is_foreign_key($field) {
	$CI =& get_instance();
	$out=false;
	if (preg_match($CI->config->item('FOREIGN_key_format'), $field)==1) $out=true;
	return $out;
}

/**
 * function is_foreign_field($field)
 *
 * Checks if this field is a foreign field (foreign_key__field), according to its name and how the name should be (see flexyadmin_config)
 *
 * @param string $field The fieldname to check
 * @return bool true if this field is a foreign key
 */
function is_foreign_field($field) {
	$key=get_prefix($field,"__");
	return (is_foreign_key($key));
}

/**
 * function foreign_table_from_key($key)
 *
 * Gives foreign tablename from given foreign key and checks if it realy exists
 *
 * @param string $key Name of foreign key
 * @return string foreign table name
 */
function foreign_table_from_key($key) {
	$CI =& get_instance();
	$sFid=rtrim($key,"_"); // make sure id_item_ is cleaned to id_item for self relations
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
	if (strcmp($key,$sFid)!=0) $s.="_";		// for self relations add a _
	return $s;
}

/**
 * function join_table_from_rel_table($rel)
 *
 * Gives join tablename from given rel_table name
 *
 * @param string $rel Name of relation table
 * @return string join table name
 */
function join_table_from_rel_table($rel) {
	$CI =& get_instance();
	$join=$CI->config->item('TABLE_prefix')."_".remove_prefix($rel,$CI->config->item('REL_table_split'));
	if (!$CI->db->table_exists($join)) $join=$CI->config->item('CFG_table_prefix')."_".remove_prefix($rel,$CI->config->item('REL_table_split'));
	return $join;
}

/**
 * function this_key_from_rel_table($rel)
 *
 * Gives this key name from given rel_table name
 *
 * @param string $rel Name of relation table
 * @return string this key name (id_...)
 */
function this_key_from_rel_table($rel) {
	$CI =& get_instance();
	$key="id_".remove_prefix(get_prefix($rel,$CI->config->item('REL_table_split')));
	return $key;
}

/**
 * function join_key_from_rel_table($rel)
 *
 * Gives join key name from given rel_table name
 *
 * @param string $rel Name of relation table
 * @return string join key name (id_...)
 */
function join_key_from_rel_table($rel) {
	$CI =& get_instance();
	$key="id_".remove_prefix($rel,$CI->config->item('REL_table_split'));
	return $key;
}


?>
