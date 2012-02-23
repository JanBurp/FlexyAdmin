<?
/**
 * FlexyAdmin V1
 *
 * form.php Created on 22-okt-2008
 *
 * @author Jan den Besten
 */


/**
 * Class Form (model)
 *
 * Handles form rendering
 *
 */

class Form {

	var $CI;

	var $caption;
	var $action;
	var $data=array();
	var $postdata=array();
	var $add_password_match=array();
	var $hash_passwords=false;
	var $hasHtmlField;
	var $isValidated;
	var $captchaWords;
	var $fieldsets;
	var $fieldsetClasses;
	var $when;  // javascript
	var $buttons;
	

	function __construct($action="") {
		$this->CI = &get_instance();
		$this->CI->load->library('form_validation');
		$this->init($action);
	}

	function init($action="") {
		$this->set_action($action);
		$this->set_caption();
		$this->set_labels();
		$this->data=array();
		$this->postdata=array();
		// $this->set_type();
		$this->add_password_match(FALSE);
		$this->set_templates();
		$this->set_fieldset_classes();
		$this->set_fieldsets();
		$this->hasHtmlField=false;
		$this->show_buttons();
		$this->set_captcha_words();
		$this->when();
		// $this->show_submit();
	}

	public function set_action($action="") {
		$this->action=$action;
	}

	public function set_caption($caption="") {
		$this->caption=$caption;
	}

	public function set_labels($labels=NULL) {
		if (isset($labels) and !empty($labels)) {
			foreach($labels as $name=>$label) {
				$this->set_label($name,$label);
			}
		}
	}

	public function set_label($name,$label) {
		$this->data[$name]["label"]=$label;
	}

	public function set_data($data=NULL,$caption="") {
		if (isset($data) and !empty($data)) {
			foreach ($data as $name => $field) {
				$this->data[$name]=$this->_check_default_field($name,$field);
			}
		}
		$this->set_caption($caption);
	}

  public function prepare_for_clearinput() {
    foreach ($this->data as $key=>$data) {
      $label=$data['label'];
      $this->data[$key]['attr']['empty_value']=$label;
    }
  }

	public function add_password_match($args=TRUE) {
		$opts=array('fields'=>array('gpw','pwd'),'label'=>' (2x)','name'=>'matches','class'=>'matches');
		if (is_array($args)) $opts=array_merge($opts,$args);
		if (is_bool($args) and !$args) $opts=FALSE;
		$this->add_password_match=$opts;
		if ($this->add_password_match) $this->data=$this->_add_matching_password($this->data);
	}
	
	public function hash_passwords($hash=true) {
		$this->hash_passwords=$hash;
	}

	public function set_captcha_words($words=NULL) {
		$this->captchaWords=$words;
	}

	public function when($when='',$field='') {
		if (empty($when))
			$this->when=array();
		else {
			$this->when[$field]=$when;
		}
	}

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

	public function show_buttons($buttons=NULL) {
		$this->set_buttons($buttons);
	}
	public function set_buttons($buttons=NULL) {
		if (empty($buttons)) {
			$buttons=array(	'cancel'	=> array( "value" => lang("form_cancel"), "class"=>"button cancel", "onClick" => "window.history.back()"),
											'reset'		=> array( "value" => lang("form_reset"), "class"=>"button reset"),
											'submit'	=> array( "submit"=>"submit", "value"=>lang("form_submit")));
		}
		foreach ($buttons as $name => $button) {
			if (!isset($button['name'])) 	$buttons[$name]['name']=$name;
			if (!isset($button['class'])) $buttons[$name]['class']="button";
			if (isset($button['submit'])) $buttons[$name]['class'].=" submit";
		}
		$this->buttons=$buttons;
	}

	public function no_submit() {
		foreach ($this->buttons as $name => $button) {
			if (isset($button['submit'])) unset($this->buttons[$name]);
		}
	}

/**
 * Template functions
 */
	public function set_templates() {
		$this->set_field_templates();
	}
	public function set_old_templates() {
		$this->set_field_templates("<div class=\"form_field %s\">","</div>");
		$this->set_fieldset_classes(array('fieldset'=>'formfields','buttons'=>'formbuttons'));
	}

