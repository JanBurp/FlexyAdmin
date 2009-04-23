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

class order extends Model {

	var $table;
	var $pk;
	var $order;

	function order($table="") {
		parent::Model();
		$this->init($table);
	}

	function init($table="") {
		$this->set_table($table);
		$this->pk=pk();
		$this->order=$this->config->item('ORDER_field_name');
	}

	function set_table($table="") {
		$this->table=$table;
	}

	function get_order($table,$id) {
		return $this->db->get_field($table,$this->order,$id);
	}

	function get_bottom($table) {
		$this->db->select($this->pk);
		$query = $this->db->get($table);
		return $query->num_rows();
	}

	/**
		* Reset order, gives them a new fresh order, but ordering stays same
		*/
	function reset($table,$shift=0) {
		$this->db->order_by($this->order);
		$this->db->select($this->pk);
		$result=$this->db->get_result($table);
		$ids=array();
		foreach($result as $id=>$v) $ids[]=$id;
		$this->set_all($table,$ids,$shift);
	}

	/**
		* Gives all items a new order according to the order of $ids array
		*/
	function set_all($table,$ids,$shift=0) {
		$n=1+$shift;
		foreach($ids as $id) {
			$this->db->where($this->pk,$id);
			$this->db->update($table, array($this->order => $n++ ));
		}
	}

	/**
		* Get ordernr for a new item after parent, and shift all others up
		*/
	function get_order_after_parent($table,$parent_id) {
		$parent_order=$this->get_order($table,$parent_id);
		$this->shift_up_from($table,$parent_order);
		return $parent_order+1;
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
	function set($table,$id,$new="") {
		$old=$this->get_order($table,$id);
		if ($old!=$new) {
			// Set new order to given id
			$this->db->where($this->pk,$id);
			$this->db->update($table, array($this->order => $new ));
		}
		// give the rest a new order (skip where the new order is reached), except the new one.
		$this->db->where($this->pk." !=",$id); // except new one
		$this->db->order_by($this->order);
		$this->db->select($this->pk);
		$query=$this->db->get($table);
		$n=1;
		foreach($query->result_array() as $res) {
			$this->db->where($this->pk,$res[$this->pk]);
			if ($n==$new) $n++;									// skip new set order
			$this->db->update($table, array($this->order => $n++ ));
		}
		return $new;
	}

	/**
		* Move given item to new place in order (give "direction" or new order nr)
		*/
	function set_to($table,$id,$newOrder) {
		$out="";
		switch($newOrder) {
			case "top" 		:
				$out=$this->to_top($table,$id);
				break;
			case "bottom"	:
				$out=$this->to_bottom($table,$id);
				break;
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
	function to_top($table,$id) {
		return $this->set($table,$id,0);
	}
	function to_bottom($table,$id) {
		$bottom=$this->get_bottom($table);
		return $this->set($table,$id,$bottom);
	}
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

	function shift($table,$shift) {
		$this->reset($table,$shift);
	}
	function shift_up($table,$up=1) {
		$this->shift($table,$up);
	}
	function shift_down($table,$down=1) {
		$this->shift($table,-$down);
	}
	
	function shift_up_from($table,$from) {
		$this->db->order_by($this->order);
		$this->db->where($this->order." >","$from");
		$this->db->select($this->pk);
		$result=$this->db->get_result($table);
		$ids=array();
		foreach($result as $id=>$v) $ids[]=$id;
		$this->set_all($table,$ids,$from+1);
	}



}

?>
