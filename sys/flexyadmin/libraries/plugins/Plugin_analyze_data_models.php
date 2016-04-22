<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Analyseert de database en kijkt of die overeenkomt met de settings van de data models
 * 
 * @author: Jan den Besten
 */
 class Plugin_analyze_data_models extends Plugin {
   
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
      
      $this->CI->load->model('data/data');
      $this->CI->load->model('data/data_model_create');
      
      $config = array();
      $keys = array();
      foreach ($tables as $table) {
        $this->add_message( div('diff'). '<h1>'.$table.'</h1>');
        
        $this->CI->config->load( 'tables/'.$table, true);
        $settings = $this->CI->config->item('tables/'.$table);
        
        // Is er al een DIFF gekozen voor deze tabel, maak dan een nieuwe config
        $diff = $this->CI->input->get();
        if ($diff) {
          $this->add_message( p().$table.' config merged and saved'._p() );
          $this->add_message( p().'<a class="button" href="admin/plugin/analyze_data_models">RETURN</a>'._p() );
          
          $config = array();
          $autoset  = $this->CI->data->_config( $table, false );
          foreach ($diff as $key => $which ) {
            switch ($which) {
              case 'left':
                $config[$key] = $settings[$key];
                break;
              case 'right':
                $config[$key] = $autoset[$key];
                break;
            }
          }
          $this->CI->data_model_create->save_config_for( $table, $config );
        }
        
        // Als er nog geen settings voor deze tabel zijn, maak het model aan
        elseif (is_null($settings)) {
          $this->add_message( p().$table.' => model &amp; config created'._p() );
          $this->CI->data_model_create->create( $table );
        }

        // Als wel settings bestaat, bepaal het verschil
        elseif ($settings) {
          $autoset  = $this->CI->data->_config( $table, false );
          if ($settings) {
            $this_keys=array_keys($settings);
            $keys = array_merge($keys,$this_keys);
          }
          if ($autoset) {
            $this_keys=array_keys($autoset);
            $keys = array_merge($keys,$this_keys);
          }
          $keys = array_unique($keys);
        
          // Als er geen verschil is, geen probleem, even melden en thats'it
          if ($settings==$autoset) {
            $this->add_message( p().$table.' => config &amp; model exists and not changed'._p() );
          }
        
          // DIFF laten zien als ze niet hetzelfde zijn
          else {
            $this->add_message( p('error').$table.' => config changed! Pick the right settings and: <span class="button diffmerge" data-table="'.$table.'">MERGE '.$table.'</span>'._p() );
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
              // zelfde
              else {
                $diff='=';
              }

              if ($diff!=='') {
                $this->CI->table->add_row(
                  $key,
                  div( 'diffleft '.($diff==='left'?'selected':'')).trace_($settings[$key],false,1,0,'')._div(),
                  ($diff=='left'?'<span class="glyphicon glyphicon-chevron-left"></span>':($diff=='right'?'<span class="glyphicon glyphicon-chevron-right"></span>':'')),
                  div( 'diffright '.($diff!=='left'?'selected':'')).trace_($autoset[$key],false,1,0,'')._div()
                );
              }
            }
            $this->add_message( $this->CI->table->generate() );
          }
        }
      }
      
      $this->add_message( _div() );

    }
    return $this->view();
	}
  
  

}

?>