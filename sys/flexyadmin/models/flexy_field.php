<?php /**
 * Class Flexy_field  Model
 *
 * @author Jan den Besten
 * @ignore
 * @internal
 *
 * This class renders and validates fields
 */

class Flexy_field extends CI_Model {

	var $table;
	var $field;
	var $formData;
	var $data;
  var $rowdata;
  var $title_field;
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

	function __construct() {
		parent::__construct();
	}

	function init($table,$field,$data,$action) {
		$this->init_table($table,$action);
		$this->init_field($field,$data);
	}

	function init_table($table,$action,$formData=array()) {
		$this->table=$table;
    $this->title_field=$this->db->get_first_field($this->table,'str');
		$this->action=$action;
		$this->formData=$formData;
		if (!isset($vars)) $this->set_variables();
	}

	function init_field($field,$data,$right=RIGHTS_ALL) {
		$this->data=$data;
		$this->field=$field;
		$this->fieldRight=$right;
		$cfg=$this->cfg->get('CFG_field',$this->table.".".$field);
		if (!empty($cfg)) $this->fieldCfg[$field]=$cfg;
		$this->pre=get_prefix($field);
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
	 * Maakt een veld van alle foreign velden and foreign keys
	 * @param array $row Row of all data
	 * @return array	Returns a new row
	 */
	function concat_foreign_fields($row) {
    $fields=array_keys($row);
    $newRow=array();
		foreach ($row as $field=>$data) {
      if (is_foreign_key($field) and !is_foreign_field($field)) {
        // pakt alle foreign fields van deze foreign key
        $ffields=filter_by($fields,$field.'__');
        // combineer alle data en verwijder de foreignfields
        $foreigndata='';
        foreach ($ffields as $ffield) {
          $foreigndata=add_string($foreigndata,$row[$ffield]);
        }
        $newRow[$field]=$foreigndata;
      }
      elseif (!is_foreign_field($field)) {
        $newRow[$field]=$data;
      }
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
		$this->rowdata=$this->concat_foreign_fields($row);
		foreach ($this->rowdata as $field=>$data) {
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
    
		// Hide field?
		if (isset($this->fieldCfg[$field]['b_show_in_grid']) and ($this->fieldCfg[$field]['b_show_in_grid']==false or $this->fieldCfg[$field]['b_show_in_grid']<0)) {
      return FALSE;
		}

		// How to show? Function or replace?
		$func=$this->_is_function();
		if ($func!==false) {
      if (method_exists($this,$func)) {
  			$out=$this->$func();
      }
      else {
        $func=trim(remove_suffix($func,'_'),'_');
        $data=$this->data;
        if (!is_numeric($data)) $data="'".$data."'";
        $func=str_replace('%s',$data,$func);
        $args=array();
        if (preg_match("/\((.*)\)/uiUsx", $func,$match)) {
          $args=$match[1];
          $args=strip_quotes($args);
          $args=explode(',',$args);
        }
        $func=get_prefix($func,'(');
        $out=call_user_func_array($func,$args);
      }
		}
		else {
			$out=$this->_replace();
		}
    
    // Editable?
    if ($this->pre=='b' or (isset($this->fieldCfg[$field]['b_editable_in_grid']) and $this->fieldCfg[$field]['b_editable_in_grid'])) {
      $out=array(
        'value'             =>$out,
        'editable'          =>$this->config->item('GRID_EDIT')
      );
      if (isset($this->fieldCfg[$field]['str_options']))      $out['options']=$this->fieldCfg[$field]['str_options'];
      if (isset($this->fieldCfg[$field]['b_multi_options']))  $out['multiple_options']=$this->fieldCfg[$field]['b_multi_options'];
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
		$this->init_table($table,"form",$data);
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
		$class='';
		
		// Must field be shown?
		$when=array();
		if (isset($this->fieldCfg[$field]))	{
		 	if (!el('b_show_in_form',$this->fieldCfg[$field],true)) {
				return FALSE;
			}
			if (isset($this->fieldCfg[$field]["str_show_in_form_where"]) and !empty($this->fieldCfg[$field]["str_show_in_form_where"])) {
				$when=trim($this->fieldCfg[$field]["str_show_in_form_where"]);
				$when=preg_split('/([=|<|>])/',$when,-1,PREG_SPLIT_DELIM_CAPTURE);
				if (count($when)==3) {
					foreach ($when as $key => $value) {$when[$key]=trim(trim(trim($value),'"'),"'");}
					$when= array('field'=>$this->field,'actor'=>$when[0],'operator'=>$when[1],'value'=>$when[2]);
					$class='hidden';
					switch ($when['operator']) {
						case '=' : if ($this->formData[$when['actor']]==$when['value']) {$class='';} break;
						case '>' : if ($this->formData[$when['actor']]>$when['value']) {$class='';} break;
						case '<' : if ($this->formData[$when['actor']]<$when['value']) {$class='';} break;
					}
				}
				else $when=array();
				
			}
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
		$out['when']=$when;
		if (isset($out['class']))
			$out['class'].=' '.$class;
		else
			$out['class']=$class;
		// trace_($out);
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
		$out["label"]			= $this->ui->get($this->field,$this->table);
		$out["type"]			= $this->type;
		$out["fieldset"]	= el('str_fieldset', el($this->field,$this->fieldCfg), $this->ui->get($this->table) );
    $out['class']     = '';

		/**
		 * Dropdown fields, if there are options.
		 */
		if (isset($options) or isset($multiOptions)) {
			if (isset($multiOptions)) $options=$multiOptions;
			// empty option on top?
			$key=foreign_table_from_key($this->field,true);
			$optCfg=$this->cfg->get('CFG_table',$key);
      $fieldOpts=$this->cfg->get('CFG_field',$out['table'].'.'.$out['name']);
			if (isset($optCfg['b_add_empty_choice']) and $optCfg['b_add_empty_choice']) {
				$options=array(''=>'') + $options;
			}
			// no empty option needed with jquery.multiselect if multiple
			if (!empty($multiOptions) and isset($options[''])) unset($options['']);
      // strace_($options);
			$out["options"] = $options;
			// type?
			if ($this->type!="dropdown") {
				$out["type"]="dropdown";
				$orderedOptions=$this->cfg->get('cfg_field_info',$out['table'].'.'.$out['name'],'b_ordered_options');
				if ($orderedOptions) {
					$out['type']='ordered_list';
					$out['value']=explode('|',$out['value']);
				}
			}
			else {
				// add a 'new' item button
				$out["button"]=api_uri('API_view_form',trim(foreign_table_from_key($this->field),'_').':-1');
        $out['class'].='has_button ';
			}
			if (!empty($multiOptions) ) $out["multiple"]="multiple";
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
		 * Wide?
		 */
		if ($this->type=="htmleditor" or $this->type=="function_dropdown_media" or $this->type=='function_dropdown_medias') {
			$out['class'].= $this->cfg->get('CFG_configurations','str_class').' ';
		}
		
		$out['validation']='';
		$validation[]=array('rules'=>$this->validation,'params'=>'');
		if (!empty($out['table'])) {
			$validations=$this->get_validations($out['table'],$out['name'],$validation);
		}
		if (!empty($validations)) $out['validation']=$this->_set_validation_params($validations);
    // trace_(array('start'=>$this->validation,'result'=>$out['validation']));
		return $out;
	}

	/**
	 * Add validation rules:
	 * -first the standard validation rules set in flexyadmin_config.php
	 * -then add validation rules set in cfg_field_info
	 * -validation rules depended on database field
	 */
  function get_validations($table,$field,$validation=array()) {
    $validation[]=$this->_get_global_set_validation($field);
		$validation[]=$this->_get_set_validation($table,$field);
		$validation[]=$this->_get_db_validation($table,$field);
		$validations=combine_validations($validation,TRUE);
    return $validations;
  }

	function _get_db_validation($table,$field) {
		$validation='';
		$info=$this->cfg->field_data($table,$field);
		if (isset($info['type']))
		switch ($info['type']) {
			case 'varchar':
				if (isset($info['max_length'])) {
					$validation['rules']='max_length[]';
					$validation['params']=$info['max_length'];
				}
        break;
			case 'decimal':
				$validation['rules']='decimal';
				$validation['params']='';
        break;
		}
		return $validation;
	}

	function _get_set_validation($table,$field) {
    $validation = array(
      'rules'		=> $this->cfg->get('CFG_field',$table.".".$field,'str_validation_rules'),
			'params'	=> $this->cfg->get('CFG_field',$table.".".$field,'str_validation_parameters')
    );
    return $validation;
	}

	function _get_global_set_validation($field) {
    $global_validation = array(
      'rules'		=> $this->cfg->get('CFG_field',"*.".$field,'str_validation_rules'),
			'params'	=> $this->cfg->get('CFG_field',"*.".$field,'str_validation_parameters')
    );
    return $global_validation;
	}

	function _set_validation_params($validations) {
		$validation='';
		foreach ($validations as $rule => $param) {
			if (!empty($param)) $rule=str_replace('[]','['.$param.']',$rule);
			$validation=add_string($validation,$rule,'|');
		}
		return $validation;
	}

	/**
	 * field functions
	 */

	function _primary_key_grid() {
		$this->id=$this->data;
		$class=$this->table." id".$this->id;
		$out="";
    if (is_editable_table($this->table)) {
      if ($this->fieldRight>=RIGHTS_EDIT)   {
        if (isset($this->extraInfoId))
          $uri=api_uri('API_view_form',$this->table.$this->config->item('URI_HASH').$this->data,'info',$this->extraInfoId);
        else
          $uri=api_uri('API_view_form',$this->table.$this->config->item('URI_HASH').$this->data);
        $out.=anchor( $uri, help(icon("edit"),lang('grid_edit')), array("class"=>"edit $class"));
      }
      if ($this->fieldRight>=RIGHTS_DELETE)  {
        $out.=help(icon("select"),lang('grid_select')).help(icon("delete item"),lang('grid_delete'));
      }
    }
    else {
      $out=$this->id;
    }
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

	function _id_group_form() {
		/**
			* User can't set itself to higher user group, Remove other options
			*/
		$user=$this->user->get_user();
		$id_group=$user->id_group;
		$this->db->where('id >=',$id_group);
		$this->db->select('id,str_description');
		$options=$this->db->get_result('cfg_groups');
		foreach ($options as $id => $value) {
			$options[$id]=$value['str_description'];
		}
		$out=$this->_standard_form_field($options);
		$out['validation'].='|greater_than['.($id_group-1).']';
		return $out;
	}

	function _self_grid() {
    if ($this->table=='res_menu_result') return $this->data;
    if ($this->field=='self_parent') {
      if (isset($this->rowdata[$this->title_field]))
        $tree=$this->rowdata[$this->title_field];
      else
        $tree=$this->rowdata[PRIMARY_KEY];
      return "[$this->data] ".$tree;
    }
    else {
      $this->db->select('id');
      $this->db->select($this->title_field);
      $this->db->where('id',$this->data);
      $self=$this->db->get_row($this->table);
      if ($self) {
        return $self[$this->title_field];
      }
      return '';
    }
    return $this->data;
	}

	function _self_form() {
    // Kies veld dat getoond wordt
		if ($this->table=='cfg_auto_menu') {
			$strField=$this->cfg->get('cfg_table_info','cfg_auto_menu','str_abstract_fields');
		}
		else {
      $strField=$this->db->get_first_field($this->table,'str');
		}
		$this->db->select(array(PRIMARY_KEY));
		if ($strField) $this->db->select($strField);
		if ($this->db->field_exists('uri',$this->table)) $this->db->select('uri');
		if ($this->db->field_exists('self_parent',$this->table)) $this->db->select('self_parent');
		if ($this->db->field_exists('order',$this->table)) $this->db->select('order');
    // self_parent kan niet naar zichzelf verwijzen
    if ($this->field=='self_parent') $this->db->where(PRIMARY_KEY." !=", $this->id);
    // Als self_parent bestaat, dan moet het op volgorde van de tree
    if ($this->db->field_exists('self_parent',$this->table)) $this->db->order_as_tree();
		if ($strField)
			$this->db->uri_as_full_uri(TRUE,$strField);
		else
			$this->db->uri_as_full_uri(TRUE,$strField);
		$res=$this->db->get_result($this->table);
		$options=array();
		$options[]="";
		foreach($res as $id=>$value) {
			if (isset($value["uri"])) $uri=$value["uri"]; else $uri='';
			if ($strField)
				$options[$id]=$value[$strField];
			else
				$options[$id]=$uri;
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
		else {
		  $out=$data;
		}
		return $out;
	}

	function _order_grid() {
		$out="";
		$data=$this->data;
    if (is_editable_table($this->table) AND $this->fieldRight>=RIGHTS_EDIT) {
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
  
  function _actions_grid() {
		$out="";
		$data=$this->data;
    foreach ($data as $key => $value) {
      $out.=anchor(api_uri('API_home',$value),lang($key),array('class' => 'button')).' ';
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
			$formManyType=el('str_form_many_type',$tableInfo);
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
		$options=array_combine($options,$options);
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
		$options=array_combine($tables,$tables);
		$out=$this->_standard_form_field($options);
		$out["type"]="dropdown";
		unset($out["button"]);
		return $out;
	}

	function _dropdown_fieldsets_form() {
		$table=get_prefix(el('field_field',$this->formData,''),'.');
		if ($table) {
			$fieldsets=$this->cfg->get('cfg_table_info',$table,'str_fieldsets');
			if (!empty($fieldsets)) {
				$fieldsets=explode(',',$fieldsets);
				if (!in_array($this->ui->get($table),$fieldsets)) array_unshift($fieldsets,$this->ui->get($table));
				array_unshift($fieldsets,'');
				$options=array_combine($fieldsets,$fieldsets);
				$out=$this->_standard_form_field($options);
				$out["type"]="dropdown";
				unset($out["button"]);
				return $out;
			}
		}
		$out=$this->_standard_form_field();
		return $out;
	}


	function _dropdown_field_form() {
		$tables=$this->db->list_tables();
		$thisRights=$this->user->get_rights();
		$normal_tables=filter_by($tables,"tbl_");
		$result_tables=filter_by($tables,"res_");
		$normal_tables=array_merge($result_tables,$normal_tables);
		$specialFields=array_keys($this->config->item('FIELDS_special'));
		$options=array();

    $commonFields=array();
		foreach ($normal_tables as $table) {
			$fields=$this->db->list_fields($table);
			foreach ($fields as $field) {
        if (!in_array($field,$commonFields)) $commonFields[]=$field;
				$pre=get_prefix($field);
				if ( ($this->table!='cfg_media_info') or ($pre=='media') or ($pre=='medias') or ($this->field=='fields_check_if_used_in')) {
					$options[]="$table.$field";
				}
			}
			// join fields?
			if ($this->table!='cfg_media_info') {
  			$jt="rel_".remove_prefix($table).$this->config->item('REL_table_split');
  			$rel_tables=filter_by($tables,"rel_");
  			foreach($rel_tables as $key=>$jtable) {
  				if (strncmp($jt,$jtable,strlen($jt))==0) {
  					$field=$jtable;
  					$options[]="$table.$field";
  				}
  			}
			}
		}
    if ($this->table!='cfg_media_info') {    
      $commonFields=array_reverse($commonFields);
      foreach ($commonFields as $field ) {
        array_unshift($options,'*.'.$field);
      }
    }
    array_unshift($options,"");
    foreach ($options as $key => $value) {
      $options[$value]=$value;
      unset($options[$key]);
    }
    ksort($options);
    
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
		$out='';
		if (!empty($this->data)) {
			$info=$this->cfg->get('CFG_media_info',$this->table.".".$this->field);
			$path=$this->config->item('ASSETS').el("path",$info)."/";
  		$filetypes=el("str_types",$info);
      $are_images=file_types_are_images($filetypes);
      $are_flash=file_types_are_flash($filetypes);
      $thumbs = $are_images or $are_flash;
			$data=explode("|",$this->data);
      // if ($thumbs) $out.='<ul>';
			foreach($data as $file) {
        if ($thumbs)
					$out.=show_thumb($path.$file);
				else
					$out=add_string($out,$file,'&nbsp;| ');
			}
      // if ($thumbs) $out.='</ul>';
		}
		return $out;
	}

	function _create_media_options($files,$types) {
		$options=array();
		foreach($files as $file) {
      
			if ($file["type"]!="dir") {
				$ext=strtolower(get_file_extension($file["name"]));
				if (in_array($ext,$types)) {
					$options[$file["name"]]=str_replace('_','_&#173;',$file["name"])." (".trim(strftime("%e %B %Y",strtotime($file["date"]))).")";
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
      
      // Determine fieldtype first
  		$filetypes=el("str_types",$info);
      $are_images=file_types_are_images($filetypes);
      $are_flash=file_types_are_flash($filetypes);
      
      $fieldType=$this->type;
  		if ( file_types_are_images($types) or file_types_are_flash($types) ) {
        $fieldType="image_dropdown";
    		if (el('b_dragndrop',$info)) $fieldType="image_dragndrop";
      }
      else {
        $fieldType='dropdown';
      }
      
      // Get Options
			$files=read_map($map,$types);
			if ($this->restrictedToUser) {
        $this->load->model('mediatable');
				$files=$this->mediatable->filter_restricted_files($files,$this->restrictedToUser);
			}
			$files=not_filter_by($files,"_");
			$order='_rawdate';
			if (isset($info['str_order']) and !empty($info['str_order'])) {
				$order=$info['str_order'];
			}
			if (substr($order,0,1)=='_') {
				$desc=TRUE;	
				$order=substr($order,1);
			}
			else {
				$desc=FALSE;
			}

			if ($fieldType=='image_dragndrop') {
				$options=sort_by($files,$order,$desc);
				if ($this->cfg->get('CFG_media_info',$path,'b_add_empty_choice') and ($this->pre!='medias'))	array_unshift($options,'');
			}
			else {
				$lastUploadMax=$this->cfg->get('CFG_media_info',$path,'int_last_uploads');
				if ($lastUploadMax>0) {
					$lastUploads=sort_by($files,array("rawdate"),TRUE,FALSE,$lastUploadMax);
					ignorecase_ksort($files);
					$options=array();
					// add empty option if needed
					if ($this->pre=='media' and $this->cfg->get('CFG_media_info',$path,'b_add_empty_choice')) $options[]="";
					$optionsLast=$this->_create_media_options($lastUploads,$types);
					if (!empty($optionsLast)) $options[langp("form_dropdown_sort_on_last_upload",$lastUploadMax)]=$optionsLast;
					$optionsNames=$this->_create_media_options($files,$types);
					if (!empty($optionsNames)) $options[lang("form_dropdown_sort_on_name")]=$optionsNames;
				}
				else {
					$options=$this->_create_media_options($files,$types);
					if ($this->cfg->get('CFG_media_info',$path,'b_add_empty_choice')) array_unshift($options,'');
				}
			}
		}
    
		$out=$this->_standard_form_field($options);
    if (isset($fieldType)) $out['type']=$fieldType;
		$out["path"]=$map;
		if ($this->pre=="medias") $out["multiple"]="multiple";
		unset($out["button"]);
		return $out;
	}
	
	function _dropdown_list_form() {
		$options=array();
		$field=$this->field;
		$list=remove_prefix($field);
    $multiple=false;
    if (substr($list,strlen($list)-1,1)=='s') {
      $multiple=true;
      $list=substr($list,0,strlen($list)-1);
    }
		$list_file=$list.'_list.js';
		$site_url=site_url();
    $links=array();
		if (file_exists(SITEPATH.'assets/lists/'.$list_file)) {
			$c=read_file(SITEPATH.'assets/lists/'.$list_file);
			$c=str_replace(array('var tinyMCE'.ucfirst($list).'List = new Array(',');'),'',$c);
      $mathes=array();
      if (preg_match_all("/\\[(.*)?\\]/uiU", $c,$matches)) {
        $links=$matches[1];
  			foreach ($links as $key => $link) {
          if ( ($link=='""') )
            unset($links[$key]);
          else
  					$links[$key]=str_replace($site_url,'',$link);
  			}
      }
		}
		$options[""]="";
		foreach($links as $link) {
			$lopt=explode(',',$link);
      if (isset($lopt[1])) {
        $url=str_replace('"','',$lopt[1]);
        $name=str_replace('"','',$lopt[0]).' - '.$url;
        $options[$url]=$name;
      }
      else {
        $options[$link]=str_replace('"','',$link);
      }
		}
    if ($multiple)
  		$out=$this->_standard_form_field(NULL,$options);
    else
      $out=$this->_standard_form_field($options);
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

	function _dropdown_plugin_form() {
		$options=array();
		$plugins=$this->plugins;
		$options[""]="";
		foreach ($plugins as $plugin) {
			$plugin=str_replace('plugin_','',$plugin);
			$options[$plugin]=$plugin;
		}
		$out=$this->_standard_form_field($options);
		unset($out["button"]);
		return $out;
	}


}


?>
