<?php 

/**
 * Verzorgt het (her)sorteren van items in een tabel (ook met boomstructuur)
 * 
 * $Author$
 * $Date$
 * $Revision$
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class order extends CI_Model {

  private $table;
  private $order;

	/**
	 * @ignore
	 */
  public function __construct($table="") {
		parent::__construct();
		$this->initialize(array('table'=>$table));
	}

  /**
   * @ignore
   */
	public function initialize($settings=array()) {
		$this->set_table(el('table',$settings,''));
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
  private function _get_order($table,$id) {
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
	private function _get_parent($table,$id) {
		return $this->db->get_field($table,"self_parent",$id);
	}

  /**
   * Pakt eind (laatste order)
   *
   * @param string $table
   * @return mixed
   * @author Jan den Besten
   * @ignore
   */
  private function _get_bottom($table) {
    $this->db->select(PRIMARY_KEY);
    $query = $this->db->get($table);
    return $query->num_rows() - 1;
  }
  
  
	/**
	 * Geeft volgende order (eventueel van bepaalde branch)
	 * (Wordt gebruikt in Plugin_automenu en in _Crud)
	 *
	 * @param string $table
	 * @param int $parent[''] 
	 * @return int De volgende order
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
   * @param int $from[0], eventueel kan alles worden opgeschoven
   * @return int Aantal verschoven items, meestal alle items;
   * @author Jan den Besten
   */
	public function reset($table,$from=0,$old=FALSE) {
    if ($this->is_a_tree($table)) {
      if ($old) $this->db->order_as_tree();
      $this->db->select('self_parent');
    }
    $this->db->select('order');
		$result=$this->db->get_result($table);
		$ids=array_keys($result);
		$this->set_all($table,$ids,$from);
    return count($ids);
	}
  
  /**
   * Reset children van een tree (als er aanpassingen er een child verwijderd of toegevoegd wordt bijvoorbeeld)
   * (Wordt gebruikt in Crud)
   *
   * @param string $table 
   * @param int $parent[0]
   * @return int Aantal verschoven items;
   * @author Jan den Besten
   */
  public function reset_tree($table,$parent=0) {
    if ($parent!=0 and $this->is_a_tree($table)) {
      $this->db->where("self_parent",$parent);
      $this->db->select(PRIMARY_KEY);
      $result=$this->db->get_result($table);
      $from_id=current($result);
      $from_id=$from_id[PRIMARY_KEY];
      $from_order=$this->_get_order($table,$from_id);
      $ids=array_keys($result);
      $this->set_all($table,$ids,$from_order);
      $count=count($ids);
    }
    else {
      $count=$this->reset($table);
    }
    return $count;
  }
  

	/**
	 * Geeft alle items een nieuwe volgorde zoals meegegeven in de $ids array
	 *
	 * @param string $table
	 * @param array $ids Array met nieuwe volgorde
	 * @param int $from[0] Begin te tellen vanaf... ??
	 * @return object $this;
	 * @author Jan den Besten
	 */
  public function set_all($table,$ids,$from=0) {
    $order=$from;
		foreach($ids as $id) {
			$this->db->where(PRIMARY_KEY,$id);
			$this->db->update($table, array($this->order => $order++ ));
		}
    return $this;
	}


  /**
   * Verplaatst item naar nieuwe plek, en geeft de nieuwe order terug
   *
   * @param string $table 
   * @param int $id 
   * @param int $new Nieuwe plek (order)
   * @return int $new
   * @author Jan den Besten
   */
	public function set($table,$id,$new) {
		$old=$this->_get_order($table,$id);
		if ($old!==$new) {
			// Set new order to given id
			$this->db->where(PRIMARY_KEY,$id);
      $this->db->set('order',$new);
			$this->db->update($table);
		}
    if ($new>$old) {
      // Schuif alle tussenliggende terug
      $sql="UPDATE `$table` SET `order`=`order`-1 WHERE `order`>'$old' AND `order`<='$new' AND `id` != '$id'";
      $this->db->query($sql);
    }
    if ($new<$old) {
      // Schuif alle tussenliggende verder
      $sql="UPDATE `$table` SET `order`=`order`+1 WHERE `order`>='$new' AND `order`<'$old' AND `id` != '$id'";
      $this->db->query($sql);
    }
		return $new;
	}

  /**
   * Verschuift item in een meegegeven richting en geeft nieuwe order terug
   *
   * @param string $table 
   * @param int $id 
   * @param string $newOrder ['up'|'down','top'|'bottom]
   * @return int
   * @author Jan den Besten
   */
  public function move_to($table,$id,$newOrder) {
		$new=false;
		switch($newOrder) {
      case "top"     :
        $new=$this->to_top($table,$id);
        break;
      case "bottom"  :
        $new=$this->to_bottom($table,$id);
        break;
			case "up"			:
				$new=$this->up($table,$id);
				break;
			case "down"		:
				$new=$this->down($table,$id);
				break;
			default:
				$new=$this->set($table,$id,$newOrder);
				break;
		}
		return $new;
	}
	
  /**
   * Verplaatst item helemaal naar boven
   *
   * @param string $table 
   * @param int $id 
   * @return int
   * @author Jan den Besten
   */
  public function to_top($table,$id) {
    return $this->set($table,$id,0);
  }

  /**
   * Verplaatst item helemaal naar onderen
   *
   * @param string $table 
   * @param int $id 
   * @return int
   * @author Jan den Besten
   */
  public function to_bottom($table,$id) {
    $bottom=$this->_get_bottom($table);
    return $this->set($table,$id,$bottom);
  }
  
  /**
   * Verplaats item naar boven en geeft nieuwe order terug
   *
   * @param string $table 
   * @param int $id 
   * @return int
   * @author Jan den Besten
   */
	public function up($table,$id) {
		$o=$this->_get_order($table,$id);
		$o--;
		if ($o<1) $o=0;
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
		$o=$this->_get_order($table,$id);
		$o++;
		$bottom=$this->_get_bottom($table);
		if ($o>$bottom) $o=$bottom;
		return $this->set($table,$id,$o);
	}

}

?>
