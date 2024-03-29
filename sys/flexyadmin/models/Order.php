<?php 

/** \ingroup models
 * Verzorgt het (her)sorteren van items in een tabel (ook met boomstructuur)
 * 
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Order extends CI_Model {

  private $table;
  private $order;

  public function __construct($table="") {
		parent::__construct();
    $this->load->model('log_activity');
		$this->initialize(array('table'=>$table));
	}

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
    return $this->data->table($table)->field_exists('self_parent');
  }

	/**
	 * Pakt inhoud van order veld
	 *
	 * @param string $table 
	 * @param int $id 
	 * @return int
	 * @author Jan den Besten
		 */
  private function _get_order($table,$id) {
		return $this->data->table($table)->where($id)->get_field($this->order);
	}

  /**
   * Pakt parent
   *
   * @param string $table 
   * @param int $id 
   * @return init
   * @author Jan den Besten
   */
	private function _get_parent($table,$id) {
		return $this->data->table($table)->where($id)->get_field('self_parent');
	}

  /**
   * Pakt eind (laatste order)
   *
   * @param string $table
   * @return mixed
   * @author Jan den Besten
   */
  private function _get_bottom($table) {
    $this->db->select(PRIMARY_KEY);
    $query = $this->db->get($table);
    return $query->num_rows() - 1;
  }
  
  
	/**
	 * Geeft volgende order (eventueel van bepaalde branch)
	 * Als in een branch, dan worden alle andere items opgeschoven.
	 * (Wordt gebruikt in Plugin_automenu en in DataCore)
	 *
	 * @param string $table
	 * @param int $parent[''] 
	 * @return int De volgende order
	 * @author Jan den Besten
	 */
	public function get_next_order($table,$parent="") {
    $sql = "SELECT `order` FROM `$table`";
    if (!empty($parent)) $sql.=" WHERE `self_parent`=$parent";
    $sql.=" ORDER BY `order` DESC";
    $query = $this->db->query($sql);
    $lastrow = $query->row_array();
    if (!$lastrow) {
      // Dit is het eerste item
      if ($parent=='') {
        $next = 0;
        return $next;
      }
      else {
        // Geen kinderen in de opgevraagde tree, dan is de order hetzelfde als de parent zelf
        $sql = "SELECT `order` FROM `$table` WHERE `id`=$parent";
        $query = $this->db->query($sql);
        $lastrow = $query->row_array();
      }
    }
    $next = $lastrow['order'] + 1;
    // Als in een tree, verschuif alles met hogere volgorde dan die van de tree op
    if (!empty($parent)) {
      $sql="UPDATE `$table` SET `order`=`order`+1 WHERE `order`>='$next'";
      $this->db->query($sql);
      $this->log_activity->database( $this->db->last_query(), $table, 'order' );
    }
		return $next;
	}
  

  /**
   * Reset volgorde nummering.
   * Neemt parents als basis, verder blijft volgorde hetzelfde.
   * De nummering wordt ververst
   *
   * @param string $table 
   * @param int $from default=0, eventueel kan alles worden opgeschoven
   * @return int Aantal geresette items
   * @author Jan den Besten
   */
	public function reset($table,$from=0) {
    $this->data->table($table);

    $has_date = $this->data->field_exists('dat_date');

    // Eenvoudig, geen self_parent
    if ( !$this->is_a_tree($table) ) {
      $result = $this->data->select('order')->order_by('order')->get_result();
      $ids = array_keys($result);
      $this->set_all($table,$ids,$from);
      return count($ids);
    }

    // Complexer: met self_parent
    // 1) Reset verwijzingen naar zichzelf
    $this->data->update( array('self_parent'=>0), '`self_parent`=`id`' );

    // 2) Groupeer per parent
    // 3) Sub results per parent
    $parents = $this->data->select('self_parent,uri')->order_by('order')->set_result_key('self_parent')->get_result();
    foreach ($parents as $parent_id => $items) {
      $this->data->select('order,self_parent,uri')->where('self_parent',$parent_id);
      if ($has_date)
        $this->data->order_by('order,dat_date DESC');
      else
        $this->data->order_by('order');
      $parents[$parent_id] = $this->data->get_result();
    }
    if (isset($parents[0])) {
      $merged = $parents[0];
      unset($parents[0]);
    }

    // 4) Voeg ze samen in nieuwe volgorde
    $depth = 10;
    while (count($parents)>0 and $depth>0 ) {
      $offset = 0;
      foreach ($merged as $parent_id => $item) {
        $offset++;
        $id = $item['id'];
        if (isset($parents[$id])) {
          array_splice($merged,$offset,0,$parents[$id]);
          $offset += count($parents[$id]);
          unset($parents[$id]);
        }
      }
    }

    // Zijn er nog 'verloren' kinderen?
    if (count($parents)>0) {
      foreach ($parents as $parent_id => $items) {
        foreach ($items as $key=>$item) {
          // Reset parent van verloren kind
          if ($item['self_parent']==$parent_id) $items[$key]['self_parent'] = 0;
        }
        $merged = array_merge($merged,$items);
        unset($parents[$parent_id]);
      }
    }
    // re-id & reorder
    $order = $from;
    $result = array();
    if ( isset($merged) && is_array($merged) ) {
      foreach ($merged as $item) {
        $result[$item['id']] = $item;
        $result[$item['id']]['order'] = $order;
        $order++;
      }
    }

    // Update all
    foreach ($result as $id => $row) {
      $this->data->table($table)->set($row)->where($id)->update();
    }
    return count($result);
	}
  

	/**
	 * Geeft de meegegeven items een nieuwe volgorde zoals meegegeven in de $ids array
	 *
	 * @param string $table
	 * @param array $ids Array met nieuwe volgorde
	 * @param int $from default=0 Begin te tellen vanaf deze order (voor als maar een deel van de items in een tabel worden meegegeven)
	 * @return array $items, geeft een array terug in met voor elke row dit formaat: 'id_van_row' => 'nieuwe order' 
	 * @author Jan den Besten
	 */
  public function set_all($table,$ids,$from=0) {
    $return=array();
    $order=$from;
    $log = array(
      'query' =>'',
      'table' => $table,
      'id'    => implode(',',$ids),
    );
		foreach($ids as $id) {
      $return[]=array('id'=>$id,'order'=>$order);
      $this->data->table($table);
			$this->data->where(PRIMARY_KEY,$id);
      $this->data->set( $this->order,$order++);
			$this->data->update();
      $log['query'] .= $this->data->last_query().';'.PHP_EOL.PHP_EOL;
		}
    if ($log['query']) {
      $this->log_activity->database( $log['query'], $log['table'], $log['id'] );
    }
    return $return;
	}


  /**
   * Verplaatst item naar nieuwe plek, en geeft de nieuwe order terug
   * Test ook of er andere items mee moeten worden verplaatst (kinderen)
   *
   * @param string $table
   * @param int $id
   * @param int $new Nieuwe plek (order)
   * @return int $new
   * @author Jan den Besten
   */
  public function set( $table,$id,$new ) {
    $is_tree=$this->is_a_tree($table);
    
    // Wat is de huidige order?
    $old=(int)$this->_get_order($table,$id);
    // Is dat hetzelfde, dan hoeft er niets te gebeuren
    if ($old===$new) return $new;
    
    $moved_ids=array($id);

    // Neem kinderen mee...
    if ($is_tree) {
      $children_ids = $this->_get_children_ids( $table, $id, $old );
      $moved_ids=array_merge($moved_ids,$children_ids);
    }

    $log = array(
      'query' =>'',
      'table' => $table,
      'id'    => $id,
    );

    // Pas order in item (en kinderen) aan
    $order = $new;
    foreach ($moved_ids as $move_id) {
      $this->data->table($table);
      $this->data->set( 'order', $order );
      $this->data->where( PRIMARY_KEY, $move_id);
      $this->data->update();
      $order++;
      $log['query'] .= $this->data->last_query().';'.PHP_EOL.PHP_EOL;
    }
    $order--;
    $shifted_count = count($moved_ids);

    // Alles opschuiven
    if ($new>$old) {
      // Schuif alle tussenliggende terug
      $sql="UPDATE `$table` SET `order`=`order`-".$shifted_count." WHERE `order`>'$old' AND `order`<='".$order."' AND `id` NOT IN(".implode(',',$moved_ids).")";
      $this->db->query($sql);
      $log['query'] .= $this->db->last_query().';'.PHP_EOL.PHP_EOL;
    }
    if ($new<$old) {
      // Schuif alle tussenliggende verder
      $sql="UPDATE `$table` SET `order`=`order`+".$shifted_count." WHERE `order`>='$new' AND `order`<'$old' AND `id` NOT IN(".implode(',',$moved_ids).")";
      $this->db->query($sql);
      $log['query'] .= $this->db->last_query().';'.PHP_EOL.PHP_EOL;
    }
    // trace_($sql);
    
    // Als order>0 dan moet de parent misschien aangepast worden: dan neemt die de parent van het item erna over
    $parent = 0;
    if ($is_tree and $new>0) {
      // item erna
      $this->data->table($table)
            ->select('id,order,self_parent')
            ->where( 'order >',$new)
            ->order_by( 'order' );
      if ($children_ids) $this->data->where_not_in('id',$children_ids);
      $next = $this->data->get_row();
      if ($next) $parent = $next['self_parent'];
    }
    // geef nieuw parent
    $this->data->set('self_parent',$parent);
    $this->data->where(PRIMARY_KEY,$id);
    $this->data->update();
    $log['query'] .= $this->data->last_query().';'.PHP_EOL.PHP_EOL;
    
    if ($log['query']) {
      $this->log_activity->add('order', $log['query'], $log['table'], $log['id'] );
    }
    return $new;
  }
  
  /**
   * Geef ids van de kinderen terug
   *
   * @param string $table
   * @param string $id de id van item
   * @param string $order de order van item 
   * @return array
   * @author Jan den Besten
   */
  private function _get_children_ids( $table, $id, $order ) {
    $children_ids = array();
    $children = $this->data->table($table)
                            ->select('id,self_parent,order')
                            ->where( 'self_parent', $id )
                            ->get_result();
    if ($children) {
      $children_ids = array_keys($children);
    }   
    return $children_ids;
  }
  
  
  //   /**
  //    * Verschuift item in een meegegeven richting en geeft nieuwe order terug
  //    *
  //    * @param string $table
  //    * @param int $id
  //    * @param string $newOrder ['up'|'down','top'|'bottom]
  //    * @return int
  //    * @author Jan den Besten
  //    */
  //   public function move_to($table,$id,$newOrder) {
  //   $new=false;
  //   switch($newOrder) {
  //       case "top"     :
  //         $new=$this->to_top($table,$id);
  //         break;
  //       case "bottom"  :
  //         $new=$this->to_bottom($table,$id);
  //         break;
  //     case "up"      :
  //       $new=$this->up($table,$id);
  //       break;
  //     case "down"    :
  //       $new=$this->down($table,$id);
  //       break;
  //     default:
  //       $new=$this->set($table,$id,$newOrder);
  //       break;
  //   }
  //   return $new;
  // }
  //
  //   /**
  //    * Verplaatst item helemaal naar boven
  //    *
  //    * @param string $table
  //    * @param int $id
  //    * @return int
  //    * @author Jan den Besten
  //    */
  //   public function to_top($table,$id) {
  //     return $this->set($table,$id,0);
  //   }
  //
  //   /**
  //    * Verplaatst item helemaal naar onderen
  //    *
  //    * @param string $table
  //    * @param int $id
  //    * @return int
  //    * @author Jan den Besten
  //    */
  //   public function to_bottom($table,$id) {
  //     $bottom=$this->_get_bottom($table);
  //     return $this->set($table,$id,$bottom);
  //   }
  //
  //   /**
  //    * Verplaats item naar boven en geeft nieuwe order terug
  //    *
  //    * @param string $table
  //    * @param int $id
  //    * @return int
  //    * @author Jan den Besten
  //    */
  // public function up($table,$id) {
  //   $o=$this->_get_order($table,$id);
  //     // Als al bovenaan dan hoeft er niets te gebeuren
  //     if ($o<=0) return 0;
  //   $o--;
  //   return $this->set($table,$id,$o);
  // }
  //
  //   /**
  //    * Verplaats item naar beneden en geeft nieuwe order terug
  //    *
  //    * @param string $table
  //    * @param int $id
  //    * @return int
  //    * @author Jan den Besten
  //    */
  // public function down($table,$id) {
  //   $o=$this->_get_order($table,$id);
  //   $o++;
  //   $bottom=$this->_get_bottom($table);
  //   if ($o>$bottom) $o=$bottom;
  //   return $this->set($table,$id,$o);
  // }

}

?>
