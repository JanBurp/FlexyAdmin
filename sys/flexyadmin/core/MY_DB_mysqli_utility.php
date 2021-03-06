<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup core
 * Uitbreiding op [CodeIgniters Database Utility](http://codeigniter.com/user_guide/database/utilities.html) class
 * 
 * Zorgt ervoor dat TEXT en BLOB velden van een SQL Export als HEX-data in de dump zitten ipv als letterlijke tekst.
 *
 * @internal
 */
class MY_DB_mysqli_utility extends CI_DB_mysqli_utility {
	
  public function __construct(&$db) {
    parent::__construct($db);
  }

  /**
   * Maak ruwe SQL schoon:
   * - Verwijder alle comments
   * - DROP TABLE wordt TRUNCATE table
   * - CREATE TABLE wordt verwijderd (INSERTS blijven)
   *
   * @param      string  $sql ruw SQL
   * @return     string  Schone SQL
   */
  public function clean_sql($sql) {
    // $sql=preg_replace("/#(.*?)\n/","",$sql);
    $sql=preg_replace("/DROP TABLE(.*) (.*?);/","# Empty $2\nTRUNCATE TABLE $2;",$sql);
    $sql=preg_replace("/CREATE TABLE (.*?) (.|\n)*?;\n/","# Inserts for $1",$sql);
    return $sql;
  }


  /**
   * Test of een sql veilig is om te importeren
   *
   * @param string $sql 
   * @param string $no_drop default=FALSE
   * @param string $no_alter default=FALSE
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
      $CI = &get_instance();
			if (preg_match_all("/(TRUNCATE\sTABLE|CREATE\sTABLE|INSERT\sINTO|DELETE\sFROM|UPDATE)\s`(.*?)`(;|\s)/i",$sql,$matches)>0) {
				$tables=$matches[2];
				$tables=array_unique($tables);
				$tables=not_filter_by($tables,'rel');
				// check if rights for found tables
				foreach ($tables as $table) {
					if ( $CI->flexy_auth->has_rights($table) < RIGHTS_ALL) $safe=FALSE;
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
    $lines=explode(PHP_EOL,$sql);
    // remove comments
    $comments="";
    $errors=array();
    foreach ($lines as $k=>$l) {
      if (substr($l,0,1)=="#")  {
        if (strlen($l)>2)  $comments.=$l.br();
        unset($lines[$k]);
      }
      $l=str_replace(array("\n","\r"),'',$l);
      if (empty($l)) unset($lines[$k]);
      $lines[$k]=$l;
    }

    $sql=implode(PHP_EOL,$lines);
    $lines=preg_split('/;'.PHP_EOL.'+/',$sql); // split at ; with EOL
    // actual import
    foreach ($lines as $key => $line) {
      $line=trim($line);
      if (!empty($line)) {
        if (!$this->db->simple_query($line)) {
          $errors[]=$this->db->error();
          break;
        }
      }
    }
    $this->reset_data_cache();
    
    return array('comments'=>$comments,'queries'=>$lines,'errors'=>$errors);
  }
  
  
  /**
   * Verwijderd interne cache van db object
   *
   * @return this
   * @author Jan den Besten
   */
  public function reset_data_cache() {
    $this->db->data_cache = array();
    return $this;
  }

  
  
	// --------------------------------------------------------------------

	/**
	 * Export
	 *
	 * @param	array	$params	Preferences
	 * @return	mixed
	 */
	protected function _backup($params = array())
	{
		if (count($params) === 0)
		{
			return FALSE;
		}

		// Extract the prefs for simplicity
		extract($params);

		// Build the output
		$output = '';

		// Do we need to include a statement to disable foreign key checks?
		if ($foreign_key_checks === FALSE)
		{
			$output .= 'SET foreign_key_checks = 0;'.$newline;
		}

		foreach ( (array) $tables as $table)
		{
			// Is the table in the "ignore" list?
			if (in_array($table, (array) $ignore, TRUE))
			{
				continue;
			}

			// Get the table schema
			$query = $this->db->query('SHOW CREATE TABLE '.$this->db->escape_identifiers($this->db->database.'.'.$table));

			// No result means the table name was invalid
			if ($query === FALSE)
			{
				continue;
			}

			// Write out the table schema
			$output .= '#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;

			if ($add_drop === TRUE)
			{
				$output .= 'DROP TABLE IF EXISTS '.$this->db->protect_identifiers($table).';'.$newline.$newline;
			}

			$i = 0;
			$result = $query->result_array();
			foreach ($result[0] as $val)
			{
				if ($i++ % 2)
				{
					$output .= $val.';'.$newline.$newline;
				}
			}

			// If inserts are not needed we're done...
			if ($add_insert === FALSE)
			{
				continue;
			}

			// Grab all the data from the current table
			$query = $this->db->query('SELECT * FROM '.$this->db->protect_identifiers($table));

			if ($query->num_rows() === 0)
			{
				continue;
			}

			// Fetch the field names and determine if the field is an
			// integer type. We use this info to decide whether to
			// surround the data with quotes or not

			$i = 0;
			$field_str = '';
			$is_int = array();
			$is_hex = array();	// JDB
			while ($field = $query->result_id->fetch_field())
			{
				// Most versions of MySQL store timestamp as a string
				$is_int[$i] = in_array($field->type, array(MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_INT24, MYSQLI_TYPE_LONG), TRUE);

				// HEX AES: JDB
				$is_hex[$i] = ($field->type == 253); // VARBINARY 

				// Create a string of field names
				$field_str .= $this->db->escape_identifiers($field->name).', ';
				$i++;
			}

			// Trim off the end comma
			$field_str = preg_replace('/, $/' , '', $field_str);

			// Build the insert string
			foreach ($query->result_array() as $row)
			{
				$val_str = '';

				$i = 0;
				foreach ($row as $v)
				{
					// Is the value NULL?
					if ($v === NULL)
					{
						$val_str .= 'NULL';
					}
					else
					{

            // Escape the data if it's hex (JDB)
            if ($is_hex[$i]) {
              if (empty($v))
                $val_str .= $this->db->escape($v);
              else
                $val_str .= str2hex($v);
            }
						// Escape the data if it's not an integer
						else {
							$val_str .= ($is_int[$i] === FALSE) ? $this->db->escape($v) : $v;
            }

					}

					// Append a comma
					$val_str .= ', ';
					$i++;
				}

				// Remove the comma at the end of the string
				$val_str = preg_replace('/, $/' , '', $val_str);

				// Build the INSERT string
				$output .= 'INSERT INTO '.$this->db->protect_identifiers($table).' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
			}

			$output .= $newline.$newline;
		}

		// Do we need to include a statement to re-enable foreign key checks?
		if ($foreign_key_checks === FALSE)
		{
			$output .= 'SET foreign_key_checks = 1;'.$newline;
		}

		return $output;
	}




}

/* End of file mysql_utility.php */
/* Location: ./system/database/drivers/mysql/mysql_utility.php */