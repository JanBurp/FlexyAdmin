<?

	// Loads a form from the flexyform tables

 class Getform extends CI_Model {

 	function __construct() {
		$this->lang->load('update_delete');
		$this->lang->load('form');
		$this->lang->load('form_validation');
 		parent::__construct();
 	}

	function by_module($module) {
		$this->db->where('str_module',$module);
		return $this->_get_form();
	}
	
	function by_title($title) {
		$this->db->where('str_title',$title);
		return $this->_get_form();
	}

	function by_id($id) {
		$this->db->where('id',$id);
		return $this->_get_form();
	}
	
	function _get_form() {
		$form=false;
		if ($this->db->table_exists('tbl_forms') and $this->db->table_exists('tbl_formfields')) {
			$form=array();
			$form['form']=$this->db->get_row('tbl_forms');
			
			if ($form['form']) {
				$this->db->where('id_form',$form['form']['id']);
				$fields=$this->db->get_result('tbl_formfields');
				array_push($fields,array('str_type'=>'##END##','str_label'=>'##END##','str_name'=>'','str_validation'=>'','str_validation_parameters'=>''));
				// trace_($fields);
				if ($fields) {
					$options=false;
					$optionsKey='';
					$fieldset=$form['form']['str_title'];
					foreach ($fields as $key => $value) {
						// Check if a fieldset
						if ($value['str_type']=='fieldset') {
							$fieldset=$value['str_label'];
							$form['fieldsets'][]=$fieldset;
							unset($fields[$key]);
						}
						else {
							// Normal field
							unset($value['id']);
							unset($value['id_form']);
							foreach ($value as $k => $v) {
								$value[remove_prefix($k)]=$v;
								unset($value[$k]);
							}
							$value['label']=$value['label'];
							$name=str_replace(' ','_',$value['label']).'_'.$key;
							$value['name']=$name;
							$value['fieldset']=$fieldset;
							$value['validation']=add_validation_parameters($value['validation'],$value['validation_parameters']);
							unset($value['validation_parameters']);
							if ($this->input->post($name)) $value['value']=$this->input->post($name);
							$fields[$key]=$value;
							// trace_($value);

							// End options setting
							if (is_array($options) and $value['type']!='option') {
								// trace_('End Options');
								// trace_($options);
								$fields[$optionsKey]['options']=$options;
								$options=FALSE;
								$optionsKey='';
							}
							// Start option settings
							if ($value['type']=='radio' or $value['type']=='select') {
								$optionsKey=$name;
								$options=array();
								// trace_('Start Options:'.$optionsKey);
							}
							// add option
							if ($value['type']=='option' and is_array($options)) {
								$optionKey=$value['name'];
								$optionValue=$value['label'];
								if (!empty($value['html'])) $optionValue=$value['html'];
								$options[safe_string($optionKey,50)]=$optionValue;
								unset($fields[$key]);
								// trace_('Add Option to '.$optionsKey.': '.$optionValue);
							}

							// button?
							if (get_prefix($value['type'],'.')=='button') {
								$form['buttons'][$value['name']]=array( 'value'=>$value['label'], 'type'=>get_postfix($value['type'],'.') );
								unset($fields[$key]);
							}

							// Remove ##END##
							if ($value['type']=='##END##' and $value['label']=='##END##') {unset($fields[$key]);}

							if (isset($fields[$key])) {
								$fields[$name]=$fields[$key];
								unset($fields[$key]);
							}
						}
					}
				}
				if (!isset($form['fieldsets'])) $form['fieldsets']=array($fieldset);
				$form['fields']=$fields;
				if (!isset($form['buttons'])) $form['buttons']=array('submit'=>array("submit"=>"submit","value"=>'submit'));
			}
		}
		return $form;
	}

 }
?>
