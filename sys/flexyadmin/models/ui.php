<?
/**
 * FlexyAdmin V1
 *
 * cfg.php Created on 15-okt-2008
 *
 * @author Jan den Besten
 */


 /**
  * Class ui extends model
  *
  * This class handles all the UI-names and help for tables and fields
  *
  */

class ui extends CI_Model {

	var $uiNames = array();
	var $help = array();

	function __construct() 	{
		parent::__construct();
		$this->load();
    $this->lang->load('field_names');
	}

	/**
	 * function load()
	 *
	 * Loads all ui names from cfg tables into $uiNames array
	 *
	 */
	function load() {
		log_('info',"ui_names: loading");
		
		$ui=$this->cfg->get('cfg_ui');
		// fill ui data
		foreach ($ui as $ui_row) {
			$this->_load_row($ui_row);
		}
	}

	private function _load_row($row) {
		if (!empty($row['path']))
			$key=$row['path'];
		elseif (!empty($row['table']))
			$key=$row['table'];
		elseif (isset($row['field_field']))
			$key=$row['field_field'];
			
		if (isset($key)) {
			$lang='';
			if (isset($this->language))
        $lang=$this->language;
      elseif (isset($this->site['language']))
        $lang=$this->site['language'];
			if (isset($row['str_title_'.$lang])) {
				$title=$row['str_title_'.$lang];
				if (!empty($title))	$this->uiNames[$key]=$row['str_title_'.$lang];
			}
			if (isset($row['txt_help_'.$lang])) {
				$help=$row['txt_help_'.$lang];
				if (!empty($help))	$this->help[$key]=$row['txt_help_'.$lang];
			}
		}
	}

	function get($name,$table="",$create=TRUE) {
		if (!is_array($name)) {
			$out=el($name,$this->uiNames,"");
			if (empty($out) and !empty($table)) $out=el($table.".".$name,$this->uiNames,"");
      if (empty($out)) $out=$this->get_standard($name);
			if (empty($out) and $create) $out=$this->create($name);
		}
		else {
			$out=array();
			foreach($name as $n=>$v) {
				$out[$n]=$this->get($v,$table);
			}
		}
		return $out;
	}

  function get_standard($name) {
    return lang($name);
  }

	function get_help($name='',$table='') {
		if (empty($name)) {
			$tableHelp=array();
			$fieldHelp=array();
			foreach ($this->help as $key => $value) {
				if (!has_string('.',$key))
					$tableHelp[$key]=$value;
				else
					$fieldHelp[$key]=$value;
			}
			//
			$help='';
			foreach ($tableHelp as $key => $value) {
				if ($this->user->has_rights($key)) {
					$help.="<h2>".$this->get($key)."</h2>".$value;
					$fields=filter_by_key($fieldHelp,$key);
					if (!empty($fields)) {
						foreach ($fields as $fkey => $fvalue) {
              // $fkey=remove_prefix($fkey,'.');
							$help.=div('helpField')."<h3>".$this->get($key)." - ".$this->get($fkey)."</h3>".$fvalue._div();
						}
					}
					$help.="<p>&nbsp;</p>";
				}
			}
			$out=$help;
		}
		elseif (!is_array($name)) {
			$out=el($name,$this->help,'');
			if (empty($out) and !empty($table)) $out=el($table.".".$name,$this->help,"");
		}
		else {
			$out=array();
			foreach($name as $n=>$v) {
				$out[$n]=$this->get_help($v,$table);
			}
		}
		return $out;
	}

	function create($s) {
		if (is_foreign_key($s)) {
			return $this->get(foreign_table_from_key($s));
		}
		$p=get_prefix($s);
    $s=remove_prefix($s,'.');
		$s=remove_prefix($s);
		$s=str_replace("__","-",$s);
		$s=str_replace("_"," ",$s);
		$s=ucwords($s);
		// if ($p=='medias' and substr($s,strlen($s))!='s') $s.="s";
		return $s;
	}
	
	function replace_ui_names($s) {
    $s=explode(' ',$s);
    foreach ($s as $key => $word) {
      // only replace if word has a undescore and no dot
      if (has_string('_',$word) and !has_string('.',$word)) {
        $newword=$this->get($word);
        if (!empty($newword)) $s[$key]=$newword;
      }
    }
    $s=implode(' ',$s);
		return $s;
	}
	
}

?>
