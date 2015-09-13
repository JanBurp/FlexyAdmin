<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Table_Model_Create extends CI_Model {
  
  protected $show_always = array('id','order','self_parent');
  
  private $messages = array();
  
	public function __construct() {
		parent::__construct();
    $this->load->library('parser');
    $this->load->library('form_validation');
    $this->load->model('cfg');
    $this->load->model('tables/table_model');
	}
  
  public function create($table='') {
    $this->messages[] = "Creating Table Models:";
    if (!empty($table)) {
      $this->messages[] = 'table = '.$table;
      $tables=array($table);
    }
    else {
      $tables = $this->db->list_tables();
    }
    
    // Load Template & Change it for everye table with its own settings
    $path = 'sys/flexyadmin/models/tables/';
    $template = file_get_contents( $path.'Table_Model_Template.php');
    // Settings
    $settings = file_get_contents( $path.'Table_Model.php');
    if (preg_match("/\* --- SETTINGS --- \*\/(.*)\/\* ---/uiUs", $settings, $matches)) {
      $settings=$matches[1];
      //  Removes multi-line comments and does not create
      //  a blank line, also treats white spaces/tabs 
      $settings = preg_replace('!^[ \s]*/\*.*?\*/[ \s]*[\r\n]!uism', '', $settings);
      //  Removes single line '//' comments, treats blank characters
      $settings = preg_replace('![ \t]*//.*[ \t]*[\r\n]!', '', $settings);
      //  Strip blank lines
      $settings = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $settings);
    }
    else {
      $settings='';
    }
    
    foreach ($tables as $table) {
      $this->messages[] = $table;
      
      // Tableinfo
      $info = $this->cfg->get( 'cfg_table_info', $table );
      $table_info = $this->db->where('table',$table)->get_row( 'cfg_table_info');
      $info = array_merge($info,$table_info);
      // Fields & Info
      $fields = $this->db->list_fields( $table );
      $fields_info = array();
      $settings_fields_info = array();
      foreach ($fields as $field) {
        $field_info = $this->cfg->get( 'cfg_field_info', $table.'.'.$field);
        $field_info_db = $this->db->where('field_field',$table.'.'.$field)->get_row('cfg_field_info');
        if (is_array($field_info) and is_array($field_info_db)) {
          $field_info = array_merge($field_info,$field_info_db);
        }
        else {
          if (!is_array($field_info)) $field_info=$field_info_db;
          if (!is_array($field_info)) $field_info=null;
        }
        $fields_info[$field] = $field_info;
        $settings_fields_info[$field] = array();
        $settings_fields_info[$field]['validation'] = $this->form_validation->get_validations( $table, $field );
        if (!empty($field_info['str_options'])) {
          $settings_fields_info[$field]['options'] = $field_info['str_options'];
          $settings_fields_info[$field]['multiple'] = $field_info['b_multi_options']?true:false;
        }
      }
      
      // Put in settings
      $settings_array = array();
      $settings_array['table']           = $table;
      $settings_array['fields']          = $fields;
      $settings_array['field_info']      = $settings_fields_info;
      $settings_array['order_by']        = el('str_order_by',$info,'');
      $settings_array['max_rows']        = el('int_max_rows',$info,0);
      $settings_array['update_uris']     = !el('b_freeze_uris',$info,false);
      $settings_array['abstract_fields'] = $this->table_model->get_abstract_fields( $settings_array['fields'] );
      $settings_array['abstract_filter'] = el('str_options_where',$info,'');
      
      // Grid settings
      $admin_grid = $this->table_model->get_admin_grid();
      $admin_grid['fields']   = $settings_array['fields'];
      foreach ($admin_grid['fields'] as $key => $value) {
        if ( !in_array($value,$this->show_always) and !el(array($value,'b_show_in_grid'),$fields_info,true) ) unset($admin_grid['fields'][$key]);
      }
      $admin_grid['order_by'] = $settings_array['order_by'];
      $settings_array['admin_grid']      = $admin_grid;
      
      // Form settings
      $admin_form = $this->table_model->get_admin_form();
      $admin_form['fields'] = $settings_array['fields'];
      foreach ($admin_form['fields'] as $key => $value) {
        if ( !in_array($value,$this->show_always) and !el(array($value,'b_show_in_form'),$fields_info,true) ) unset($admin_form['fields'][$key]);
      }
      $fieldsets=el('str_fieldsets',$info,'');
      if ($fieldsets) {
        if (!is_array($fieldsets)) $fieldsets=explode(',',$fieldsets);
        $form_sets = array();
        foreach ($fieldsets as $key=>$fieldset) {
          $form_sets[$fieldset] = array();
        }
        // Stop de juiste velden in de fieldsets
        foreach ($settings_array['fields'] as $field) {
          $fieldset = el('str_fieldset',$fields_info[$field],'');
          if (isset($form_sets[$fieldset])) array_push( $form_sets[$fieldset], $field );
        }
        $admin_form['fieldsets'] = $form_sets;
      }
      $settings_array['admin_form']      = $admin_form;
      
      
      // All data to template and save this table model
      $settings = $this->set_setting( $settings, $settings_array );
      $data=array(
        'NAME'     => $table,
        'DATE'     => date('D j F Y, H:i'),
        'SETTINGS' => "\t// SETTINGS\n".$settings."// --SETTINGS\n",
      );
      $model = $this->parser->parse_string($template, $data, true);
      file_put_contents( $path.$table.'.php', $model);
    }
    
  }

  private function set_setting($settings,$settings_array) {
    foreach ($settings_array as $key => $value) {
      $value = $this->value_to_string($value);
      $settings = preg_replace('/(protected\s\$'.$key.'.*=)(.*);/uUism', '$1 '.$value.';', $settings);
    }
    return $settings;
  }
  
  private function value_to_string($value,$tabs=1,$lev=0) {
    if (is_numeric($value)) {
      $value = (int)$value;
    }
    if (is_string($value)) {
      $value = "'".$value."'";
    }
    if (is_array($value)) {
      if (is_assoc($value)) {
        $items=$value;
        $longest_item=array_keys($items);
        $longest_item=array_sort_length($longest_item);
        $longest_item=current($longest_item);
        $len = strlen($longest_item) + 2;
        $value="array(";
        if ($lev<1) $value.="\n";
        foreach ($items as $key => $sub_value) {
          if ($lev<1)
            $value.= repeater("\t",$tabs+1) . sprintf('%-'.$len.'s',"'".$key."'");
          else
            $value.= "'".$key."'";
          $value .= " => ".$this->value_to_string($sub_value,$tabs+2,$lev+1).", ";
          if ($lev<1) $value.="\n";
        }
        $value = str_replace(", )"," )",$value);
        if ($lev<1) $value.= repeater("\t",$tabs);
        $value .= ")";
      }
      else {
        $value = "array('".implode("','",$value)."')";
      }
    }
    if (is_bool($value)) {
      $value = ($value?'true':'false');
    }
    return $value;
  }
  
  public function output() {
    return implode(PHP_EOL,$this->messages).PHP_EOL;
  }
  



}
