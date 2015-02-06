<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Uitbreiding op [CodeIgniters Database Utility](http://codeigniter.com/user_guide/database/utilities.html) class
 * 
 * Zorgt ervoor dat TEXT en BLOB velden van een SQL Export als HEX-data in de dump zitten ipv als letterlijke tekst.
 *
 * @package default
 * @internal
 * @ignore
 */
class MY_DB_mysql_utility extends CI_DB_mysql_utility {
	
  
  public function __construct() {
    parent::__construct();
  }
  
  
  /**
   * Test of een sql veilig is om te importeren
   *
   * @param string $sql 
   * @return bool
   * @author Jan den Besten
   */
	public function is_safe_sql($sql,$no_drop=false,$no_alter=false) {
		$safe=TRUE;
		// Check on DROP/ALTER/RENAME statements ;
    $checks='DROP|ALTER|RENAME|REPLACE|LOAD\sDATA|SET';
    if ($no_drop) $checks=remove_prefix($checks,'|');
    if ($no_alter) $checks=remove_prefix($checks,'|');
		if (preg_match("/\b(".$checks.")\b/i",$sql)>0)	$safe=FALSE;
		// Check on TRUNCATE / CREATE table names, if it has rights for tables
		if ($safe) {
      $CI=&get_instance();
			if (preg_match_all("/(TRUNCATE\sTABLE|CREATE\sTABLE|INSERT\sINTO|DELETE\sFROM|UPDATE)\s(.*?)(;|\s)/i",$sql,$matches)>0) {
				$tables=$matches[2];
				$tables=array_unique($tables);
				$tables=not_filter_by($tables,'rel');
				// check if rights for found tables
				foreach ($tables as $table) {
					if ($CI->user->has_rights($table) < RIGHTS_ALL) $safe=FALSE;
				}
			}
		}
		return $safe;
	}

  /**
   * Import sql
   *
   * @param string $sql 
   * @return bool
   * @author Jan den Besten
   */
  public function import($sql) {
		$lines=explode("\n",$sql);
    // remove comments
		$comments="";
    $errors='';
		foreach ($lines as $k=>$l) {
			if (substr($l,0,1)=="#")	{
				if (strlen($l)>2)	$comments.=$l.br();
				unset($lines[$k]);
			}
		}
		$sql=implode("\n",$lines);
		$lines=preg_split('/;\n+/',$sql); // split at ; with EOL
    // actual import
		foreach ($lines as $key => $line) {
			$line=trim($line);
			if (!empty($line)) {
				$query=$this->db->query($line);
        if ($this->db->_error_message()) {
          $errors.=$this->db->_error_message();
        }
			}
		}
    
    return array('comments'=>$comments,'queries'=>$lines,'errors'=>$errors);
  }
  
  
	/**
	 * MySQL Export
	 *
	 * @access private
	 * @param array	Preferences
	 * @return mixed
	 * @ignore
	 */
	public function _backup($params = array()) {
		if (count($params) == 0) {
			return FALSE;
		}

		// Extract the prefs for simplicity
		extract($params);
	
		// Build the output
		$output = '';
		foreach ((array)$tables as $table) {
			// Is the table in the "ignore" list?
			if (in_array($table, (array)$ignore, TRUE)) {
				continue;
			}

			// Get the table schema
			$query = $this->db->query("SHOW CREATE TABLE `".$this->db->database.'`.'.$table);
			
			// No result means the table name was invalid
			if ($query === FALSE) {
				continue;
			}
			
			// Write out the table schema
			$output .= '#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;

 			if ($add_drop == TRUE) {
				$output .= 'DROP TABLE IF EXISTS '.$table.';'.$newline.$newline;
			}
			
			$i = 0;
			$result = $query->result_array();
			foreach ($result[0] as $val) {
				if ($i++ % 2) { 					
					$output .= $val.';'.$newline.$newline;
				}
			}
			
			// If inserts are not needed we're done...
			if ($add_insert == FALSE) {
				continue;
			}

			// Grab all the data from the current table
			$query = $this->db->query("SELECT * FROM $table");
			
			if ($query->num_rows() == 0) {
				continue;
			}
		
			// Fetch the field names and determine if the field is an
			// integer type.  We use this info to decide whether to
			// surround the data with quotes or not
			
			$i = 0;
			$field_str = '';
			$is_int = array();
			$is_blob = array();	// added by JdB
			while ($field = mysql_fetch_field($query->result_id)) {
				// Most versions of MySQL store timestamp as a string
				$is_int[$i] = (in_array(
										strtolower(mysql_field_type($query->result_id, $i)),
										array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'), //, 'timestamp'), 
										TRUE)
										) ? TRUE : FALSE;
				// Added by Jdb
				$is_blob[$i] = (in_array(
										strtolower(mysql_field_type($query->result_id, $i)),
										array('text', 'blob', 'tinytext', 'tinyblob', 'mediumtext', 'mediumblob', 'longtext', 'longblob'), 
										TRUE)
										) ? TRUE : FALSE;
				
				// Create a string of field names
				$field_str .= '`'.$field->name.'`, ';
				$i++;
			}
			
			// Trim off the end comma
			$field_str = preg_replace( "/, $/" , "" , $field_str);
			
			
			// Build the insert string
			foreach ($query->result_array() as $row) {
				$val_str = '';
			
				$i = 0;
				foreach ($row as $v) {
					// Is the value NULL?
					if ($v === NULL) {
						$val_str .= 'NULL';
					}
					else {
						// Hex the data if it's a blob (Added by JdB)
						if ($is_blob[$i])
						{
							if (empty($v))
								$val_str.=$this->db->escape($v);
							else
								$val_str .= str2hex($v);
						}					
						// Escape the data if it's not an integer
						elseif ($is_int[$i] == FALSE)
						{
							$val_str .= $this->db->escape($v);
						}
						// Hex the data if it's a blob (Added by JdB)
						elseif ($is_blob[$i])
						{
							$val_str .= str2hex($v);
						}					
						else
						{
							$val_str .= $v;
						}					
					}					
					
					// Append a comma
					$val_str .= ', ';
					$i++;
				}
				
				// Remove the comma at the end of the string
				$val_str = preg_replace( "/, $/" , "" , $val_str);
								
				// Build the INSERT string
				$output .= 'INSERT INTO '.$table.' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
			}
			
			$output .= $newline.$newline;
		}

		return $output;
	}


}

/* End of file mysql_utility.php */
/* Location: ./system/database/drivers/mysql/mysql_utility.php */