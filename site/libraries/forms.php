<?php 
/**
	* Formulieren
	* 
	* Deze module maakt formulieren voor je website en regelt meteen de afhandeling.
	* 
	* - Er kunnen meerdere formulieren worden gemaakt
	* - Met de config kun je de diverse formulieren aanmaken, instellen welke velden ze hebben, en wat de afhandeling van een formulier is
	* - De verschillende formulieren kunnen aangeroepen worden met hun naam als een submodule bv: forms.contact en forms.reservation.
	* 
	* Aanroepen
	* ----------------
	* 
	* Je roept een formulier aan zoals een andere module, met als method de naam van je formulier zoals je die hebt ingesteld in de config, bijvoorbeeld:
	* 
	* - forms.contact
	* - forms.upload_demo
	* 
	* Je kunt forms ook vanuit een andere module aanroepen, je krijgt dan de HTML terug van het formulier, inclusief validatie fouten etc.:
	* 
	* - `$this->CI->_call_library('forms','comments');`
	* 
	* Instellingen
	* ----------------
	* 
	* Er zijn veel verschillende instellingen mogelijk. In _site/config/forms.php_ vindt je diverse voorbeelden en uitleg bij de diverse instellingen.
	* Mocht het formulier gebruik maken van een formaction, dan worden alle instellingen ook naar het formaction gestuurd.
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
  
  private $form_id='';

  private $settings=array();
  
  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
	}


  /**
   * Hier wordt bepaald welk formulier wordt gevraagd (welke method wordt aangeroepen)
   *
   * @param string $function 
   * @param array $args 
   * @return mixed
   * @author Jan den Besten
   * @ignore
   */
	public function __call($function, $args) {
    // Test of gevraagd formulier bestaat
    if (isset($this->config[$function])) {
      // Ja: stel de naam in, laad de config, en stel de output config goed in
      $this->set_name($function);
      $this->settings=$this->config[$function];
      if (isset($this->settings['__return'])) {
        $this->config['__return.'.$function]=$this->settings['__return'];
      }
      // Extra settings instellen
      if (isset($args[0])) {
        $this->settings=array_merge($this->settings,$args[0]);
      }
      // Laad eventueel benodigde libraries
  		$this->CI->load->library('form');
      if ($this->settings('prevend_double_submit')) $this->CI->load->library('session');
      return $this->index($args);
    }
    else {
      // Bestaat niet: geef een melding
      echo '<div class="warning">'.langp('error_not_exists',$function).'<div>';
    }
    return false;
	}

  /**
   * Voegt een formulier toe
   *
   * @param string $form Naam van het formulier dat later met ->forms->naam() kan worden aangeroepen.
   * @param array $config De instellingen voor het formulier, eenzelfde array als in de config file per formulier.
   * @return this
   * @author Jan den Besten
   */
  public function initialize($form,$config) {
    $this->config[$form]=$config;
    return $this;
  }


  /**
  	* Hier wordt het formulier gegenereerd
  	*
  	* @param string $page 
  	* @return mixed
  	* @author Jan den Besten
  	* @ignore
  	*/
	public function index($page) {
    $this->form_id=$this->name;
		$html='';
    $errors='';
    
    // Test if allready submitted (and testing for that is possible)
    if ($this->settings('prevend_double_submit')) {
      $isSubmit=$this->CI->session->userdata($this->form_id.'__submit');
      if ($isSubmit) {
        return $this->_view_thanks();
      }
    }
    
		// Welke velden (en buttons): zijn ze los ingesteld?
    $formFields=$this->settings('fields');
    $formButtons=$this->settings('buttons');
    // Geen velden ingesteld, maar wel een model: vraag de model.method naar de velden
    if (!$formFields and $this->settings('model')) {
      $model=$this->settings('model');
      $method=get_suffix($model,'.');
      $model=get_prefix($model,'.');
      if (!isset($this->CI->$model)) $this->CI->load->model($model);
      $this->CI->$model->initialize($this->settings);
      $formFields=$this->CI->$model->$method();
    }
    // Geen velden ingesteld, maar wel een tabel: haal ze uit de tabel
    if (!$formFields and $this->settings('table')) {
      $fields=$this->CI->db->list_fields( $this->settings('table') );
      $formFields=array2formfields($fields);
      unset($formFields['id']);
    }
    // Geen velden en geen tabel, maar een flexyform
    if (!$formFields) {
      $this->CI->load->model('getform');
      $flexyform=str_replace('flexyform_','',$this->name);
      $formData=$this->CI->getform->by_title($flexyform);
      if ($formData) {
  			$formFieldSets=$formData['fieldsets'];
  			$formFields=$formData['fields'];
  			$formButtons=$formData['buttons'];
        // other settings
        $this->settings['title']=$flexyform;
        $this->settings['thanks']=$formData['form']['txt_text_'.$this->CI->site['language']];
        $emailField=find_row_by_value($formFields,'valid_email','validation',true);
        if ($emailField) {
          $this->settings['from_address_field']=key($emailField);
        }
      }
    }
    
    // Populate fields
    if (isset($this->settings['populate_fields'])) {
      $method=get_suffix($this->settings['populate_fields'],'.');
      $model=get_prefix($this->settings['populate_fields'],'.');
      if (!isset($this->CI->$model)) $this->CI->load->model($model);
      $formFields=$this->CI->$model->$method($formFields);
    }
    
    if (!$formFields) {
      echo '<div class="warning">'.langp('error_no_fields',$this->name).'<div>';
      return false;
    }
    
    // Extra veld toevoegen om op spamrobot te testen (die zal dit veld meestal automatisch vullen)
    if ($this->settings('check_for_spam')) $formFields['__test__']=array('type'=>'textarea', 'class'=>'hidden');
    
    $formAction=$this->CI->uri->get();
    if (isset($this->settings['action_query'])) $formAction.=$this->settings('action_query');
		$form=new form($formAction,$this->form_id);
		$form->set_data($formFields, $this->settings('title',$this->form_id) );
    // Is er een wachtwoord wat een extra check verlangt?
    if ($this->settings('add_password_match')) {
  		$form->add_password_match();
    }
    if ($this->settings('placeholders_as_labels')) $form->add_placeholders();
		if (isset($formFieldSets)) $form->set_fieldsets($formFieldSets);
    if ($formButtons) $form->set_buttons($formButtons);

		// Validate, and test filled form
    $isValidated=$form->validation($this->form_id);
    $isSpam=false;
  
		if ($isValidated) {
      $data=$form->get_data();
    
      // Spamcheck?
      if ($this->settings('check_for_spam')) {
        $this->CI->load->library('spam');
        $isSpam=$this->CI->spam->check($data,'__test__');
        $this->settings['spam_rapport']=$this->CI->spam->get_rapport();
        $data['int_spamscore']=$this->CI->spam->get_score();
        unset($formFields['__test__']);
        unset($data['__test__']);
      }
    
      if (!$isSpam) {
        // Do the Action(s)
        $formaction=$this->settings('formaction');
        if (!is_array($formaction)) $formaction=array($formaction);
        foreach ($formaction as $faction) {
          $action='action_'.$faction;
          $this->CI->load->model($faction,$action);
          $this->CI->$action->initialize($this->settings)->fields( $formFields );
          $this->CI->$action->set_form_id($this->form_id);
          $result=$this->CI->$action->go( $data );
    			if (!$result) {
    		    $errors.=$this->CI->$action->get_errors();
    			}
          else {
            if ($this->settings('prevend_double_submit')) {
              $this->CI->session->set_userdata($this->form_id.'__submit',true);
              redirect($formAction);
            }
            $html.=$this->_view_thanks($result);
          }
        }
      }
      else {
        $errors='<p class="error">'.lang('error_spam').'</p>';
      }
		}

    if (!$isValidated or $isSpam or $this->settings('always_show_form',false))	{
			// Form isn't filled or validated or regarded as spam: show form and validation errors
      if ($this->settings('validation_place','form')=='field') {
        $form->show_validation_errors(true);
      }
      else {
        $error=$form->validation_errors($this->form_id,'<p class="error">', '</p>');
        if (!empty($error)) {
          if ($this->settings('validation_place','form')=='form')
            $errors.=$error;
          else
            $errors.='<p class="error">'.$this->settings('validation_place','').'</p>';
        }
      }
			$html.=$form->render();
		}
    
    if (isset($this->settings['title'])){
      return $this->CI->view('forms',array('title'=>$this->settings['title'],'form'=>$html,'errors'=>$errors),true);
    } else {
      return $this->CI->view('forms',array('form'=>$html,'errors'=>$errors),true);
    }
	}
  
  
  /**
   * Toont melding als formulier is ingevuld
   *
   * @param string $errors 
   * @return string
   * @author Jan den Besten
   * @ignore
   */
  private function _view_thanks($result,$errors='') {
    if ($this->settings('prevend_double_submit')) {
      $this->CI->session->unset_userdata($this->form_id.'__submit');
    }
    if ($this->settings('thanks_model')) {
      $model=get_prefix($this->settings('thanks_model'),'.');
      $method=get_suffix($this->settings('thanks_model'),'.');
      if (!isset($this->CI->$model)) $this->CI->load->model($model);
      return $this->CI->$model->$method($result);
    }
    $html=div('message').$this->settings('thanks','Thank you!')._div();
    return $html;
    // return $this->CI->view('forms',array('title'=>$this->settings['title'],'form'=>$html,'errors'=>$errors),true);
  }
  
  
  /**
   * Geeft instelling
   *
   * @param string $item 
   * @param string $default[NULL]
   * @return mixed
   * @author Jan den Besten
   * @ignore
   */
	private function settings($item,$default=NULL) {
		return el($item,$this->settings,$default);
	}
  

}

?>
