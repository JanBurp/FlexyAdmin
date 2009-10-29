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

class Form Extends Model {

	var $caption;
	var $action;
	var $data=array();
	var $type;			// html
	var $hasHtmlField;
	var $isValidated;
	var $captchaWords;
	
	var $buttons;
	// var $showSubmit;
	

	function Form($action="") {
		parent::Model();
		$this->init($action);
	}

	function init($action="") {
		$this->set_action($action);
		$this->set_caption();
		$this->set_labels();
		$this->data=array();
		$this->set_type();
		$this->hasHtmlField=false;
		$this->show_buttons();
		$this->set_captcha_words();
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

	function set_type($type="html") {
		$this->type=$type;
		$func="set_".$type."_templates";
		$this->$func();
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

	function _check_default_field($name, $field) {
		if (!isset($field['type']))				$field['type']="input";
		if (!isset($field['name']))				$field['name']=$name;
		if (!isset($field['label']))			$field['label']=ucfirst(remove_prefix($name));
		if (!isset($field['class']))			$field['class']="";
		if (!isset($field['value']))			$field['value']="";
		if (!isset($field['validation']))	$field['validation']="";
		return $field;
	}

	function show_buttons($buttons=NULL) {
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
 * HTML template functions
 */
	function set_html_templates() {
		$this->set_html_field_templates();
	}

	function set_html_field_templates($start="<div class=\"form_field %s\">",$end="</div>") {
		$this->tmpFieldStart=$start;
		$this->tmpFieldEnd=$end;
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
		// trace_($value);
		if (is_array($value)) {
			// multi options (string)
			$hidden=$this->input->post($name.'__hidden');
			if ($hidden) {
				$out=$hidden;
			}
			else {
				if (count($value)==0)
					$out="";
				else
					$out=implode("|",$out);
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
					$out=true;
				else
					$out=false;
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

		$out=array("value"=>$out,"error"=>$error);
		return $out;
	}

	function _after_update($id) {
		/**
		* Moved to controllers (show and edit)
		*/
	}

	function _get_uri_field($fields) {
		$uriField="";
		/**
		 * Auto uri field according to prefixes
		 */
		if (empty($uriField)) {
			$preTypes=$this->config->item('URI_field_pre_types');
			$loop=true;
			while ($loop) {
				$field=current($fields);
				$pre=get_prefix($field);
				if (in_array($pre,$preTypes)) {
					$uriField=$field;
				}
				$field=next($fields);
				$loop=(empty($uriField) and $field!==FALSE);
			}
		}
		/**
		 * If still nothing set... just get the first field (after id,order and uri)
		 */
		if (empty($uriField)) {
			unset($fields["order"]);
			reset($fields);
			$uriField=current($fields);
		}
		return $uriField;
	}

	function _existing_uri($uri,$table,$id) {
		$this->db->select("uri");
		$this->db->where("uri",$uri);
		$this->db->where("id !=",$id);
		$uris=$this->db->get_result($table);
		if (empty($uris))
			return FALSE;
		return current($uris);
	}

	function _create_uri($original_uri_field,$table,$id) {
		$current_uri=$this->db->get_field($table,"uri",$id);
		if (empty($current_uri) or !($this->cfg->get('CFG_table',$table,'b_freeze_uris')) ) {
			static $counter=1;
			// lowercase
			$uri=strtolower($original_uri_field);
			// replace spaces
			$uri=str_replace(" ","_",trim($uri));
			// replace specialchars
			$uri=clean_string($uri);
			// forbidden uri's
			$forbidden=array("site","sys","admin");
			if (in_array($uri,$forbidden)) $uri="_".$uri;
			// check if uri exists allready
			while ($this->_existing_uri($uri,$table,$id)) {
				$uri=$uri."_".$counter++;
			}
		}
		else
			$uri=$current_uri;
		return $uri;
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
		$set=array();
		if ($this->isValidated and $this->db->table_exists($table)) {
			$pk=pk();
			$joins=array();
			foreach($this->data as $name=>$field) {
				// set primary key (id)
				if ($name==$pk) {
					$id=$this->input->post($pk);
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
						if (empty($value)) $value=array();
						$joins[$name]=$value;
						$hidden=$this->input->post($name.'__hidden');
						if ($hidden) {
							$joins[$name]=explode('|',$hidden);
						}
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
				 * First create an uri if necessary
				 */
				if (isset($uri)) {
					$original_uri_field=$this->_get_uri_field(array_keys($set));
					$uri=$this->_create_uri($set[$original_uri_field],$table,$id);
					$set["uri"]=$uri;
				}

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
				$staticFields=combine($staticFields,$staticFields);
				unset($staticFields[$pk]);
				foreach($set as $name=>$value) {
					unset($staticFields[$name]);
				}
				if (!empty($staticFields)) {
					$this->db->select($staticFields);
					$this->db->where($pk,$id);
					$query=$this->db->get($table);
					$staticData=$query->row_array();
					foreach($staticData as $name=>$value) {
						$set[$name]=$value;
					}
				}
				/**
				 * Update data
				 */
				foreach($set as $name=>$value) {
					$this->db->set($name,$value);
				}
				if ($id==-1) {
					$this->db->insert($table);
					$id=$this->db->insert_id();
					log_('info',"form: inserting data in '$table', id='$id'");
				}
				else {
					$this->db->where($pk,$id);
					$this->db->update($table);
					log_('info',"form: updating data from '$table', id='$id'");
				}
				/**
				 * If Joins, update them to
				 */
				if (!empty($joins)) {
					// trace_($joins);
					foreach($joins as $name=>$value) {
						// first delete current selection
						$relTable=$name;
						$thisKey=this_key_from_rel_table($relTable);
						$joinKey=join_key_from_rel_table($relTable);
						if ($thisKey==$joinKey) {
							// self relation
							$joinKey.="_";
						}
						$this->db->where($thisKey,$id);
						$this->db->delete($relTable);
						// insert new selection
						foreach ($value as $data) {
							$this->db->set($thisKey,$id);
							$this->db->set($joinKey,$data);
							$this->db->insert($relTable);
							$inId=$this->db->insert_id();
						}
						log_('info',"form: updating join data from '$table', id='$id'");
					}
				}
				/**
				 * Actions after update
				 */
				$this->_after_update($id);

				return intval($id);
			}
		}
		return strval($error);
	}

	function get_data() {
		$data=array();
		foreach($this->data as $name=>$field) {
			$data[$name]=$field;
			$data[$name]["value"]=$field["repopulate"];
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

	function render($type="", $class="") {
		$this->lang->load("form");
		if (!empty($type)) $this->set_type($type);
		
		$out=form_open_multipart($this->action,array("class"=>$class));
		$out.=form_fieldset($this->caption,array("class"=>"formfields"));
		$data=$this->data;
		foreach($data as $name => $field) {
			$out.=$this->render_field($name,$field,$class);
		}
		$out.=form_fieldset_close();
		$out.=form_fieldset("",array("class"=>"formbuttons"));
		foreach ($this->buttons as $name => $button) {
			if (isset($button['submit']))
				$out.=form_submit($button);
			else
				$out.=form_reset($button);
		}
		// $out.=form_reset(array("name"=>"cancel", "value" => lang("form_cancel"), "class"=>"button cancel", "onClick" => "window.history.back()"));
		// if ($this->showSubmit) {
		// 	$out.=form_reset(array("name"=>"reset", "value" => lang("form_reset"), "class"=>"button reset"));
		// 	$out.=form_submit(array("submit"=>"submit", "value"=>lang("form_submit"),"class"=>"button submit"));
		// }
		$out.=form_fieldset_close();
		$out.=form_close();
		log_('info',"form: rendering");
		return $out;
	}

	function render_field($name,$field,$class="") {
		$out="";
		$pre=get_prefix($name);
		if ($pre==$name) $pre="";
		$class="$pre $name ".$field['type']." ".$field['class'];
		if (isset($field['multiple'])) $class.=" ".$field['multiple'];
		$class=" ".$class;
		if (!empty($field["repopulate"])) $field["value"]=$field["repopulate"];
		$attr=array("name"=>$name,"id"=>$name,"value"=>$field["value"], "class"=>$class);

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
			else
				$out.=form_label($field["label"],$name);
		}



		switch($field["type"]):

			case "hidden":
				$out.=form_hidden($name,$field["value"]);
				break;


		case "checkbox":
				if ($attr["value"])
					$attr["checked"]="checked";
				else
					$attr["checked"]="";
				$attr["value"]="true";
				$out.=form_checkbox($attr);
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


			case "dropdown":
			case 'ordered_list':
			case "image_dropdown":
			case "image_dragndrop":
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
					// $out.=div('buttons').icon('up').icon('delete')._div();
					$out.='<ul class="choices">';
					foreach($options as $img) {
						$image=$img['name'];
						if (!in_array($image,$medias))	$out.='<li>'.show_thumb(array("src"=>$field["path"]."/".$image,"class"=>"media",'title'=>$image)).'</li>';
					}
					$out.='</ul>';					
				}

				//
				// Normal dropdown (also normal image dropdown)
				//
				if ($field['type']=='dropdown' or $field['type']=='image_dropdown') {
					$out.=form_dropdown($name,$options,$value,$extra);
				}

				//
				// Ordered lists
				//
				if ($field['type']=='ordered_list') {
					// show (ordered) choices	
					$out.='<ul class="list list_choices">';
					foreach($options as $id=>$option) {
						if (!in_array($id,$value)) $out.='<li id="'.$id.'">'.$option.'</li>';
					}
					$out.='</ul>';
					// $out.=icon('right');
					// show values
					$hiddenValue='';
					if (!is_array($value)) $value=array($value);
					$out.='<ul class="list list_values '.$attr['class'].'">';
					foreach($value as $val) {
						if (!empty($val)) {
							$out.='<li id="'.$val.'">'.$options[$val].'</li>';
							$hiddenValue=add_string($hiddenValue,$val,'|');
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

			case "upload":
				if (!empty($field["value"])) $out.=popup_img($field["upload_path"]."/".$field["value"],img($field["upload_path"]."/".$field["value"]));
				$out.=form_input($attr);
				$attr["class"].=" browse";
				$out.=form_upload($attr);
				break;

			case "date":
				$date=trim(strval($field["value"]));
				if (($date=="0000-00-00") or ($date=="")) {
					$date=date("Y-m-d");
				}
				$attr["value"]=$date;
			case "time":
			case "input":
			case "default":
			default:
				$out.=form_input($attr);

		endswitch;

		if ($field["type"]!="hidden") {
//			$out.=_div();
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
