<?

/**
 * Verzorgt het (her)sorteren van items in een tabel (ook met boomstructuur)
 *
 * @package default
 * @author Jan den Besten
 */
class order extends CI_Model {

  private $table;
  private $order;

	/**
	 * @ignore
	 */
  public function __construct($table="") {
		parent::__construct();
		$this->init($table);
	}

  /**
   * @ignore
   */
	public function init($table="") {
		$this->set_table($table);
		$this->order=$this->config->item('ORDER_field_name');
    return $this;
	}

	/**
	 * Zet de tabel
	 *
	 * @param string $table 
	 * @return object $this;
	 * @author Jan den Besten
	 */
  public function set_table($table="") {
		$this->table=$table;
    return $this;
	}

  /**
   * Test of tabel een boomstructuur heeft
   *
   * @param string $table 
   * @return bool
   * @author Jan den Besten
   */
	public function is_a_tree($table) {
		$fields=$this->db->list_fields($table);
		return (in_array("self_parent",$fields));
	}

	/**
	 * Pakt inhoud van order veld
	 *
	 * @param string $table 
	 * @param int $id 
	 * @return int
	 * @author Jan den Besten
	 * @ignore
	 */
  private function get_order($table,$id) {
		return $this->db->get_field($table,$this->order,$id);
	}

  /**
   * Pakt parent
   *
   * @param string $table 
   * @param int $id 
   * @return init
   * @author Jan den Besten
   * @ignore
   */
	private function get_parent($table,$id) {
		return $this->db->get_field($table,"self_parent",$id);
	}

  /**
   * Pakt eind
   *
   * @param string $table 
   * @return mixed
   * @author Jan den Besten
   * @ignore
   */
	private function get_bottom($table) {
		$this->db->select(PRIMARY_KEY);
		$query = $this->db->get($table);
		return $query->num_rows();
	}
  
  
	/**
	 * Geeft volgende order
	 *
	 * @param string $table
	 * @param int $parent[''] 
	 * @return int
	 * @author Jan den Besten
	 */
	public function get_next_order($table,$parent="") {
		if (!empty($parent)) $this->db->where("self_parent",$parent);
		$this->db->select("order");
		$this->db->order_by("order DESC");
		$row=$this->db->get_row($table);
		return $row["order"]+1;
	} 

  /**
   * Reset volgorde nummering. Volgorde blijft hetzelde, alleen de nummering wordt ververst
   *
   * @param string $table 
   * @param int $shift[0]
   * @return object $this;
   * @author Jan den Besten
   */
	public function reset($table,$shift=0) {
		if ($this->is_a_tree($table))	$this->db->order_as_tree();
		$this->db->select(PRIMARY_KEY);
		$result=$this->db->get_result($table);
		$ids=array();
		foreach($result as $id=>$v) $ids[]=$id;
		$this->set_all($table,$ids,$shift);
    return $this;
	}
  
  /**
   * Schuift item op naar boven
   *
   * @param string $table 
   * @param int $parent[0]
   * @param int $up[1]
   * @param int $from[0] 
   * @return object $this;
   * @author Jan den Besten
   */
	public function shift_up($table,$parent=0,$up=1,$from=0) {
		if ($this->is_a_tree($table)) {
			$this->db->where("self_parent",$parent);
			if ($from!=0) $this->db->where("order >",$from);
			$this->db->select(PRIMARY_KEY);
			$result=$this->db->get_result($table);
			$ids=array();
			foreach($result as $id=>$v) $ids[]=$id;
			$this->set_all($table,$ids,$up,$from);
		}
		else
			$this->reset($table,$up);
    return $this;
	}
  

	/**
	 * Geeft alle items een nieuwe volgorde zoals meegegeven in de $ids array
	 *
	 * @param string $table
	 * @param array $ids Array met nieuwe volgorde
	 * @param int $shift[0]
	 * @param int $from[0]
	 * @return object $this;
	 * @author Jan den Besten
	 */
  public function set_all($table,$ids,$shift=0,$from=0) {
		$isTree=$this->is_a_tree($table);
		$orders=array();
		foreach($ids as $id) {
			if ($isTree)
				$parent=$this->db->get_field($table,"self_parent",$id);
			else
				$parent=0;
			if (isset($orders[$parent]))
				$orders[$parent]++;
			else
				$orders[$parent]=$from+$shift;
			$this->db->where(PRIMARY_KEY,$id);
			$this->db->update($table, array($this->order => $orders[$parent] ));
		}
    return $this;
	}


  /**
   * Verplaatst item naar nieuwe plek, en geeft de nieuwe order terug
   *
   * @param string $table 
   * @param int $id 
   * @param int $new 
   * @return int $new
   * @author Jan den Besten
   */
	public function set($table,$id,$new) {
		$old=$this->get_order($table,$id);
		if ($old!=$new) {
			// Set new order to given id
			$this->db->where(PRIMARY_KEY,$id);
			$this->db->update($table, array($this->order => $new ));
		}
		// give the rest a new order (skip where the new order is reached), except the new one.
		if ($this->is_a_tree($table)) {
			$parentId=$this->get_parent($table,$id);
			$this->db->where("self_parent",$parentId);
		}
		$this->db->where(PRIMARY_KEY." !=",$id); // except new one
		$this->db->order_by($this->order);
		$this->db->select(PRIMARY_KEY);
		$query=$this->db->get($table);
		$n=1;
		foreach($query->result_array() as $res) {
			$this->db->where(PRIMARY_KEY,$res[PRIMARY_KEY]);
			if ($n==$new) $n++;									// skip new set order
			$this->db->update($table, array($this->order => $n++ ));
		}
		$query->free_result();
		return $new;
	}

  /**
   * Verschuift item in een meegegeven richting en geeft nieuwe order terug
   *
   * @param string $table 
   * @param int $id 
   * @param string $newOrder ['up'|'down']
   * @return int
   * @author Jan den Besten
   */
  public function set_to($table,$id,$newOrder) {
		$out="";
		switch($newOrder) {
			// case "top" 		:
			// 	$out=$this->to_top($table,$id);
			// 	break;
			// case "bottom"	:
			// 	$out=$this->to_bottom($table,$id);
			// 	break;
			case "up"			:
				$out=$this->up($table,$id);
				break;
			case "down"		:
				$out=$this->down($table,$id);
				break;
			default:
				$out=$this->set($table,$id,$newOrder);
				break;
		}
		return $out;
	}
	
  // public function to_top($table,$id) {
	// 	return $this->set($table,$id,0);
	// }
	// public function to_bottom($table,$id) {
	// 	$bottom=$this->get_bottom($table);
	// 	return $this->set($table,$id,$bottom);
	// }
  
  /**
   * Verplaats item naar boven en geeft nieuwe order terug
   *
   * @param string $table 
   * @param int $id 
   * @return int
   * @author Jan den Besten
   */
	public function up($table,$id) {
		$o=$this->get_order($table,$id);
		$o--;
		if ($o<1) $o=1;
		return $this->set($table,$id,$o);
	}
  
  /**
   * Verplaats item naar beneden en geeft nieuwe order terug
   *
   * @param string $table 
   * @param int $id 
   * @return int
   * @author Jan den Besten
   */
	public function down($table,$id) {
		$o=$this->get_order($table,$id);
		$o++;
		$bottom=$this->get_bottom($table);
		if ($o>$bottom) $o=$bottom;
		return $this->set($table,$id,$o);
	}

}

?>
