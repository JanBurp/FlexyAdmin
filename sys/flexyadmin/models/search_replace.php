<?php 
/**
 * Zoekt/vervangt items in de database
 *
 * @package default
 * @author Jan den Besten
 */
 
class Search_replace Extends CI_Model {

   private $table_types = array('tbl','res');
   private $field_types = array('txt');
   private $media_types = array('media','medias');
   private $langRegex = '';
	
	
   /**
    * @ignore
    */
   public function __construct() {
		parent::__construct();
		// This is for sites with uri's to different languages
		$languages=$this->config->item('languages');
		if (count($languages)>1) {
			$autoMenuCfg=$this->cfg->get('cfg_auto_menu');
			if ($autoMenuCfg) {
				$languageCfg=find_row_by_value($autoMenuCfg,'split by language');
				if (isset($languageCfg['str_parameters'])) {
					$languages=$languageCfg['str_parameters'];
				}
			}
			$languagesRegex=implode('/|',$languages).'/|';
			$languagesRegex=str_replace('/','\/',$languagesRegex);
			$this->langRegex=$languagesRegex;
		}
	}
	
  
  /**
   * Vervangt tekst in de hele database in bepaald veldsoorten
   *
   * @param string $search 
   * @param string $replace 
   * @param string $types 
   * @return void
   * @author Jan den Besten
   */
  public function replace_all($search,$replace,$types='txt') {
    if (!is_array($types)) $types=array($types);
		$result=FALSE;
		$tables=$this->db->list_tables();
		foreach($tables as $table) {
			$type=get_prefix($table);
			if (in_array($type,$this->table_types)) {
				$fields=$this->db->list_fields($table);
				foreach ($fields as $field) {
					$pre=get_prefix($field);
					// Only in set field types
					if (in_array($pre,$types)) {
						$result[$table] = $this->replace_in( $table, $field, $search, $replace);
					}
				}
			}
		}
    return $result;
  }
  
  /**
   * Vervangt in gegeven tabellen in gegeven velden
   *
   * @param string $search 
   * @param string $replace 
   * @param array $fields[array()] als leeg dan in alle velden
   * @param array $tables[array()] als leeg dan in alle tabellen
   * @return void
   * @author Jan den Besten
   */
  public function replace_value($search,$replace,$fields='',$tables='') {
    if (empty($tables)) $tables=$this->db->list_tables();
		$result=FALSE;
		foreach($tables as $table) {
      if (empty($fields)) $fields=$this->db->list_fields($table);
      if (is_string($fields)) $fields=explode('|',$fields);
			foreach ($fields as $field) {
				$res = $this->replace_value_in( $table, $field, $search, $replace);
        if ($res) {
          if (!$result) $result=array();
          $result[]=$res;
        }
			}
		}
    return $result;
  }
  
  
  /**
   * Vervangt waarde in bepaald veld van bepaalde tabel
   *
   * @param string $table 
   * @param string $field 
   * @param string $search 
   * @param string $replace 
   * @return array
   * @author Jan den Besten
   */
  public function replace_value_in($table,$field,$search,$replace) {
		$result=FALSE;
    if ($this->db->field_exists($field,$table)) {
  		$this->db->select("id,$field");
  		$this->db->where($field,$search);
  		$query=$this->db->get($table);
  		foreach($query->result_array() as $row) {
  			$id=$row["id"];
        $this->db->update($table,array($field=>$replace),"id = $id");
  			$result[]=array('table'=>$table,'id'=>$id,'field'=>$field);
  		}
  		$query->free_result();
    }
		return $result;
  }
  
  
  
  

  /**
   * Vervangt tekst in bepaald veld van bepaalde tabel
   *
   * @param string $table 
   * @param string $field 
   * @param string $search 
   * @param string $replace 
   * @return array
   * @author Jan den Besten
   */
  public function replace_in($table,$field,$search,$replace) {
		$result=FALSE;
		$this->db->select("id,$field");
		$this->db->where("$field !=","");
		$query=$this->db->get($table);
		foreach($query->result_array() as $row) {
			$id=$row["id"];
			$txt=$row[$field];
      $newtxt=str_replace($search,$replace,$txt);
			// Update in database if changed
			if ($txt!=$newtxt) {
				$this->db->update($table,array($field=>$newtxt),"id = $id");
				$result[]=array('table'=>$table,'id'=>$id,'field'=>$field);
			}
		}
		$query->free_result();
		return $result;
  }
  
  
  
  
	
  /**
   * Vervangt alle links in alle teksten van de database (in alle content tabellen)
   *
   * @param string $search Te zoeken link
   * @param string $replace Te vervangen in...
   * @return array Resultaat
   * @author Jan den Besten
   */
   public function links($search,$replace='') {
		$result=FALSE;
		$tables=$this->db->list_tables();
		foreach($tables as $table) {
			$type=get_prefix($table);
			// Only in set table types
			if (in_array($type,$this->table_types)) {
				$fields=$this->db->list_fields($table);
				foreach ($fields as $field) {
					$pre=get_prefix($field);
					// Only in set field types
					if (in_array($pre,$this->field_types)) {
						$result[$table] = $this->links_in( $table, $field, $search, $replace);
					}
				}
			}
		}
		return $result;
	}


