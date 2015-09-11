<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Data_Model_Create extends CI_Model {
  
  private $messages = array();
  
	public function __construct() {
		parent::__construct();
    $this->load->library('parser');
	}
  
  public function create($table='') {
    $this->messages[] = "Creating datamodels:";
    if (!empty($table)) {
      $this->messages[] = 'table = '.$table;
      $tables=array($table);
    }
    else {
      $tables = $this->db->list_tables();
    }
    
    //
    $path = 'sys/flexyadmin/models/data/';
    $template = file_get_contents( $path.'Data_Model_Template.php');
    $settings = file_get_contents( $path.'Data_Model.php');
    // preg_match("//\\* --- SETTINGS --- \\*/(.*)/* ---/uiUs", $searchText)
    if (preg_match("/\* --- SETTINGS --- \*\/(.*)\/\* ---/uiUs", $settings, $matches)) {
      $settings=$matches[1];
      //  Removes multi-line comments and does not create
      //  a blank line, also treats white spaces/tabs 
      $settings = preg_replace('!^[ \s]*/\*.*?\*/[ \s]*[\r\n]!uism', '', $settings);
      //  Removes single line '//' comments, treats blank characters
      $settings = preg_replace('![ \t]*//.*[ \t]*[\r\n]!', '', $settings);
      //  Strip blank lines
      $settings = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $settings);
      
      // Fill in some basic settings
      $settings = str_replace('protected $table            = \'\';',    'protected $table            = \''.$table.'\';', $settings);
      $fields = $this->db->list_fields( $table );
      $fields = "'".implode("','",$fields)."'";
      $settings = str_replace('protected $fields           = array();', 'protected $fields           = array('. $fields .');', $settings);
      
      
    }
    else {
      $settings='';
    }
    
    foreach ($tables as $table) {
      $this->messages[] = $table;
      
      $data=array(
        'NAME'     => $table,
        'SETTINGS' => $settings,
      );
      $model = $this->parser->parse_string($template, $data, true);
      file_put_contents( $path.$table.'.php', $model);
    }
    
  }
  
  public function output() {
    return implode(PHP_EOL,$this->messages).PHP_EOL;
  }
  



}
