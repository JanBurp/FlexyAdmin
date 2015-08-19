<?php 
/** \ingroup libraries
 * Form
 * 
 * Met deze class kun je formulieren maken, valideren en uitlezen.
 * Het combineerd de standaard CodeIgniter [Form Helper](http://codeigniter.com/user_guide/helpers/form_helper.html) en [Form Validation](http://codeigniter.com/user_guide/libraries/form_validation.html) en nog wat extra's. 
 * 
 * Voorbeeld
 * =========
 * 
 * Hieronder een voorbeeld voor een eenvoudig contactformulier:
 * 
 *  
 *     // Maak array met de velden voor het formulier
 *     $form_fields = array(
 *       'str_name'		=> array(
 *                         'label'       => 'Naam',
 *                         'validation'  => 'required'
 *                        ),
 *       'email_email'	=> array(
 *                         'label'       => 'Email',
 *                         'validation'  => 'required|valid_email'
 *                        ),
 *       'txt_text'	  => array(
 *                         'fieldset'    => 'volgende',
 *                         'label'       => 'Vraag',
 *                         'type'        => 'textarea',
 *                         'validation'=>'required'
 *                        ),
 *     );
 *  
 *     // Maak formulier
 *     $form=new form( 'contact' );                      // Geef de uri van de action pagina mee
 *     $form->set_data( $form_fields, 'Contact' );       // Stel de velden en de naam van het formulier in
 *  
 *     // Kijk of formulier is ingevuld en goed door de validatie is gekomen
 *     if ( $form->validation() ) {
 *      // Ja, lees de formulier data uit en doe er iets mee
 *      $data=$form->get_data();
 *      ...
 *     }
 *     else {
 *      // Zo niet, toon dan het formulier, eventueel met validation errors
 *      $validation_errors=validation_errors('<p class="error">', '</p>');
 *      $htmlForm=$form->render();
 *      echo $validation_errors . $htmlForm;
 *     }
 * 
 * Velden instellen
 * ================
 * 
 * Wat je iig per veld kunt instellen:
 * 
 * - label - Het label dat voor het veld komt, als dit niet wordt meegegeven dan wordt een label gemaakt aan de hand van de naam van het veld (zonder prefix)
 * - type - Type veld, mogelijk zijn de standaard HTML velden: input,textarea,select, etc. (defaultwaarde is 'input')
 * - options - als het type _select_ is moet je hier een array meegeven van de mogelijke opties
 * - validation - validatie van het veld. Zie de [Form Validation van CodeIgniter](http://codeigniter.com/user_guide/libraries/form_validation.html) voor mogelijke waarden.
 * 
 * 
 * @author Jan den Besten
 */
class Form {

	private $CI;

  private $form_id;
	private $caption;
	private $action;
	private $data=array();
	private $postdata=array();
	private $add_password_match=array();
	private $hash_passwords=false;
	private $hasHtmlField;
	private $isValidated;
	private $captchaWords;
	private $fieldsets;
	private $fieldsetClasses;
	private $when;  // javascript
	private $buttons;
  private $validation_error = false;
  // private $validation_error_class = 'error';
  private $framework = 'default';
  private $view_path='admin/form';
  
  private $styles=array(
    'default'   => array(
      'form'                  => '',
      'fieldset'              => 'flexyFormFieldset',
      'fieldset_buttons'      => 'flexyFormButtons',
      'fieldset_info'         => true,
      'field_container'       => 'flexyFormField',
      'field_html'            => 'flexyFormHtml',
      'field_container_info'  => true,
      'label'                 => '',
      'field'                 => '',
      'field_info'            => true,
      'button'                => 'button',
      'validation_error_class'=> 'error',
      'status_default'        => '',
      'status_success'        => 'has-success',
      'status_warning'        => 'has-warning',
      'status_error'          => 'has-error',
    ),
    'bootstrap' => array(
      'form'                  => '',
      'fieldset'              => '',
      'fieldset_buttons'      => '',
      'fieldset_info'         => false,
      'field_html'            => '',
      'field_container'       => 'form-group',
      'field_container_info'  => false,
      'label'                 => 'control-label',
      'field'                 => 'form-control',
      'field_info'            => false,
      'button'                => 'btn btn-primary',
      'validation_error_class'=> 'alert alert-danger',
      'status_default'        => '',
      'status_success'        => 'has-success',
      'status_warning'        => 'has-warning',
      'status_error'          => 'has-error',
    ),
    
  );

