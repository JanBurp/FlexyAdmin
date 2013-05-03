<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Met dit model kun je de basis database handelingen uitvoeren (CRUD)
 *
 * @package default
 * @author Jan den Besten
 * @todo read methods
 */
class Crud extends CI_Model {

	private $table;
	private $user_id;
	private $data;
	private $where;
	private $limit;
	private $offset;
	private $order;

	function __construct() {
		parent::__construct();
		$this->table='';
	}

	/**
	 * Stel tabel in waarvoor de acties gelden
	 *
	 * @param string $table : table name
	 * @return object $this
	 * @author Jan den Besten
	 */
	public function table($table='',$user_id=FALSE) {
		if (empty($table)) return FALSE;
		if (!$this->db->table_exists($table)) return FALSE;
		$this->table=$table;
		$this->_set_args();
		$this->user_id=$user_id;
		log_message('debug', 'Model: Crud; Method: table("'.$table.')"');
		return $this;
	}


	// args = array( where'=>array(), 'limit'=>int, 'offset'=>int, 'order'=>array() );
	// public function retrieve($args='') {
	// 	if (empty($this->table)) return FALSE;
	// 
	// 	$this->_set_args($args);
	// 	if (is_array($this->where)) {
	// 		$this->_set_order();
	// 		$this->_set_limit();
	// 		$this->db->where($this->where);
	// 		return $this->db->get($this->table);
	// 	}
	// 	return FALSE;
	// }



	/**
	 * Maakt item in database, inclusief many tables (join/rel)
	 * Zelfde als ->create()
	 *
	 * @param array $args : array( 'data'=>array() )
	 * @return int : id van inserted item
	 * @author Jan den Besten
	 */
	public function insert($args='') {
		if (empty($this->table)) return FALSE;
		$this->_set_args($args);
		return $this->_update_insert(TRUE);
	}
  
	/**
	 * Maakt item in database, inclusief many tables (join/rel)
	 * Zelfde als ->insert()
	 *
	 * @param array $args : array( 'data'=>array() )
	 * @return int : id van inserted item
	 * @author Jan den Besten
	 */
	public function create($args='') {
		return $this->insert($args);
	}


	/**
	 * Update item(s) in database, inclusief many tables (join/rel)
	 *
	 * @param array $args : array( 'where'=>array(), 'data'=>array() )
	 * @return bool : TRUE als gelukt
	 * @author Jan den Besten
	 */
	public function update($args='') {
		if (empty($this->table)) return FALSE;
		$this->_set_args($args);
		return $this->_update_insert(FALSE);
	}


	/**
	 * private _update_insert, does the actual updateing/inserting for create/update
	 * @author Jan den Besten
	 * @ignore
	 * @internal
	 */
	private function _update_insert($insert=FALSE) {
		$id=FALSE;
		if (is_array($this->where) && is_array($this->data)) {

			/**
			 * Set user (id) if needed
			 */
			if ($insert and isset($this->data['user'])) {
				$this->data['user']=$user_id;
			}

			/**
			 * Set new order if needed
			 */
			if (isset($this->data["order"]) and $insert) {
				$this->load->model('order','order_model');
				if (isset($this->data["self_parent"])) 
					$this->data["order"]=$this->order_model->get_next_order($this->table,$this->data["self_parent"]);
				else
					$this->data["order"]=$this->order_model->get_next_order($this->table);
			}

			/**
			 * Make sure all not given fields in data stays the same | #### kan dit niet gewoon weg??
			 */
			// $staticFields=$this->db->list_fields($this->table);
			// $staticFields=array_combine($staticFields,$staticFields);
			// unset($staticFields[PRIMARY_KEY]);
			// foreach($this->data as $name=>$value) {
			// 	unset($staticFields[$name]);
			// }
			// if (!empty($staticFields)) {
			// 	$this->db->select($staticFields);
			// 	$this->db->where(PRIMARY_KEY,$id);
			// 	$query=$this->db->get($this->table);
			// 	$staticData=$query->row_array();
			// 	$query->free_result();
			// 	foreach($staticData as $name=>$value) {
			// 		if (!isset($value))
			// 			$this->data[$name]='';
			// 		else
			// 			$this->data[$name]=$value;
			// 	}
			// }


			$data=$this->data;

			/**
			 * Split many data if any
			 */
			$many=filter_by_key($this->data,'rel');
			if (!empty($many)) {
				foreach ($many as $key => $value) {
					unset($data[$key]);
				}
			}

			
			/**
			* Start updating the data
			*/
			$this->db->trans_start();
			
			if ($insert) unset($data[PRIMARY_KEY]);
			$this->db->set($data);

			if ($insert) {
				$this->db->insert($this->table);
				$id=$this->db->insert_id();
			}
			else {
				$this->db->where($this->where);
				$this->db->update($this->table);
				$id=$this->_get_id();
			}
			
			/**
			 * If Many, update them to
			 */
			if (!empty($many)) {
				foreach($many as $relTable=>$value) {
					// first delete current selection
					$thisKey=this_key_from_rel_table($relTable);
					$joinKey=join_key_from_rel_table($relTable);
					if ($thisKey==$joinKey) $joinKey.="_";
					// trace_(array('id'=>$id,'thisKey'=>$thisKey,'joinKey'=>$joinKey,'relTable'=>$relTable,'value'=>$value));
					$this->db->where($thisKey,$id);
					$this->db->delete($relTable);
					// insert new selection
					if (!is_array($value)) $value=explode('|',$value);
					foreach ($value as $jdata) {
            if (is_array($jdata)) $jid=$jdata[PRIMARY_KEY]; else $jid=$jdata;
						$this->db->set($thisKey,$id);
						$this->db->set($joinKey,$jid);
						$this->db->insert($relTable);
						$inId=$this->db->insert_id();
					}
				}
			}
			
			/**
			 * Data is updated.
			 */
			
			$this->db->trans_complete();
		}
		return intval($id);
	}



