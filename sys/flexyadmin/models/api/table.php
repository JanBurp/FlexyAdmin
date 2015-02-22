<?


/**
 * Returns a table from the database (if user has rights)
 * 
 * Arguments:
 * - table
 * - limit
 * - offset
 *
 * @package default
 * @author Jan den Besten
 */

class Table extends ApiModel {
  
  var $needs = array(
    'table'   => '',
    'limit'   => 0,
    'offset'  => 0
  );
  
	public function __construct() {
		parent::__construct();
    $this->load->model('ui');
	}
  
  /**
   * Gets the data and information and returns it
   *
   * @return void
   * @author Jan den Besten
   */
  public function index() {
    if (!$this->_has_rights($this->args['table'])) return $this->_result_status401();
    
    if ( !$this->has_args() ) {
      return $this->_result_wrong_args();
    }
    
    // DEFAULTS
    $items=FALSE;
    // CFG
    $this->_get_config(array('table_info','field_info'));
    // GET DATA
    $items=$this->_get_data();
    // PROCESS DATA
    if ($items) {
      $items = $this->_process_data($items);
    }
    
    // RESULT
    $this->result['data']=$items;
    return $this->_result_ok();
  }
  
  
  /**
   * Gets the data from the table
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_data() {
    $this->db->unselect( el(array('field_info','hidden_fields'),$this->cfg_info,array()) );
    $this->db->max_text_len(100);
    if ( el( array($this->args['table'],'tree'), $this->cfg_info,false) ) $this->db->order_as_tree();
    $items = $this->crud->get($this->args);
    return $items;
  }
  
  
  /**
   * Loop through all rows and process them
   *
   * @param string $items 
   * @param string $table_info 
   * @param string $field_info 
   * @return void
   * @author Jan den Besten
   */
  private function _process_data($items) {

    // init STRIP TAGS in txt fields
    $fields=$this->cfg_info['table_info']['fields'];
    $txtKeys=array_combine($fields,$fields);
    $txtKeys=filter_by_key($txtKeys,'txt');

    // INIT TREE
    $parents=array();

    // LOOP ROWS
    foreach ($items as $id => $row) {
      
      // STRIP TAGS in txt fields
      foreach ($txtKeys as $key) {
        $items[$id][$key]=strip_tags($row[$key]);
      }
      
      // TREE, BRANCHES & NODES
      if ($this->cfg_info['table_info']['tree']) {
        $parent_id = $row['self_parent'];
      
        // toplevel: no branch, no node
        if ($parent_id == 0) {
          $level=0;
          $is_branch=false;
          $is_node=false;
        }
        // find out what level
        else {
          $is_node=true;
          // are we on a known level?
          if (isset($parents[$parent_id])) {
            $level=$parents[$parent_id];
          }
          else {
            // no: remember new level
            $level++;
            $parents[$parent_id]=$level;
          }
        }
        // add this info to this item
        $items[$id]['_info'] = array(
          'is_branch' => false, // this will be set later...
          'is_node'   => $is_node,
          'level'     => $level
        );
      }
    }
    
    // LOOP AGAIN TO ADD BRANCH INFO
    if (el( array('table_info','tree'),$this->cfg_info,false) and !empty($parents)) {
      foreach ($parents as $id => $level) {
        $items[$id]['_info']['is_branch']=true;
      }
    }
    
    return $items;
  }
  

}


?>
