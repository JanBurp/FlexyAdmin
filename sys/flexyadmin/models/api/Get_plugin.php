<?php

/**
 * API: Geeft plugin pagina, voor backend van FlexyAdmin
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class get_plugin extends Api_Model {
  
	public function __construct($name='') {
		parent::__construct();
    $this->load->model('plugin_handler');
    $this->load->library('documentation');
    $this->plugin_handler->init_plugins();
    return $this;
	}
  
  public function index() {
    if (!$this->flexy_auth->allowed_to_use_cms()) return $this->_result_status401();
    if (!$this->has_args()) return $this->_result_wrong_args();
    
    if ($this->args['plugin']) {
      $args = explode('/',$this->args['plugin']);
      $plugin  = 'plugin_'.array_shift($args);
      $help    = $this->documentation->get( $this->config->item('SYS').'flexyadmin/libraries/plugins/'.ucfirst($plugin).'.php', '<br>' );
      if (empty($help)) $help = $this->documentation->get( $this->config->item('SITE').'libraries/plugins/'.ucfirst($plugin).'.php', '<br>' );

      $content = $this->plugin_handler->call_plugin_admin_api($plugin,$args,$help);
      if (empty($content)) {
        $content = '<h2>'.$help['short'].'</h2>'.trim(trim($help['long']),'<br>');
      }
      // title
      $ui_name = el(array($plugin,'config','title'),$this->plugin_handler->plugins);
      if (empty($ui_name)) $ui_name = lang($plugin);
      if (substr($ui_name,0,1)==='[') $ui_name = ucfirst(str_replace(array('Plugin_','_'),array('',' '),$help['name']));
      if (empty($ui_name)) $ui_name = ucfirst(str_replace(array('plugin_','_'),array('',' '),$plugin));

      // RESULT
      $this->result['data']=array(
        'plugin'  => $plugin,
        'title'   => $ui_name,
        'html'    => str_replace(array("\r","\n","\t","'"),array('',"'"),$content),
      );
      return $this->_result_ok();
    }

    // Lijst van alle plugins
    if ( $this->flexy_auth->is_super_admin() ) {
      $plugins = $this->plugin_handler->get_plugins();
      ksort($plugins);
      foreach ($plugins as $name => $plugin) {
        $help='';
        if ($name!=='plugin_template' and $name!=='plugin' and isset($plugin['config']['admin_api_method'])) {
          $help = $this->documentation->get( $this->config->item('SYS').'flexyadmin/libraries/plugins/'.ucfirst($name).'.php', '<br>' );
          if (empty($help)) $help = $this->documentation->get( $this->config->item('SITE').'libraries/plugins/'.ucfirst($name).'.php', '<br>' );
          $plugins[$name] = array(
            'name'   => str_replace('plugin_','',$name),
            'uri'    => '/plugin/'.str_replace('plugin_','',$name),
            'doc'    => $help,
          );
        }
        else {
          unset($plugins[$name]);
        }
      }

      // RESULT
      $this->result['data']=array(
        'title'   => 'Plugins',
        'plugins' => $plugins,
      );
      return $this->_result_ok();
    }

    return $this->_result_ok();
  }

}


?>