	public function set_field_templates($start="<div class=\"flexyFormField %s\">",$end="</div>") {
		$this->tmpFieldStart=$start;
		$this->tmpFieldEnd=$end;
	}

	public function set_fieldset_classes($fieldsetClasses=array('fieldset'=>'flexyFormFieldset','buttons'=>'flexyFormButtons')) {
		$this->fieldsetClasses=$fieldsetClasses;
	}
	
	public function set_fieldsets($fieldsets=array('fieldset')) {
		if (!is_array($fieldsets)) $fieldsets=array($fieldsets);
		$this->fieldsets=$fieldsets;
	}
	public function add_fieldset($fieldset='',$class='') {
		$this->fieldsets[]=$fieldset;
		if (empty($class)) $class=$this->fieldsetClasses['fieldset'];
		$this->fieldsetClasses[$fieldset]=$class;
	}


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

	private function tmp($tmp,$class="") {
		return str_replace("%s",$class,$tmp);
	}

/**
 * function validation()
 *
 * Returns TRUE if validation passed
 *
 *TODO: ! multi options Validation
 *
 * @return bool	Validation succes
 */
	public function validation() {
		$data=$this->data;
		// trace_($data);
		$hasCaptcha=FALSE;

		foreach($data as $name=>$field) {

			// Change multiple data to string (str_, medias_)
			if (isset($field['multiple']) and isset($_POST[$name]) and is_array($_POST[$name]) ) { // and in_array(get_prefix($name),array('str','medias')) ) {
				if (isset($_POST[$name.'__hidden']))
					$_POST[$name]=$_POST[$name.'__hidden'];
				else
					$_POST[$name]=implode('|',array_unique($_POST[$name]));
				// strace_($_POST[$name]);
				// trace_($field);
			}

			// set validation rules
			$this->CI->form_validation->set_rules($field["name"], $field["label"], $field["validation"]);
			
			if ($field['type']=='captcha') $hasCaptcha=$name;
			$this->data[$name]["repopulate"]=$this->CI->input->post($name);
		}

		log_('info',"form: validation");
		$this->isValidated=$this->CI->form_validation->run();
		// validate captcha
		if ($hasCaptcha!=FALSE) {
			$value=$this->CI->input->post($hasCaptcha);
			$code=str_reverse($this->CI->input->post($hasCaptcha.'__captcha'));
			$this->isValidated=($value==$code);
		}

		if ($this->isValidated) {
			foreach ($data as $name => $field) {
				$this->data[$name]["repopulate"]=$this->CI->input->post($name);
			}
		}
		
		return $this->isValidated;
	}

	public function reset_data() {
		$this->reset();
	}
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
 *
 */
	private function prepare_field($name,$value) {
		$out=$value;
		$value=$this->_value_from_hidden($name,$value);
		$data=$this->data[$name];

		$type=el("type",$data);
		switch ($type) {
			case "checkbox" :
				if ($value=="true")
					$out=1;
				else
					$out=0;
				break;
		}
		return $out;
	}

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

	public function get_data() {
		$data=array();
		if (!empty($this->post_data))
			$data=$this->post_data;
		else
			$data=$this->get_postdata();	
		if (empty($data)) {
			foreach($this->data as $name=>$field) {
				if (isset($field['repopulate']))
					$data[$name]=$field['repopulate'];
				else
					$data[$name]=$field['value'];
			}
		}
		return $data;
	}

/**
 * function render()
 *
 * Returns form output (a table) according to template
 *
 * @param string $type html or other format
 * @param string $class extra attributes such as class
 * @return string	grid output
 */

