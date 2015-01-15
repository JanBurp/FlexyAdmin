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
   * Geeft form_id
   *
   * @return void
   * @author Jan den Besten
   */
  private function _get_form_id() {
    $form_id=$this->CI->input->post('form_id');
    if (!$form_id) $form_id=$this->CI->input->post('__form_id');
    return $form_id;
  }

  /**
   * Geeft opgevraagde formulier terug
   *
   * @param string $args 
   * @return array
   * @author Jan den Besten
   */
  public function index($args) {
    $form_id=$this->_get_form_id();
    $form=$this->CI->forms->$form_id();
    $settings=$this->CI->forms->get_settings($form_id);
    $is_spam=$this->CI->forms->is_spam();
    $is_validated=$this->CI->forms->is_validated();
    return $this->result(array('_message'=>__CLASS__,'form_id'=>$form_id,'is_spam'=>$is_spam,'is_validated'=>$is_validated,'form'=>$form,'settings'=>$settings));
  }
  

}

?>