<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup models
 * Met dit model kun je de basis database handelingen uitvoeren (CRUD)
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

class Crud_ extends CI_Model {

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
	 * Stel tabel in waarvoor de acties gelden
	 *
	 * @param string $table : table name
	 * @param mixed $user_id default=FALSE
	 * @return object $this
	 * @author Jan den Besten
	 */
	public function table($table='',$user_id=FALSE) {
		if (empty($table)) return FALSE;
		if (!$this->db->table_exists($table)) return FALSE;
		$this->table=$table;
		$this->_set_args();
		$this->user_id=$user_id;
		log_message('debug', 'Crud->table( '.$table.' )');
		return $this;
	}

  
  /**
   * Geef aan of bij insert/update de data eerst moet worden gevalideerd
   *
   * @param bool $validate default=TRUE
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
    $result=$this->_update_insert(TRUE);
		log_message('debug', 'Crud->insert() '.el('affected_rows',$this->info).' items into table '.$this->table);
		return $result;
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
    $result = $this->_update_insert(FALSE);
		log_message('debug', 'Crud->update() '.el('affected_rows',$this->info).' from table '.$this->table);
		return $result;
	}


	/**
	 * private _update_insert, does the actual updateing/inserting for create/update
	 * @author Jan den Besten
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
      
      

      
      // trace_(['insert'=>$insert,'table'=>$this->table,'where'=>$this->where,'data'=>$data,'many'=>$many]);

			/**
			* Start updating the data
			*/
			$this->db->trans_start();
		
			if ($insert) unset($data[PRIMARY_KEY]);
			
      if (!empty($data)) {

        // Remove empty passwords, so they can't be saved...
        foreach ($data as $key => $value) {
          if (in_array(get_prefix($key),array('gpw','pwd')) and empty($value)) {
            unset($data[$key]);
          }
        }
        
        // Set data if it has a safe value (not NULL), and if the field exists
        $set=array();
        foreach ($data as $key => $value) {
          if (isset($value) and $this->db->field_exists($key,$this->table)) $set[$key]=$value;
        }
        
        if (!empty($set)) {
          // Set user_changed (if known and field exists)
          if ($this->user_id and $this->db->field_exists('user_changed',$this->table)) {
            $set['user_changed']=$this->user_id;
            // first timestamp?
            if ($insert and $this->db->field_exists('tme_last_changed',$this->table)) {
              $set['tme_last_changed']=date(DATE_W3C);
            }
          }
          
          // SET
          $this->db->set($set);
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
          // trace_(['sql'=>$this->db->last_query(),'rows'=>$this->db->affected_rows(),'info'=>$this->info]);
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
        
        // /**
        //  * Check if it is a tree, if so, get children (to move them up later)
        //  */
        //         if ($isTree) {
        //           $parent=$this->db->get_field_where($this->table,'self_parent',PRIMARY_KEY,$id);
        //   $this->db->where('self_parent',$parent);
        //   $this->db->select(PRIMARY_KEY);
        //   $children=$this->db->get_result($this->table);
        //         }

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
  				 * Reset order of children (if any)
  				 */
  				if ($isTree) {
					  $this->info['moved_rows'] = $this->order_model->reset($this->table);
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
				
  				log_message('debug', 'Crud->delete() '.$this->info['affected_rows'].' items from table '.$this->table);
  			}

  			$this->db->trans_complete();
      }
      
		}
		return $is_deleted;
	}


  /**
   * Stelt alle standaard argumenten in
   *
   * @param string $args default=NULL
   * @return void
   * @author Jan den Besten
   * @internal
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
   */
	private function _set_limit() {
		if ($this->limit) {
			$this->db->limit($limit,$offset);
		}
	}
  
  
  /**
   * Geeft gebruikte tabel terug
   *
   * @return string $table
   * @author Jan den Besten
   */
  public function get_table() {
    return $this->table;
  }
  
  
  /**
   * Geeft array van rijen als resultaat
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
      'table_rows'  => $this->db->count_all($this->table),
      'query'       => $this->db->last_qb_query()
    );
    // trace_($this->info);
    return $result;
  }
  
  /**
   * Zelfde als get()
   *
   * @param string $args 
   * @return array $result
   * @author Jan den Besten
   */
  public function get_array($args=array()) {
    return $this->get($args);
  }

  /**
   * Geeft rij als resultaat
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
