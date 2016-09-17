<?php 

/** \ingroup helpers
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/form_helper.html" target="_blank">Form_helper van CodeIgniter</a>.
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 * @file
 */

 /**
  * Aanpassing op CodeIgniter's form_dropdown, zie [hier](http://codeigniter.com/forums/viewthread/49348/)
  *
  * @param string $name  default=''
  * @param array $options  default=array()
  * @param array $selected  default=array()
  * @param mixed $extra  default=''
  * @return string
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
 * Maakt van een array form-data die naar form gestuurd kan worden.
 * 
 * @param array $array kan een resultaat zijn uit de database, of een array met velden (zoals door db->list_fields())
 * @param array $validation_rules_prefixes  default=array()
 * @param array $validation_rules_fields  default=array()
 * @param array $extra  default=array()
 * @param array $field_options  default=array()
 * @return array
 * @author Jan den Besten
 */
function array2formfields($array,$validation_rules_prefixes=array(),$validation_rules_fields=array(),$extra=array(),$field_options=array()) {
  $CI=&get_instance();
  $CI->load->library('form_validation');
  
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
    if (!$assoc) {
       $field=$value;
       $value='';
    }
    
		// standard attributes
		$type='input';
		
    $options=array();
    if (isset($field_options[$field])) {
      $options=$field_options[$field];
      $type='dropdown';
    }
    
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
    $validation=$CI->form_validation->combine_validations($validation);
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

