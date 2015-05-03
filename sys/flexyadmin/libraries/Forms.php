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
	* Of:
	* 
	*         $this->CI->load->library('forms');
  *         $this->CI->forms->contact();
  * 
  * Je kunt de instellingen ook meegeven met:
  * 
  *         $this->CI->forms->initialize('naam_van_form',$config_array);
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
  private $spam=false;
  private $validated=false;
  
  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
    $this->CI->load->library('session');
    $this->CI->load->model('formaction');
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
    $formAction=$this->get_action();
    $thanks='';
		$html='';
    $errors='';
    
    // Test if allready submitted (and testing for that is possible)
    if ($this->settings('prevend_double_submit',false)) {
      $thanks=$this->CI->session->flashdata($this->form_id.'__thanks');
      if ($thanks) {
        if (!$this->settings('always_show_form',false)) {
          return $this->_view_thanks(true,$thanks);
        }
      }
    }
    
    // Test set to not fill again
    if ($this->settings('restrict_this_ip_days')) {
      $ip=$this->CI->input->ip_address();
      $this->CI->db->where('ip',$ip)->where('str_form',$this->form_id);
      $filled=$this->CI->db->get_row('log_forms_submit');
      if ($filled) {
        $date=mysql_to_unix($filled['dat_date']);
        $expire_date=unixdate_add_days($date,$this->settings('restrict_this_ip_days'));
        if (time()<$expire_date) {
          return $this->settings('restrict_message');
        }
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
      $formData=$this->CI->getform->by_name($flexyform);
      if ($formData) {
  			$formFieldSets=$formData['fieldsets'];
  			$formFields=$formData['fields'];
  			$formButtons=$formData['buttons'];
        // other settings
        $this->settings['title']=$formData['form']['str_title_'.$this->CI->site['language']];
        $this->settings['thanks']=$formData['form']['txt_text_'.$this->CI->site['language']];
        $emailField=find_row_by_value($formFields,'valid_email','validation',true);
        if ($emailField) {
          $this->settings['from_address_field']=key($emailField);
        }
      }
    }

    // Captcha?
    if (el('add_captcha',$this->settings)) {
      // first check if not set allready
      if (!find_row_by_value($formFields,'captcha','type')) {
        $formFields['_captcha']=array('label'=>lang('captcha'),'type'=>'captcha');
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
    
		$form=new form($formAction,$this->form_id);
    
    $framework=$this->CI->config->item('framework');
    if (isset($this->settings['framework'])) $framework=$this->settings('framework','default');
    $form->set_framework($framework);

		$form->set_data($formFields, $this->settings('title',$this->form_id) );
    // Is er een wachtwoord wat een extra check verlangt?
    if ($this->settings('add_password_match')) {
  		$form->add_password_match();
    }
    if ($this->settings('placeholders_as_labels')) $form->add_placeholders();
		if (isset($formFieldSets)) $form->set_fieldsets($formFieldSets);
    if ($formButtons) $form->set_buttons($formButtons);

		// Validate, and test filled form
    $this->validated=$form->validation($this->form_id);
    $this->spam=false;
  
		if ($this->validated) {
      $data=$form->get_data();
    
      // Spamcheck?
      if ($this->settings('check_for_spam')) {
        $this->CI->load->library('spam');
        $this->spam=$this->CI->spam->check($data,'__test__');
        $this->settings['spam_rapport']=$this->CI->spam->get_rapport();
        $data['int_spamscore']=$this->CI->spam->get_score();
        unset($formFields['__test__']);
        unset($data['__test__']);
      }
    
      if (!$this->spam) {
        // Do the Action(s)

        if ($this->settings('restrict_this_ip_days')) {
          // remove (old) entries
          $this->CI->db->where('ip',$ip)->where('str_form',$this->form_id);
          $this->CI->db->delete('log_forms_submit');
          $set=array(
            'str_form'  => $this->form_id,
            'ip'        => $this->CI->input->ip_address(),
            'dat_date'  => unix_to_mysql(time())
          );
          $this->CI->db->set($set);
          $this->CI->db->insert('log_forms_submit');
        }
        
        $formaction=$this->settings('formaction');
        if (!is_array($formaction)) $formaction=array($formaction);
        $result=true;
        foreach ($formaction as $faction) {
          $action='action_'.$faction;
          $this->CI->load->model($faction,$action);
          $this->CI->$action->initialize($this->settings)->fields( $formFields );
          $this->CI->$action->set_form_id($this->form_id);
          $this_result=$this->CI->$action->go( $data );
    			if (!$this_result) {
    		    $errors.=$this->CI->$action->get_errors();
    			}
          $result=($result AND $this_result);
          if (method_exists($this->CI->$action,'return_data')) {
            $data=$this->CI->$action->return_data();
          }
        }
        if ($result) {
          if ($this->settings('prevend_double_submit',false)) {
            $this->CI->session->set_flashdata($this->form_id.'__thanks', $this->_view_thanks($result) );
            redirect($formAction, 'location');
          }
          $html.=$this->_view_thanks($result);
        }
      }
      else {
        $errors='<p class="error">'.lang('error_spam').'</p>';
      }
		}

    if (!$this->validated or $this->spam)	{
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
      $html.=$thanks;
			$html.=$form->render($this->settings('class',''));
		}

    if (isset($this->settings['title'])){
      return $this->CI->load->view('forms',array('title'=>$this->settings['title'],'form'=>$html,'errors'=>$errors),true);
    } else {
      return $this->CI->load->view('forms',array('form'=>$html,'errors'=>$errors),true);
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
  private function _view_thanks($result='',$thanks='') {
    if ($thanks) {
      return $thanks;
    }
    if ($this->settings('thanks_model')) {
      $model=get_prefix($this->settings('thanks_model'),'.');
      $method=get_suffix($this->settings('thanks_model'),'.');
      if (!isset($this->CI->$model)) $this->CI->load->model($model);
      $html=$this->CI->$model->$method($result);
    }
    else {
      $html=div('message').$this->settings('thanks','')._div();
    }
    return $html;
  }
  
  
  /**
   * Geeft form action
   *
   * @return string
   * @author Jan den Besten
   */
  private function get_action() {
    $action=$this->CI->uri->get();
    if (isset($this->settings['action_query'])) $action.=$this->settings('action_query');
    return $action;
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
  
  
  /**
   * Geeft instellingen van een formulier
   *
   * @param string $form_id 
   * @return array
   * @author Jan den Besten
   */
  public function get_settings($form_id) {
    return el($form_id,$this->config,false);
  }
  
  /**
   * Geeft spam status van laatst gebruikte formulier
   *
   * @return bool
   * @author Jan den Besten
   */
  public function is_spam() {
    return $this->spam;
  }
  
  /**
   * Geeft validation status van laatst gebruikte formulier
   *
   * @return bool
   * @author Jan den Besten
   */
  public function is_validated() {
    return $this->validated;
  }
  
}

?>
