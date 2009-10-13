<?
/**
 * FlexyAdmin V1
 *
 * flexy_field.php Created on 21-okt-2008
 *
 * @author Jan den Besten
 */

/**
 * Class Flexy_field  Model
 *
 * This class renders and validates fields
 *
 */

class Flexy_field extends Model {

	var $table;
	var $field;
	var $data;
	var $id;
	var $action;
	var $pre;
	var $type;
	var $validation;
	var $vars=array();
	var $fieldCfg=array();
	var $fieldInfo=array();
	var $fieldRight;
	var $restrictedToUser;
	var $user_id;
	var $extraInfoId;

	function Flexy_field() {
		parent::Model();
	}

	function init($table,$field,$data,$action) {
		$this->init_table($table,$action);
		$this->init_field($field,$data);
	}

	function init_table($table,$action) {
		$this->table=$table;
		$this->action=$action;
		if (!isset($vars)) $this->set_variables();
	}

	function init_field($field,$data,$right=RIGHTS_ALL) {
		$this->data=$data;
		$this->field=$field;
		$this->fieldRight=$right;
		$cfg=$this->cfg->get('CFG_field',$this->table.".".$field);
		if (!empty($cfg)) $this->fieldCfg[$field]=$cfg;
		$replacePre=el("str_overrule_prefix",$cfg);
		if (empty($replacePre))
			$this->pre=get_prefix($field);
		else
			$this->pre=$replacePre;
		$this->type();
	}
	function set_restricted_to_user($restrictedToUser=TRUE,$user_id='') {
		$this->restrictedToUser=$restrictedToUser;
		$this->user_id=$user_id;
	}

	function set_variables() {
		$this->vars=array(
				"IMG_MAP"=> $this->cfg->get('CFG_media_info',$this->table,"path")
				);
	}

	function set_info_id($id='') {
		$this->extraInfoId=$id;
	}

	/**
	 * function type()
	 *
	 * Determines the type of rendering/validation that should be done.
	 * Look in flexyadmin_config.php for what it can be.
	 */
	function type() {
		/**
		 * is it a special fieldname?
		 */
		$specials=$this->config->item('FIELDS_special');
		if (isset($specials[$this->field][$this->action])) {
			//trace_($this->field." - special");
			$type=$specials[$this->field][$this->action];
			$validation=el("validation",$specials[$this->field]);
			$this->_set_type($type);
			$this->_set_validation($validation);
			return $type;
		}

		/**
		 * or is it a known prefix?
		 */
		$prefixes=$this->config->item('FIELDS_prefix');
		if (isset($prefixes[$this->pre][$this->action])) {
			//trace_($this->field." - known prefix:".$this->pre);
			$type=$prefixes[$this->pre][$this->action];
			$validation=el("validation",$prefixes[$this->pre]);
			$this->_set_type($type);
			$this->_set_validation($validation);
			return $type;
		}

		/**
		 * or is it a known field information from database
		 */
		$platform=$this->db->platform();
		$info=$this->cfg->field_data($this->table);
		$database=$this->config->item('FIELDS_'.$platform);
		if (isset($database[$this->field][$this->action])) {
			// trace_($this->field." - from database field info");
			$type=$database[$this->field][$this->action];
			$validation=el("validation",$database[$this->field]);
			$this->_set_type($type);
			$this->_set_validation($validation);
			return $type;
		}

		/**
		 * if nothing else, just the default
		 */
		$default=$this->config->item('FIELDS_default');
		$type=el($this->action,$default);
		$validation=el("validation",$default);
		$this->_set_type($type);
		$this->_set_validation($validation);
		//trace_($this->field." - default");
		return $type;
	}

	function _set_type($type) {
		$this->type=$type;
	}

	function _set_validation($validation) {
		$this->validation=$validation;
	}


	function _is_function() {
		$pre=get_prefix($this->type);
		if ($pre=="function") {
			return "_".remove_prefix($this->type)."_".$this->action;
		}
		return false;
	}

