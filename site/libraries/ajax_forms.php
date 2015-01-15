<?php 

/**
 * Maakt het mogelijk om formulieren gemaakt met forms via ajax te laten zien en te submitten.
 *
 * @package default
 * @author Jan den Besten
 */
class Ajax_forms extends Ajax_module {

  public function __construct() {
    parent::__construct();
    $this->CI->load->helper('language');
    $this->CI->load->library('module');
    $this->CI->load->library('forms');
    $this->CI->load->model('formaction');
  }

  /**
   * Geeft opgevraagde formulier terug
   *
   * @param string $args 
   * @return array
   * @author Jan den Besten
   */
  public function show($args) {
    $form_id=$this->CI->input->post('form_id');
    $form=$this->CI->forms->$form_id();
    return $this->result(array('_message'=>__CLASS__,'form_id'=>$form_id,'form'=>$form));
  }
  

  /**
   * Submit gevraagde formulier
   *
   * @param string $args 
   * @return array
   * @author Jan den Besten
   */
  public function submit($args) {
    $form_id=$this->CI->input->post('__form_id');
    $form=$this->CI->forms->$form_id();
    return $this->result(array('_message'=>__CLASS__,'form_id'=>$form_id,'form'=>$form));
  }
  
  

}

?>