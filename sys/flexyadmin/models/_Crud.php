<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Met dit model kun je de basis database handelingen uitvoeren (CRUD)
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 * $HeadURL$ 
 */

class _crud extends CI_Model {

  private $get_as = "array";

	private $table;
	private $user_id;
	private $data;
  private $select;
	private $where;
	private $limit;
	private $offset;
	private $order;
  
  private $validate_data = FALSE;
  private $info = FALSE;

	public function __construct() {
		parent::__construct();
		$this->table='';
	}
  
  /**
   * Validate data bij insert/update
   *
   * @param bool $validate=TRUE
   * @return this
   * @author Jan den Besten
   */
  public function validate($validate=TRUE) {
    $this->validate_data=$validate;
    if ($validate) {
      $this->load->library('form_validation');
    }
    return $this;
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
  
  public function get_table() {
    return $this->table;
  }


	/**
	 * Maakt item in database, inclusief many tables (join/rel)
	 * Zelfde als ->create()
	 *
	 * @param array $args : array( 'data'=>array() )
	 * @return int : id van inserted item
	 * @author Jan den Besten
	 */
	public function insert($args='') {
		$this->_set_args($args);
		if (empty($this->table)) return FALSE;
		return $this->_update_insert(TRUE);
	}
  
	/**
	 * Maakt item in database, inclusief many tables (join/rel)
	 * Zelfde als ->insert()
	 *
	 * @param array $args : array( 'data'=>array() )
	 * @return int : id van inserted item, anders FALSE
	 * @author Jan den Besten
	 */
	public function create($args='') {
		return $this->insert($args);
	}


	/**
	 * Update item(s) in database, inclusief many tables (join/rel)
	 *
	 * @param array $args : array( 'where'=>array(), 'data'=>array() )
	 * @return bool : id als gelukt, anders FALSE
	 * @author Jan den Besten
	 */
	public function update($args='') {
		$this->_set_args($args);
		if (empty($this->table)) return FALSE;
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
		if ((is_array($this->where) or $insert) && is_array($this->data)) {

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

			$data=$this->data;
      
      /**
       * Validate data
       */
      if ($this->validate_data) {
        $validated=$this->form_validation->validate_data($data,$this->table);
        if (!$validated) {
          $this->info=array(
            'validation'        => FALSE,
            'validation_errors' => $this->form_validation->get_error_messages()
          );
          return FALSE;
        }
      }
      

			/**
			 * Split many data if any
			 */
			$many=filter_by_key($this->data,'rel');
			if (!empty($many)) {
				foreach ($many as $key => $value) {
					unset($data[$key]);
				}
			}


      // trace_(['table'=>$this->table,'where'=>$this->where,'data'=>$data,'many'=>$many]);


			/**
			* Start updating the data
			*/
			$this->db->trans_start();
		
			if ($insert) unset($data[PRIMARY_KEY]);
			
      if (!empty($data)) {
  			$this->db->set($data);

  			if ($insert) {
  				$this->db->insert($this->table);
  				$id=$this->db->insert_id();
          $this->info=array(
            'insert_id' =>$id
          );
  			}
  			else {
  				$this->db->where($this->where);
  				$this->db->update($this->table);
          $this->info=array(
            'affected_rows' => $this->db->affected_rows()
          );
  				$id=$this->_get_id();
  			}
      }
			
			/**
			 * If Many, update them to
			 */
			if (!empty($many)) {
        if (!$id) $id=$this->_get_id();
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
		$is_deleted=FALSE;
		$this->_set_args(array('where'=>$where));
		if (empty($this->table)) return FALSE;

    $wheres=$this->where;

		if (is_array($wheres)) {
      
      $isTree=$this->db->field_exists('self_parent',$this->table);
      if ($isTree) {
        $this->load->model('order','order_model');
      }
      
      foreach ($wheres as $key => $where) {
        // Key & id
        $id=$this->_get_id($where);
        // $key=key($where);
        // $where=current($where);
        
  			/**
  			 * Check if it is a tree, if so, get branches (to move them up later)
  			 */
        if ($isTree) {
          $branches=array();
  				// get info from current
          $this->db->where($key,$where);
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
			
        $this->db->where($key,$where);
  			$is_deleted=$this->db->delete($this->table);
        
        $this->info = array(
          'affected_rows' => $this->db->affected_rows()
        );
			
  			if ($is_deleted) {

  				/**
  				 * Move branches up if any
  				 */
  				if ($isTree and $branches) {
  					$count=count($branches);
  					$this->order_model->shift_up($this->table,$parent,$count,$order);
            $moved=0;
  					foreach($branches as $branch=>$value) {
  						$this->db->set('self_parent',$parent);
  						$this->db->set('order',$order++);
  						$this->db->where(PRIMARY_KEY,$value[PRIMARY_KEY]);
  						$this->db->update($this->table);
              $moved++;
  					}
            $this->info['moved_rows'] = $moved;
  				}


  				/**
  				 * Check if some data set in rel tables (if exists), if so delete them also
  				 */
  				$jTables=$this->db->get_many_tables($this->table);
  				if (!empty($jTables)) {
            $affected=0;
  					foreach ($jTables as $jt=>$jItem) {
  						$this->db->where($jItem['id_this'],$id);
  						$this->db->delete($jt);
              $affected+=$this->db->affected_rows();
  					}
            $this->info['affected_rel_rows'] = $affected;
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
	private function _set_args($args=array()) {
    $table = element('table',$args,FALSE);
    if ($table) $this->table($table);

		$this->data   = element('data',$args,FALSE);
    $this->select = element('select',$args,FALSE);
		$this->limit  = element('limit',$args,FALSE);
		$this->offset = element('offset',$args,0);
		$this->where  = element('where',$args,FALSE);
		if ($this->where) {
      // Make it an array of where statements
      if (!is_array($this->where)) {
        if ($this->where=='first') {
          $this->where=FALSE;
          $this->limit=1;
        }
        else {
  		    $this->where=array(PRIMARY_KEY => $this->where);
        }
      }
      // WHERE IN
      // trace_($this->where);
		}
		$this->order 	= element('order',$args,FALSE);
    // if ($this->order and !is_array($this->order)) $this->order=array($this->order.' DESC');
    // trace_(['table'=>$this->table,'where'=>$this->where]);
	}

  /**
   * Geeft id van een where statement
   *
   * @param mixed id | 'id'=>...
   * @return int $id
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _get_id($where='') {
    if (empty($where)) $where=$this->where;
    if (is_array($where)) $where=current($where);
    if (!is_array($where) and is_numeric($where)) {
      $id = $where;
    }
    else {
      $id = element(PRIMARY_KEY,$where,FALSE);
    }
    // not in where? find in db
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
  
  
  /**
   * Get result
   *
   * @param string $args 
   * @return mixed $result
   * @author Jan den Besten
   */
  public function get($args=array()) {
		$this->_set_args($args);
		if (empty($this->table)) return FALSE;
    if ($this->select)  $this->db->select($this->select);
    if ($this->where)   $this->db->where($this->where);
    if ($this->order)   $this->db->order_by($this->order);
    if ($this->limit)   $this->db->limit($this->limit);
    if ($this->offset)  $this->db->offset($this->offset);
    $result = $this->db->get_result($this->table);
    $this->info = array(
      'rows'        => count($result),
      'total_rows'  => $this->db->last_num_rows_no_limit(),
      'table_rows'  => $this->db->count_all($this->table)
    );
    return $result;
  }
  
  /**
   * Get result as array
   *
   * @param string $args 
   * @return array $result
   * @author Jan den Besten
   */
  public function get_array($args=array()) {
    return $this->get($args);
  }

  /**
   * Get result as row
   *
   * @param string $args 
   * @return array $result
   * @author Jan den Besten
   */
  public function get_row($args=array()) {
    $args['limit'] = 1;
    $result=$this->get($args);
    return current($result);
  }
  
  
  /**
   * Geeft informatie over de laatste actie. De beschikbare keys hangen af van soort actie:
   * 
   * - rows             - Aantal records bij een get actie
   * - total_rows       - Aantal records zonder limit, maar met where
   * - table_rows       - Totaal aantal records van gevraagde tabel (zonder where en limit)
   * - insert_id        - Bij een INSERT, de id van het nieuwe record
   * - affected_rows    - Bij een UPDATE of DELETE het aantal records dat beinvloed is (aangepast of verwijderd)
   *
   * @param string $key['']
   * @return array
   * @author Jan den Besten
   */
  public function get_info($key='') {
    if (empty($key)) return $this->info;
    return el($key,$this->info,false);
  }

}



/* End of file crud.php */
/* Location: ./system/application/models/crud.php */