	/**
	 * function concat_foreign_fields($row)
	 *
	 * Creates one field from all foreign fields and foreign key
	 * @param array $row Row of all data
	 * @return array	Returns a new row
	 */
	function concat_foreign_fields($row) {
		$newRow=array();
		$fkey="";
		foreach ($row as $field=>$data) {
			if (is_foreign_field($field) and !empty($fkey)) {
				$newRow[$fkey]=add_string($newRow[$fkey],$data);
			} elseif (is_foreign_key($field)) {
				$fkey=$field;
				$newRow[$field]="";
			}
			else
			 $newRow[$field]=$data;
		}
		return $newRow;
	}


	/**
	 * function render_grid($table,$data)
	 *
	 * Renders full data set according to type and action (grid|form)
	 *
	 */
	function render_grid($table,$data,$right=RIGHTS_ALL,$extraInfoId=NULL) {
		$this->init_table($table,"grid",$right);
		$out=array();
		foreach ($data as $idRow=>$row) {
			$out[$idRow]=$this->render_grid_row($table,$row,$right,$extraInfoId);
		}
		return $out;
	}

	/**
	 * function render_grid_row($table,$row)
	 *
	 * Renders row according to type and action (grid|form)
	 *
	 */
	function render_grid_row($table,$row,$right=RIGHTS_ALL,$extraInfoId=NULL) {
		$out=array();
		// first create one field from foreign data
		$row=$this->concat_foreign_fields($row);
		foreach ($row as $field=>$data) {
			$renderedField=$this->render_grid_field($table,$field,$data,$right,$extraInfoId);
			if ($renderedField!==FALSE)	$out[$field]=$renderedField;
		}
		return $out;
	}

	/**
	 * function render_grid_field($table,$field,$data)
	 *
	 * Renders field according to type and action (grid|form)
	 *
	 */
	function render_grid_field($table,$field,$data,$right=RIGHTS_ALL,$extraInfoId=NULL) {
		$this->extraInfoId=$extraInfoId;
		$this->init_field($field,$data,$right);
		// Must field be shown?
		if (isset($this->fieldCfg[$field]) and (!$this->fieldCfg[$field]["b_show_in_grid"])) {
			return FALSE;
		}
		// How to show? Function or replace?
		$func=$this->_is_function();
		if ($func!==false) {
			$out=$this->$func();
		}
		else {
			$out=$this->_replace();
		}
		return $out;
	}

	/**
	 * function _replace()
	 *
	 * Standard rendering function. Replaces %s with the data, and known variables with their content.
	 */

	function _replace() {
		$out=$this->type;
		$out=str_replace("%s",$this->data,$out);
		//$this->set_variables();
		foreach ($this->vars as $search=>$replace) {
			$out=str_replace("#".$search."#",$replace,$out);
		}
		return $out;
	}

	/**
	 * function render_form($table,$data)
	 *
	 * Renders full data set according to type and action (grid|form)
	 *
	 */
	function render_form($table,$data,$options=NULL,$multiOptions=NULL,$extraInfoId=NULL) {
		$this->init_table($table,"form");
		$out=array();
		foreach ($data as $name => $value) {
			$opt=el($name,$options);
			$mOpt=el($name,$multiOptions);
			$renderedField=$this->render_form_field($table,$name,$value,$opt,$mOpt,$extraInfoId);
			if ($renderedField!==FALSE)	$out[$name]=$renderedField;
		}
		return $out;
	}

	/**
	 * function render_form_field($table,$name,$value)
	 *
	 * Renders full data set according to type and action (grid|form)
	 *
	 */
	function render_form_field($table,$field,$value,$options=NULL,$multiOptions=NULL,$extraInfoId=NULL) {
		$this->extraInfoId=$extraInfoId;
		$out=array();
		$this->init_field($field,$value);
		// Must field be shown?
		if (isset($this->fieldCfg[$field]) and (!$this->fieldCfg[$field]["b_show_in_form"])) {
			return FALSE;
		}
		// Show
		$func=$this->_is_function();
		if ($func!==false) {
			if (method_exists($this,$func)) {
				if (isset($options))
					$out=$this->$func($options);
				else
					$out=$this->$func();
			}
			else
				$out=$this->_standard_form_field($options,$multiOptions);
		}
		else {
			$out=$this->_standard_form_field($options,$multiOptions);
		}
		return $out;
	}

