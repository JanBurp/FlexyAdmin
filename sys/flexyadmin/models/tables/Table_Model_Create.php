<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Table_Model_Create extends CI_Model {
  
  private $messages = array();
  
	public function __construct() {
		parent::__construct();
    $this->load->library('parser');
    $this->load->model('cfg');
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
      
      // Fill in some basic settings
      $settings = $this->set_setting( $settings, 'table', "'".$table."'" );
      $fields = $this->db->list_fields( $table );
      $fields = "'".implode("','",$fields)."'";
      $settings = $this->set_setting( $settings, 'fields', 'array('. $fields .')' );
      // Fill in settings from cfg_table_info (if exists)
      $info = $this->cfg->get( 'cfg_table_info', $table );
      $settings = $this->set_setting( $settings, 'order_by', "'".el('str_order_by',$info,'')."'" );
      $settings = $this->set_setting( $settings, 'max_rows', "'".el('int_max_rows',$info,0)."'" );
      $settings = $this->set_setting( $settings, 'update_uris', el('b_freeze_uris',$info,false)?'false':'true' );
      $abstract_fields = el('str_abstract_fields',$info,'');
      if (!empty($abstract_fields))
        $abstract_fields='array('.$abstract_fields.')';
      else
        $abstract_fields='array()';
      $settings = $this->set_setting( $settings, 'abstract_fields', $abstract_fields );
      $settings = $this->set_setting( $settings, 'abstract_filter', "'".el('str_options_where',$info,'')."'" );
      
      $data=array(
        'NAME'     => $table,
        'DATE'     => date('D j F Y, H:i'),
        'SETTINGS' => $settings,
      );
      $model = $this->parser->parse_string($template, $data, true);
      file_put_contents( $path.$table.'.php', $model);
    }
    
  }

  private function set_setting($settings,$key,$value) {
    $settings = preg_replace('/(protected\s\$'.$key.'.*=\s)(.*);/uUism', '$1'.$value.';', $settings);
    return $settings;
  }
  
  public function output() {
    return implode(PHP_EOL,$this->messages).PHP_EOL;
  }
  



}