	public function render($class='flexyForm') {
		$this->CI->lang->load("form");
		// if (!empty($type)) $this->set_type($type);
		
		$data=$this->data;
		
		$out=form_open_multipart($this->action,array("class"=>$class));
		
		// fieldsets && fields
		$nr=1;
		foreach ($this->fieldsets as $fieldset) {
			$fieldset=trim($fieldset);
			$fieldSetClass='fieldset fieldSet_'.$fieldset.' fieldset_'.$nr;
			$fieldSetId='fieldset_'.$nr++;
			if (isset($this->fieldsetClasses[$fieldset])) $fieldSetClass.=' '.$this->fieldsetClasses[$fieldset];
			$caption=$fieldset;
			if ($caption=='fieldset') $caption=$this->caption;

			$out.=form_fieldset($caption,array("class"=>$fieldSetClass,'id'=>$fieldSetId));
			foreach($data as $name => $field) {
				$fieldFieldset=$field['fieldset'];
				if ($fieldset==$fieldFieldset)	$out.=$this->render_field($field['name'],$field,$class);
			}
			$out.=form_fieldset_close();
		}

		// Buttons
		$out.=form_fieldset("",array("class"=>$this->fieldsetClasses['buttons']));
		foreach ($this->buttons as $name => $button) {
			if (isset($button['submit']))
				$out.=form_submit($button);
			else
				$out.=form_reset($button);
		}
		$out.=form_fieldset_close();
		$out.=form_close();

		// prepare javascript for conditional field showing
		if (!empty($this->when)) {
			$json=array2json($this->when);
			$out.="\n<script language=\"javascript\" type=\"text/javascript\">\n<!--\nvar formFieldWhen=".$json.";\n-->\n</script>\n";
		}
		log_('info',"form: rendering");
		return $out;
	}

