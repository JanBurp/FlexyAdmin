<?

/**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/form_helper.html" target="_blank">Form_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * @link http://codeigniter.com/user_guide/helpers/form_helper.html
 */

 /**
  * Aanpassing op CodeIgniter's form_dropdown, zie [hier](http://codeigniter.com/forums/viewthread/49348/)
  *
  * @param string $name 
  * @param array $options 
  * @param array $selected 
  * @param mixed $extra
  * @return string
  * @link http://codeigniter.com/forums/viewthread/49348/
  */
function form_dropdown($name = '', $options = array(), $selected = array(), $extra = '') {
	if ( ! is_array($selected))	{
		$selected = array($selected);
	}

	// If no selected state was submitted we will attempt to set it automatically
	if (count($selected) === 0) 	{
		// If the form name appears in the $_POST array we have a winner!
		if (isset($_POST[$name])) 		{
			$selected = array($_POST[$name]);
		}
	}

	if ($extra != '') $extra = ' '.$extra;

	$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

	$form = '<select name="'.$name.'"'.$extra.$multiple.">\n";

	if ( ! empty($options)) {
		foreach ($options as $key => $val)	{
			$key = (string) $key;
			if (is_array($val))		{
				$form .= '<optgroup label="'.$key.'">'."\n";
				foreach ($val as $optgroup_key => $optgroup_val) {
					$sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';
					$form .= '<option title="'.(string) $optgroup_val.'" value="'.$optgroup_key.'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
				}
				$form .= '</optgroup>'."\n";
			}
			else {
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
				$form .= '<option title="'.(string) $val.'" value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
			}
		}
	}

	$form .= '</select>';
	return $form;
}

/**
 * Voegt validation parameters bij elkaar
 *
 * @param string $rules 
 * @param string $params 
 * @return string
 * @author Jan den Besten
 */
function add_validation_parameters($rules,$params) {
	$validation=array();
	$rules=explode('|',$rules);
	$params=explode('|',$params);
	foreach ($rules as $key => $rule) {
		$validation[$rule]=$rule;
		if (isset($params[$key]) and !empty($params[$key])) $validation[$rule]=str_replace('[]','['.$params[$key].']',$validation[$rule]);
	}
	return implode($validation,'|');
}

/**
 * Maakt van een array (een row resultaat array uit de database) form-data dat naar form gestuurd kan worden
 *
 * @param string $array 
 * @return array
 * @author Jan den Besten
 */
function array2formfields($array,$validation_rules_prefixes=array(),$validation_rules_fields=array(),$extra=array()) {
  $default_validation_rules_prefixes=array(
    'email'  => 'required|valid_email|max_length[255]',
    'str'    => 'max_length[255]'
  );
  $validation_rules_prefixes=array_merge($default_validation_rules_prefixes,$validation_rules_prefixes);
  $default_validation_rules_fields=array(
    'id'  => 'required'
  );
  $validation_rules_fields=array_merge($default_validation_rules_fields,$validation_rules_fields);

	$formData=array();
  $assoc=is_assoc($array);
  
	foreach ($array as $field=>$value) {
    // if (!$assoc) {
    //    $field=$value;
    //    $value='';
    // }
		// standard attributes
		$type='input';
		$options=array();
		$validation='';
    $label=lang($field);
    if (empty($label)) {
      if (isset($value['label']))
        $label=$value['label'];
      else
        $label=remove_prefix($field);
      $label=nice_string($label);
    }

    $pre=get_prefix($field);
    $validation=trim(el($pre,$validation_rules_prefixes,'').'|'.el($field,$validation_rules_fields,''),'|');
    $validation=combine_validations($validation);
		switch ($pre) {
			case 'id':
				$type='hidden';
        $validation='required';
				break;
			case 'txt':
				$type='htmleditor';
				break;
			case 'stx':
				$type='textarea';
				break;
			case 'b':
				$type='checkbox';
				break;
		}
		if (!empty($type)) $formData[$field]=array_merge(array('type'=>$type,'label'=>$label,'value'=>$value,'options'=>$options,'validation'=>$validation),$extra);
	}
	return $formData;
}


/**
 * Combineerd diverse validatieregels. Verwijderd ook de dubbelingen (op een slimme manier)
 * 
 * Meegegeven parameter kan de volgende formaten hebben:
 * 
 * - een string met een optelling van validatieregels gescheiden door |
 * - een array waar de keys de validatieregels zijn en de values de eventuele paramaters
 * - een array waarbij elke rij de volgende array bevat: array('rules'=>#hier de validatieregels gescheiden door |#, 'params'=>#hier de eventuele paramaters gescheiden door |#)
 *
 * @param mixed $validations
 * @return array
 * @author Jan den Besten
 */
function combine_validations($validations,$as_array=FALSE) {
  // trace_(array('start'=>$validations));
	$validation=array();
  // Als een string meegegeven, maak er een validatiearray van
  if (is_string($validations)) {
    $vals=explode('|',$validations);
    $validations=array();
    foreach ($vals as $rule) {
      $param='';
			if (preg_match("/(.*?)\[(.*)\]/", $rule, $match)) {
				$rule	= $match[1];
				$param	= $match[2];
			}
      $validations[$rule]=$param;
    }
  }

  // Splits de rules en de params als dat nodig is
  if (!isset($validations[0]) or !is_array($validations[0])) {
    $vals=$validations;
    $validations=array();
    $rules='';
    $params='';
    foreach ($vals as $rule => $param) {
      $rules.=$rule.'|';
      $params.=$param.'|';
    }
    $validations[]=array('rules'=>rtrim($rules,'|'),'params'=>rtrim($params,'|'));
  }
	foreach ($validations as $val) {
		if (!empty($val) and !empty($val['rules'])) {
			$rules=explode('|',$val['rules']);
			$params=explode('|',$val['params']);
			foreach ($rules as $key => $rule) {
				$param='';
				if (isset($validation[$rule])) 	$param=$validation[$rule];
				if (isset($params[$key])) 			$param=$params[$key];
				if (isset($validation[$rule])) {
					switch($rule) {
						case 'max_length':
							if ($validation[$rule]<$param) $param=$validation[$rule]; // get smallest
							break;
						case 'min_length':
							if ($validation[$rule]>$param) $param=$validation[$rule]; // get biggest
							break;
					}
				}
				$validation[$rule]=$param;
			}
		}
	}
  // Cleanup double
  foreach ($validation as $rule => $param) {
    switch ($rule) {
      case 'valid_emails':
        if (isset($validation['valid_email'])) unset($validation['valid_email']);
        break;
    }
  }
  if (!$as_array) {
    $vals=$validation;
    $validation='';
    foreach ($vals as $rule => $param) {
      if (!empty($param)) $rule=$rule.'['.$param.']';
      $validation=add_string($validation,$rule,'|');
    }
  }
  // trace_(array('result'=>$validation));
	return $validation;
}



