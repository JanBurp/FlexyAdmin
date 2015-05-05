<?php require_once(APPPATH."core/AjaxController.php");

// ------------------------------------------------------------------------

/**
 * Ajax Controller Class
 *
 * This Controller handles all AJAX requests
 *
 * $Author$
 * $Date$
 * $Revision$
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Ajax extends AjaxController {

	public function __construct() {
		parent::__construct();
		$this->load->model('ui');
    $this->load->model('flexy_field','ff');
    $this->load->model("login_log");
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
			if ($this->user->has_rights($table)>=RIGHTS_EDIT) {
				if ($ids) {
					$this->load->model("order");
					$this->load->model('queu');
					$this->order->set_all($table,$ids);
          $this->_after_update($table);
          $this->login_log->update($table);
					$this->queu->run_calls();
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
		$this->select('id,order,self_parent,uri');
    // $this->db->order_as_tree();
		$this->db->uri_as_full_uri();
		return $this->db->get_result($table);
	}
  


  /**
   * Past een waarde aan in de database
   * - valideert eerste de data
   *
   * @param string $table 
   * @param string $id 
   * @param string $field 
   * @param string $value 
   * @param string $plugins 
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
			if ($this->db->table_exists($table) and $this->db->field_exists($field,$table)) {
        
 				if ($this->user->has_rights($table,$id)>=RIGHTS_EDIT) {

					if ($plugins) $this->load->model('queu');

          // Get olddata
					$this->db->where(PRIMARY_KEY,$id);
					$oldData=$this->db->get_row($table);
					$newData=$oldData;
          $old_value=$oldData[$field];
          // Get newdate
					$newData[$field]=$value;
          
          // Only update & validate if different
          if ($oldData!=$newData) {
            
            // Validate new Data
            $this->ff->table=$table;
            $this->ff->init_field($field,$value);
            
            if (! $this->form_validation->validate_data($newData,$table)) {
              $validation_errors = $this->form_validation->get_error_messages();
            }
            
            // $validation[]=array('rules'=>$this->ff->validation,'params'=>'');
            // $validations=$this->form_validation->get_validations($table,$field,$validation,true);
            // foreach ($validations as $rule => $param) {
            //   $rule=str_replace(array('[',']'),'',$rule);
            //   if (!$this->form_validation->$rule($value,$param)) {
            //     if (empty($param))
            //       $validation_errors=langp($rule,$field);
            //     else
            //       $validation_errors=langp($rule,$field,$param);
            //     break;
            //   }
            // }
            
            // Options?
            // if (!$validation_errors) {
            //   $options=$this->cfg->get('cfg_field_info',$table.'.'.$field,'str_options');
            //   if ($options) {
            //     $aOptions=explode('|',$options);
            //     if (!in_array($value,$aOptions)) $validation_errors[$field]=langp('valid_option',$field).str_replace('|',',',$options);
            //   }
            // }

            if (!$validation_errors) {
              // Call Plugins
    					if ($plugins)	$newData=$this->_after_update($table,$oldData,$newData);

              // Update in database
    					$this->crud->table($table);
    					$this->crud->update(array('where'=>array(PRIMARY_KEY=>$id), 'data'=>$newData));
              $this->login_log->update($table);

    					if ($plugins) {
    						$this->queu->run_calls();
    					}
    					delete_all_cache();
              
              // Fetch new value from database as double check and feedback to user
              $value=$this->db->get_field_where($table,$field,PRIMARY_KEY,$id);
            }
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
      $result['validation_errors']=safe_quotes($this->ui->replace_ui_names($validation_errors));
    }
    else {
      $result['validation_errors']=false;
    }
    if (isset($options)) $result['opions']=$options;
    $result['old_value']=$old_value;
    $result['new_value']=$value;
    $this->message->reset_errors();
    return $this->_result($result);
 	}
  
  
  /**
   * AJAX call ro resize an image according to img_info from given path
   *
   * @param string $path 
   * @param string $file 
   * @return void
   * @author Jan den Besten
   */
  public function resize_image($path,$file) {
    $result=array('method'=>__METHOD__,'path'=>$path,'file'=>$file,'message'=>'-');
    $this->load->library('upload');
    
    if ($this->upload->resize_image($file,assets().$path)) {
      $result['message']='resized';
    }
    else {
      $result['message']='ERROR while resizing';
      $result['success']=false;
    }
    return $this->_result($result);
  }
  
  
  
	

}

?>