	private function render_field($name,$field,$class="") {
		$out="";
		$pre=get_prefix($name);
		if ($pre==$name) $pre="";
		$class="$pre $name ".$field['type']." ".$field['class'];
		if (isset($field['multiple'])) $class.=" ".$field['multiple'];
		// $class=" ".$class;
		
		if (!empty($field["repopulate"])) $field["value"]=$field["repopulate"];
		$attr=array("name"=>$name,"id"=>$name,"value"=>$field["value"], "class"=>$class);
		if (isset($field['attr'])) {$attr=array_merge($attr,$field['attr']);}
		if (isset($field['attributes'])) {$attr=array_merge($attr,$field['attributes']);}

		// Label or Captcha
		if ($field["type"]!="hidden") {
			$out.=$this->tmp($this->tmpFieldStart,$class);
			if ($field["type"]=='captcha') {
				$vals = array(
								'img_path'	 	=> assets('captcha/'),
								'img_url'	 		=> site_url(assets().'captcha/'),
								'img_width'	 	=> '125',
								'img_height' 	=> '25',
								'expiration' => '600',
							);
				if ($this->captchaWords!=NULL) $vals['word']=random_element($this->captchaWords);
				$cap = create_captcha($vals);
				$out.=div('captcha').$cap['image'].form_hidden($name.'__captcha',str_reverse($cap['word']))._div();
			}
			else {
				$out.=form_label($field["label"],$name);
			}
		}

		// When (javascript triggers)
		if (!empty($field['when'])) $this->when($field['when'],$name);

		switch($field["type"]):

			case "hidden":
				$out.='<input type="hidden" ';
				foreach ($attr as $name => $value) {$out.=$name.'="'.$value.'" ';}
				$out.='/>';
				break;

			case "html":
				$out.=$field['value'];
				if (isset($field['html'])) $out.=div('flexyFormHtml').$field['html']._div();
				break;

			case "checkbox":
				if ($attr["value"])
					$attr["checked"]="checked";
				else
					$attr["checked"]="";
				$attr["value"]="true";
				$out.=form_checkbox($attr);
				if (isset($field['html'])) $out.=div('flexyFormHtml').$field['html']._div();
				break;

			case 'radio':
				if (isset($field['html'])) $out.=div('flexyFormHtml').$field['html']._div();
				$options=$field['options'];
				$value=$field['value'];
				foreach ($options as $option => $optLabel) {
					$attr['value']=$option;
					if ($value==$option) $attr['checked']='checked'; else $attr['checked']='';
					$attr['id']=str_replace('.','_',$name.'__'.$option);
					// $out.=div('radioOption '.$option).span('optionLabel').$optLabel._span().form_radio($attr)._div();
					$out.=div('radioOption '.$option).form_radio($attr).span('optionLabel').$optLabel._span()._div();
				}
				break;

			case "htmleditor":
				$this->hasHtmlField=true;
			case "textarea":
				if ($field["type"]=="textarea") {
					$attr["rows"]=5;
					$attr["cols"]=60;
				}
				$out.=form_textarea($attr);
				break;

			case 'select':
			case 'dropdown':
			case 'ordered_list':
			case 'image_dropdown':
			case 'image_dragndrop':
				//
				// set classes etc
				//
				$extra="";
				$options=el("options",$field);
				$value=$attr["value"];
				$button=el("button",$field);
				if (isset($button))	$attr["class"].=" button";
				if (isset($field["path"])) 	$extra.=" path=\"".$field["path"]."\"";
				if (isset($field["multiple"]) or is_array($value)) {
					$extra.=" multiple=\"multipe\"";
					$name.="[]";
					if (is_array($value)) 
						$value=array_keys($value);
					else
						$value=explode("|",$value);
				}
				$extra.="class=\"".$attr["class"]."\" id=\"".$name."\"";
				
				
				
				//
				// Show images if it is an image dropdown
				//
				if ($field["type"]=="image_dropdown" or $field["type"]=="image_dragndrop") {
					// show values
					if (!is_array($value)) $medias=array($value); else $medias=$value;
					// if (empty($medias)) $medias=$this->CI->config->item('ADMINASSETS').'icons/empty.gif';
					$out.='<ul class="values '.$attr['class'].'">';
					$hiddenValue='';
					foreach($medias as $media) {
						if (!empty($media)) {
							$out.='<li>'.show_thumb(array("src"=>$field["path"]."/".$media,"class"=>"media")).'</li>';
							$hiddenValue=add_string($hiddenValue,$media,'|');
						}
					}
					$out.='</ul>';
					// hidden value, needed for jQuery dragndrop and sortable
					if ($field["type"]=="image_dragndrop")
						$hiddenName=$field['name'];
					else
						$hiddenName=$field['name'].'__hidden';
					$out.=form_hidden($hiddenName,$hiddenValue);
				}

				//
				// Show all possible images as images instead of a dropdown
				//
				if ($field["type"]=="image_dragndrop") {
					$preName=get_prefix($field['name']);
					$out.='<ul class="choices">';
					foreach($options as $img) {
						if (empty($img)) {
							$out.='<li><img src="'.$this->CI->config->item('ADMINASSETS').'icons/flexyadmin_empty_image.gif" class="media empty" /></li>';
						}
						else {
							$image=$img['name'];
							if ($preName=='media' or !in_array($image,$medias)) $out.='<li>'.show_thumb(array("src"=>$field["path"]."/".$image,"class"=>"media",'alt'=>$image,'title'=>$image)).'</li>';
						}
					}
					$out.='</ul>';					
				}

				//
				// Normal dropdown (also normal image dropdown)
				//
				if ($field['type']=='select' or $field['type']=='dropdown' or $field['type']=='image_dropdown') {
					$out.=form_dropdown($name,$options,$value,$extra);
				}

				//
				// Ordered lists
				//
				if ($field['type']=='ordered_list') {
					// show (ordered) choices	
					// trace_($field);
					// trace_($options);
					$out.='<ul class="list list_choices">';
					$value=$field['value'];
					// trace_($options);
					// trace_($value);
					foreach($options as $id=>$option) {
						// if (!in_array($id,$value)) $out.='<li id="'.$id.'">'.$option.'</li>';
						if (!array_key_exists($id,$value)) $out.='<li id="'.$id.'">'.$option.'</li>';
						
					}
					$out.='</ul>';
					// $out.=icon('right');
					// show values
					$hiddenValue='';
					if (!is_array($value)) $value=array($value);
					$out.='<ul class="list list_values '.$attr['class'].'">';
					// trace_($value);
					foreach($value as $valID => $val) {
						if (!empty($val)) {
							if (isset($options[$valID])) {
								$out.='<li id="'.$valID.'">'.$options[$valID].'</li>';
								$hiddenValue=add_string($hiddenValue,$valID,'|');
							}
						}
					}
					$out.='</ul>';
					$out.=form_hidden($field['name'].'__hidden',$hiddenValue);				
				}
				
				if (isset($button)) {
					$out.=div("add_button").anchor($button,icon("add"))._div();
				}
				break;
				
			// #BUSY Form->Subfields
			case "subfields":
				$out.=icon('new');
				$out.=div('sub');
				foreach ($field['value'] as $id => $subfields) {
					$first=true;
					foreach ($subfields as $subfieldName => $subfieldValue) {
						$preSub=get_prefix($subfieldName);
						$subAttr['name']=$name.'___'.$subfieldName.'[]';
						$subAttr['value']=$subfieldValue;
						switch ($preSub) {
							case 'id':
								if ($subfieldName=='id') {
									$out.=form_hidden($subAttr['name'],$subAttr['value']);
								}
								break;
							default:
								$labelClass=array();
								if ($first) {
									$labelClass=array('class'=>'first');
									$out.=icon('delete');
									$first=FALSE;
								}
								$out.=form_label($this->CI->ui->get($subfieldName),$subAttr['name'],$labelClass);
								if ($preSub=='txt') {
									$this->hasHtmlField=true;
									$subAttr["rows"]=5;
									$subAttr["cols"]=60;
									$subAttr['class']='htmleditor';
									$out.=form_textarea($subAttr);
								}
								else {
									$out.=form_input($subAttr);
								}
								break;
						}
					}
					$out.=br(2);
				}
				$out.=_div();
				break;

			case "file":
				$attr["class"].=" browse";
				$out.=form_upload($attr);
				break;

			// case "upload":
			// 	if (!empty($field["value"])) $out.=popup_img($field["upload_path"]."/".$field["value"],img($field["upload_path"]."/".$field["value"]));
			// 	$out.=form_input($attr);
			// 	$attr["class"].=" browse";
			// 	$out.=form_upload($attr);
			// 	break;

				
			case "image":
				$attr['src']=$attr['value'];
				$out.=img($attr);
				break;

			case "date":
				$date=trim(strval($field["value"]));
				if (($date=="0000-00-00") or ($date=="")) {
					$date=date("Y-m-d");
				}
				$attr["value"]=$date;
				$out.=form_input($attr);
				break;
			case 'datetime':
				$date=trim(strval($field["value"]));
				if (($date=="0000-00-00 00:00:00") or ($date=="")) {
					$date=date("Y-m-d H:i:s");
				}
				$attr["value"]=$date;
				$out.=form_input($attr);
				break;
			case "time":
				$time=trim(strval($field["value"]));
				if (($time=="00:00:00") or ($time=="")) {
					$time=date("H:i:s");
				}
				$attr["value"]=$time;
				$out.=form_input($attr);
				break;

			case "password":
				if (substr($this->action,0,12)=='/admin/show/') {
					$attr['value']='';
					$out.=form_input($attr);
				}
				else
					$out.=form_password($attr);
				break;
				
			case "input":
			case "default":
			default:
				$out.=form_input($attr);

		endswitch;

		if ($field["type"]!="hidden") {
			$out.=$this->tmp($this->tmpFieldEnd);
		}
		return $out;
	}


/**
 * function has_htmlfield()
 *
 * Checks if a field in the form needs a html editor.
 * @return	bool True if one ore more fields is a html editor
 */
 	public function has_htmlfield() {
 		return $this->hasHtmlField;
	}


}

?>
