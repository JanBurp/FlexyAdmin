<?
/**
 * FlexyAdmin V1
 *
 * cfg.php Created on 15-okt-2008
 *
 * @author Jan den Besten
 */


 /**
  * Class order extends model
  *
  * This class handles all re-ordering of tables in database
  *
  *TODO: multi level ordering (tree)
  *
  */

class order extends CI_Model {

	var $table;
	var $order;

	function __construct($table="") {
		parent::__construct();
		$this->init($table);
	}

	function init($table="") {
		$this->set_table($table);
		$this->order=$this->config->item('ORDER_field_name');
	}

	function set_table($table="") {
		$this->table=$table;
	}

	function is_a_tree($table) {
		$fields=$this->db->list_fields($table);
		return (in_array("self_parent",$fields));
	}

	function get_order($table,$id) {
		return $this->db->get_field($table,$this->order,$id);
	}

	function get_parent($table,$id) {
		return $this->db->get_field($table,"self_parent",$id);
	}

	function get_bottom($table) {
		$this->db->select(PRIMARY_KEY);
		$query = $this->db->get($table);
		return $query->num_rows();
	}
	
	function get_next_order($table,$parent="") {
		if (!empty($parent)) $this->db->where("self_parent",$parent);
		$this->db->select("order");
		$this->db->order_by("order DESC");
		$row=$this->db->get_row($table);
		return $row["order"]+1;
	} 

	/**
		* Reset order, gives them a new fresh order, but ordering stays same
		*/
	function reset($table,$shift=0) {
		if ($this->is_a_tree($table))	$this->db->order_as_tree();
		$this->db->select(PRIMARY_KEY);
		$result=$this->db->get_result($table);
		$ids=array();
		foreach($result as $id=>$v) $ids[]=$id;
		$this->set_all($table,$ids,$shift);
	}
	function shift_up($table,$parent=0,$up=1,$from=0) {
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
	}
	/**
		* Gives all items a new order according to the order of $ids array
		*/
	function set_all($table,$ids,$shift=0,$from=0) {
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
	}

	/**
		* Inserts a new item after id
		*/
	// function insert_after_id($table,$id,$before_id) {
	// 	$before_order=$this->get_order($table,$before_id);
	// 	return $this->set($table,$id,$before_order+1);
	// }

	/**
		* Move given item to new place (give the new order nr)
		*/
	function set($table,$id,$new) {
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
		* Move given item to new place in order (give "direction" or new order nr)
		*/
	function set_to($table,$id,$newOrder) {
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
	// function to_top($table,$id) {
	// 	return $this->set($table,$id,0);
	// }
	// function to_bottom($table,$id) {
	// 	$bottom=$this->get_bottom($table);
	// 	return $this->set($table,$id,$bottom);
	// }
	function up($table,$id) {
		$o=$this->get_order($table,$id);
		$o--;
		if ($o<1) $o=1;
		return $this->set($table,$id,$o);
	}
	function down($table,$id) {
		$o=$this->get_order($table,$id);
		$o++;
		$bottom=$this->get_bottom($table);
		if ($o>$bottom) $o=$bottom;
		return $this->set($table,$id,$o);
	}

}

?>