  /**
   */
	function __construct($action="",$form_id='') {
		$this->CI = &get_instance();
    $this->CI->lang->load('form');
		$this->CI->load->library('form_validation');
		$this->init($action,$form_id);
	}

  /**
   * (Her)initialiseer het formulier
   *
   * @param string $action default=''
   * @param string $form_id='
   * @return void
   * @author Jan den Besten
   */
	public function init($action="",$form_id='') {
    $this->set_form_id($form_id);
		$this->set_action($action);
		$this->set_caption();
		$this->set_labels();
		$this->data=array();
		$this->postdata=array();
		$this->add_password_match(FALSE);
		$this->set_fieldsets();
		$this->hasHtmlField=false;
		$this->show_buttons();
		$this->set_captcha_words();
		$this->when();
    return $this;
	}

  /**
   * Stelt de id in van deze form. Dit is nodig om meerdere formulieren op eenzelfde pagina te onderscheiden
   *
   * @param string $form_id 
   * @return void
   * @author Jan den Besten
   */
	public function set_form_id($form_id='') {
		$this->form_id=$form_id;
    return $this;
	}

  /**
   * set_action()
   *
   * @param string $action 
   * @return void
   * @author Jan den Besten
   */
	public function set_action($action="") {
    if (empty($action)) $action=$this->CI->uri->get();
		$this->action=$action;
    return $this;
	}
  
  /**
   * Stel de css styling in. Mogelijke opties zijn: default|bootstrap
   *
   * @param string $style default='default
   * @return void
   * @author Jan den Besten
   */
  public function set_framework($style="default") {
    $this->framework=$style;
    return $this;
  }
  
  /**
   * Stel het pad in waar de views van de form staan.
   *
   * @param string $path 
   * @return void
   * @author Jan den Besten
   */
  public function set_view_path($path='') {
    if (!empty($path)) {
      $this->view_path=$path;
    }
    return $this;
  }

  /**
   * Stel formulier kop in
   *
   * @param string $caption De kop 
   * @return void
   * @author Jan den Besten
   */
	public function set_caption($caption="") {
		$this->caption=$caption;
    return $this;
	}

  /**
   * Hiermee kun je in één keer alle ingesteld labels vervangen
   *
   * @param array $labels 
   * @return void
   * @author Jan den Besten
   */
	public function set_labels($labels=NULL) {
		if (isset($labels) and !empty($labels)) {
			foreach($labels as $name=>$label) {
				$this->set_label($name,$label);
			}
		}
	}

  /**
   * Vervang een bestaande label
   *
   * @param string $name Naam van het veld waar je de label van wilt aanpassen
   * @param string $label Nieuwe label
   * @return void
   * @author Jan den Besten
   */
	public function set_label($name,$label) {
		$this->data[$name]["label"]=$label;
	}

  /**
   * Hiermee stel je alle formuliervelden in en eventueel een kop
   *
   * @param array $data default=NULL
   * @param string $caption['']
   * @return void
   * @author Jan den Besten
   */
	public function set_data($data=NULL,$caption="") {
		if (isset($data) and !empty($data)) {
			foreach ($data as $name => $field) {
        // Default name
				$this->data[$name]=$this->_check_default_field($name,$field);
        // Password always empty value
        if (isset($field['table']) and $field['table']=='cfg_users' and $field['type']=='password') {
          $this->data[$name]['value']='';
        }
        // Fieldset
        if (isset($field['fieldset'])) {
          $fieldset=$field['fieldset'];
          if (!in_array($fieldset,$this->fieldsets)) {
            $this->fieldsets[]=$fieldset;
          }
        }
			}
		}
		$this->set_caption($caption);
	}
  
  /**
   * Laat validation error zien bij de velden zelf
   *
   * @param mixed $class default='error', als TRUE, of een stringwaarde, dan worden de errors bij de velden getoond. De stringwaarde wordt de meegegeven class.
   * @return object this
   * @author Jan den Besten
   */
  public function show_validation_errors($class='error') {
    if (empty($class)) {
      $this->validation_error=false;
    }
    else {
      $this->validation_error=true;
      if (is_string($class)) $this->validation_error_class=$class;
    }
    return $this;
  }

  /**
   * Geeft alle formuliervelden een placeholder attribuut mee als die er nog niet is
   *
   * @return void
   * @author Jan den Besten
   */
  public function add_placeholders() {
    foreach ($this->data as $key=>$data) {
      $label=$data['label'];
      $this->data[$key]['attr']['placeholder']=$label;
    }
  }

