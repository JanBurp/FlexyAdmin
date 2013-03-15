<?

/**
  * Reservation
  *  
	* Eenvoudige module die een invulformulier toont (voor een reservering) en de gegevens worden in de database gestopt
	*
	* @author Jan den Besten
	*/
 
 class Reservation extends Module {

  public function __construct() {
    parent::__construct();
    $this->CI->load->library('form');
  }

	public function index($page) {
    $out='';
    $errors='';
    
    $fields=$this->CI->db->list_fields( $this->config('table') );
    $formData['fields']=array2formfields($fields);
    unset($formData['fields']['id']);
    $formData['buttons']=$this->config('form_buttons');
    
    $form_id='reservation_form';
		$form=new form($this->CI->uri->get(),$form_id);
		$form->set_data( $formData['fields'] );
    $form->set_buttons( $formData['buttons'] );
    $form->prepare_for_clearinput();

		// Is form validation ok?
		if ($form->validation($form_id)) {
      $data=$form->get_data();

      // Action(s)
      $formaction=$this->config('formaction');
      if (!is_array($formaction)) $formaction=array($formaction);
      foreach ($formaction as $faction) {
        $action='action_'.$faction;
        $this->CI->load->model($faction,$action);
        $this->CI->$action->initialize($this->config)->fields( $formData['fields'] );
  			if (!$this->CI->$action->go( $data )) {
  		    $errors.=$this->CI->$action->get_errors();
          $out.=div('message').$errors._div();
  			}
        else {
    			// Show Send message
    			$out=div('message').'Thanks for using this example'._div();
        }
      }
		}
	
		else {
			// Form isn't filled or validated: show form and validation errors
			$errors=validation_errors('<p class="error">', '</p>');
			$out.=$form->render();
		}
		
    return $this->CI->view('reservation_form',array('form'=>$out,'errors'=>$errors),true);
	}

}

?>