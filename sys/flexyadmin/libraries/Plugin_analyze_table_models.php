<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Analyseert de database en kijkt of die overeenkomt met de settings van de table models
 * 
 * @author: Jan den Besten
 */
 class Plugin_analyze_table_models extends Plugin {
   
   public function __construct() {
     parent::__construct();
   }


	public function _admin_api($args=NULL) {
    if ($this->CI->user->is_super_admin()) {
      if (isset($args[0]))
        $tables=array($args[0]);
      else
        $tables = $this->CI->db->list_tables();
      
      $this->CI->load->model('tables/table_model');
      $this->CI->load->model('tables/table_model_create');
      
      $config = array();
      foreach ($tables as $table) {
        $this->CI->config->load( 'tables/'.$table, true);
        $config[$table] = array(
          'config'  => $this->CI->config->item('tables/'.$table),
          'autoset' => $this->CI->table_model->_config( $table, false ),
        );
        if ($config[$table]['config']) {
          $config[$table]['diff'] = array_diff_multi( $config[$table]['config'], $config[$table]['autoset'] );
        }
        else {
          $config[$table]['diff'] = $config[$table]['autoset'];
        }
        
        if ($config[$table]) {
          $this->add_message('<h2>'.$table.'</h2>');
          $this->add_message( '<pre>'.trace_($config[$table], false).'</pre>' );
        }
        
        
      }
      
      
      

    }
    return $this->view();
	}
  
  

}

?>