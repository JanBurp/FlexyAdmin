<?

class get_table extends ApiModel {
  
  var $args = array(
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
    if (!$this->_has_rights($this->args['table'])) return $this->result;
    
    // DEFAULTS
    $items=FALSE;
    $field_info=FALSE;
    $table_info=FALSE;

    if ($this->args['table']) {
      // FIELD INFO
      $this->fields = $this->db->list_fields($this->args['table']);
      $field_info=$this->_get_field_info();
      // TABLE INFO
      $table_info=$this->_get_table_info();
      // GET DATA
      $items=$this->_get_data($table_info);
      // PROCESS DATA
      if ($items) {
        $items = $this->_process_data($items,$table_info,$field_info);
      }
    }
    
    // RESULT
    $data=array(
      'table_info'  =>$table_info,
      'field_info'  =>$field_info,
      'items'       =>$items
    );
    $this->result['data']=$data;
    return $this->result;
  }
  
  
  /**
   * Gets the data from the table
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_data($table_info) {
    $this->db->unselect($this->hidden_fields);
    $this->db->max_text_len(100);
    if ($table_info['tree']) $this->db->order_as_tree();
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
  private function _process_data($items,$table_info,$field_info) {

    // init STRIP TAGS in txt fields
    $txtKeys=array_combine($this->fields,$this->fields);
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
      if ($table_info['tree']) {
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
    if ($table_info['tree'] and !empty($parents)) {
      foreach ($parents as $id => $level) {
        $items[$id]['_info']['is_branch']=true;
      }
    }
    
    return $items;
  }
  

}


?>