  /**
   * Geef alle formuliervelden een empty_value mee voor het placeholder attribuut
   *
   * @return void
   * @author Jan den Besten
   * @deprecated
   */
  public function prepare_for_clearinput() {
    foreach ($this->data as $key=>$data) {
      $label=$data['label'];
      $this->data[$key]['attr']['empty_value']=$label;
      $this->data[$key]['attr']['placeholder']=$label;
    }
  }
  

  /**
   * Voeg een extra wachtwoord veld toe zodat een wachtwoord dubbel moet worden ingevoerd. Checkt automatisch of ze overeenkomen.
   *
   * @param array $args default=TRUE Als TRUE dan worden automatisch alle pwd en gpw (paswoord) velden gedubbeld. Geef anders een array met de opties
   * @return void
   * @author Jan den Besten
   */
	public function add_password_match($args=TRUE) {
    $opts=array('fields'=>array('gpw','pwd'),'label'=>' (2x)','name'=>'matches','class'=>'matches');
    if (is_array($args)) $opts=array_merge($opts,$args);
    if (is_bool($args) and !$args) $opts=FALSE;
    $this->add_password_match=$opts;
    if ($this->add_password_match) $this->data=$this->_add_matching_password($this->data);
	}
	
	/**
	 * Stel in dat alle passwords moeten worden gehashed
	 *
	 * @param bool $hash default=TRUE
	 * @return void
	 * @author Jan den Besten
	 */
  public function hash_passwords($hash=true) {
		$this->hash_passwords=$hash;
	}

  /**
   * Stel hier de mogelijke captcha woorden in voor het captcha veld
   *
   * @param string $words 
   * @return void
   * @author Jan den Besten
   */
	public function set_captcha_words($words=NULL) {
		$this->captchaWords=$words;
	}

  /**
   * Voorwaarde wanneer een veld wordt getoond
   *
   * @param string $when 
   * @param string $field 
   * @return void
   * @author Jan den Besten
   */
	public function when($when='',$field='') {
		if (empty($when))
			$this->when=array();
		else {
			$this->when[$field]=$when;
		}
	}

  /**
   * undocumented function
   *
   * @param string $name 
   * @param string $field 
   * @return void
   * @author Jan den Besten
   * @internal
   */
	private function _check_default_field($name, $field) {
		if (!isset($field['type']))				$field['type']="input";
		if (!isset($field['name']))				$field['name']=$name;
		if (!isset($field['fieldset']))		$field['fieldset']='fieldset';
		if (!isset($field['label']))			$field['label']=ucfirst(remove_prefix($name));
		if (!isset($field['class']))			$field['class']="";
		if (!isset($field['value']))			$field['value']="";
		if (!isset($field['validation']))	$field['validation']="";
		return $field;
	}

  /**
   * Zelfde als set_buttons()
   *
   * @param array $buttons 
   * @return void
   * @author Jan den Besten
   */
	public function show_buttons($buttons=NULL) {
		$this->set_buttons($buttons);
	}
  
  /**
   * Stel de buttons in de getoond moeten worden
   *
   * @param array $buttons default=NULL als dit leeg is dan worden de drie standaard buttons getoond (cancel,reset,submit)
   * @return void
   * @author Jan den Besten
   */
	public function set_buttons($buttons=NULL) {
		if (empty($buttons)) {
			$buttons=array(	'cancel'	=> array( "value" => lang("form_cancel"), "class"=>$this->styles[$this->framework]['button']." cancel", "onClick" => "window.history.back()"),
											'reset'		=> array( "value" => lang("form_reset"), "class"=>$this->styles[$this->framework]['button']." reset"),
											'submit'	=> array( 'type'=>'submit',"value"=>lang("form_submit")));
		}
		foreach ($buttons as $name => $button) {
			if (!isset($button['name'])) 	$buttons[$name]['name']=$name;
			if (!isset($button['class'])) $buttons[$name]['class']=$this->styles[$this->framework]['button'];
			if (isset($button['submit'])) $buttons[$name]['class'].=" submit";
		}
		$this->buttons=$buttons;
	}

  /**
   * Verwijderd de submit button uit de buttonlijst
   *
   * @return void
   * @author Jan den Besten
   */
	public function no_submit() {
		foreach ($this->buttons as $name => $button) {
			if (isset($button['submit'])) unset($this->buttons[$name]);
		}
	}