	/**
	 * Verwijderd item van een tabel.
	 * Als ze bestaan worden ook relaties vanuit andere tabellen naar dit item verwijderd.
	 * Als dit item in een tree tabel zit, worden de onderliggende takken omhoog geplaatst. (menu tabellen)
	 *
	 * @param array $where : array( 'key'=> 'value' [, ...])
	 * @return boolean : TRUE if succes, FALSE if not
	 * @author Jan den Besten
	 */

	public function delete($where=array()) {
		if (empty($this->table)) return FALSE;

		$is_deleted=FALSE;
		$this->_set_args(array('where'=>$where));

    $wheres=$this->where;
		if (is_array($wheres)) {
      $first=current($wheres);
      if (!is_array($first)) $wheres[]=$wheres;

      $isTree=$this->db->field_exists('self_parent',$this->table);
      if ($isTree) {$this->load->model('order','order_model');}
      
      foreach ($wheres as $key => $where) {
        // Get id
        $id=$this->_get_id($where);
        
  			/**
  			 * Check if it is a tree, if so, get branches (to move them up later)
  			 */
        if ($isTree) {
          $branches=array();
  				// get info from current
  				$this->db->where($where);
  				$this->db->select('id,order,self_parent');
  				$result=$this->db->get_result($this->table);
          foreach ($result as $id => $row) {
    				$parent=$row['self_parent'];
    				$order=$row['order'];
    				// get branches
    				$this->db->where('self_parent',$id);
    				$this->db->select(PRIMARY_KEY);
    				$branches=$this->db->get_result($this->table);
          }
        }

  			/**
  			 * Remove database entry('s)
  			 */
  			$this->db->trans_start();			
			
  			$this->db->where($where);
  			$is_deleted=$this->db->delete($this->table);
			
  			if ($is_deleted) {

  				/**
  				 * Move branches up if any
  				 */
  				if ($isTree and $branches) {
  					$count=count($branches);
  					$this->order_model->shift_up($this->table,$parent,$count,$order);
  					foreach($branches as $branch=>$value) {
  						$this->db->set('self_parent',$parent);
  						$this->db->set('order',$order++);
  						$this->db->where(PRIMARY_KEY,$value[PRIMARY_KEY]);
  						$this->db->update($this->table);
  					}
  				}


  				/**
  				 * Check if some data set in rel tables (if exists), if so delete them also
  				 */
  				$jTables=$this->db->get_many_tables($this->table);
  				if (!empty($jTables)) {
  					foreach ($jTables as $jt=>$jItem) {
  						$this->db->where($jItem['id_this'],$id);
  						$this->db->delete($jt);
  					}
  				}
				
  				log_message('debug', 'Model: Crud->Delete('.$id.') from table "'.$this->table.'"');
  			}

  			$this->db->trans_complete();
      }
      
		}
		return $is_deleted;
	}



  /**
   * Stelt alle standaard argumenten in
   *
   * @param string $args[NULL]
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _set_args($args=NULL) {
		$this->data   = element('data',$args,FALSE);
		$this->where  = element('where',$args,FALSE);
		if (!is_array($this->where)) $this->where=array(PRIMARY_KEY,$this->where);
		$this->limit  = element('limit',$args,FALSE);
		$this->offset = element('offset',$args,0);
		$this->order 	= element('order',$args,FALSE);
		if (!is_array($this->order)) $this->order=array($this->order=>'DESC');
	}

  /**
   * Geeft id van een where statement
   *
   * @param string $where[''] 
   * @return int $id
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _get_id($where='') {
    if (empty($where)) $where=$this->where;
		$id = element(PRIMARY_KEY,$where,FALSE);
		if (!$id) {
			$this->db->select(PRIMARY_KEY);
			$this->db->where($where);
			$row=$this->db->get_row($this->table);
			$id=$row[PRIMARY_KEY];
		}
		return $id;
	}

  /**
   * Stelt order_by in ad hand van $this->order
   *
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _set_order() {
		foreach ($this->order as $order_by => $direction) {
			$this->db->order_by($order_by,$direction);
		}
	}
	
  /**
   * LIMIT
   *
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _set_limit() {
		if ($this->limit) {
			$this->db->limit($limit,$offset);
		}
	}

}



/* End of file crud.php */
/* Location: ./system/application/models/crud.php */
