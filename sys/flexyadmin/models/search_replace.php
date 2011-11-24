<?

// ## TODO: also for images/files...

class Search_replace Extends CI_Model {

	var $table_types = array('tbl','res');
	var $field_types = array('txt');
	var $langRegex = '';
	
	
	
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
				$res=$this->db->update($table,array($field=>$newtxt),"id = $id");
				$result[$id]=$id;
			}
		}

		$query->free_result();
		return $result;
	}



}

?>
