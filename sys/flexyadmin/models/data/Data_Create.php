<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Data_Create extends CI_Model {
  
  
  private $paths = array();
  private $default_data = array();
  
  protected $site_models = array('tbl','rel');
  // protected $show_always = array('id','order','self_parent');
  
  private $messages = array();
  
	public function __construct() {
		parent::__construct();
    $this->paths = array(
      'sys_model'   => 'sys/flexyadmin/models/data/',
      'site_model'  => SITEPATH.'models/data/',
      'sys_config'  => 'sys/flexyadmin/config/data/',
      'site_config' => SITEPATH.'config/data/',
    );
    if (SAFE_INSTALL) {
      $this->paths = array(
        'sys_model'   => '../sys/flexyadmin/models/data/',
        'site_model'  => SITEPATH.'models/data/',
        'sys_config'  => '../sys/flexyadmin/config/data/',
        'site_config' => SITEPATH.'config/data/',
      );
    }
    
    $this->load->library('parser');
    $this->load->library('form_validation');
    $this->load->model( 'data/data', 'data' );

    $default_data_file = $this->paths['sys_config'].'data.php';
    $this->config->load($default_data_file,true);
    $this->default_data = $this->config->config['data/data'];
	}
  
  
  /**
   * Clear data settings cache
   *
   * @param string [$table] 
   * @return bool
   * @author Jan den Besten
   */
  public function resetcache( $table='' ) {
    $result = FALSE;
    if (!empty($table)) {
      $result = $this->cache->delete('data_settings_'.$table);
      if ($result) $this->messages[] = 'Settings cache for '.$table.' removed.';
    }
    else {
      $cached_results = $this->cache->cache_info();
      foreach ($cached_results as $cache) {
        if (substr($cache['name'],0,14)==='data_settings_' ) {
          $this->cache->delete($cache['name']);
        }
      }
      if ($result) $this->messages[] = 'Settings cache for all tables removed.';
    }
    return $result;
  }
  
  
  public function create( $table='', $save = TRUE ) {
    $returned_config = array();
    
    $this->messages[] = "Creating Data Models:";
    if (!empty($table)) {
      $this->messages[] = 'table = '.$table;
      $tables=array($table);
    }
    else {
      $tables = $this->data->list_tables();
    }
    
    // Load Template & Change it for everye table with its own settings
    $model_template = file_get_contents( $this->paths['sys_model'].'Data_Template.php');
    $config_template= file_get_contents( $this->paths['sys_config'].'data.php');
    // Remove all comments from config_template
    $config_template = preg_replace("~/\*\*.*\/\n~uUs", "", $config_template);
    // Remove empty lines
    $config_template = preg_replace("/^\n/uim", "", $config_template);
    
    foreach ($tables as $table) {
      $this->messages[] = $table;
      $this->data->table( $table );
      $settings = $this->data->get_settings();
      // All data to template and save this table model
      $data=array(
        'NAME'     => $table,
        'DATE'     => date('D j F Y, H:i'),
      );
      $model  = $this->parser->parse_string( $model_template, $data, true );
      $config = $this->replace_config($config_template,$settings);
      $config = $this->parser->parse_string( $config, $data, true );
      if ($save) {
        $file = $table.'.php';
        $mfile = ucfirst($file);
        if (is_string($save)) $file=$save.'/'.$file;
        // sys or site
        if ( in_array(get_prefix($table),$this->site_models) ) {
          file_put_contents( $this->paths['site_model'].$mfile, $model);
          file_put_contents( $this->paths['site_config'].$file, $config );
        }
        else {
          file_put_contents( $this->paths['sys_model'].$mfile, $model);
          file_put_contents( $this->paths['sys_config'].$file, $config );
        }
      }
      $returned_config[$table] = $settings;
    }
    
    return $returned_config;
  }
  
  
  public function save_config_for($table,$config) {
    $config_template= file_get_contents( $this->paths['sys_config'].'data_model.php');
    // Remove all comments from config_template
    $config_template = preg_replace("~/\*\*.*\/\n~uUs", "", $config_template);
    // All data to template and save this table model
    $data=array(
      'NAME'     => $table,
      'DATE'     => date('D j F Y, H:i'),
    );
    $config = $this->replace_config($config_template,$config);
    $config = $this->parser->parse_string( $config, $data, true );
    // sys or site
    $file = $table.'.php';
    if ( in_array(get_prefix($table),$this->site_models) ) {
      file_put_contents( $this->paths['site_config'].$file, $config );
    }
    else {
      file_put_contents( $this->paths['sys_config'].$file, $config );
    }
  }
  
  
  public function save_config( $name, $template, $file, $config, $replace=array()) {
    $config_template= file_get_contents( $template );
    
    // Remove all comments from config_template
    $config_template = preg_replace("~/\*\*.*\/\n~uUs", "", $config_template);
    
    // All data to template and save this table model
    $replace['NAME'] = $name;
    $replace['DATE'] = date('D j F Y, H:i');

    // Remove defaults from config
    foreach ($config as $key => $item) {
      if ($config[$key]==$this->default_data[$key]) {
        unset($config[$key]);
      }
    }
      
    $config = $this->replace_config($config_template,$config);
    $config = $this->parser->parse_string( $config, $replace, true );
    
    file_put_contents( $file, $config );
  }
  
  
  
  /**
   * Vervang $config['...'] door een waarde in een config bestand
   *
   * @param string $template 
   * @param array $config 
   * @return string
   * @author Jan den Besten
   */
  private function replace_config( $template, $config ) {
    $config = array_merge_recursive_distinct($this->default_data,$config);
    foreach ($config as $key => $value) {
      if (is_null($value) or $value=='NULL' or $value==='' or $value==$this->default_data[$key]) {
        $template = preg_replace("/(.config\['".$key."']\s?).*;\n*/um", '', $template);
      }
      else {
        $value = $this->value_to_string($value);
        $template = preg_replace("/(config\['".$key."']\s?).*;/um", "$1= ".$value.";", $template);
      }
    }
    return $template;
  }

  /**
   * Maakt eens string van een waarde
   *
   * @param mixed $value 
   * @param int $tabs 
   * @param int $lev 
   * @return string
   * @author Jan den Besten
   */
  private function value_to_string($value,$tabs=1,$lev=0) {
    if (is_null($value)) {
      $value = 'NULL';
      return $value;
    }
    if (is_numeric($value)) {
      $value = (string)$value;
      return $value;
    }
    if (is_string($value)) {
      $value = "'".$value."'";
      return $value;
    }
    if (is_array($value)) {
      if (empty($value)) {
        $value='array()';
      }
      else {
        if (is_assoc($value) or is_multi($value) ) {
          $items=$value;
          $longest_item=array_keys($items);
          $longest_item=array_sort_length($longest_item);
          $longest_item=current($longest_item);
          $len = strlen($longest_item) + 2;
          $value="array( ";
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
          $value = "array('" . implode("','",$value) . "')";
        }
      }
      return $value;
    }
    if (is_bool($value)) {
      $value = ($value?'true':'false');
      return $value;
    }
    return $value;
  }
  
  public function output() {
    return implode(PHP_EOL,$this->messages).PHP_EOL;
  }
  



}
