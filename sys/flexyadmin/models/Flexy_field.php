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
  var $settings = array();
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
    $this->data->table($table);
    $this->title_field = $this->data->list_fields('str',1);
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
    $validation_set = FALSE;
    if ( $validation = el(array('field_info',$this->field,'validation'),$this->settings) ) {
      $validation_set = TRUE;
      $this->_set_validation( implode('|',$validation) );
    }
      
    
		/**
		 * is it a special fieldname?
		 */
		$specials=$this->config->item('FIELDS_special');
		if (isset($specials[$this->field][$this->action])) {
			$type=$specials[$this->field][$this->action];
			$validation=el("validation",$specials[$this->field]);
			$this->_set_type($type);
      if (!$validation_set) $this->_set_validation($validation);
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
      if (!$validation_set) $this->_set_validation($validation);
			return $type;
		}

		/**
		 * or is it a known field information from database
		 */
		$platform=$this->db->platform();
		$info=$this->db->field_data($this->table);
		$database=$this->config->item('FIELDS_'.$platform);
		if (isset($database[$this->field][$this->action])) {
			// trace_($this->field." - from database field info");
			$type=$database[$this->field][$this->action];
			$validation=el("validation",$database[$this->field]);
			$this->_set_type($type);
      if (!$validation_set) $this->_set_validation($validation);
			return $type;
		}

		/**
		 * if nothing else, just the default
		 */
		$default=$this->config->item('FIELDS_default');
		$type=el($this->action,$default);
		$validation=el("validation",$default);
		$this->_set_type($type);
		if (!$validation_set) $this->_set_validation($validation);
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
	function concat_foreign_fields($row,$grid=false) {
    $fields=array_keys($row);
    foreach ($row as $field=>$data) {
      if ( (is_foreign_key($field) and !is_foreign_field($field)) or substr($field,0,4)==='user') {
        if (isset($this->relations['many_to_one'][$field])) {
          $abstract_field = $this->relations['many_to_one'][$field]['result_name'].'.abstract';
          if ($grid) $row[$field] = $row[$abstract_field];
          unset($row[$abstract_field]);
        }
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
	function render_grid($table,$data,$right=RIGHTS_ALL, $settings, $extraInfoId=NULL) {
    $this->settings = $settings;
    $this->relations = el('relations',$settings);
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
		$this->rowdata=$this->concat_foreign_fields($row,true);
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
	public function render_form($table,$data,$options=NULL,$settings,$extraInfoId=NULL) {
    $this->settings = $settings;
    $this->form_set = $settings['form_set'];
    $this->relations = $settings['relations'];
		$this->init_table($table,"form",$data);
		$out=array();
    $data=$this->concat_foreign_fields($data);
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
    // trace_($func);
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
    // trace_($out);
		return $out;
	}

	function _standard_form_field( $options=NULL ) {
    
		/**
		 * Standard form field information
		 */
    $fieldset='';
    if (isset($this->form_set['fieldsets'])) {
      foreach ($this->form_set['fieldsets'] as $fieldset_name => $fieldset_fields) {
        if (in_array($this->field,$fieldset_fields)) $fieldset=$fieldset_name;
        if ($fieldset==$this->table) $fieldset=$this->ui->get($this->table);
      }
    }
    
		$out = array(
  		'table'    => $this->table,
  		'name'     => $this->field,
  		'value'    => $this->fieldData,
  		'label'    => $this->ui->get($this->field,$this->table),
  		'type'     => $this->type,
      // 'fieldset' => el('str_fieldset', el( $this->field, $this->fieldCfg), $this->ui->get($this->table) ),
      'fieldset' => $fieldset,
      'class'    => '',
		);

		/**
		 * Dropdown fields, if there are options.
		 */
		if ( !is_null($options) ) {

			// type?
			if ($this->type!=="dropdown") {
				$out["type"]="dropdown";
				$orderedOptions=$this->cfg->get('cfg_field_info',$out['table'].'.'.$out['name'],'b_ordered_options');
				if ($orderedOptions) {
					$out['type']='ordered_list';
					$out['value']=explode('|',$out['value']);
				}
			}

			// add a 'new' item button
      $other_table = el('table',$options);
      if ($other_table) {
        $out["button"] = api_uri('API_view_form',$other_table.':-1');
        $out['class'].='has_button ';
      }
      
      // Options
      if (isset($options['error'])) {
        $options=$options['error'];
      }
      else {
        $out['multiple']=( el('multiple',$options)?'multiple':'');
        
        // Opties op standaard manier key=>value
        if (isset($options['data'])) $options = $options['data'];
        $out['options']=array();
        foreach ($options as $key => $option) {
          $out['options'][$key]=$option;
        }
        
  			// Empty option? Niet nodig met FORM_NICE_DROPDOWNS en 'multiple'
        if ( !$this->config->item('FORM_NICE_DROPDOWNS') or empty($out['multiple']) ) {
    			$key       = foreign_table_from_key($this->field,true);
    			$optCfg    = $this->cfg->get('CFG_table',$key);
          $fieldOpts = $this->cfg->get('CFG_field',$out['table'].'.'.$out['name']);
    			if ( el('b_add_empty_choice',$optCfg) ) {
            $options = array_unshift_assoc( $options,'','');
    			}
        }
        else {
          if (current($out['options'])=='') {
            unset($out['options']['']);
          }
        }
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
    if ( el('multiple',$out,'')==='' ) unset($out['multiple']);
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
    $out=$this->fieldData;
    return $out;
  }
  
  

	function _user_form() {
		if ($this->restrictedToUser===FALSE) {
      $users = $this->data->table('cfg_users')->select_abstract()->get_result();
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
      $self = $this->data->table($this->table)
                          ->select('id')
                          ->select($this->title_field)
                          ->where('id',$this->fieldData)
                          ->get_row();
      if ($self) {
        return $self[$this->title_field];
      }
      return '';
    }
    return $this->fieldData;
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
		$tables=$this->data->list_tables();
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
      $options['data'][$value]=$value;
    }
		$out=$this->_standard_form_field($options);
		$out["type"]="dropdown";
		$out["multiple"]="multiple";
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
		}
    $options = array_slice($options,0,50); // TODO: LIMIET WEG
		return $options;
	}
  
  function _dropdown_media_form($options) {
    $out=$this->_standard_form_field($options);
    $out['path']=$this->config->item('ASSETS').$options['path'];
    $out['type']="image_dropdown";
    if ( $this->cfg->get('CFG_media_info',$this->table.".".$this->field,'b_dragndrop') ) {
      $out['type']='image_dragndrop';
      $out['options'] = array_pop($out['options']);
    }
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
    $options['data'][]=array('name'=>'','value'=>'');
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