  /**
   * Stel de fieldsets in
   *
   * @param string $fieldsets default=array('fieldset')
   * @return void
   * @author Jan den Besten
   */
	public function set_fieldsets($fieldsets=array('fieldset')) {
		if (!is_array($fieldsets)) $fieldsets=array($fieldsets);
		$this->fieldsets=$fieldsets;
	}
  
  /**
   * Voeg een fieldset toe
   *
   * @param string $fieldset['']
   * @param string $class['']
   * @author Jan den Besten
   */
	public function add_fieldset($fieldset='',$class='') {
		$this->fieldsets[]=$fieldset;
		if (empty($class)) $class=$this->fieldsetClasses['fieldset'];
		$this->fieldsetClasses[$fieldset]=$class;
	}

  /**
   * Voegt de matching passwords toe
   *
   * @param string $data 
   * @return void
   * @author Jan den Besten
   * @internal
   */
	private function _add_matching_password($data) {
		foreach ($data as $name => $field) {
			$pre=get_prefix($name);
			if (in_array($pre,$this->add_password_match['fields'])) {
				$match=$field;
				$match['matches']=$name;
				$match['name'].='__'.$this->add_password_match['name'];
				$match['label'].=$this->add_password_match['label'];
				$match['class'].=$this->add_password_match['class'];
				$match['validation']='|matches['.$name.']';
				$data=array_add_after($data,$name,array($match['name']=>$match));
				$data[$name]['validation'].='|matches['.$match['name'].']';
			}
		}
		return $data;
	}

  /**
   * Checkt of formulier matched met juiste POST data
   *
   * @param string $form_id 
   * @return bool
   * @author Jan den Besten
   * @internal
   */
  private function _this_form($form_id='') {
    if (!empty($form_id)) {
      if (!isset($_POST['__form_id'])) return FALSE;
      if ($_POST['__form_id']!=$form_id) return FALSE;
    }
    return TRUE;
  }

  /**
   * Kijkt of het formulier goed is gevalideerd, geeft TRUE als dat zo is, anders FALSE
   *
   * @param string $form_id default=''
   * @return boolean TRUE als formulier door validatie is gekomen
   * @author Jan den Besten
   */
	public function validation($form_id='') {
		$data=$this->data;
    
    if (!$this->_this_form($form_id)) return FALSE;
    
		$hasCaptcha=FALSE;
    
		foreach($data as $name=>$field) {

			// Change multiple data to string (str_, medias_)
			if (isset($field['multiple']) and isset($_POST[$name]) and is_array($_POST[$name]) and get_prefix($name)!='rel') {
				if (isset($_POST[$name.'__hidden']))
					$_POST[$name]=$_POST[$name.'__hidden'];
				else
					$_POST[$name]=implode('|',array_unique($_POST[$name]));
			}

			// set extra validation rules for passwords if new (required)
      if ($field['type']=='password' and !isset($field['matches'])) {
        $id=el(array('id','value'),$data,-1);
        if ($id<0) {
          // if new 'user' password is required
          $field['validation']='required|'.$field['validation'];
        }
      }
      
      // captcha
			if ($field['type']=='captcha') {
        $this->CI->load->helper('captcha');
			  $hasCaptcha=$name; 
        $cap=get_captcha();
        $code=$cap['word'];
        $field['validation']='required|valid_same['.$code.']';
			}
      
      $set_rule=true;
      // Check if file and required
      if ($field['type']==='file' and has_string('required',$field['validation'],false)) {
        if ( ! empty($_FILES[$field['name']]['name']) ) {
          $set_rule=false;
        }
      }
      $field['validation']=trim($field['validation'],'|');
      
      if ($set_rule) $this->CI->form_validation->set_rules($field["name"], $field["label"], $field["validation"]);
			
			$this->data[$name]["repopulate"]=$this->CI->input->post($name);
		}

		log_('info',"form: validation");
    
		$this->isValidated=$this->CI->form_validation->run();

		foreach ($data as $name => $field) {
      $repopulate=$this->CI->input->post($name);
      if (is_array($repopulate)) {
        $repopulate=array_values($repopulate);
        $repopulate=array_combine($repopulate,$repopulate);
      }
			$this->data[$name]["repopulate"]=$repopulate;
		}
    
		return $this->isValidated;
	}

  
  /**
   * Geeft validation errors van gegeven form id
   *
   * @param string $form_id 
   * @param string $open_tag[''] 
   * @param string $close_tag[''] 
   * @return string
   * @author Jan den Besten
   */
  public function validation_errors($form_id,$open_tag='',$close_tag='') {
    if (!$this->_this_form($form_id)) return '';
    return validation_errors($open_tag,$close_tag);
  }


