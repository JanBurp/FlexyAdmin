<?php
/**
 * This class renders and validates fields
 *
 * @author Jan den Besten
 * @internal
 */

class Flexy_field extends CI_Model {

	var $table;
	var $field;
	var $formData;
	var $fieldData;
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
    $this->load->library('form_validation');
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
		$this->fieldData=$data;
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
    // trace_([$this->field." - default",$this->validation]);
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
		foreach ($row as $field=>$data) {
      if (is_foreign_key($field) and !is_foreign_field($field)) {
        $abstract_field = $this->relations['many_to_one'][$field]['result_name'].'.abstract';
        $row[$field] = $row[$abstract_field];
        unset($row[$abstract_field]);
      }
		}
    return $row;
	}


	/**
	 * function render_grid($table,$data)
	 *
	 * Renders full data set according to type and action (grid|form)
	 *
	 */
	function render_grid($table,$data,$right=RIGHTS_ALL,$relations=array(),$extraInfoId=NULL) {
    $this->relations = $relations;
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
        $data=$this->fieldData;
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
		$out=str_replace("%s",$this->fieldData,$out);
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
	public function render_form($table,$data,$options=NULL,$extraInfoId=NULL) {
		$this->init_table($table,"form",$data);
		$out=array();
		foreach ($data as $name => $value) {
			$opt=el($name,$options);
			$renderedField=$this->render_form_field($table,$name,$value,$opt,$extraInfoId);
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
	public function render_form_field($table,$field,$value,$options=NULL,$extraInfoId=NULL) {
		$this->extraInfoId = $extraInfoId;
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
    
    // trace_(['render_form_field',$field,$options]);
    
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
				$out=$this->_standard_form_field($options);
		}
		else {
			$out=$this->_standard_form_field($options);
		}
		$out['when']=$when;
		if (isset($out['class']))
			$out['class'].=' '.$class;
		else
			$out['class']=$class;
		return $out;
	}

	function _standard_form_field( $options=NULL ) {

		/**
		 * Standard form field information
		 */
		$out = array(
  		'table'    => $this->table,
  		'name'     => $this->field,
  		'value'    => $this->fieldData,
  		'label'    => $this->ui->get($this->field,$this->table),
  		'type'     => $this->type,
  		'fieldset' => el('str_fieldset', el($this->field,$this->fieldCfg), $this->ui->get($this->table) ),
      'class'    => '',
		);

		/**
		 * Dropdown fields, if there are options.
		 */
		if ( !is_null($options) ) {

			// empty option on top?
			$key = foreign_table_from_key($this->field,true);
			$optCfg = $this->cfg->get('CFG_table',$key);
      $fieldOpts = $this->cfg->get('CFG_field',$out['table'].'.'.$out['name']);
      // trace_([$this->field,$optCfg]);
			if ( el('b_add_empty_choice',$optCfg) ) {
        array_unshift( $options['data'], array('name'=>'','value'=>'') );
			}
			// no empty option needed with jquery.multiselect if multiple
			if ( el('multiple',$options) and isset( $options['data'][''] )) unset($options['data']['']);

			// type?
			if ($this->type!=="dropdown") {
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
      
      // Flat options
			$out["multiple"]=( el('multiple',$options)?'multiple':'');
      $out['options'] = array();
      if (!isset($options['data'])) {
        // trace_(['_standard_form_field',$this->field,$options]);
        throw new ErrorException( __CLASS__.'->'.__METHOD__.'() options not properly set.' );
      }
      if (!in_array($this->pre,array('media','medias'))) {
        foreach ($options['data'] as $key => $option) {
          $out['options'][el('value',$option,$key)] = $option['name'];
        }
      }
      else {
        $out['options']=el('data',$options, el('error',$options) );
      }
		}
		/**
		 * Upload field
		 */
		if ($this->type=="upload") {
			$uploadCfg=$this->cfg->get('CFG_media_info',$this->table);
			$out['upload_path'] 	= el("path",$uploadCfg);
			$out['allowed_types'] = el("str_types",$uploadCfg);
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
			$out['validation']=$this->form_validation->get_validations($out['table'],$out['name'],$validation);
		}
    // trace_($out);
		return $out;
	}


	/**
	 * field functions
	 */

	function _primary_key_grid() {
		$this->id=$this->fieldData;
		$class=$this->table." id".$this->id;
		$out="";
    if (is_editable_table($this->table)) {
      if ($this->fieldRight>=RIGHTS_EDIT)   {
        if (isset($this->extraInfoId))
          $uri=api_uri('API_view_form',$this->table.$this->config->item('URI_HASH').$this->fieldData,'info',$this->extraInfoId);
        else
          $uri=api_uri('API_view_form',$this->table.$this->config->item('URI_HASH').$this->fieldData);
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
		$this->id=$this->fieldData;
		$out=$this->_standard_form_field();
		$out["type"]="hidden";
		return $out;
	}

	function _user_grid() {
		$this->db->as_abstracts();
		$this->db->where("id",$this->fieldData);
		$user=$this->db->get_row("cfg_users");
		$out=$user['abstract'];
		return $out;		
	}

	function _user_form() {
		if ($this->restrictedToUser===FALSE) {
			$this->db->as_abstracts();
			$users=$this->db->get_result("cfg_users");
			$options=array();
			$options['data']['']='';
			foreach ($users as $key => $value) {
				$options['data'][$key]=$value['abstract'];
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

  // function _id_group_form() {
  //   /**
  //     * User can't set itself to higher user group, Remove other options
  //     */
  //   $user=$this->flexy_auth->user();
  //   $id_group=$user->id_group;
  //   $this->db->where('id >=',$id_group);
  //   $this->db->select('id,str_description');
  //   $options=$this->db->get_result('cfg_groups');
  //   foreach ($options as $id => $value) {
  //     $options[$id]=$value['str_description'];
  //   }
  //   $out=$this->_standard_form_field($options);
  //   $out['validation'].='|greater_than['.($id_group-1).']';
  //   return $out;
  // }

	function _self_grid() {
    if ($this->table=='res_menu_result') return $this->fieldData;
    if ($this->field=='self_parent') {
      if (isset($this->rowdata[$this->title_field]))
        $tree=$this->rowdata[$this->title_field];
      else
        $tree=$this->rowdata[PRIMARY_KEY];
      return "[$this->fieldData] ".$tree;
    }
    else {
      $this->db->select('id');
      $this->db->select($this->title_field);
      $this->db->where('id',$this->fieldData);
      $self=$this->db->get_row($this->table);
      if ($self) {
        return $self[$this->title_field];
      }
      return '';
    }
    return $this->fieldData;
	}

	function _self_form( $options ) {
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
		if ($strField)
			$this->db->uri_as_full_uri(TRUE,$strField);
		else
			$this->db->uri_as_full_uri(TRUE,$strField);
		$res=$this->db->get_result($this->table);
    // Zorg ervoor dat er altijd een lege optie is
    array_unshift( $options['data'], array('name'=>'','value'=>0));
    // Veld verder instellen volgens standaard
		$out=$this->_standard_form_field($options);
		$out["type"]="dropdown";
		unset($out["button"]);
		return $out;		
	}

	function _foreign_key_grid() {
		$out="";
		$data=$this->fieldData;
		if (is_array($data)) {
			if (isset($data[$this->config->item('ABSTRACT_field_name')])) {
				$out=$data[$this->config->item('ABSTRACT_field_name')];
			}
			else {
				$out=implode("|",$data);
      }
		}
		else {
		  $out=$data;
		}
		return $out;
	}

	function _order_grid() {
		$out="";
		$data=$this->fieldData;
    if (is_editable_table($this->table) AND $this->fieldRight>=RIGHTS_EDIT) {
			$out=	anchor(api_uri('API_view_order',$this->table,$this->id,"up"),help(icon("up"),lang('grid_order'))).
						anchor(api_uri('API_view_order',$this->table,$this->id,"down"),help(icon("down"),lang('grid_order')));
		}
		return $out;
	}


	function _join_grid() {
		$out=$this->fieldData;
    $out=str_replace(array('{','}',', '),array('<span class="rel_data">','</span>',''),$out);
		return $out;
	}

  
  function _actions_grid() {
		$out="";
		$data=$this->fieldData;
    foreach ($data as $key => $value) {
      $out.=anchor(api_uri('API_home',$value),lang($key),array('class' => 'button')).' ';
    }
		return $out;
  }

	function _join_form($options) {
		$out=$this->_standard_form_field($options);
    if (!isset($out['multiple'])) $out['multiple']='multiple';
		$out["button"] = api_uri('API_view_form',join_table_from_rel_table($out["name"]).':-1');
		if (get_prefix($out['name'])==$this->config->item('REL_table_prefix')) {
			$table=join_table_from_rel_table($out['name']);
			$tableInfo=$this->cfg->get('CFG_table',$table);
			$formManyType=el('str_form_many_type',$tableInfo);
			if (!empty($formManyType) and $formManyType!='dropdown') {
				$out['type']=$formManyType;
				unset($out['button']);
			}
      $values=$out['value'];
      $values=array_keys($out['value']);
      $out['value']=array_combine($values,$values);
		}
		return $out;
	}

	function _boolean_grid() {
		if ($this->fieldData)
			$out=icon("yes");
		else
			$out=icon("no");
		return $out;
	}

	function _text_grid() {
		return strip_string($this->fieldData,30);
	}

	function _dropdown_rights_form() {
		$tables=$this->db->list_tables();
		$folders=$this->cfg->get('CFG_media_info');
		$folders=array_keys($folders);
		$not=filter_by($folders,"tbl_");
		$folders=array_diff($folders,$not);
		$media=array();
		foreach($folders as $folder) { $media[]="media_".$folder; }
    $options=array('multiple'=>true,'data'=>array());
		$options_data=array("","*","cfg_*","tbl_*","media_*");
		$options_data=array_merge($options_data,$tables);
		$options_data=array_merge($options_data,$media);
    foreach ($options_data as $key => $value) {
      $options['data'][$value]=array('name'=>$value,'value'=>$value);
    }
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
    $options=array('data'=>array());
    foreach ($tables as $key => $value) {
      $options['data'][$value]=array('name'=>$value,'value'=>$value);
    }
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
        $options=array('data'=>array());
        foreach ($fieldsets as $key => $value) {
          $options['data'][$value]=array('name'=>$value,'value'=>$value);
        }
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
		$thisRights=$this->flexy_auth->get_rights();
    // $normal_tables=$tables;
    // $normal_tables=filter_by($tables,"tbl_");
    // $result_tables=filter_by($tables,"res_");
    // $normal_tables=array_merge($result_tables,$normal_tables);
		$specialFields=array_keys($this->config->item('FIELDS_special'));
		$options=array();

    $commonFields=array();
		foreach ($tables as $table) {
			$fields=$this->db->list_fields($table);
			foreach ($fields as $field) {
        if (!in_array($field,$commonFields)) $commonFields[]=$field;
				$pre=get_prefix($field);
				if ( ($this->table!='cfg_media_info') or ($pre=='media') or ($pre=='medias') or ($this->field=='fields_check_if_used_in')) {
					$options[]="$table.$field";
				}
			}
			// join fields?
      // if ($this->table!=='cfg_media_info') {
      //         $jt="rel_".remove_prefix($table).$this->config->item('REL_table_split');
      //         $rel_tables=filter_by($tables,"rel_");
      //         foreach($rel_tables as $key=>$jtable) {
      //           if (strncmp($jt,$jtable,strlen($jt))==0) {
      //             $field=$jtable;
      //             $options[]="$table.$field";
      //           }
      //         }
      // }
		}
    if ($this->table!=='cfg_media_info') {    
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
    
    $data=$options;
    $options=array('data'=>array());
    foreach ($data as $key => $value) {
      $options['data'][$value]=array('name'=>$value,'value'=>$value);
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
		if (!empty($this->fieldData)) {
			$info=$this->cfg->get('CFG_media_info',$this->table.".".$this->field);
			$path=el("path",$info);
			$media=$this->config->item('ASSETS').$path."/".$this->fieldData;
			$out=show_thumb($media);
		}
		return $out;
	}

	function _dropdown_medias_grid() {
		$out='';
		if (!empty($this->fieldData)) {
			$info=$this->cfg->get('CFG_media_info',$this->table.".".$this->field);
			$path=$this->config->item('ASSETS').el("path",$info)."/";
  		$filetypes=el("str_types",$info);
      $are_images=file_types_are_images($filetypes);
      $are_flash=file_types_are_flash($filetypes);
      $thumbs = $are_images or $are_flash;
			$data=explode("|",$this->fieldData);
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
      $options[$file['name']] = str_replace('_','_&#173;',$file["name"])." (".trim(strftime("%e %B %Y",strtotime($file["date"]))).")";
      // if ($file["type"]!=="dir") {
      //   $ext=strtolower($file["type"]);
      //   if (in_array($ext,$types)) {
      //           $name = str_replace('_','_&#173;',$file["name"])." (".trim(strftime("%e %B %Y",strtotime($file["date"]))).")";
      //     $options[$file["name"]] = array('name'=>$name,'value'=>$file['name']);
      //   }
      // }
		}
    $options = array_slice($options,0,50); // TODO: LIMIET WEG
		return $options;
	}
  
	function _dropdown_media_form() {
		$info = $this->cfg->get('CFG_media_info',$this->table.".".$this->field);
		if (empty($info)) {
      $options=array('error'=>'ERROR: add this field in Media Info');
  		$out=$this->_standard_form_field($options);
      $out['type']='input';
      $out['value']='ERROR: add this field in Media Info';
      return $out;
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
      
      // Get files as Options
      $options = array('data'=>array());
  		if ($this->pre==="medias") $options['multiple']=true;
      
      $this->data->table('res_media_files');
      $filter = array(
        'user' => $this->restrictedToUser,
        'type' => $types,
      );
      
			if ($fieldType=='image_dragndrop') {
        $options['data'] = $this->data->get_files( $path, $filter );
			}
			else {
				$lastUploadMax = $this->cfg->get('CFG_media_info',$path,'int_last_uploads');
				if ( $lastUploadMax>0 ) {
          $filesOnName = $this->data->order_by('str_title')->get_files( $path, $filter );
          $filesRecent = $this->data->order_by('rawdate','DESC')->get_files( $path, $filter, $lastUploadMax );
          $options['data'] = array(
            langp("form_dropdown_sort_on_last_upload",$lastUploadMax) => $this->_create_media_options( $filesRecent,$types ),
            lang("form_dropdown_sort_on_name")                        => $this->_create_media_options( $filesOnName,$types )
          );
				}
				else {
          $files = $this->data->get_files( $path, $filter );
					$options['data'] = $this->_create_media_options($files,$types);
				}
			}
  		// add empty option if needed
  		if ($this->cfg->get('CFG_media_info',$path,'b_add_empty_choice')) array_unshift( $options['data'],'');
      
		}
    
		$out = $this->_standard_form_field($options);

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
    if (!in_array($list,array('link','img','media','embed'))) $list='link'; // defaults to link
    $multiple=false;
    if (substr($list,strlen($list)-1,1)=='s') {
      $multiple=true;
      $list=substr($list,0,strlen($list)-1);
    }
		$list_file=$list.'_list.js';
		$site_url=site_url();
    $links=array();
		if (file_exists(SITEPATH.'assets/lists/'.$list_file)) {
			$c=file_get_contents(SITEPATH.'assets/lists/'.$list_file);
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
    $options=array('data'=>array());
    $options['data']=array('name'=>'','value'=>'');
		foreach($links as $link) {
			$lopt=explode(',',$link);
      if (isset($lopt[1])) {
        $url=str_replace('"','',$lopt[1]);
        $name=str_replace('"','',$lopt[0]).' - '.$url;
        $options['data'][$url]=array('name'=>$name,'value'=>$name);
      }
      else {
        $link = str_replace('"','',$link);
        $options['data'][$link]=array('name'=>$link,'value'=>$link);
      }
		}
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
  		$options=array('data'=>array());
  		$options['data'][]=array('name'=>'','value'=>'');
			foreach($files as $file) {
				$options['data'][$path."/".$file["name"]]=array('name'=>$file["name"],'value'=>$file['name']);
			}
		}
		$out=$this->_standard_form_field($options);
		unset($out["button"]);
		return $out;
	}

	function _dropdown_path_form() {
		$map=$this->config->item('ASSETS');
		$files=read_map($map,'dir');
    $files=array_unset_keys($files,array('css','fonts','img','js','lists','_thumbcache','less-bootstrap','less-default'));
    
		$options=array('data'=>array());
    $options['data']['']=array('name'=>'','value'=>'');
		foreach($files as $file) {
			$options['data'][$file["name"]]=array('name'=>$file['name'],'value'=>$file["name"]);
		}
		$out=$this->_standard_form_field($options);
		unset($out["button"]);
		return $out;
	}

	function _dropdown_api_form() {
		$apis=$this->config->config;
		$apis=filter_by_key($apis,'API');
		$options=array('data'=>array());
		$options['data'][]=array('name'=>'','value'=>'');
		foreach ($apis as $key => $value) {
			$options['data'][$key]=array('name'=>$value,'value'=>$value);
		}
		$out=$this->_standard_form_field($options);
		unset($out["button"]);
		return $out;
	}

	function _dropdown_plugin_form() {
		$plugins=$this->plugins;
		$options=array('data'=>array());
		$options['data'][]=array('name'=>'','value'=>'');
		foreach ($plugins as $plugin) {
			$plugin=str_replace('plugin_','',$plugin);
			$options['data'][$plugin]=array('name'=>$plugin,'value'=>$plugin);
		}
		$out=$this->_standard_form_field($options);
		unset($out["button"]);
		return $out;
	}


}


?>
