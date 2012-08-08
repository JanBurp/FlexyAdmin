<?
/**
 * Handles all backed plugin loading, calling etc.
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 * @internal
 */
class Plugin_handler extends CI_Model {

	var $plugins=array();
	var $trigger_methods;
	var $data;

	public function __construct() {
		parent::__construct();
		$this->load_plugin('plugin');
	}

	
	public function init_plugins() {
		$plugin_config_files=read_map(APPPATH.'config','php',FALSE,FALSE,FALSE);
		$plugin_config_files_site=read_map(SITEPATH.'config','php',FALSE,FALSE,FALSE);
		$plugin_config_files=array_merge($plugin_config_files,$plugin_config_files_site);
		$plugin_config_files=filter_by($plugin_config_files,'plugin');
		unset($plugin_config_files['plugin_template.php']);

		// set first order
		$plugins_ordered=array();
		$pluginOrder=$this->config->item('PLUGIN_ORDER');
		foreach ($pluginOrder['first'] as $plugin) {
			$file='plugin_'.$plugin.'.php';
			if (isset($plugin_config_files[$file])) {
				$plugins_ordered[$file]=$plugin_config_files[$file];
				unset($plugin_config_files[$file]);
			}
		}
		// add other plugins
		$plugin_config_files=array_merge($plugins_ordered,$plugin_config_files);
		// check last order
		foreach ($pluginOrder['last'] as $plugin) {
			$file='plugin_'.$plugin.'.php';
			if (isset($plugin_config_files[$file])) {
				$swap=$plugin_config_files[$file];
				unset($plugin_config_files[$file]);
				$plugin_config_files[$file]=$swap;
			}
		}
		
		// loop through all plugins and set config and triggers
		foreach ($plugin_config_files as $filename => $value) {
			$pluginName=get_file_without_extension($filename);
			$this->plugins[$pluginName]['name']=$pluginName;

			// load plugin config file
			$this->config->load($pluginName,true);
			$this->plugins[$pluginName]['config']=$this->config->item($pluginName);

			// set triggers
			if (isset($this->plugins[$pluginName]['config']['trigger'])) {
				$this->_set_triggers($pluginName,$this->plugins[$pluginName]['config']['trigger']);
				unset($this->plugins[$pluginName]['config']['trigger']);
			}
			if (isset($this->plugins[$pluginName]['config']['trigger_method'])) {
				$this->_set_triggers($pluginName, $this->get_dynamic_triggers($pluginName, $this->plugins[$pluginName]['config']['trigger_method']) );
				unset($this->plugins[$pluginName]['config']['trigger_method']);
			}
			
			// add to trigger methods
			$methods=array('admin_api_method','ajax_api_method','logout_method','before_grid_method','after_update_method','after_delete_method');
			foreach ($methods as $method) {
				if (isset($this->plugins[$pluginName]['config'][$method])) {
					$this->trigger_methods[$method][$pluginName] = $this->plugins[$pluginName]['config'][$method];
					if ($method=='admin_api_method') {
						$this->config->set_item('API_'.$pluginName, '/admin/plugin/'.str_replace('plugin_','',$pluginName));
					}
				}
			}
			
			// set standard info
			$this->plugins[$pluginName]['is_loaded']=false;
		}

    // trace_($this->plugins);
    // trace_($this->trigger_methods);
	}

	private function _set_triggers($plugin,$add_triggers=array()) {
		if (!empty($add_triggers)) {
			$trigger=array();
			if (isset($this->plugins[$plugin]['trigger'])) $trigger=$this->plugins[$plugin]['trigger'];
			$this->plugins[$plugin]['trigger']=array_merge($trigger,$add_triggers);
		}
		if (!isset($this->plugins[$plugin]['trigger'])) $this->plugins[$plugin]['trigger']=array();
		return $this->plugins[$plugin]['trigger'];
	}

	private function get_dynamic_triggers($plugin,$dynamic_trigger_method) {
		$triggers=array();
		// load plugin
		$this->load_plugin($plugin);
		// call trigger method
		if (method_exists($plugin,$dynamic_trigger_method)) {
			$triggers=$this->$plugin->$dynamic_trigger_method();
		}
		return $triggers;
	}


	public function load_plugin($plugin) {
		$this->load->library($plugin);
		if (is_object($this->$plugin)) {
			$this->plugins[$plugin]['is_loaded']=true;
		}
    // Not needed anymore, config will be loaded by the plugin itself, but need to load trigger
    // $this->$plugin->set_config( $this->plugins[$plugin] ); 
    if (isset($this->plugins[$plugin]['trigger'])) {
      $config['trigger']=$this->plugins[$plugin]['trigger'];
      $config['config']=array();
      $this->$plugin->set_config( $config ); 
    }
		return $this->plugins[$plugin]['is_loaded'];
	}



