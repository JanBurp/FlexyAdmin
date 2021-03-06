<?php require_once(APPPATH."core/AjaxController.php");

// ------------------------------------------------------------------------

/**
 * Ajax Controller Class
 *
 * This Controller handles all AJAX requests
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Ajax extends AjaxController {

	public function __construct() {
		parent::__construct();
		    $this->load->library('form_validation');
    $this->lang->load('ajax');
    $this->lang->load('form_validation');
    $this->load->helper('string');
	}

  /**
   * Maak nieuwe volgorde in een grid
   *
   * @param string $table 
   * @return void
   * @author Jan den Besten
   */
	public function order($table="") {
    $table=$this->input->post('table');
		$ids=$this->input->post("ids");

		$error='';
		if (!empty($table) and $this->db->table_exists($table)) {
			if ($this->flexy_auth->has_rights($table)>=RIGHTS_EDIT) {
				if ($ids) {
					$this->load->model("order");
					$this->order->set_all($table,$ids);
          $this->_after_update($table);
					delete_all_cache();
				}
				else {
					$error='ajax_error_wrong_parameters';
				}
			}
			else {
				$error='ajax_error_no_rights';
			}
		}
		else {
			$error='ajax_error_wrong_parameters';
		}
    if ($error) return $this->_result(array('method'=>__METHOD__,'error'=>lang($error)));
    return $this->_result(array('method'=>__METHOD__));
	}
  
  /**
   * Geeft huidige volgorde terug
   *
   * @param string $table 
   * @return void
   * @author Jan den Besten
   */
	private function _get_current_order($table) {
    $this->data->table( $table )
                ->select('id,order,self_parent,uri')
                ->tree( 'uri' );
		return $this->data->get_result();
	}
  


  /**
   * Past een waarde aan in de database
   * - valideert eerste de data
   *
   * @param bool $plugins  default=true 
   * @return void
   * @author Jan den Besten
   */
 	public function edit($plugins=TRUE) {
    $result=array();
    
    $table=$this->input->post('table');
    $id=$this->input->post('id');
    $field=$this->input->post('field');
    $value=$this->input->post('value');
    
		$error='';
    $validation_errors='';
    $old_value=NULL;
		if (!empty($table) and ($id!="") and !empty($field)) {
			if ($this->db->table_exists($table) ) { //and $this->db->field_exists($field,$table)) {
        
 				if ($this->flexy_auth->has_rights($table,$id)>=RIGHTS_EDIT) {

          // Get olddata
          $oldData = $this->data->table($table)->where( PRIMARY_KEY, $id )->get_row();
					$newData=$oldData;
          $old_value=el($field,$oldData);
          // Get newdate
					$newData[$field]=$value;
          
          // Only update & validate if different
          if ($oldData!=$newData) {
            
            // Call Plugins
            if ($plugins)  $newData=$this->_after_update($table,$oldData,$newData);

            // Update in database
            $this->data->table($table);
            // $this->data->validate();
            if ( !$this->data->update( $newData, array(PRIMARY_KEY=>$id) )) {
              $validation_errors = $this->data->get_query_info('validation_errors');
            }
            
  					delete_all_cache();
          }
				}
				else $error='ajax_error_no_rights';		 		
	 		}
			else $error='ajax_error_wrong_parameters';
		}
		else $error='ajax_error_wrong_parameters';


    $result['method']=__METHOD__;
    if ($error) $result['error']=lang($error);
    if ($validation_errors) {
      $result['validation_errors']=safe_quotes($this->lang->replace_ui($validation_errors));
    }
    else {
      $result['validation_errors']=false;
    }
    if (isset($options)) $result['opions']=$options;
    $result['old_value']=$old_value;
    $result['new_value']=$value;
    $result['field']=$field;
    $this->message->reset_errors();
    return $this->_result($result);
 	}
  
  
  
  /**
   * AJAX plugin call (_admin/ajax/plugin/...)
   *
   * @return array ajax result
   * @author Jan den Besten
   */
  public function plugin() {
		$args=func_get_args();
    $plugin=array_shift($args);
    // load the plugin
    $plugin_name='plugin_'.$plugin;
    $this->load->library('plugins/plugin_'.$plugin,$plugin_name);
    // Call the plugin
    $result = call_user_func_array( array( $this->$plugin_name,'_ajax_api'), $args);
    return $this->_result( $result );
  }
  
  
  
	

}

?>
