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
function array2formfields($array) {
	$formData=array();
	foreach ($array as $field=>$value) {
		// standard attributes
		$type='input';
		$options=array();
		$validation='';
    $label=lang($field);
    if (empty($label)) $label=nice_string(remove_prefix($field));

		switch (get_prefix($field)) {
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
			case 'email':
				$validation='required|valid_email';
				break;
      case 'dat':
      case 'tme':
        $validation='';
        break;
			case 'b':
				$type='checkbox';
				$validation='';
				break;
		}
		if (!empty($type)) $formData[$field]=array('type'=>$type,'label'=>$label,'value'=>$value,'options'=>$options,'validation'=>$validation);
	}
	return $formData;
}


