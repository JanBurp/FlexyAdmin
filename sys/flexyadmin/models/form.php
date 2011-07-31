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

class Form Extends CI_Model {

	var $caption;
	var $action;
	var $data=array();
	// var $type;			// html
	var $hasHtmlField;
	var $isValidated;
	var $captchaWords;
	var $fieldsets;
	var $fieldsetClasses;
	var $when;  // javascript
	var $buttons;
	

	function __construct($action="") {
		parent::__construct();
		$this->init($action);
	}

	function init($action="") {
		$this->set_action($action);
		$this->set_caption();
		$this->set_labels();
		$this->data=array();
		// $this->set_type();
		$this->set_templates();
		$this->set_fieldset_classes();
		$this->set_fieldsets();
		$this->hasHtmlField=false;
		$this->show_buttons();
		$this->set_captcha_words();
		$this->when();
		// $this->show_submit();
	}

	function set_action($action="") {
		$this->action=$action;
	}

	function set_caption($caption="") {
		$this->caption=$caption;
	}

	function set_labels($labels=NULL) {
		if (isset($labels) and !empty($labels)) {
			foreach($labels as $name=>$label) {
				$this->set_label($name,$label);
			}
		}
	}

	function set_label($name,$label) {
		$this->data[$name]["label"]=$label;
	}

	function set_data($data=NULL,$caption="") {
		if (isset($data) and !empty($data)) {
			foreach ($data as $name => $field) {
				$this->data[$name]=$this->_check_default_field($name,$field);
			}
		}
		$this->set_caption($caption);
	}

	function set_captcha_words($words=NULL) {
		$this->captchaWords=$words;
	}

	function when($when='',$field='') {
		if (empty($when))
			$this->when=array();
		else {
			$this->when[$field]=$when;
		}
	}

	function _check_default_field($name, $field) {
		if (!isset($field['type']))				$field['type']="input";
		if (!isset($field['name']))				$field['name']=$name;
		if (!isset($field['fieldset']))		$field['fieldset']='fieldset';
		if (!isset($field['label']))			$field['label']=ucfirst(remove_prefix($name));
		if (!isset($field['class']))			$field['class']="";
		if (!isset($field['value']))			$field['value']="";
		if (!isset($field['validation']))	$field['validation']="";
		return $field;
	}