  /**
   * Zelfde als reset()
   *
   * @return void
   * @author Jan den Besten
   */
	public function reset_data() {
		$this->reset();
	}
  
  /**
   * Reset alle data (maakt de _value_ en _repopulate_  velden leeg)
   *
   * @return void
   * @author Jan den Besten
   */
	public function reset() {
		foreach ($this->data as $key => $field) {
			$this->data[$key]["value"]="";
			$this->data[$key]["repopulate"]="";
		}
	}


  /**
   * function prepare_field($name,$value)
   *
   * This functions prepares data coming from a form. Some fields needs to be adjusted, ie: checkboxes for example
   *
   * @param string $name Name of field
   * @param mixed	$value Data to prepare
   * @return mixed The prepped data
   * @internal
   */
	private function prepare_field($name,$value) {
		$out=$value;
		$value=$this->_value_from_hidden($name,$value);
		$data=$this->data[$name];
    $type=el("type",$data);
    switch ($type) {
      case "checkbox" :
        $out=true;
        if ($value==false) {
           $out='';
        }
        $this->data[$name]['value']=$out;
        break;
    }
		return $out;
	}

  /**
   * Get value from hidden fields
   *
   * @param string $name 
   * @param string $value 
   * @return mixed value
   * @author Jan den Besten
   * @internal
   */
	private function _value_from_hidden($name,$value) {
		if (is_array($value) or empty($value)) {
			// multi options (string)
			$hidden=$this->CI->input->post($name.'__hidden');
			if ($hidden) {
				$out=$hidden;
			}
			else {
				if (is_array($value) and count($value)>0)	$out=implode("|",$out);
			}
		}
		return $value;
	}


  /**
   * function get_postdata$table)
   *
   * Update the data in form
   * @param string $table Table to update
   * @return bool	Validation succes
   * @internal
   */
	private function get_postdata() {

		$data=array();
		$joins=array();

		if ($this->isValidated) {
			foreach($this->data as $name=>$field) {
				$pre=get_prefix($name);
				$value=$this->CI->input->post($name);
				// remove matches if any
        if ($this->add_password_match) {
          if (in_array($pre,$this->add_password_match['fields']) and isset($field['matches'])) {
            unset($this->data[$name]);
            continue;
          }
        }
				/**
				 *  Is data from join?
				 */
				if ($pre==$this->CI->config->item('REL_table_prefix')) {
					if (empty($value)) $value=array();
					$hidden=$this->CI->input->post($name.'__hidden');
					if ($hidden) $value=$hidden;
					if (!is_array($value)) $value=explode('|',$value);
					$joins[$name]=array();
					foreach ($value as $key => $id) {
						$joins[$name][$id]=array('id'=>$id);
					}
				}

				/**
				* Password hash it (or leave it same when empty)
				*/
				elseif (in_array($pre,array('gpw','pwd'))) {
					if ($this->hash_passwords and !empty($value))
						$data[$name]=$this->CI->ion_auth_model->hash_password($value);
					else
						$data[$name]=$value;
				}
				
				/**
				 * Normal data
				 */
				else {
					$data[$name]=$this->prepare_field($name,$value);
				}
			}
			
			$this->post_data=array_merge($data,$joins);
		}
		return $this->post_data;
	}

  /**
   * Geeft de ingevulde data van het formulier
   *
   * @return array Ingevulde data
   * @author Jan den Besten
   */
	public function get_data() {
		$data=array();
		if (!empty($this->post_data)) {
			$data=$this->post_data;
    }
    else {
      // Nodig om checkboxes goed mee te krijgen
      $data=$this->get_postdata();
    }
		if (empty($data)) {
			foreach($this->data as $name=>$field) {
				if (isset($field['repopulate']))
					$data[$name]=$field['repopulate'];
				else
					$data[$name]=$field['value'];
			}
		}
    unset($data['captcha']);
    unset($data['_captcha']);
		return $data;
	}

