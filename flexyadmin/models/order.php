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
	var $orderDec;

	function order($table="") {
		parent::Model();
		$this->init($table);
	}

	function init($table="") {
		$this->set_table($table);
		$this->set_decimals();
		$this->pk=pk();
		$this->order=$this->config->item('ORDER_field_name');
	}

	function set_table($table="") {
		$this->table=$table;
	}

	function set_decimals($dec="") {
		if (empty($dec)) {
			$this->orderDec=$this->config->item('ORDER_decimals');
		}
		else {
			$this->orderDec=$dec;
		}
	}

	function get_order($table,$id) {
		$this->db->select($this->order);
		$this->db->where($this->pk,$id);
		$query=$this->db->get($table);
		$row=$query->row_array();
		return $row[$this->order];
	}

	function _val($n) {
		return sprintf("%0".$this->orderDec."d",strval($n));
	}

	function set_order($table,$ids) {
		$n=0;
		foreach($ids as $id) {
			$this->db->where($this->pk,$id);
			$this->db->update($table, array($this->order => $this->_val($n++) ));
		}
	}

	function bottom($table) {
		$this->db->select($this->pk);
		$query = $this->db->get($table);
		return $query->num_rows();
	}

	function reorder($table="",$id="",$newOrder="") {
		if (empty($table)) $table=$this->table;
		if (!$this->db->field_exists($this->order,$table)) {
			return false;
		}
		else {
			if (!empty($id) and !empty($newOrder)) {
				return $this->new_order($table,$id,$newOrder);
			}
			else {
				$this->give_order($table);
				return true;
			}
		}
	}

	function give_order($table,$id="",$new="") {
		$reset=TRUE;
		if (!empty($id) and intval($new)>=0) {
			$o=$this->get_order($table,$id);
			if ($o!=$new) {
				$reset=FALSE;
				// Set new order to given id
				$this->db->where($this->pk,$id);
				$this->db->update($table, array($this->order => $this->_val($new) ));
				// give the rest a new order (skip where the new order is reached), except the new one.
				$this->db->where($this->pk." !=",$id);
				$this->db->order_by($this->order);
				$this->db->select($this->pk);
				$query=$this->db->get($table);
				$n=0;
				foreach($query->result_array() as $res) {
					$this->db->where($this->pk,$res[$this->pk]);
					if ($n==$new) $n++;
					$this->db->update($table, array($this->order => $this->_val($n++) ));
				}
			}
		}

		if ($reset) {
			$this->db->order_by($this->order);
			$this->db->select($this->pk);
			$query=$this->db->get($table);
			$n=0;
			foreach($query->result_array() as $res) {
				$this->db->where($this->pk,$res[$this->pk]);
				$this->db->update($table, array($this->order => $this->_val($n++) ));
			}
		}
		return TRUE;
	}

	function new_order($table,$id,$newOrder) {
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
		return $this->give_order($table,$id,0);
	}

	function to_bottom($table,$id) {
		$bottom=$this->bottom($table);
		return $this->give_order($table,$id,$bottom);
	}

	function up($table,$id) {
		$o=$this->get_order($table,$id);
		$o--;
		if ($o<0) $o=0;
		return $this->give_order($table,$id,$o);
	}

	function down($table,$id) {
		$o=$this->get_order($table,$id);
		$o++;
		$bottom=$this->bottom($table);
		if ($o>$bottom) $o=$bottom;
		return $this->give_order($table,$id,$o);
	}

	function set($table,$id,$new) {
		return $this->give_order($table,$id,$new);
	}

}

?>
