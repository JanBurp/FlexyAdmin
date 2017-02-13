<?php 
/** \ingroup models
 * Zoekt/vervangt items in de database
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */
 
class Search_replace extends CI_Model {

  /**
   * De tabellen waarin de vervangingen mogen plaatsvinden
   */
   private $table_types = array('tbl','res');
   
   /**
    * De tekstvelden waar de vervangingen mogen plaatsvinden
    */
   private $field_types = array('txt');
   
   /**
    * Media velden waar de vervangingen mogen plaatsvinden
    */
   private $media_types = array('media','medias');

   private $langRegex = '';


   public function __construct() {
		parent::__construct();
		// TODO: This is for sites with uri's to different languages
		$languages=$this->config->item('languages');
		if (count($languages)>1) {
			$autoMenuCfg = $this->data->table('cfg_auto_menu')->get_result();
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
   * @param string $types Veldsoorten
   * @param boolean $regex [FALSE]
   * @return void
   * @author Jan den Besten
   */
  public function replace_all($search,$replace,$types='txt',$regex=false) {
    if (!is_array($types)) $types=array($types);
		$result=FALSE;
		$tables=$this->data->list_tables();
		foreach($tables as $table) {
			$type=get_prefix($table);
			if (in_array($type,$this->table_types)) {
				$fields=$this->db->list_fields($table);
				foreach ($fields as $field) {
					$pre=get_prefix($field);
					// Only in set field types
					if (in_array($pre,$types)) {
						$result[$table] = $this->replace_in( $search, $replace, $table, $field, $regex);
					}
				}
			}
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
   * @param boolean $regex [FALSE]
   * @return array
   * @author Jan den Besten
   */
  public function replace_in($search,$replace,$table,$field,$regex=FALSE) {
		$result=FALSE;
		$this->db->select("id,$field");
		$this->db->where("$field !=","");
		$query=$this->db->get($table);
		foreach($query->result_array() as $row) {
			$id  = $row["id"];
			$txt = $row[$field];
      if ($regex) {
        $newtxt = preg_replace($search,$replace,$txt);
      }
      else {
        $newtxt = str_replace($search,$replace,$txt);
      }
      // medias?
      if (get_prefix($field)==='medias') {
        $newtxt = trim($newtxt,'| ');
      }
			// Update in database if changed
      $changed = ($txt != $newtxt);
			if ($changed) {
				$this->data->table($table)->where('id',$id)->set($field,$newtxt)->update();
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
      $this->data->table( $table )->select( $field );
      $this->data->find( $text, $field);
      $row=$this->data->get_row();
      $found=(!empty($row));
      if ($found) break;
    }
    return $found;
  }


  /**
   * Vervangt alle bestandsnamen en afbeeldingen
   *
   * @param array $search Te zoeken bestandsnaam, of een array van meerdere te zoeken/vervangen bestanden
   * @param string $replace['']
   * @return array Resultaten
   * @author Jan den Besten
   */
  public function media($search,$replace='') {
    $search = str_replace('.','\.',$search);
    if (empty($replace)) {
      $search = array( '/<img.*src=\".*'.$search.'\".*>/uU', '/'.$search.'/uU');
    }
    else {
      $search  = array( '/<img(.*)src=\"(.*)'.$search.'\"(.*)>/uU', '/'.$search.'/uU' );
      $replace = array( '<img$1src="$2'.$replace.'"$3>', $replace );
    }
    
    return $this->replace_all($search,$replace, array_merge($this->field_types,$this->media_types), true );
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
    $search = str_replace(array('/','.'),array('\/','\.'),$search);
    if (empty($replace)) {
      $search = array( '/<a.*href=\".*'.$search.'.*\".*>(.*)<\/a>/uU', '/'.$search.'/uU');
      $replace = array( '$1', '' );
    }
    else {
      $search  = array( '/<a(.*)href=\"(.*)'.$search.'(.*)\"(.*)>/uU', '/'.$search.'/uU' );
      $replace = array( '<a$1href="$2'.$replace.'$3"$4>', $replace );
    }
    return $this->replace_all($search,$replace, $this->field_types, true );
	}

}

?>