  /**
   * Geeft het gerenderde formulier terug (HTML)
   *
   * @param string $class default='flexyForm' eventueel mee te geven CSS class dat aan formulier wordt gegeven
   * @return string	formulier
   */
	public function render($class='flexyForm') {
		$this->CI->lang->load("form");
    $styles=$this->styles[$this->framework];

		$data=$this->data;
    
		// fieldsets with fields
		$nr=1;
    $fieldsets=array();
    
		foreach ($this->fieldsets as $title) {
      $fieldsets[$nr]=array(
        'title'  => ($title=='fieldset'?$this->caption:trim($title)),
        'id'     => 'fieldset_'.$this->form_id.'_'.$nr,
        'class'  => $styles['fieldset'],
        'fields' => '',
      );
			foreach($data as $name => $field) {
				if ( $title==$field['fieldset'] or
            ($nr==1 and (empty($field['fieldset']) or $field['fieldset']=='fieldset') ))	$fieldsets[$nr]['fields'].=$this->render_field($field['name'],$field,$class);
			}
      $nr++;
		}

		// Buttons fieldset
    $fieldsets[$nr]=array(
      'title'   => '',
      'id'      => 'fieldset_'.$this->form_id.'_buttons',
      'class'   => $styles['fieldset_buttons'],
      'fields'  => '',
    );
		foreach ($this->buttons as $name => $button) {
			if (isset($button['submit']))
				$fieldsets[$nr]['fields'].=form_submit($button);
			else
				$fieldsets[$nr]['fields'].=form_reset($button);
		}
    
    $form=array(
      'id'        => $this->form_id,
      'form_id'   => $this->form_id,
      'class'     => $class,
      'action'    => site_url($this->action),
      'method'    => 'POST',
      'fieldsets' => $fieldsets
    );
    // trace_($form);
    $render=$this->CI->load->view($this->view_path.'/form',$form,true);

		// prepare javascript for conditional field showing
		if (!empty($this->when)) {
			$json=array2json($this->when);
			$render.="\n<script language=\"javascript\" type=\"text/javascript\">\n<!--\nvar formFieldWhen=".$json.";\n-->\n</script>\n";
		}
		log_('info',"form: rendering");
		return $render;
	}

