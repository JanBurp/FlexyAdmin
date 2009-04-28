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

	function set_data($data=NULL,$name="") {
		if (isset($data) and !empty($data)) {
			$this->data=$data;
		}
		$this->set_caption($name);
	}


/**
 * HTML template functions
 */
	function set_html_templates() {
		$this->set_html_field_templates();
	}

	function set_html_field_templates($start="<p class=\"form_field %s\">",$end="</p>") {
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
		foreach($data as $name=>$field) {
			if (!isset($field["multiple"])) {
				$this->form_validation->set_rules($field["name"], $field["label"], $field["validation"]);
			}
			$this->data[$name]["repopulate"]=$this->input->post($name);
		}
		log_('info',"form: validation");
		$this->isValidated=$this->form_validation->run();
		return $this->isValidated;
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
		if (is_array($value)) {
			// multi options (string)
			if (count($value)==0)
				$out="";
			else
				$out=implode("|",$out);
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

		/**
		 *  Is it a updated link from links table?
		 */

		if 	(isset($data["table"])
			and $data["table"]==$this->cfg->get('CFG_editor','table')
			and ($name=="url_url")
			and (!empty($value))
			and ($value!="http://")) {

			/**
			 * Update all links in txt fields...
			 */
			if ($id>=0) {
				// get old value
				$this->db->select("url_url");
				$this->db->where("id",$id);
				$query=$this->db->get($data["table"]);
				$result=$query->row_array();
				$oldUrl=$result["url_url"];
				// loop through all txt fields..
				$tables=$this->db->list_tables();
				foreach($tables as $table) {
					if (get_prefix($table)==$this->config->item('TABLE_prefix')) {
						$fields=$this->db->list_fields($table);
						foreach ($fields as $field) {
							if (get_prefix($field)=="txt") {
								$this->db->select("id,$field");
								$this->db->where("$field !=","");
								$query=$this->db->get($table);
								foreach($query->result_array() as $row) {
									$thisId=$row["id"];
									$txt=$row[$field];
									$txt=str_replace("href=\"$oldUrl","href=\"$value",$txt);
									$res=$this->db->update($table,array($field=>$txt),"id = $thisId");
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Ready
		 */

		$out=array("value"=>$out,"error"=>$error);
		return $out;
	}

/**
 * function _after_update($id)
 *
 * Actions to perform when update is done
 *
 *	@param int $id	id of updated record (-1 is inserted)
 */
	function _after_update($id) {
		/**
		* TODO: hook this
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

	function _create_uri($original_uri,$table,$id) {
		static $counter=1;
		// lowercase
		$uri=strtolower($original_uri);
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
		return $uri;
	}


/**
 * function update($table)
 *
 * Update the data in form
 * @param string $table Table to update
 * @return bool	Validation succes
 */
	function update($table) {
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
				if ($name==$pk) {
					$id=$this->input->post($pk);
				}
				elseif ($name=="uri") {
					$uri=$this->input->post($name);
				}
				else {
					$pre=get_prefix($name);
					$value=$this->input->post($name);
					/**
					 *  Is data from join?
					 */
					if ($pre==$this->config->item('REL_table_prefix')) {
						if (empty($value)) $value=array();
						$joins[$name]=$value;
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
				 * Set order
				 */
				if (isset($set["order"])) {
					if ($id==-1) {
						$set["order"]=1;
						if (isset($set["self_parent"]))
							$this->order->shift_up($table,$set["self_parent"]);
						else
							$this->order->shift_up($table);
					}
					elseif (isset($set["self_parent"])) {
						$old_parent=$this->db->get_field($table,"self_parent",$id);
						if ($old_parent!=$set["self_parent"]) {
							$set["order"]=1;
							$this->order->shift_up($table,$set["self_parent"]);
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
					foreach($joins as $name=>$value) {
						// first delete current selection
						$relTable=$name;
						$thisKey=this_key_from_rel_table($relTable);
						$joinKey=join_key_from_rel_table($relTable);
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
		//trace_($data);
		foreach($data as $name => $field) {
			$out.=$this->render_field($name,$field,$class);
		}
		$out.=form_fieldset_close();
		$out.=form_fieldset("",array("class"=>"formbuttons"));
		$out.=form_reset(array("name"=>"cancel", "value" => lang("form_cancel"), "class"=>"button cancel", "onClick" => "window.history.back()"));
		$out.=form_reset(array("name"=>"reset", "value" => lang("form_reset"), "class"=>"button reset"));
		$out.=form_submit(array("submit"=>"submit", "value"=>lang("form_submit"),"class"=>"button submit"));
		$out.=form_fieldset_close();
		$out.=form_close();
		log_('info',"form: rendering");
		return $out;
	}

	function render_field($name,$field,$class="") {
		$out="";
		$pre=get_prefix($name);
		if ($pre==$name) $pre="";
		$class="$pre $name ".$field['type'];
		if (isset($field['multiple'])) $class.=" ".$field['multiple'];
		$class.=" ".$class;
		if (!empty($field["repopulate"])) $field["value"]=$field["repopulate"];
		$attr=array("name"=>$name,"id"=>$name,"value"=>$field["value"], "class"=>$class);
		if ($field["type"]!="hidden") {
			$out.=$this->tmp($this->tmpFieldStart,$class);
			$out.=form_label($field["label"],$name);
		}

//		if ($field["type"]!="hidden") {
//			$out.=div("field");
//		}

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


			case "image_dropdown":
			case "dropdown":
				$extra="";
				$options=el("options",$field);
				$value=$attr["value"];
				$button=el("button",$field);
				if (isset($button))	$attr["class"].=" button";
				if (isset($field["path"])) {
					$extra.=" path=\"".$field["path"]."\"";
				}
				if (isset($field["multiple"]) or is_array($value)) {
					$extra.=" multiple=\"multipe\"";
					$name.="[]";
					if (is_array($value)) {
						$value=array_keys($value);
					}
					else {
						$value=explode("|",$value);
					}
				}
				$extra.="class=\"".$attr["class"]."\" id=\"".$name."\"";
				if ($field["type"]=="image_dropdown") {
					if (!is_array($value)) $medias=array($value); else $medias=$value;
					foreach($medias as $media) {
						if (!empty($media))	$out.=show_thumb(array("src"=>$field["path"]."/".$media,"class"=>"media"));
					}
				}
				$out.=form_dropdown($name,$options,$value,$extra);
				if (isset($button)) {
					$out.=div("add_button").anchor($button,icon("add"))._div();
				}
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
