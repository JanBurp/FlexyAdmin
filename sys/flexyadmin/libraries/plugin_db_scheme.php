<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package default
 * @author Jan den Besten
 */
 class Plugin_db_scheme extends Plugin {
   
   public function __construct() {
     parent::__construct();
   }


	public function _admin_api($args=NULL) {
    $analyze=$this->analyze($args);
    if (is_array($analyze)) {
      // analyze=trace_(analyze,false);
      $this->add_message(h('Statistics'));
      foreach ($analyze['statistics'] as $key => $value) {
        $this->add_message(p().$key.' = '.$value._p());
      }
      $this->add_message(h('Tables'));
      foreach ($analyze['tables'] as $key => $table) {
        $this->add_message(h($table['name'],2));
        if (!empty($table['foreign_keys'])) {
          foreach ($table['foreign_keys'] as $key) {
            $this->add_message($key.' => '.foreign_table_from_key($key));
          }
        }
      }
    }
    return $this->view('admin/plugin_db_scheme',$analyze,true);
	}
  
  
  /**
   * ANALYSEREN
   *
   * @return void
   * @author Jan den Besten
   */
  private function analyze() {
    $tables=$this->CI->db->list_tables();
    $analysedTables=array();
    $statistics=array(
      'total_tables'            => count($tables),
      'total_foreign_keys'      => 0,
    );
    
    foreach ($tables as $tid => $table) {
      $analysedTables[$table]=array(
        'name'              => $table,
        'is_reltable'       => $this->isRelationTable($table),
        'fields'            => $this->listFields($table),
      );
    }
    return array('statistics'=>$statistics,'tables'=>$analysedTables);
  }
  
  private function listFields($table) {
    $fields=$this->list_fields($table);
    $analyzedFields=array(
      'fields'        => array(),
      'foreign_keys'  => array()
    );
    foreach ($fields as $key => $field) {
      if ($this->isForeignKey($field,$table))
        $analyzedFields['foreign_keys'][]=$field;
      else
        $analyzedFields['fields'][]=$field;
    }
    return $analyzedFields;
  }
  
  
  /**
   * Analyseert of tabel een relatietabel is
   * 
   * @param string $table
   * @return bool
   * @author Jan den Besten
   */
  private function isRelationTable($table) {
    return get_prefix($table)=='rel';
    // $isRelationTable = FALSE;
    // $hasUnderscoreInName = (strpos($table,'_')>0);
    // if ($hasUnderscoreInName) {
    //   $fields = $this->list_fields($table);
    //   // Eerste twee velden na 'id' met '_id' aan het eind
    //   if (count($fields)>=3) {
    //     $isRelationTable =  $this->isPrimaryKey($fields[0])
    //                         AND $this->isForeignKey($fields[1],$table)
    //                         AND $this->isForeignKey($fields[2],$table);
    //   }
    // }
    // return $isRelationTable;
  }
  
  
  /**
   * Geeft alle velden die duidelijk foreignkeys zijn
   *
   * @param string $table 
   * @return array
   * @author Jan den Besten
   */
  private function listForeignKeys($table) {
    $foreign_keys = array();
    $fields=$this->list_fields($table);
    foreach ($fields as $field) {
      if ($this->isForeignKey($field,$table)) $foreign_keys[]=$field;
    }
    return $foreign_keys;
  }

  // /**
  //  * Test of de tabel alleen maar dezelfde data heeft, dus eigenlijk onnodig is
  //  *
  //  * @param string $table 
  //  * @return bool
  //  * @author Jan den Besten
  //  */
  // private function isRedundant($table) {
  //   $isRedundant = ($this->CI->db->count_all($table) <=1);
  //   if (!$isRedundant) {
  //     $query = $this->CI->db->query("SELECT DISTINCT * FROM $table");
  //     $isRedundant = ($query->num_rows() <=1);
  //   }
  //   return $isRedundant;
  // }
  // 
  // 
  // 
  // /**
  //  * Geeft alle velden die geen enkele unieke waarde hebben
  //  *
  //  * @param string $table 
  //  * @return array
  //  * @author Jan den Besten
  //  */
  // private function listRedundantFields($table) {
  //   $redundantFields = array();
  //   $fields = $this->list_fields($table);
  //   foreach ($fields as $field) {
  //     $query = $this->CI->db->query("SELECT DISTINCT $field FROM $table");
  //     $isRedundant = ($query->num_rows() <=1);
  //     if ($isRedundant) $redundantFields[] = $field;
  //   }
  //   return $redundantFields;
  // }
  // 



  /*************************************************************
   * HELPERS
   */

  private function find_tables_with_field($field) {
    $for_tables=array();
    $tables=$this->get_old_acc_tables();
    foreach ($tables as $table) {
      $fields=$this->list_fields($table);
      foreach ($fields as $f) {
        if ($f==$field) $for_tables[]=$table;
      }
    }
    return $for_tables;
  }

  private function list_fields($table) {
    if (!isset($this->fields[$table])) {
      $this->fields[$table] = $this->CI->db->list_fields($table);
    }
    return $this->fields[$table];
  }

  private function isPrimaryKey($field) {
    return $field=='id';
  }
  
  private function isForeignKey($field,$table) {
    return is_foreign_key($field);
    // $data=$this->CI->db->field_data($table);
    // $type='';
    // foreach ($data as $field_data) {
    //   if ($field_data->name==$field) {
    //     $type=$field_data->type;
    //     break;
    //   }
    // }
    // return (substr($field,-3)=='_id' AND has_string('int',$type));
  }
  
  function table_exists($tableName) {
    $result = $this->CI->db->list_tables();
    foreach( $result as $row ) {
      if( $row == $tableName ) return true;
    }
    return false;
  }
  
  // private function run_sql($sql) {
  //     $lines=preg_split('/;\n+/',$sql); // split at ; with EOL
  //     foreach ($lines as $key => $line) {
  //       $line=trim($line);
  //       if (!empty($line))
  //         $query=$this->CI->db->query($line);
  //     else
  //       unset($lines[$key]);
  //     }
  //   return $lines;
  // }


}

?>