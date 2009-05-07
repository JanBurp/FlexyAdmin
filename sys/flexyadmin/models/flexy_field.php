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

	function init_field($field,$data) {
		$this->data=$data;
		$this->field=$field;
		$cfg=$this->cfg->get('CFG_field',$this->table.".".$field);
		if (!empty($cfg)) $this->fieldCfg[$field]=$cfg;
		$replacePre=el("str_overrule_prefix",$cfg);
		if (empty($replacePre))
			$this->pre=get_prefix($field);
		else
			$this->pre=$replacePre;
		$this->type();
	}

	function set_variables() {
		$this->vars=array(
				"IMG_MAP"=> $this->cfg->get('CFG_media_info',$this->table,"str_path")
				);
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
			//trace_($this->field." - from database field info");
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
	function render_grid($table,$data) {
		$this->init_table($table,"grid");
		$out=array();
		foreach ($data as $idRow=>$row) {
			$out[$idRow]=$this->render_grid_row($table,$row);
		}
		return $out;
	}

	/**
	 * function render_grid_row($table,$row)
	 *
	 * Renders row according to type and action (grid|form)
	 *
	 */
	function render_grid_row($table,$row) {
		$out=array();
		// first create one field from foreign data
		$row=$this->concat_foreign_fields($row);
		foreach ($row as $field=>$data) {
			$renderedField=$this->render_grid_field($table,$field,$data);
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
	function render_grid_field($table,$field,$data) {
		$this->init_field($field,$data);
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
	function render_form($table,$data,$options=NULL,$multiOptions=NULL) {
		$this->init_table($table,"form");
		$out=array();
		foreach ($data as $name => $value) {
			$opt=el($name,$options);
			$mOpt=el($name,$multiOptions);
			$renderedField=$this->render_form_field($table,$name,$value,$opt,$mOpt);
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
	function render_form_field($table,$field,$value,$options=NULL,$multiOptions=NULL) {
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
			$out['upload_path'] 	= el("str_path",$uploadCfg);
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
		if (!empty($extraValidation)) $out["validation"]=add_string($out["validation"],$extraValidation,"|");
		return $out;
	}

	/**
	 * field functions
	 */

	function _primary_key_grid() {
		$this->id=$this->data;
		$class=$this->table." id".$this->id;
		return 	anchor(api_uri('API_view_form',$this->table,$this->data),icon("edit"),array("class"=>"edit $class")).
						anchor(api_uri('API_confirm',$this->table,$this->data),icon("delete"),array("class"=>"delete $class"));
	}

	function _primary_key_form() {
		$this->id=$this->data;
		$out=$this->_standard_form_field();
		$out["type"]="hidden";
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
		$out=	anchor(api_uri('API_view_order',$this->table,$this->id,"up"),icon("up")).
					anchor(api_uri('API_view_order',$this->table,$this->id,"down"),icon("down"));
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
		$out["button"]=api_uri('API_view_form',join_table_from_rel_table($out["name"]),-1);
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
		$tables=filter_by($tables,"tbl_");
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
			$path=el("str_path",$info);
			$media=$this->config->item('ASSETS').$path."/".$this->data;
			$out=show_thumb($media);
		}
		return $out;
	}

	function _dropdown_medias_grid() {
		$out="";
		if (!empty($this->data)) {
			$info=$this->cfg->get('CFG_media_info',$this->table.".".$this->field);
			$path=$this->config->item('ASSETS').el("str_path",$info)."/";
			$data=explode("|",$this->data);
			$media=$this->config->item('ASSETS').$path."/".$this->data;
			foreach($data as $img) {
				$out.=show_thumb($path.$img);
			}
		}
		return $out;
	}

	function _dropdown_media_form() {
		$info=$this->cfg->get('CFG_media_info',$this->table.".".$this->field);
		$types=explode(",",el("str_types",$info));
		$path=el("str_path",$info);
		$map=$this->config->item('ASSETS').$path;
		$files=read_map($map);
		ignorecase_ksort($files);
		$options[""]="";
		foreach($files as $file) {
			if ($file["type"]!="dir") {
				$ext=get_file_extension($file["name"]);
				if (in_array($ext,$types)) {
					$options[$file["name"]]=$file["name"];
				}
			}
		}
		$out=$this->_standard_form_field($options);
		$out["path"]=$map;
		$type=el("str_type",$info);
		if ($type=="all" or $type=="image" or $type=="flash") $out["type"]="image_dropdown";
		unset($out["button"]);
		return $out;
	}

	function _dropdown_medias_form() {
		$out=$this->_dropdown_media_form();
		$out["multiple"]="multiple";
		return $out;
	}


}


?>
