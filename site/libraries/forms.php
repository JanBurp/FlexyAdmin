<?

/**
	* Formulieren
	* 
	* Deze module maakt formulieren voor je website en regelt meteen de afhandeling.
	* 
	* - Er kunnen meerdere formulieren worden gemaakt
	* - Met de config kun je de diverse formulieren aanmaken, instellen welke velden ze hebben, en wat de afhandeling van een formulier is
	* - De verschillende formulieren kunnen aangeroepen worden met hun naam als een submodule bv: forms.contact en forms.reservation. 
	*
	* Bestanden
	* ----------------
	*
	* - site/config/forms.php - Hier worden alle formulieren ingesteld
	* - site/views/forms.php - De view waarin de comments en het formulier geplaatst worden
	* - site/language/##/forms_lang.php - Taalbestanden
	*
	* Installatie
	* ----------------
	*
	* - Pas de configuratie aan indien nodig (zie: site/config/forms.php)
	* - Pas de view (en styling) aan indien nodig
	* - Maak je eigen taalbestand en/of wijzig de bestaande
	*
	* @author Jan den Besten
	* @package FlexyAdmin_comments
	*
	*/
class Forms extends Module {
  
  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
		$this->CI->load->library('form');
	}


  /**
   * Hier wordt bepaald welk formulier wordt gevraagd (welke method wordt aangeroepen)
   *
   * @param string $function 
   * @param array $args 
   * @return mixed
   * @author Jan den Besten
   */
	public function __call($function, $args) {
    // Test of gevraagd formulier bestaat
    if (isset($this->config[$function])) {
      // Ja: stel de naam in, laad de config, en stel de output config goed in
      $this->set_name($function);
      $this->config=$this->config[$function];
      if (isset($this->config['__return'])) {
        $this->config['__return.'.$function]=$this->config['__return'];
      }
      return $this->index($args);
    }
    else {
      // Bestaat niet: geef een melding
      echo '<div class="warning">'.langp('error_not_exists',$function).'<div>';
    }
    return false;
	}


  /**
  	* Hier wordt het formulier gegenereerd
  	*
  	* @param string $page 
  	* @return mixed
  	* @author Jan den Besten
  	*/
	public function index($page) {
		$html='';
    $errors='';
		
		// Welke velden (en buttons): zijn ze los ingesteld?
    $formFields=$this->config('fields');
    $formButtons=$this->config('buttons');
    // Geen velden ingesteld, maar wel een tabel: haal ze uit de tabel
    if (!$formFields and $this->config('table')) {
      $fields=$this->CI->db->list_fields( $this->config('table') );
      $formFields=array2formfields($fields);
      unset($formFields['id']);
    }
    // Geen velden en geen tabel, maar een flexyform
    if (!$formFields) {
      $this->CI->load->model('getform');
      $flexyform=str_replace('flexyform_','',$this->name);
      $formData=$this->CI->getform->by_name($flexyform);
      if ($formData) {
  			$formFieldSets=$formData['fieldsets'];
  			$formFields=$formData['fields'];
  			$formButtons=$formData['buttons'];
        // other settings
        $this->config['title']=$flexyform;
        $this->config['thanks']=$formData['form']['txt_text_'.$this->CI->site['language']];
        $emailField=find_row_by_value($formFields,'valid_email','validation',true);
        if ($emailField) {
          $this->config['from_address_field']=key($emailField);
        }
      }
    }
    
    if (!$formFields) {
      echo '<div class="warning">'.langp('error_no_fields',$this->name).'<div>';
      return false;
    }

    $form_id=$this->name;
		$form=new form($this->CI->uri->get(),$form_id);
		$form->set_data($formFields, $this->config('title',$form_id) );
    if ($this->config('placeholders_as_labels')) $form->add_placeholders();
		if (isset($formFieldSets)) $form->set_fieldsets($formFieldSets);
    if ($formButtons) $form->set_buttons($formButtons);

		// Is form validated?
		if ($form->validation($form_id)) {
      $data=$form->get_data();

      // Do the set Action(s)
      $formaction=$this->config('formaction');
      if (!is_array($formaction)) $formaction=array($formaction);
      foreach ($formaction as $faction) {
        $action='action_'.$faction;
        $this->CI->load->model($faction,$action);
        $this->CI->$action->initialize($this->config)->fields( $formFields );
  			if (!$this->CI->$action->go( $data )) {
  		    $errors.=$this->CI->$action->get_errors();
          $html.=div('message').$errors._div();
  			}
        else {
    			$html=div('message').$this->config('thanks','Thank you!')._div();
        }
      }
		}
		else {
			// Form isn't filled or validated: show form and validation errors
			$errors=validation_errors('<p class="error">', '</p>');
			$html.=$form->render();
		}
		
    return $this->CI->view('forms',array('title'=>$this->config['title'],'form'=>$html,'errors'=>$errors),true);
	}

}

?>