	function _standard_form_field($options=NULL,$multiOptions=NULL) {
		$out=array();
		/**
		 * Standard form field information
		 */
		$out["table"]			= $this->table;
		$out["name"]			= $this->field;
		$out["value"]			= $this->data;
		$out["label"]			= $this->uiNames->get($this->field);
		$out["type"]			= $this->type;
		$out["class"]			= "";
		/**
		 * Dropdown fields, if there are options.
		 */
		if (isset($options) or isset($multiOptions)) {
			// $name=$out['name'];
			// trace_(array('out'=>$out,'options'=>$options,'multi opt'=>$multiOptions));
			// if (get_prefix($name)==$this->config->item('REL_table_prefix')) {
			// }
			if (isset($multiOptions)) $options=$multiOptions;
			$out["options"] = $options;
			if ($this->type!="dropdown") {
				$out["type"]="dropdown";
			}
			else {
				// add a 'new' item button
				$out["button"]=api_uri('API_view_form',foreign_table_from_key($this->field),-1);
			}
			if (!empty($multiOptions)) $out["multiple"]="multiple";
		}
		/**
		 * Upload field
		 */
		if ($this->type=="upload") {
			$uploadCfg=$this->cfg->get('CFG_media_info',$this->table);
			$out['upload_path'] 	= el("path",$uploadCfg);
			$out['allowed_types'] = el("str_types",$uploadCfg);
			//$out['max_size'] = '100';
			//$out['max_width'] = '1024';
			//$out['max_height'] = '768';
		}
		/**
		 * HTML editor field
		 */
		if ($this->type=="htmleditor") {
			$out['class'] = $this->cfg->get('CFG_editor','str_class');
		}		/**
		 * Add validation rules:
		 * -first the standard validation rules set in flexyadmin_config.php.
		 * -then add validation rules set in cfg_field_info
		 */
		$out["validation"]= $this->validation;
		$extraValidation=$this->cfg->get('CFG_field',$this->table.".".$this->field,'str_validation_rules');
		if (!empty($extraValidation)) {
			$extraValidation=explode("|",$extraValidation);
			$validationParams=$this->cfg->get('CFG_field',$this->table.".".$this->field,'str_validation_parameters');
			$validationParams=explode(",",$validationParams);
			$addValidation='';
			foreach ($extraValidation as $thisValidation) {
				if (strpos($thisValidation,'[]')>0) {
					$thisParam=current($validationParams);
					$thisValidation=str_replace('[]','['.$thisParam.']',$thisValidation);
				}
				$addValidation=add_string($thisValidation,$addValidation,'|');
			}
			$out["validation"]=add_string($out["validation"],$addValidation,"|");
		}
		return $out;
	}

	/**
	 * field functions
	 */

	function _primary_key_grid() {
		$this->id=$this->data;
		$class=$this->table." id".$this->id;
		$out="";
		if ($this->fieldRight>=RIGHTS_EDIT) 	{
			if (isset($this->extraInfoId))
				$uri=api_uri('API_view_form',$this->table.':'.$this->data,'info',$this->extraInfoId);
			else
				$uri=api_uri('API_view_form',$this->table.':'.$this->data);
			$out.=anchor( $uri, help(icon("edit"),lang('grid_edit')), array("class"=>"edit $class"));
		}
		// if ($this->fieldRight>=RIGHTS_DELETE)	{
		// 	if (isset($this->extraInfoId))
		// 		$uri=api_uri('API_confirm',$this->table.':'.$this->data,'info',$this->extraInfoId);
		// 	else
		// 		$uri=api_uri('API_confirm',$this->table.':'.$this->data);
		// 	$out.=anchor($uri, help(icon("delete"),lang('grid_delete')), array("class"=>"delete $class"));
		// }
		return $out;
	}

