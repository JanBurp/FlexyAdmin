<?
/**
 * See http://codeigniter.com/forums/viewthread/49348/
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

