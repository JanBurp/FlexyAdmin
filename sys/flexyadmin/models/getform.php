<?php 
/**
 * Dit model wordt gebruikt om een formulier te creeren aan de hand van Flexy Form tabellen, zie ook de module [Flexy Form]({Flexy_form})
 *
 * @package default
 * @author Jan den Besten
 */

 class Getform extends CI_Model {

   /**
    * @ignore
    */
  public	function __construct() {
    $this->lang->load('update_delete');
    $this->lang->load('form');
    $this->lang->load('form_validation');
    parent::__construct();
  }

  /**
   * Kiest formulier aan de hand van module in moduleveld ('str_module') in tbl_forms
   *
   * @param string $module
   * @return string
   * @author Jan den Besten
   * @ignore
   * @depricated
   */
	public function by_module($module) {
		$this->db->where('str_module',$module);
		return $this->_get_form();
	}

  /**
   * Kiest formulier aan de hand van de naam ('str_name') in tbl_forms
   *
   * @param string $module
   * @return string
   * @author Jan den Besten
   */
	public function by_name($title) {
		$this->db->where('LOWER(`str_name`) = LOWER("'.$title.'")');
		return $this->_get_form();
	}

	
  /**
   * Kiest formulier aan de hand van titel ('str_title') in tbl_forms
   *
   * @param string $module
   * @return string
   * @author Jan den Besten
   */
	public function by_title($title) {
		$this->db->where('LOWER(`str_title_'.$this->site['language'].'`) = LOWER("'.$title.'")');
		return $this->_get_form();
	}

  /**
   * Kiest formulier aan de hand van id in tbl_forms
   *
   * @param string $module
   * @return string
   * @author Jan den Besten
   */
	public function by_id($id) {
		$this->db->where('id',$id);
		return $this->_get_form();
	}
	
  /**
   * Kiest gewenste formulier en geeft alle formulier data terug
   *
   * @return array
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _get_form() {
		$form=false;
    $lang=$this->site['language'];
		if ($this->db->table_exists('tbl_forms') and $this->db->table_exists('tbl_formfields')) {
			$form=array();
      $row=$this->db->get_row('tbl_forms');
			if ($row) {
        $form['form']=$row;
        $this->db->where('id_form',$form['form']['id']);
				$fields=$this->db->get_result('tbl_formfields');
				array_push($fields,array('str_type'=>'##END##','str_label'=>'##END##','str_name'=>'','str_validation'=>'','str_validation_parameters'=>''));
				// trace_($fields);
				if ($fields) {
					$options=false;
					$optionsKey='';
          if (isset($form['form']['str_title_'.$lang]))
            $fieldset=$form['form']['str_title_'.$lang];
          else
            $fieldset=$form['form']['str_title'];
          
					foreach ($fields as $key => $value) {
						if ($value['str_type']=='fieldset') {
              // FIELDSET
							$fieldset=$value['str_label'];
							$form['fieldsets'][]=$fieldset;
							unset($fields[$key]);
						}
						else {
							// FIELD
              // cleanup keys
							unset($value['id']);
							unset($value['id_form']);
							foreach ($value as $k => $v) {
								$value[remove_prefix($k)]=$v;
								unset($value[$k]);
							}
              // label
              if (isset($value['label_'.$lang])) $value['label']=$value['label_'.$lang];
              // name
							$name=str_replace(' ','_',$value['label']);
              if (empty($name)) $name.='_'.$key;
							$value['name']=$name;
              // fieldset
							$value['fieldset']=$fieldset;
              // options (inline)
              if (isset($value['options']) and !empty($value['options'])) {
                $opts=explode('|',$value['options']);
                $opts=array_combine($opts,$opts);
                $value['options']=$opts;
              }
              // validation
							$value['validation']=add_validation_parameters($value['validation'],$value['validation_parameters']);
							unset($value['validation_parameters']);
              // value?
							if ($this->input->post($name)) $value['value']=$this->input->post($name);
              // save
							$fields[$key]=$value;

              // OPTIONS

							// End options setting
							if (is_array($options) and $value['type']!='option') {
								// trace_('End Options');
								// trace_($options);
								$fields[$optionsKey]['options']=$options;
								$options=FALSE;
								$optionsKey='';
							}
							// Start option settings
							if (($value['type']=='radio' or $value['type']=='select') and (!isset($value['options']) or empty($value['options'])) ) {
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

							// BUTTON
							if (get_prefix($value['type'],'.')=='button') {
								$form['buttons'][$value['name']]=array( 'value'=>$value['label'], 'type'=>get_suffix($value['type'],'.') );
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