	function _primary_key_form() {
		$this->id=$this->data;
		$out=$this->_standard_form_field();
		$out["type"]="hidden";
		return $out;
	}

	function _user_grid() {
		$this->db->as_abstracts();
		$this->db->where("id",$this->data);
		$user=$this->db->get_row("cfg_users");
		$out=$user['abstract'];
		return $out;		
	}

	function _user_form() {
		if ($this->restrictedToUser===FALSE) {
			$this->db->as_abstracts();
			$users=$this->db->get_result("cfg_users");
			$options=array();
			$options[""]="";
			foreach ($users as $key => $value) {
				$options[$key]=$value['abstract'];
			}
			$out=$this->_standard_form_field($options);
			$out["type"]="dropdown";
			unset($out["button"]);
			if (empty($out['value'])) $out['value']=$this->user_id;
		}
		else {
			$out=$this->_standard_form_field();
			$out["type"]="hidden";
		}
		return $out;
	}


	function _get_tree($id,$branch="",$tree="") {
		if (!empty($tree)) $tree="/$tree";
		$this->db->select(array(pk(),"uri","self_parent"));
		$this->db->where(pk(),$id);
		$res=$this->db->get_row($this->table);
		if (empty($branch))
			$tree=$res["uri"].$tree;
		if ($res["self_parent"]>0) {
			$tree=$branch.$tree;
			$tree=$this->_get_tree($res["self_parent"],$branch,$tree);
		}
		return $tree;
	}

	function _self_grid() {
		$out=$this->_get_tree($this->data);
		if (!empty($out)) $out="[$this->data] ".$out;
		return $out;		
	}

	// TODO: Meer self_ velden mogelijk (nu alleen nog self_parent)
	function _self_form() {
		$this->db->select(array(pk(),"uri","self_parent"));
		$this->db->where(pk()." !=", $this->id);
		$this->db->order_as_tree();
		$res=$this->db->get_result($this->table);
		$options=array();
		$options[]="";
		foreach($res as $id=>$value) {
			$options[$id]=$this->_get_tree($value["self_parent"],"",$value["uri"]);
			if (substr($options[$id],0,1)=="/") $options[$id]=substr($options[$id],1);
		}
		$out=$this->_standard_form_field($options);
		$out["type"]="dropdown";
		unset($out["button"]);
		return $out;		
	}

	function _foreign_key_grid() {
		$out="";
		$data=$this->data;
		if (is_array($data)) {
			if (isset($data[$this->config->item('ABSTRACT_field_name')]))
				$out=$data[$this->config->item('ABSTRACT_field_name')];
			else
				$out=implode("|",$data);
		}
		else
			$out=$data;
		return $out;
	}

	function _order_grid() {
		$out="";
		$data=$this->data;
		if ($this->fieldRight>=RIGHTS_EDIT) {
			$out=	anchor(api_uri('API_view_order',$this->table,$this->id,"up"),help(icon("up"),lang('grid_order'))).
						anchor(api_uri('API_view_order',$this->table,$this->id,"down"),help(icon("down"),lang('grid_order')));
		}
		return $out;
	}


	function _join_grid() {
		$out="";
		foreach($this->data as $data) {
			if (isset($data[$this->config->item('ABSTRACT_field_name')])) {
				$out=add_string($out,$data[$this->config->item('ABSTRACT_field_name')],"|");
			}
			else {
				foreach($data as $field=>$value) {
					$pre=get_prefix($field);
					if (in_array($pre,$this->config->item('ABSTRACT_field_pre_types'))) {
						$out=add_string($out,$value,"|");
					}
				}
			}
		}
		return $out;
	}

	function _join_form($options) {
		$out=$this->_standard_form_field($options);
		$out["multiple"]="multiple";
		$out["button"]=api_uri('API_view_form',join_table_from_rel_table($out["name"]).':-1');
		if (get_prefix($out['name'])==$this->config->item('REL_table_prefix')) {
			$table=join_table_from_rel_table($out['name']);
			$tableInfo=$this->cfg->get('CFG_table',$table);
			$formManyType=$tableInfo['str_form_many_type'];
			if (!empty($formManyType) and $formManyType!='dropdown') {
				$out['type']=$formManyType;
				unset($out['button']);
			}
		}
		return $out;
	}

