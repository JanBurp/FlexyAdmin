<?

/**
 * Verzorgt de naamgeving van tabellen en velden
 * 
 * Alle tabellen en velden kunnen in het admin deel een voor de gebruiker vriendelijkere naam krijgen, eventueel zelfs meertalig.
 * Die mooie namen kunnen samen met helpteksten ingesteld in de tabe: **cfg_ui**. Zie ook [Help Teksten]({Help-teksten})
 *
 * @package default
 * @author Jan den Besten
 */
 
class ui extends CI_Model {

  private $uiNames = array();
  private $help = array();


  /**
   * @ignore
   */
	public function __construct() 	{
		parent::__construct();
		$this->load();
    $this->lang->load('field_names');
	}

  /**
   * Laad alle ui-names in van de tabel **cfg_ui**
   *
   * @return object $this;
   * @author Jan den Besten
   */
	public function load() {
		log_('info',"ui_names: loading");
		
		$ui=$this->cfg->get('cfg_ui');
		// fill ui data
		foreach ($ui as $ui_row) {
			$this->_load_row($ui_row);
		}
    return $this;
	}

  /**
   * Laad rij 
   *
   * @param string $row 
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _load_row($row) {
		if (!empty($row['path']))
			$key=$row['path'];
		elseif (!empty($row['table']))
			$key=$row['table'];
		elseif (isset($row['field_field']))
			$key=$row['field_field'];
			
		if (isset($key)) {
			$lang='';
      if (isset($this->site)) {
        $lang=$this->site['language'];
      }
			elseif (isset($this->language)) {
        $lang=$this->language;
      }
      else {
        $lang=$this->config->item('language');
      }
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

  /**
   * Geeft een mooie UI-name terug
   * 
   * Hieronder de volgorde hoe een naam wordt gevonden/gemaakt:
   * 
   * - Uit **cfg_ui**
   * - Een standaard (in config bestand ingesteld)
   * - Maak er zelf iets moois van
   *
   * @param string $name Item (kan een tabel, veld of media-pad zijn)
   * @param string $table[''] Als je bij $name een veld geeft kun je hier een tabel specificeren
   * @param string $create[TRUE] Als niet gevonden in **cfg_ui** dan wordt bij TRUE eentje gemaakt
   * @return string
   * @author Jan den Besten
   */
	public function get($name,$table="",$create=TRUE) {
		if (!is_array($name)) {
      $out='';
      if (empty($out) and !empty($table)) $out=el("*.".$name,$this->uiNames,"");
			if (empty($out) and !empty($table)) $out=el($table.".".$name,$this->uiNames,"");
      if (empty($out)) $out=el($name,$this->uiNames,"");
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

  /**
   * Geef de standaard naam, als die bestaat
   *
   * @param string $name 
   * @return string
   * @author Jan den Besten
   */
  public function get_standard($name) {
    return lang($name);
  }

  /**
   * Geeft helptekst behorend bij dit item zoals het in **cfg_ui** staat
   *
   * @param string $name['']
   * @param string $table['']
   * @return string
   * @author Jan den Besten
   */
	public function get_help($name='',$table='') {
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
      $table=get_prefix($name,'.');
      if (!empty($table)) $name=remove_prefix($name,'.');
      $out='';
			if (empty($out) and !empty($table)) $out=el($table.".".$name,$this->help,"");
      if (empty($out)) $out=el("*.".$name,$this->help,"");
			if (empty($out)) $out=el($name,$this->help,'');
		}
		else {
			$out=array();
			foreach($name as $n=>$v) {
				$out[$n]=$this->get_help($v,$table);
			}
		}
		return $out;
	}

  /**
   * Maak een mooie naam
   * 
   * - Verwijder prefix
   * - Vervang __ door -
   * - Vervang _ door -
   * - Begin elk woord met een hoofdletter
   *
   * @param string $s 
   * @return string
   * @author Jan den Besten
   */
	public function create($s) {
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
	
  /**
   * Vervang in meegegeven tekst alle woorden (die gevonden worden en op z'n minste één _ in de naam hebben) door een mooie UI name
   *
   * @param string $s 
   * @return string
   * @author Jan den Besten
   */
	public function replace_ui_names($s) {
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