	function show_buttons($buttons=NULL) {
		$this->set_buttons($buttons);
	}
	function set_buttons($buttons=NULL) {
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

	function no_submit() {
		foreach ($this->buttons as $name => $button) {
			if (isset($button['submit'])) unset($this->buttons[$name]);
		}
	}

/**
 * Template functions
 */
	function set_templates() {
		$this->set_field_templates();
	}
	function set_old_templates() {
		$this->set_field_templates("<div class=\"form_field %s\">","</div>");
		$this->set_fieldset_classes(array('fieldset'=>'formfields','buttons'=>'formbuttons'));
	}

	function set_field_templates($start="<div class=\"flexyFormField %s\">",$end="</div>") {
		$this->tmpFieldStart=$start;
		$this->tmpFieldEnd=$end;
	}

	function set_fieldset_classes($fieldsetClasses=array('fieldset'=>'flexyFormFieldset','buttons'=>'flexyFormButtons')) {
		$this->fieldsetClasses=$fieldsetClasses;
	}
	
	function set_fieldsets($fieldsets=array('fieldset')) {
		$this->fieldsets=$fieldsets;
	}
	function add_fieldset($fieldset='',$class='') {
		$this->fieldsets[]=$fieldset;
		if (empty($class)) $class=$this->fieldsetClasses['fieldset'];
		$this->fieldsetClasses[$fieldset]=$class;
	}

	function tmp($tmp,$class="") {
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
	function validation() {
		$data=$this->data;
		$hasCaptcha=FALSE;

		foreach($data as $name=>$field) {
			// set validation rules
			if (!isset($field["multiple"])) {
				$this->form_validation->set_rules($field["name"], $field["label"], $field["validation"]);
			}
			if ($field['type']=='captcha') $hasCaptcha=$name;
			$this->data[$name]["repopulate"]=$this->input->post($name);
		}
		
		log_('info',"form: validation");
		$this->isValidated=$this->form_validation->run();
		// validate captcha
		if ($hasCaptcha!=FALSE) {
			$value=$this->input->post($hasCaptcha);
			$code=str_reverse($this->input->post($hasCaptcha.'__captcha'));
			$this->isValidated=($value==$code);
		}
		return $this->isValidated;
	}

	function reset_data() {
		foreach ($this->data as $key => $field) {
			$this->data[$key]["value"]="";
			$this->data[$key]["repopulate"]="";
		}
	}

/**
 * function prepare_data($name,$value)
 *
 * This functions prepares data coming from a form. Some fields needs to be adjusted, ie: checkboxes for example
 *
 * @param string $name Name of field
 * @param mixed	$value Data to prepare
 * @return mixed The prepped data
 *
 */
	function prepare_data($name,$value,$id) {
		$out=$value;
		$error="";
		if (is_array($value) or empty($value)) {
			// multi options (string)
			$hidden=$this->input->post($name.'__hidden');
			if ($hidden) {
				$out=$hidden;
			}
			else {
				if (is_array($value) and count($value)>0)	$out=implode("|",$out);
			}
		}
		$data=$this->data[$name];

		/**
		 * Special form fields
		 */
		$type=el("type",$data);
		switch ($type) {
			case "checkbox" :
				if ($value=="true")
					$out=1;
				else
					$out=0;
				break;
			case "upload" :
				if (!empty($_FILES[$name]['name'])) {
					$config['upload_path'] 		= $data['upload_path'];
					$config['allowed_types'] 	= str_replace(",","|",$data['allowed_types']);
					$this->upload->config($config);
					$ok=$this->upload->upload_file($name);
					if (!$ok) {
						$error=$this->upload->get_error();
					}
					else {
						$out=$this->upload->get_file();
						// reset lists
						$this->load->library("editor_lists");
						$this->editor_lists->create_list("img");
						$this->editor_lists->create_list("media");
					}
				}
				break;
			default:
				//$out=htmlentities($value);
				break;
		}
		$this->data[$name]['newvalue']=$out;
		$out=array("value"=>$out,"error"=>$error);
		return $out;
	}


/**
 * function update($table)
 *
 * Update the data in form
 * @param string $table Table to update
 * @return bool	Validation succes
 */
	function update($table,$user_id='') {
		$error="";
		$id=-1;
		/**
		 * Sets data to update/insert
		 */
		// trace_($_POST);
		$set=array();
		if ($this->isValidated and $this->db->table_exists($table)) {
			$joins=array();
			foreach($this->data as $name=>$field) {
				// set primary key (id)
				if ($name==PRIMARY_KEY) {
					$id=$this->input->post(PRIMARY_KEY);
				}
				// set user (id) if set
				elseif ($name=="user") {
					if ($user_id===FALSE)
						$set[$name]=$this->input->post($name);
					else
						$set[$name]=$user_id;
				}
				// set uri
				elseif ($name=="uri") {
					$uri=$this->input->post($name);
				}
				// set other fields
				else {
					$pre=get_prefix($name);
					$value=$this->input->post($name);
					/**
					 *  Is data from join?
					 */
					if ($pre==$this->config->item('REL_table_prefix')) {
						// strace_($name);
						if (empty($value)) $value=array();
						$joins[$name]=$value;
						$hidden=$this->input->post($name.'__hidden');
						if ($hidden) {
							$joins[$name]=explode('|',$hidden);
						}
						// trace_($joins);
					}
					/**
					* Password, hash it
					*/
					elseif (in_array($pre,array('gpw','pwd'))) {
						$set[$name]=$this->ion_auth_model->hash_password($value);
					}
					/**
					 * Normal data
					 */
					else {
						$prep=$this->prepare_data($name,$value,$id);
						$error=el("error",$prep);
						$value=el("value",$prep,"");
						$set[$name]=$value;
					}
				}
			}
			
			
			if (!empty($error)) {
			}
			else {
				/**
				 * If no error setting data, insert/update data (looping through the set)
				 */

				/**
				 * Set (new) order
				 */
				if (isset($set["order"])) {
					if ($id==-1) {
						if (isset($set["self_parent"])) 
							$set["order"]=$this->order->get_next_order($table,$set["self_parent"]);
						else
							$set["order"]=$this->order->get_next_order($table);
					}
					elseif (isset($set["self_parent"])) {
						$old_parent=$this->db->get_field($table,"self_parent",$id);
						if ($old_parent!=$set["self_parent"]) {
							$set["order"]=$this->order->get_next_order($table,$set["self_parent"]);
						}
					}
				}

				/**
				 * Make sure all not given fields stays the same
				 */
				$staticFields=$this->db->list_fields($table);
				$staticFields=array_combine($staticFields,$staticFields);
				unset($staticFields[PRIMARY_KEY]);
				foreach($set as $name=>$value) {
					unset($staticFields[$name]);
				}
				if (!empty($staticFields)) {
					$this->db->select($staticFields);
					$this->db->where(PRIMARY_KEY,$id);
					$query=$this->db->get($table);
					$staticData=$query->row_array();
					// trace_($staticData);
					foreach($staticData as $name=>$value) {
						if (!isset($value))
							$set[$name]='';
						else
							$set[$name]=$value;
					}
				}

				/**
				 * Update data
				 */
				foreach($set as $name=>$value) {
					$this->db->set($name,$value);
					$this->data[$name]['newvalue']=$value;
				}
				if ($id==-1) {
					$this->db->insert($table);
					$id=$this->db->insert_id();
					log_('info',"form: inserting data in '$table', id='$id'");
				}
				else {
					$this->db->where(PRIMARY_KEY,$id);
					$this->db->update($table);
					log_('info',"form: updating data from '$table', id='$id'");
				}
				/**
				 * If Joins, update them to
				 */
				if (!empty($joins)) {
					// strace_($joins);
					foreach($joins as $name=>$value) {
						// first delete current selection
						$relTable=$name;
						$thisKey=this_key_from_rel_table($relTable);
						$joinKey=join_key_from_rel_table($relTable);
						if ($thisKey==$joinKey) {
							// self relation
							$joinKey.="_";
						}
						// strace_(array('id'=>$id,'thisKey'=>$thisKey,'joinKey'=>$joinKey,'relTable'=>$relTable,'value'=>$value));
						$this->db->where($thisKey,$id);
						$this->db->delete($relTable);
						// insert new selection
						foreach ($value as $data) {
							$this->db->set($thisKey,$id);
							$this->db->set($joinKey,$data);
							$this->db->insert($relTable);
							$inId=$this->db->insert_id();
						}
						// strace_('Should be updated..... ok');
						log_('info',"form: updating join data from '$table', id='$id'");
					}
				}
				return intval($id);
			}
		}
		return strval($error);
	}

	function get_data() {
		$data=array();
		foreach($this->data as $name=>$field) {
			if (isset($field['newvalue']))
				$data[$name]=$field['newvalue'];
			elseif (isset($field['repopulate']))
				$data[$name]=$field['repopulate'];
			else
				$data[$name]=$field['value'];
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

	function render($class='flexyForm') {
		$this->lang->load("form");
		// if (!empty($type)) $this->set_type($type);
		
		$data=$this->data;
		$out=form_open_multipart($this->action,array("class"=>$class));
		
		// fieldsets
		foreach ($this->fieldsets as $fieldset) {
			$fieldSetClass='fieldSet_'.$fieldset;
			if (isset($this->fieldsetClasses[$fieldset])) $fieldSetClass.=' '.$this->fieldsetClasses[$fieldset];
			$caption=$fieldset;
			if ($caption=='fieldset') $caption=$this->caption;
			$out.=form_fieldset($caption,array("class"=>$fieldSetClass));
			foreach($data as $name => $field) {
				if ($field['fieldset']==$fieldset) $out.=$this->render_field($field['name'],$field,$class);
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
			// strace_($this->when);
			// strace_($json);
			$out.="\n<script language=\"javascript\" type=\"text/javascript\">\n<!--\nvar formFieldWhen=".$json.";\n-->\n</script>\n";
		}
		log_('info',"form: rendering");
		return $out;
	}

	function render_field($name,$field,$class="") {
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
								'img_path'	 	=> assets().'captcha/',
								'img_url'	 		=> site_url().assets().'captcha/',
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
					// if (empty($medias)) $medias=$this->config->item('ADMINASSETS').'icons/empty.gif';
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
							$out.='<li><img src="'.$this->config->item('ADMINASSETS').'icons/flexyadmin_empty_image.gif" class="media empty" /></li>';
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
					foreach($options as $id=>$option) {
						if (!in_array($id,$value)) $out.='<li id="'.$id.'">'.$option.'</li>';
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
								$out.=form_label($this->uiNames->get($subfieldName),$subAttr['name'],$labelClass);
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
 	function has_htmlfield() {
 		return $this->hasHtmlField;
	}


}

?>
