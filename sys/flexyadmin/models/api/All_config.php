<?php


/**
 * interne API: Geeft alle config van alle tabellen en mappen die er bestaan.
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

class All_config extends Api_Model {
  
	public function __construct() {
		parent::__construct();
    $this->load->model('ui');
	}

  /**
   * Gets the data and information and returns it
   *
   * @return void
   * @author Jan den Besten
   */
  public function index() {
    if (!$this->_is_super_admin()) return $this->_result_status401();
    
    // DEFAULTS
    $fields=FALSE;
    
    // CFG
    $this->_get_config(array('table_info','field_info'));

    // GET
    $this->result['data']=$this->_get_all_config();
    return $this->_result_ok();
  }
  
  /**
   * Gets all the config there is
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_all_config() {
    $config=array();
    // table_info & field_info
    $tables=$this->db->list_tables();
    foreach ($tables as $table) {
      $config[$table] = array(
        'table_info' => $this->_get_table_info($table),
        'field_info' => $this->_get_field_info($table)
      );
    }
    // media_info & img_info
    $paths=$this->cfg->get('cfg_media_info');
    $paths=array_keys($paths);
    foreach ($paths as $path) {
      $config['media_'.$path] = array(
        'media_info' => $this->_get_media_info($path),
        'img_info'   => $this->_get_img_info($path)
      );
    }
    return $config;
  }


}


?>
