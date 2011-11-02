<?

/**
 * BasicController Class extends MY_Controller
 *
 * Same as MY_Controller
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class BasicController extends MY_Controller {

	var $user_name;
	var $user_id;
	var $language;
	var $plugins;

	function __construct($isAdmin=false) {
		parent::__construct($isAdmin);
		$this->load->library('session');
		$this->load->library('user');
		$this->load->helper("language");
		
		if ( ! $this->_user_logged_in()) {
			redirect($this->config->item('API_login'));
		}

		$lang=$this->language."_".strtoupper($this->language);
		setlocale(LC_ALL, $lang);
		
		// load plugins
		$this->_load_plugins();
	}

	function _user_logged_in() {
		$logged_in = $this->user->logged_in();
		if ($logged_in) {
			$this->user_id=$this->session->userdata("user_id");
			$this->user_name=$this->session->userdata("str_username");
			$this->language=$this->session->userdata("language");
			$this->user_group_id=$this->session->userdata("id_user_group");
		}
		return $logged_in;
	}

	function _update_links_in_txt($oldUrl,$newUrl="") {
		// loop through all txt fields..
		$tables=$this->db->list_tables();
		foreach($tables as $table) {
			if (get_prefix($table)==$this->config->item('TABLE_prefix')) {
				$fields=$this->db->list_fields($table);
				foreach ($fields as $field) {
					if (get_prefix($field)=="txt") {
						$this->db->select("id,$field");
						$this->db->where("$field !=","");
						$query=$this->db->get($table);
						foreach($query->result_array() as $row) {
							$thisId=$row["id"];
							$txt=$row[$field];
							if (empty($newUrl)) {
								// remove
								$pattern='/<a(.*?)href="'.str_replace("/","\/",$oldUrl).'"(.*?)>(.*?)<\/a>/';
								$txt=preg_replace($pattern,'\\3',$txt);
							}
							else {
								$txt=str_replace("href=\"$oldUrl","href=\"$newUrl",$txt);
							}
							$res=$this->db->update($table,array($field=>$txt),"id = $thisId");
						}
						$query->free_result();
					}
				}
			}
		}
	}

	/**
	 * Here are functions that hook into the grid/form/update proces.
	 * They check if a standard hook method for the current table/field/id, if so call it
	 */
	
	function _load_plugins() {
		// needed libraries for plugins
		$this->load->library("editor_lists"); // (kan de plugin zelf laden!!!)
		
		// load plugins
		if (empty($this->plugins)) {
			// sys plugins
			$files=read_map(APPPATH.'plugins');

			// $plugin_config_files=read_map(APPPATH.'config','php',FALSE,FALSE,FALSE);
			// $plugin_config_files=filter_by($plugin_config_files,'plugin');
			// trace_($plugin_config_files);


			// site plugins
			$siteMap=$this->config->item('PLUGINS');
			if (file_exists($siteMap)) {
				$siteFiles=read_map($siteMap);
				if (!empty($siteFiles)) {
					foreach ($siteFiles as $file => $value) {
						$siteFiles[$file]['site']=$siteMap;
					}
					$files=array_merge($files,$siteFiles);
				}
			}
			
			// check first order
			$pluginFiles=array();
			$pluginOrder=$this->config->item('PLUGIN_ORDER');
			foreach ($pluginOrder['first'] as $plugin) {
				$file='plugin_'.$plugin.'.php';
				if (isset($files[$file])) {
					$pluginFiles[$file]=$files[$file];
					unset($files[$file]);
				}
			}
			
			// trace_($pluginFiles);
			
			// add other plugins
			$pluginFiles=array_merge($pluginFiles,$files);
			
			// check last order
			foreach ($pluginOrder['last'] as $plugin) {
				$file='plugin_'.$plugin.'.php';
				if (isset($pluginFiles[$file])) {
					$swap=$pluginFiles[$file];
					unset($pluginFiles[$file]);
					$pluginFiles[$file]=$files[$file];
				}
			}
			
			// remove templates and parent class
			unset($pluginFiles['plugin_template.php']);
			unset($pluginFiles['plugin_.php']);

			// trace_($pluginFiles);

			// set plugin cfg
			$cfg=$this->cfg->get('cfg_plugins');
			$pluginCfg=array();
			foreach ($cfg	as $c) {
				$p=$c['plugin'];
				$pluginCfg[$p][$c['str_set']]=$c['str_value'];
			}
			// ok load them
			$this->load->plugin('plugin_');
			foreach ($pluginFiles as $file => $plugin) {
				$Name=get_file_without_extension($file);
				if (substr($Name,0,6)=='plugin') {
					$this->load->plugin($plugin['alt']);
					$pluginName=str_replace('_pi','',$Name);
					$shortName=str_replace('plugin_','',$pluginName);
					$this->$pluginName = new $pluginName($pluginName);
					$this->plugins[]=$pluginName;
					// set config in plugin
					if (isset($pluginCfg[$shortName])) $this->$pluginName->_cfg=$pluginCfg[$shortName];
					// add api call to config if it exist
					if (method_exists($this->$pluginName,'_admin_api')) {
						if (method_exists($this->$pluginName,'_admin_api_calls'))
							$apiCalls=$this->$pluginName->_admin_api_calls();
						else
							$apiCalls=array('');
						foreach ($apiCalls as $call) {
							if (empty($call))
								$this->config->set_item('API_'.$pluginName, 'admin/plugin/'.$shortName);
							else
								$this->config->set_item('API_'.$pluginName.'__'.$call, 'admin/plugin/'.$shortName.'/'.$call);
						}
					}
				}
			}
		}
		// trace_($this->plugins);
		return $this->plugins;
	}

	function _get_parent_uri($table,$uri,$parent) {
		if ($parent!=0) {
			$this->db->select('id,uri,self_parent');
			$this->db->where(PRIMARY_KEY,$parent);
			$parentRow=$this->db->get_row($table);
			$uri=$parentRow['uri']."/".$uri;
			if ($parentRow['self_parent']!=0) $uri=$this->_get_parent_uri($table,$uri,$parentRow['self_parent']);
		}
		return $uri;
	}

	function _clean_plugin_data($data) {
		// clean up many and foreign fields in data
		$cleanUp=array('rel','tbl','cfg');
		if ($data) {
			foreach ($data as $field => $value) {
				$pre=get_prefix($field);
				if (in_array($pre,$cleanUp)) unset($data[$field]);
			}
		}
		return $data;
	}


	function _after_delete($table,$oldData=NULL) {
		// clean up many and foreign fields in data
		$oldData=$this->_clean_plugin_data($oldData);
		// Call all plugins
		foreach ($this->plugins as $plugin) {
			if (method_exists($this->$plugin,'_after_delete')) {
				$this->$plugin->after_delete(array('table'=>$table,'oldData'=>$oldData));
			}
		}
	}
	
	function _after_update($table,$id='',$oldData=NULL,$newData=NULL) {
		// clean up many and foreign fields in data
		if (isset($oldData)) $oldData=$this->_clean_plugin_data($oldData);
		if (isset($newData)) $newData=$this->_clean_plugin_data($newData);
		// Call all plugins
		foreach ($this->plugins as $plugin) {
			if (method_exists($this->$plugin,'_after_update')) {
				$newData=$this->$plugin->after_update(array('table'=>$table,'id'=>$id,'oldData'=>$oldData,'newData'=>$newData));
			}
		}
	}

}

?>