	function _boolean_grid() {
		if ($this->data)
			$out=icon("yes");
		else
			$out=icon("no");
		return $out;
	}

	function _text_grid() {
		return strip_string($this->data,30);
	}

	function _dropdown_rights_form() {
		$tables=$this->db->list_tables();
		$folders=$this->cfg->get('CFG_media_info');
		$folders=array_keys($folders);
		$not=filter_by($folders,"tbl_");
		$folders=array_diff($folders,$not);
		$media=array();
		foreach($folders as $folder) { $media[]="media_".$folder; }
		$options=array("","*","cfg_*","tbl_*","media_*");
		$options=array_merge($options,$tables);
		$options=array_merge($options,$media);
		$options=combine($options,$options);
		$out=$this->_standard_form_field($options);
		$out["type"]="dropdown";
		$out["multiple"]="multiple";
		unset($out["button"]);
		return $out;
	}

	function _dropdown_tables_form() {
		$tables=$this->db->list_tables();
		$tables=not_filter_by($tables,"cfg_");
		$tables=not_filter_by($tables,"log_");
		$tables=not_filter_by($tables,"rel_users");
		$tables=array_merge(array(""),$tables);
		$options=combine($tables,$tables);
		$out=$this->_standard_form_field($options);
		$out["type"]="dropdown";
		unset($out["button"]);
		return $out;
	}

	function _dropdown_field_form() {
		$tables=$this->db->list_tables();
		$tables=filter_by($tables,"tbl_");
		$specialFields=array_keys($this->config->item('FIELDS_special'));
		$options=array();
		$options[""]="";
		foreach ($tables as $table) {
			$fields=$this->db->list_fields($table);
			foreach ($fields as $field) {
				//if (!in_array($field,$specialFields))
				$options["$table.$field"]="$table . $field";
			}
			// join fields?
			$jt=$this->config->item('REL_table_prefix')."_".remove_prefix($table).$this->config->item('REL_table_split');
			foreach($tables as $key=>$jtable) {
				if (strncmp($jt,$jtable,strlen($jt))==0) {
					$field=$jtable;
					$options["$table.$field"]="$table . $field";
				}
			}
		}
		$out=$this->_standard_form_field($options);
		$out["type"]="dropdown";
		unset($out["button"]);
		return $out;
	}

	function _dropdown_fields_form() {
		$out=$this->_dropdown_field_form();
		$out["multiple"]="multiple";
		return $out;
	}

	function _dropdown_media_grid() {
		$out="";
		if (!empty($this->data)) {
			$info=$this->cfg->get('CFG_media_info',$this->table.".".$this->field);
			$path=el("path",$info);
			$media=$this->config->item('ASSETS').$path."/".$this->data;
			$out=show_thumb($media);
		}
		return $out;
	}

	function _dropdown_medias_grid() {
		$out="";
		if (!empty($this->data)) {
			$info=$this->cfg->get('CFG_media_info',$this->table.".".$this->field);
			$type=el("str_type",$info);
			if ($type=="image" or $type=="flash") {
				$path=$this->config->item('ASSETS').el("path",$info)."/";
				$data=explode("|",$this->data);
				$media=$this->config->item('ASSETS').$path."/".$this->data;
				$out.='<ul>';
				foreach($data as $img) {
					$out.='<li>'.show_thumb($path.$img).'</li>';
				}
				$out.='</ul>';
			}
			else $out=$this->data;
		}
		return $out;
	}



	/* This came form file_manager */

	function _get_unrestricted_files($restrictedToUser) {
		$this->db->where('user',$restrictedToUser);
		$this->db->set_key('file'); 
		return $this->db->get_result("cfg_media_files");
	}
	function _filter_restricted_files($files,$restrictedToUser) {
		if ($this->db->table_exists("cfg_media_files")) {
			if ($restrictedToUser) {
				$unrestrictedFiles=$this->_get_unrestricted_files($restrictedToUser);
				$unrestrictedFiles=array_keys($unrestrictedFiles);
				$assetsPath=assets();
				foreach ($files as $name => $file) {
					$file=str_replace($assetsPath,"",$file['path']);
					if (!in_array($file,$unrestrictedFiles)) unset($files[$name]);
				}
			}
		}
		return $files;
	}