	public function call_plugin($plugin,$method,$args=NULL) {
		$return = FALSE;
		// load if needed
		if (!$this->plugins[$plugin]['is_loaded']) {
			$this->load_plugin($plugin);
		}
		// call
		if (method_exists($plugin,$method)) {
			// strace_("Call Plugin: $plugin->$method");
			$return = $this->$plugin->$method($args);
		}
		return $return;
	}

	public function call_plugin_admin_api($plugin,$args) {
		if (isset($this->plugins[$plugin]['config']['admin_api_method'])) {
			return $this->call_plugin($plugin,$this->plugins[$plugin]['config']['admin_api_method'],$args);
		}
		return false;
	}

	public function call_plugin_ajax_api($plugin,$args) {
		if (isset($this->plugins[$plugin]['config']['ajax_api_method'])) {
			return $this->call_plugin($plugin,$this->plugins[$plugin]['config']['ajax_api_method'],$args);
		}
		return false;
	}

	public function call_plugin_logout($plugin,$args) {
		if (isset($this->plugins[$plugin]['config']['logout_method'])) {
			return $this->call_plugin($plugin,$this->plugins[$plugin]['config']['logout_method'],$args);
		}
		return false;
	}



	public function get_plugin_showtype($plugin) {
		return $this->call_plugin($plugin,'get_show_type');
	}


	public function get_plugin_content($plugin) {
		return $this->call_plugin($plugin,'get_content');
	}


	public function set_data($type,$data) {
		$this->data[$type]=$data;
	}
	
	private function _set_additional_data() {
		$fields=array();
		$types=array();
		if (isset($this->data['old']) and is_array($this->data['old'])) $fields=array_keys($this->data['old']);
		foreach ($fields as $field) {
			$type=get_prefix($field);
			if ($type) $types[$type]=$type;
		}
		$this->set_data('fields',$fields);
		$this->set_data('types',$types);
	}

	private function _give_data_to_plugin($plugin) {
		$this->call_plugin($plugin,'set_data',$this->data);
	}

  public function call_plugins_before_grid_trigger() {
    $this->_set_additional_data();
    // strace_($this->data);
    if (isset($this->trigger_methods['before_grid_method'])) {
      foreach ($this->trigger_methods['before_grid_method'] as $plugin => $method) {
        if ($this->is_triggered($plugin)) {
          $this->_give_data_to_plugin($plugin);
          $this->data['new']=$this->call_plugin($plugin,$method);
        }
      }
    }
    if (!isset($this->data['new'])) return NULL;
    // strace_($this->data);
    return $this->data['new'];
  }


	public function call_plugins_after_update_trigger() {
		$this->_set_additional_data();
		// strace_($this->data);
		if (isset($this->trigger_methods['after_update_method'])) {
			foreach ($this->trigger_methods['after_update_method'] as $plugin => $method) {
				if ($this->is_triggered($plugin)) {
					$this->_give_data_to_plugin($plugin);
					$this->data['new']=$this->call_plugin($plugin,$method);
				}
			}
		}
		if (!isset($this->data['new'])) return NULL;
		// strace_($this->data);
		return $this->data['new'];
	}

	public function call_plugins_after_delete_trigger() {
		$delete=TRUE;
		$this->_set_additional_data();
		if (isset($this->trigger_methods['after_delete_method'])) {
			foreach ($this->trigger_methods['after_delete_method'] as $plugin => $method) {
				if ($this->is_triggered($plugin)) {
					$this->_give_data_to_plugin($plugin);
					$return = $this->call_plugin($plugin,$method);
					$delete = $delete && $return;
				}
			}
		}
		return $delete;
	}

	public function call_plugins_logout() {
		$logout=TRUE;
		$this->_set_additional_data();
		if (isset($this->trigger_methods['logout_method'])) {
			foreach ($this->trigger_methods['logout_method'] as $plugin => $method) {
				$this->_give_data_to_plugin($plugin);
				$return = $this->call_plugin($plugin,$method);
				$logout = $logout && $return;
			}
		}
		return $logout;
	}



	private function is_triggered($plugin) {
		$is_triggered=false;
		$triggers=$this->plugins[$plugin]['trigger'];
		
		// existing tables
		if (isset($triggers['existing_tables'])) {
			foreach ($triggers['existing_tables'] as $table) {
				if (!$is_triggered) $is_triggered = $this->db->table_exists($table);
			}
		}
		// tables
		if (!$is_triggered) $is_triggered = (isset($triggers['tables']) and in_array($this->data['table'],$triggers['tables']));
		// fields
		if (!$is_triggered) $is_triggered = (isset($triggers['fields']) and one_of_array_in_array($triggers['fields'],$this->data['fields']));
		// field_types
		if (!$is_triggered) $is_triggered = (isset($triggers['field_types']) and one_of_array_in_array($triggers['field_types'],$this->data['types']));

		return $is_triggered;
	}


}
	
?>