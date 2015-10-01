<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Analyseert de database en kijkt of die overeenkomt met de settings van de table models
 * 
 * @author: Jan den Besten
 */
 class Plugin_analyze_table_models extends Plugin {
   
   public function __construct() {
     parent::__construct();
     $this->CI->load->library('table');
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
      $keys = array();
      foreach ($tables as $table) {
        
        $this->CI->config->load( 'tables/'.$table, true);
        $settings = $this->CI->config->item('tables/'.$table);
        $autoset  = $this->CI->table_model->_config( $table, false );
        if ($settings) {
          $this_keys=array_keys($settings);
          $keys = array_merge($keys,$this_keys);
        }
        if ($autoset) {
          $this_keys=array_keys($autoset);
          $keys = array_merge($keys,$this_keys);
        }
        $keys = array_unique($keys);
        $config[$table] = array(
          'settings'  => $settings,
          'autoset'   => $autoset
        );

        $this->CI->table->set_heading( $table, 'config', '', 'autoset' );
        foreach ($keys as $key) {
          $diff = '';
          // config
          if ( !isset($autoset[$key]) or is_null($autoset[$key]) ) {
            $diff='left';
          }
          // autoset
          elseif ( !isset($settings[$key]) or is_null($settings[$key])) {
            $diff='right';
          }
          // niet gelijk => autoset
          elseif ( $settings[$key]!=$autoset[$key] ) {
            $diff='right';
          }
          // gelijk
          else {
            $diff='=';
          }

          if ($diff!=='') {
            $this->CI->table->add_row(
              $key,
              div( 'diffleft '.($diff==='left'?'selected':'')).trace_($settings[$key],false,1,0,'')._div(),
              ($diff=='left'?'<span class="glyphicon glyphicon-chevron-left"></span>':($diff=='right'?'<span class="glyphicon glyphicon-chevron-right"></span>':'')),
              div( 'diffright '.($diff!=='left'?'selected':'')).trace_(el($key,$autoset),false,1,0,'')._div()
            );
          }
        }
        $this->add_message( div('diff').$this->CI->table->generate()._div() );
        
        
        
      }

    }
    return $this->view();
	}
  
  

}

?>