	function _create_media_options($files,$types) {
		$options=array();
		foreach($files as $file) {
			if ($file["type"]!="dir") {
				$ext=strtolower(get_file_extension($file["name"]));
				if (in_array($ext,$types)) {
					$options[$file["name"]]=$file["name"]." (".trim(strftime("%e %B '%y",strtotime($file["date"]))).")";
				}
			}
		}
		return $options;
	}
	function _dropdown_media_form() {
		$info=$this->cfg->get('CFG_media_info',$this->table.".".$this->field);
		if (empty($info)) {
			$options=array(""=>"ERROR: add this field in Media Info");
			$map="";
		}
		else {
			$types=el("str_types",$info);
			$types=str_replace(",","|",$types);
			$types=explode("|",$types);
			$path=el("path",$info);
			$map=$this->config->item('ASSETS').$path;
			$files=read_map($map);
			if ($this->restrictedToUser) {
				$files=$this->_filter_restricted_files($files,$this->restrictedToUser);
			}
			$files=not_filter_by($files,"_");
			if (el('b_dragndrop',$info)) {
				$options=sort_by($files,"rawdate",TRUE);
			}
			else {
				$lastUploadMax=$this->cfg->get('CFG_media_info',$path,'int_last_uploads');
				$lastUploads=array_slice(sort_by($files,"rawdate",TRUE),0,$lastUploadMax);
				ignorecase_ksort($files);
				$options=array();
				$options[]="";
				$optionsLast=$this->_create_media_options($lastUploads,$types);
				if (!empty($optionsLast)) $options[langp("form_dropdown_sort_on_last_upload",$lastUploadMax)]=$optionsLast;
				$optionsNames=$this->_create_media_options($files,$types);
				if (!empty($optionsNames)) $options[lang("form_dropdown_sort_on_name")]=$optionsNames;
			}
		}
		$out=$this->_standard_form_field($options);
		$out["path"]=$map;
		if ($this->pre=="medias") $out["multiple"]="multiple";
		$type=el("str_type",$info);
		if ($type=="all" or $type=="image" or $type=="flash") $out["type"]="image_dropdown";
		if (el('b_dragndrop',$info)) $out["type"]="image_dragndrop";
		unset($out["button"]);
		return $out;
	}

	function _dropdown_form() {
		$out=$this->_dropdown_media_form();
		$out["multiple"]="multiple";
		return $out;
	}

	function _dropdown_allfiles_form() {
		$info=$this->cfg->get('CFG_media_info');
		$info=not_filter_by($info,"tbl_");
		$options=array();
		foreach ($info as $path => $i) {
			$map=$this->config->item('ASSETS').$path;
			$files=read_map($map);
			ignorecase_ksort($files);
			$options[""]="";
			foreach($files as $file) {
				$options[$path."/".$file["name"]]=$file["name"];
			}
		}
		$out=$this->_standard_form_field($options);
		unset($out["button"]);
		return $out;
	}

	function _dropdown_path_form() {
		$options=array();
		$map=$this->config->item('ASSETS');
		$files=read_map($map,'dir');
		unset($files['css']);
		unset($files['img']);
		unset($files['js']);
		unset($files['lists']);
		$options[""]="";
		foreach($files as $file) {
			$options[$file["name"]]=$file["name"];
		}
		$out=$this->_standard_form_field($options);
		unset($out["button"]);
		return $out;
	}

	function _dropdown_api_form() {
		$options=array();
		$apis=$this->config->config;
		$apis=filter_by_key($apis,'API');
		$options[""]="";
		foreach ($apis as $key => $value) {
			$options[$key]=$value;
		}
		$out=$this->_standard_form_field($options);
		unset($out["button"]);
		return $out;
	}


}


?>