  /**
   * Rendered een veld
   *
   * @author Jan den Besten
   * @internal
   */
	private function render_field($name,$field,$form_class="") {
		$pre=get_prefix($name);
		if ($pre==$name) $pre="";
    $styles=$this->styles[$this->framework];
    
    // field class
    $field['container_class']=$styles['field_container'];
    $field['label_class']=$styles['label'];
    $class=$field['class'];
    // class: field types & status
    if ($styles['field_info']) {
      // $class.=" $pre $name";
      $class.=' '.$field['type'];
		  if (isset($field['multiple'])) {
        $class.=" ".$field['multiple'];
      }
    }
    if (!empty($field["repopulate"])) $field["value"]=$field["repopulate"];
    $field['container_class']=trim($field['container_class'].' '.$class);
    $class=trim($styles['field'].' '.$class);
    
    // class: validation
    if (isset($field['validation']) and has_string('required',$field['validation'])) $class.=" required";
    
    // attributes
    $attr=array_merge( el('attr',$field,array()), el('attributes',$field,array()) );
    $attr["name"]  = $name;
    $attr["id"]    = (isset($field['id'])?$id:$name);
    $attr["class"] = $class;
    $attr["value"] = $field["value"];
    if (isset($field['placeholder'])) $attr['placeholder']=$field['placeholder'];
    if (isset($field['readonly'])) $attr['readonly']=$field['readonly'];
    if (isset($field['disabled'])) $attr['disabled']=$field['disabled'];

    // Status / Validation error
    $field['status']=$this->styles[$this->framework]['status_default'];
    if ($field['value']) $field['status']=$this->styles[$this->framework]['status_success'];
    if ($this->validation_error) {
      $field['validation_error']=form_error($field['name'],'<span class="'.$this->styles[$this->framework]['validation_error_class'].'" role="alert"> ','</span>');
      if ($field['validation_error']) $field['status']=$this->styles[$this->framework]['status_error'];
    }
    if (has_string('required',$field['validation'])) $field['status'].=' required';
    
		// When (javascript triggers)
		if (!empty($field['when'])) $this->when($field['when'],$name);
    
		switch($field["type"]):

			case "hidden":
        $field['container_class'].='hidden';
				$field['control']='<input type="hidden" '.attributes($attr).'>';
				break;
        
      case 'captcha':
        $this->CI->load->helper('captcha');
  			$vals = array(
  							'img_path'	 	=> assets().'_thumbcache/',
  							'img_url'	 		=> site_url(assets().'_thumbcache').'/',
  							'img_width'	 	=> '125',
  							'img_height' 	=> '25',
  							'expiration' => '600',
  						);
  			if ($this->captchaWords!=NULL) $vals['word']=random_element($this->captchaWords);
  			$cap=create_captcha($vals);
        save_captcha($cap);
        $field['control']=div('captcha').$cap['image'].form_hidden($name.'__captcha',str_reverse($cap['word'])).form_input($attr)._div();
        $field['control']=div('captcha').$cap['image'].form_input($attr)._div();
        break;

			case "html":
				$field['control']=$field['html'];
				break;

			case "checkbox":
				if (!empty($attr["value"]))
					$attr["checked"]="checked";
				else
					$attr["checked"]="";
        if (empty($attr['value'])) $attr['value']=1;
				$field['control']=form_checkbox($attr);
				break;

			case 'radio':
				$options=$field['options'];
				$value=$field['value'];
        $field['control']='';
				foreach ($options as $option => $optLabel) {
					$attr['value']=$option;
					if ($value==$option) $attr['checked']='checked'; else $attr['checked']='';
					$attr['id']=str_replace('.','_',$name.'__'.$option);
          $for=$attr['id'];
          $labelAttr=$attr;
          $labelAttr['class'].=' optionLabel';
          unset($labelAttr['id']);
          $field['control'].=div('radioOption '.$option).form_radio($attr).form_label($optLabel,$for,$labelAttr)._div();
				}
				break;

			case "htmleditor":
				$this->hasHtmlField=true;
			case "textarea":
				if ($field["type"]=="textarea") {
					$attr["rows"]=5;
					$attr["cols"]=60;
				}
				$field['control']=form_textarea($attr);
				break;

			case 'select':
			case 'dropdown':
			case 'ordered_list':
			case 'image_dropdown':
			case 'image_dragndrop':
        $field['control']='';
				$extra="";
				$options=el("options",$field);
				$value=$attr["value"];
        // $button=el("button",$field);
				if (isset($field["path"])) 	$extra.=" path=\"".$field["path"]."\"";
				if (isset($field["multiple"]) or is_array($value)) {
					$extra.=" multiple=\"multipe\" ";
					$name.="[]";
					if (is_array($value)) {
						$value=array_keys($value);
          }
					else {
						$value=explode("|",$value);
          }
				}
				$extra.="class=\"".$attr["class"]."\" id=\"".$name."\"";
				//
				// Show images if it is an image dropdown
				//
				if ($field["type"]=="image_dropdown" or $field["type"]=="image_dragndrop") {
					// show values
					if (!is_array($value)) $medias=array($value); else $medias=$value;
					$field['control'].='<ul class="values '.$attr['class'].'">';
					$hiddenValue='';
					foreach($medias as $media) {
						if (!empty($media)) {
							$field['control'].='<li>'.show_thumb(array("src"=>$field["path"]."/".$media,"class"=>"media")).'</li>';
							$hiddenValue=add_string($hiddenValue,$media,'|');
						}
					}
					$field['control'].='</ul>';
					// hidden value, needed for jQuery dragndrop and sortable
					if ($field["type"]=="image_dragndrop")
						$hiddenName=$field['name'];
					else
						$hiddenName=$field['name'].'__hidden';
					$field['control'].=form_hidden($hiddenName,$hiddenValue);
				}
				//
				// Show all possible images as images instead of a dropdown
				//
				if ($field["type"]=="image_dragndrop") {
					$preName=get_prefix($field['name']);
					$field['control'].='<ul class="choices">';
					foreach($options as $img) {
						if (empty($img)) {
							$field['control'].='<li><img src="'.$this->CI->config->item('ADMINASSETS').'icons/flexyadmin_empty_image.gif" class="media empty" /></li>';
						}
						else {
							$image=$img['name'];
							if ($preName=='media' or !in_array($image,$medias)) $field['control'].='<li>'.show_thumb(array("src"=>$field["path"]."/".$image,"class"=>"media",'alt'=>$image,'title'=>$image)).'</li>';
						}
					}
					$field['control'].='</ul>';					
				}
				//
				// Normal dropdown (also normal image dropdown)
				//
				if ($field['type']=='select' or $field['type']=='dropdown' or $field['type']=='image_dropdown') {
					$field['control']=form_dropdown($name,$options,$value,$extra);
				}
				//
				// Ordered lists
				//
				if ($field['type']=='ordered_list') {
					// show (ordered) choices	
					$field['control'].='<ul class="list list_choices">';
					$value=$field['value'];
					foreach($options as $id=>$option) {
						if (!array_key_exists($id,$value)) $field['control'].='<li id="'.$id.'">'.$option.'</li>';
					}
					$field['control'].='</ul>';
					// show values
					$hiddenValue='';
					if (!is_array($value)) $value=array($value);
					$field['control'].='<ul class="list list_values '.$attr['class'].'">';
					foreach($value as $valID => $val) {
						if (!empty($val)) {
							if (isset($options[$valID])) {
								$field['control'].='<li id="'.$valID.'">'.$options[$valID].'</li>';
								$hiddenValue=add_string($hiddenValue,$valID,'|');
							}
						}
					}
					$field['control'].='</ul>';
					$field['control'].=form_hidden($field['name'].'__hidden',$hiddenValue);				
				}
        // if (isset($button)) {
        //   $field['control'].=div("add_button").anchor($button,icon("add"))._div();
        // }
				break;
				
			// #BUSY Form->Subfields
      // case "subfields":
      //   $out.=icon('new');
      //   $out.=div('sub');
      //   foreach ($field['value'] as $id => $subfields) {
      //     $first=true;
      //     foreach ($subfields as $subfieldName => $subfieldValue) {
      //       $preSub=get_prefix($subfieldName);
      //       $subAttr['name']=$name.'___'.$subfieldName.'[]';
      //       $subAttr['value']=$subfieldValue;
      //       switch ($preSub) {
      //         case 'id':
      //           if ($subfieldName=='id') {
      //             $out.=form_hidden($subAttr['name'],$subAttr['value']);
      //           }
      //           break;
      //         default:
      //           $labelClass=array();
      //           if ($first) {
      //             $labelClass=array('class'=>'first');
      //             $out.=icon('delete');
      //             $first=FALSE;
      //           }
      //           $out.=form_label($this->CI->ui->get($subfieldName),$subAttr['name'],$labelClass);
      //           if ($preSub=='txt') {
      //             $this->hasHtmlField=true;
      //             $subAttr["rows"]=5;
      //             $subAttr["cols"]=60;
      //             $subAttr['class']='htmleditor';
      //             $out.=form_textarea($subAttr);
      //           }
      //           else {
      //             $out.=form_input($subAttr);
      //           }
      //           break;
      //       }
      //     }
      //     $out.=br(2);
      //   }
      //   $out.=_div();
      //   break;

			case "file":
				$attr["class"].=" browse";
				$field['control']=form_upload($attr);
				break;
				
			case "image":
				$attr['src']=$attr['value'];
				$field['control']=img($attr);
				break;

			case "date":
				$date=trim(strval($field["value"]));
				if (($date=="0000-00-00") or ($date=="")) {
					$date=date("Y-m-d");
				}
				$attr["value"]=$date;
        $attr['type']='date';
				$field['control']=form_input($attr);
				break;
			case 'datetime':
				$date=trim(strval($field["value"]));
				if (($date=="0000-00-00 00:00:00") or ($date=="")) {
					$date=date("Y-m-d H:i:s");
				}
				$attr["value"]=$date;
        $attr['type']='datetime';
				$field['control']=form_input($attr);
				break;
			case "time":
				$time=trim(strval($field["value"]));
				if (($time=="00:00:00") or ($time=="")) {
					$time=date("H:i:s");
				}
				$attr["value"]=$time;
        $attr['type']='time';
				$field['control']=form_input($attr);
				break;

			case "password":
				if (substr($this->action,0,12)=='/admin/show/') {
					$attr['value']='';
					$field['control']=form_input($attr);
				}
				else
					$field['control']=form_password($attr);
				break;
        
			case "input":
			case "default":
			default:
        switch ($pre) {
          case 'email': $attr['type']='email'; break;
          case 'url': $attr['type']='url'; break;
          case 'int': $attr['type']='number'; break;
          case 'dec': $attr['type']='number'; break;
          case 'rgb': $attr['type']='color'; break;
        }
				$field['control']=form_input($attr);
		endswitch;
    
		if ($field["type"]=="hidden") return $field['control'];
    $field['horizontal_bootstrap']=($this->framework=='bootstrap' and has_string('form-horizontal',$form_class));
    $attributes=implode_attributes(el('attributes',$field,array()));
    $rendered_field=$this->CI->load->view($this->view_path.'/field',array('field'=>$field,'styles'=>$this->styles[$this->framework],'attributes'=>$attributes),true);
		return $rendered_field;
	}


  /**
   * Kijkt of een veld in het formulier een HTML editor nodig heeft
   *
   * @return bool True if one ore more fields is a html editor
   * @internal
   */
 	public function has_htmlfield() {
 		return $this->hasHtmlField;
	}


}

?>