  /**
   * Vervangt alle links in een bepaald veld van een bepaalde tabel
   *
   * @param string $table Tabel waar wordt vervangen
   * @param string $field Veld waar wordt vervangen
   * @param string $search Gezochte link
   * @param string $replace Vervangen door..
   * @return array Resultaat rij
   * @author Jan den Besten
   */
	public function links_in($table,$field,$search,$replace='') {
		$result=FALSE;
		$this->db->select("id,$field");
		$this->db->where("$field !=","");
		$query=$this->db->get($table);
		foreach($query->result_array() as $row) {
			$id=$row["id"];
			$txt=$row[$field];

			if (empty($replace)) {
				// remove
				$pattern='/<a(.*?)href="('.$this->langRegex.')'.str_replace("/","\/",$search).'"(.*?)>(.*?)<\/a>/';
				$newtxt=preg_replace($pattern,'$4',$txt);
			}
			else {
				// replace
				$pattern='/<a(.*?)href="('.$this->langRegex.')'.str_replace("/","\/",$search).'"(.*?)>(.*?)<\/a>/';
				$newtxt=preg_replace($pattern,'<a$1href="$2'.$replace.'"$3>$4</a>',$txt);
			}

			// Update in database if changed
			if ($txt!=$newtxt) {
				$this->db->update($table,array($field=>$newtxt),"id = $id");
				$result[]=array('table'=>$table,'id'=>$id,'field'=>$field);
			}
		}

		$query->free_result();
		return $result;
	}
  
  
  /**
   * Test if text is found
   *
   * @param string $text 
   * @param array $fields[''] Fields to find in
   * @return bool TRUE if found
   * @author Jan den Besten
   */
  public function has_text($text,$fields='') {
    $found=FALSE;
    if (is_string($fields)) $fields=explode('|',$fields);
    foreach ($fields as $field) {
      $table=get_prefix($field,'.');
      $field=remove_prefix($field,'.');
      $this->db->search(array('search'=>$text,'field'=>$field))->select($field);
      $row=$this->db->get_row($table);
      $found=(!empty($row));
      if ($found) break;
    }
    return $found;
  }


  /**
   * Vervangt alle bestandsnamen
   *
   * @param array $search Te zoeken bestandsnaam, of een array van meerdere te zoeken/vervangen bestanden
   * @param string $replace['']
   * @return array Resultaten
   * @author Jan den Besten
   */
  public function media($search,$replace='') {
    $result=FALSE;
		$tables=$this->db->list_tables();
		foreach($tables as $table) {
			$type=get_prefix($table);
			// Only in set table types
			if (in_array($type,$this->table_types)) {
				$fields=$this->db->list_fields($table);
				foreach ($fields as $field) {
					$pre=get_prefix($field);
					// Only in set field types
					if (in_array($pre,$this->field_types) or in_array($pre,$this->media_types)) {
						$result[$table] = $this->media_in( $table, $field, $search, $replace);
					}
				}
			}
		}
		return $result;
  }

  /**
   * Vervangt bestandsnamen in specifieke tabel/veld
   *
   * @param string $table 
   * @param string $field 
   * @param string $search 
   * @param string $replace['']
   * @return array resultaat
   * @author Jan den Besten
   */
  public function media_in($table,$field,$search,$replace='') {
		$result=array();
    $path=$this->cfg->get('cfg_media_info',$table.'.'.$field,'path');
    if (!empty($path)) $path.='/';
    // strace_($path);
    
    if (!is_array($search)) {
      $search=array($search=>$replace);
    }
    foreach ($search as $s => $r) {
      unset($search[$s]);
      $s=str_replace($path,'', remove_assets($s) );
      if (!empty($r)) $r=str_replace($path,'', remove_assets($r) );
      $search[$s]=$r;
    }
    
    // strace_($search);
    
		$this->db->select("id,$field");
    $this->db->where("$field !=","");
		$query=$this->db->get($table);
    
		foreach($query->result_array() as $row) {
			$id=$row["id"];
			$data=$row[$field];
      $newData=$data;
      $fieldType=get_prefix($field);
      
      switch ($fieldType) {
        case 'media':
        case 'medias':
          // strace_($search);
          // strace_($newData);
          $newData=str_replace(array_keys($search),array_values($search),$newData);
          $newData=str_replace('||','|',$newData);
          $newData=trim($newData,'|');
          // strace_($newData);
          break;
        case 'txt':
          foreach ($search as $s => $r) {
            $s=assets().$path.$s;
            $s=str_replace('/','\/',$s);
            if (empty($r)) {
              // remove
              $regex='/<img(.*)?src=\"'.$s.'\"(.*)?\/\>/uiUsm';
              // strace_(array('regex_s'=>$regex,'regex_r'=>''));
              $newData = preg_replace($regex, '', $newData);
            }
            else {
              // replace
              $r=assets().$path.$r;
              $regex='/<img(.*)?src=\"'.$s.'\"(.*)?\/\>/uiUsm';
              $regex_r='<img$1src="'.$r.'"$3/>';
              // strace_(array('regex_s'=>$regex,'regex_r'=>$regex_r));
              $newData = preg_replace($regex, $regex_r, $newData);
            }
          }
          break;
      }

			// Update in database if changed
			if ($data!=$newData) {
        // trace_('CHANGED');
				$this->db->update($table,array($field=>$newData),"id = $id");
        // trace_($this->db->last_query());
				$result[]=array('table'=>$table,'id'=>$id,'field'=>$field);
			}
		}

		$query->free_result();
		return $result;
  }

}

?>
