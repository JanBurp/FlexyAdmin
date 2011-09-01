<?

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2008, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MY_Controller Class
 *
 * This Controller Class handles authentication, loading basic data class
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class MY_Controller extends CI_Controller {


	function __construct($isAdmin=false) {
		parent::__construct();
		
		if ($this->_check_if_flexy_database_exists())
			$this->_init_flexy_admin($isAdmin);
		else {
			// database login correct, but no database found, try to load the demodatabase
			$succes=false;
			// try to load latest demodatabase
			if (file_exists('db')) {
				$demoDB=read_map('db','sql');
				$demoDB=filter_by($demoDB,'flexyadmin_demo_');
				if ($demoDB) {
					$demoDB=current($demoDB);
					$demoDB=$demoDB['path'];
					// trace_($demoDB);
					$SQL=read_file($demoDB);
					if ($SQL) {
						$lines=explode("\n",$SQL);
						$comments="";
						foreach ($lines as $k=>$l) {
							if (substr($l,0,1)=="#")	{
								if (strlen($l)>2)	$comments.=$l.br();
								unset($lines[$k]);
							}
						}
						$sql=implode("\n",$lines);
						$lines=preg_split('/;\n+/',$sql); // split at ; with EOL

						foreach ($lines as $key => $line) {
							$line=trim($line);
							if (!empty($line)) {
								$query=$this->db->query($line);
							}
						}
						$succes=TRUE;
						redirect('admin');
					}
				}
			}

			if (!$succes) {
				show_error('Database login: correct.<br/>No tables (for flexyadmin) found.<br/>Tried to load demodatabase, no succes.');
			}
		}
	}

	function _check_if_flexy_database_exists() {
		return $this->db->table_exists('cfg_configurations');
	}

	function _init_flexy_admin($isAdmin=false) {
		// $this->output->enable_profiler(TRUE);
		$this->load->model('cfg');
		$this->cfg->set_if_admin($isAdmin);
	}
	
	
	
	

	/**
	 * Here are some own form validation callback functions
	 * Routings are set so that admin/show/valid_* is routed to admin/show, so these callbacks are not reached by url
	 */

		function valid_rgb($rgb) {
			$rgb=trim($rgb);
			if (empty($rgb)) {
				return TRUE;
			}
			$rgb=str_replace("#","",$rgb);
			$len=strlen($rgb);
			if ($len!=3 and $len!=6) {
				$this->lang->load("form_validation");
				$this->form_validation->set_message('valid_rgb', lang('valid_rgb'));
				return FALSE;
			}
			$rgb=strtoupper($rgb);
			if (ctype_xdigit($rgb))
				return "#$rgb";
			else {
				$this->lang->load("form_validation");
				$this->form_validation->set_message('valid_rgb', lang('valid_rgb'));
				return FALSE;
			}
		}
	
	

}


/**
 * FrontEndController Class extends MY_Controller
 *
 * Same as MY_Controller
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class FrontEndController extends MY_Controller {

	var $site;

	function __construct() {
		/**
		 * Init controller, and load all libraries
		 */
		parent::__construct();
		$this->load->library('user_agent');
		$this->load->helper('date');
		$this->load->helper("html_helper");
		$this->load->helper("language");
		$this->load->library("menu");
		$this->load->library("content");
		$this->load->library('form_validation');
				

		/**
			*	Set $_GET if asked for
			* See http://www.askaboutphp.com/tutorials/58/codeigniter-mixing-segment-based-url-with-querystrings.html
			* For this to work, config.php: $config['uri_protocol']	= "PATH_INFO";
			*/
		if ($this->cfg->get('CFG_configurations','b_query_urls'))	parse_str($_SERVER['QUERY_STRING'],$_GET);

		/**
		 * Init global site data
		 */
		$this->_init_globals();

		/**
		 * Add this page to statistics, if statistisc table exists
		 */
		if ($this->db->table_exists($this->config->item('LOG_table_prefix')."_".$this->config->item('LOG_stats'))) {
			$this->load->library("stats");
			$this->stats->add_uri($this->uri->get());
		}

		/**
		 * Load standard Module Class
		 */
		$this->load->library('module');
		
	}
	
	// For compatibility with older sites
	function FrontEndController() {
		$this->__construct();
	}
	

	/**
	 * _init_globals()
	 *
	 * Here are all global site parameters set.
	 * - asset folder
	 * - all fields from tbl_site (the not standard with prefix)
	 */
	function _init_globals() {
		$this->site=array();
		/**
		 * Set Asset folders
		 */
		$this->site["assets"]=assets();
		$this->site["rel_assets"]=$this->config->item("ASSETS");
		$this->site["admin_assets"]=admin_assets();

		/**
		 * Set Some Config
		 */
		$this->site['languages']=$this->config->item('languages');


		/**
		 * Set global site info from tbl_site (if it doesn't exist, put some standard info)
		 */
		if ($this->db->table_exists("tbl_site")) {
			$fields = $this->db->list_fields("tbl_site");
			$stdFields=array("str_title","str_author","url_url","email_email","stx_description","stx_keywords");
			$query=$this->db->get("tbl_site");
			$row=$query->row_array();
			// first standard fields
			foreach ($stdFields as $f) {
				if (isset($row[$f])) {
					$this->site[remove_prefix($f)]=$row[$f];
					unset($fields[$f]);
				}
			}
			// remaining fields, if any
			foreach ($fields as $f) {
					$this->site[$f]=$row[$f];
			}
			// remove the unneeded
			unset($this->site['id']);
		}
		else {
			$this->site["title"]="title";
			$this->site["author"]="author of site";
			$this->site["url"]="http://www.delaatstepagina.nl/";
			$this->site["email"]="email of site administrator";
			$this->site["description"]="Put some site description here,";
			$this->site["keywords"]="site, keywords";
		}
		/**
		 * Set home uri (top from tbl_menu)
		 */
		$menuTable=get_menu_table();
		if (!empty($menuTable)) {
			if ($this->db->has_field($menuTable,"self_parent")) $this->db->order_as_tree();
			if ($this->db->has_field($menuTable,"uri")) {
				$this->db->select("uri");
				$top=$this->db->get_row($menuTable);
				$this->uri->set_home($top["uri"]);
			}
			else {
				$this->uri->set_home('');
			}
		}
		/**
		 * Set empty content and class
		 */
		$this->site['content']='';
		$this->site['class']='';
	}

	function add_keywords($words) {
		$this->site["keywords"]=add_string($this->site["keywords"],$words,",");
	}
	
	function add_title($title) {
		$this->site["title"].=" - ".$title;
	}

	function set_uri($uri) {
		$this->site["uri"]=$uri;
	}
	function get_uri($max=0) {
		if ($max==0) return $this->site["uri"];
		$u=explode("/",$this->site["uri"]);
		$u=array_slice($u,0,$max);
		return implode("/",$u);
	}

	function add_content($c) {
		if (!isset($this->site["content"]))
			$this->site["content"]=$c;
		else
			$this->site["content"].=$c;
	}
	
	function has_content() {
		return (isset($this->site["content"]) and !empty($this->site["content"]));
	}
	
	function no_content() {
		return !$this->has_content();
	}
	
	function add_class($class) {
		$this->site['class']=add_string($this->site['class'],$class,' ');
	}
	
	function add($key,$value) {
		$this->site[$key]=$value;
	}

	function get($key) {
		return el($key,$this->site);
	}

	function view($view='',$data='',$return=FALSE) {
		if (empty($view)) {
			$view=$this->config->item('main_view');
			if ( ! $view) $view='home'; // for backwards compatibility
		}
		if (empty($data)) $data=$this->site;
		return $this->load->view($view,$data,$return);
	}
	// this one only for backwards compatibility
	function show($view='home',$data='',$return=FALSE) {
		return $this->view($view,$data,$return);
	}
	

	function module($module) {
		if (!function_exists('_module_'.$module) and file_exists('site/modules/module_'.$module.'.php')) {
			include_once('site/modules/module_'.$module.'.php');
		}
		if (function_exists('_module_'.$module)) {
			$module='_module_'.$module;
			$module();
		}
	}
	
	// These are just here for backward compability... ##DEPRICATED
	function getFormByModule($module) {
		$this->load->module('getform');
		return $this->getform->by_module($module);
	}
	function getFormByTitle($title) {
		$this->load->module('getform');
		return $this->getform->by_title($module);
	}
	function getFormById($id) {
		$this->load->module('getform');
		return $this->getform->by_id($module);
	}
	
}

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
					}
				}
			}
		}
	}

	/**
	 * Here are fuctions that hook into the grid/form/update proces.
	 * They check if a standard hook method for the current table/field/id, if so call it
	 */
	
	function _load_plugins() {
		// needed libraries for plugins
		$this->load->library("editor_lists");
		
		// load plugins
		if (empty($this->plugins)) {
			// sys plugins
			$files=read_map(APPPATH.'plugins');
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



/**
 * AdminController Class extends MY_Controller
 *
 * Adds view methods and loads/views automatic header, menu and message.
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class AdminController extends BasicController {

	var $currentTable;
	var $currentId;
	var $currentUser;
	var $content;
	var $showEditor;
	var $showType;
	var $helpTexts;
	var $js;

	function __construct() {
		parent::__construct(true);
		
		if ( ! $this->_user_logged_in()) {
			redirect($this->config->item('API_login'));
		}
		$this->currentTable="";
		$this->currentId="";
		$this->currentUser="";
		$this->currentMenuItem="";
		$this->content="";
		$this->showEditor=false;
		$this->load->model("ui_names","uiNames");
		$this->load->library("menu");
		$this->load->dbforge();
		$this->helpTexts=array();
	}

	function set_message($message) {
		$this->session->set_userdata("message",$message);
	}

	function use_editor() {
		$this->showEditor=true;
	}

	function _set_current_table($table) {
		$this->currentTable=$table;
	}

	function _get_current_table() {
		return $this->currentTable;
	}

	// show methods

	function _show_header() {
		$site=$this->db->get_row('tbl_site');
		$title=$site['str_title'];
		if ($this->config->item('LOCAL'))	$title=' # '.$title; else $title=' - '.$title;
		$url=str_replace('http://','',$site['url_url']);
		$editor=$this->showEditor and $this->cfg->get('CFG_editor',"b_use_editor");
		$type="";
		if (isset($this->showType)) $type=$this->showType;
		$buttons1=$this->cfg->get('CFG_editor',"str_buttons1");
		$buttons2=$this->cfg->get('CFG_editor',"str_buttons2");
		$buttons3=$this->cfg->get('CFG_editor',"str_buttons3");
		$previewWidth=$this->cfg->get('CFG_editor',"int_preview_width");
		if (!$previewWidth) $previewWidth=450;
		$previewHeight=$this->cfg->get('CFG_editor',"int_preview_height");
		if (!$previewHeight) $previewHeight=500;
		if ($this->user->is_super_admin()) {
			if (strpos($buttons1,"code")===FALSE) $buttons1.=",|,code";
		}
		$formats=$this->cfg->get('CFG_editor',"str_formats");
		$styles=$this->cfg->get('CFG_editor',"str_styles");
		$this->load->view('admin/header', array("title"=>$title,"url"=>$url,"jsVars"=>$this->js,"show_type"=>$type,"show_editor"=>$editor,"buttons1"=>$buttons1,"buttons2"=>$buttons2,"buttons3"=>$buttons3,'preview_width'=>$previewWidth,'preview_height'=>$previewHeight,"formats"=>$formats,"styles"=>$styles,"language"=>$this->language));
	}

	function _show_table_menu($tables,$type) {
		$a=array();
		$tables=filter_by($tables,$type."_");
		// trace_($tables);
		$excluded=$this->config->item('MENU_excluded');
		// trace_($this->cfg);
		$cfgTables=$this->cfg->get("CFG_table");
		// trace_($cfgTables);
		$cfgTables=filter_by($cfgTables,$type);
		$cfgTables=sort_by($cfgTables,"order");
		// trace_($cfgTables);
		$oTables=array();
		foreach ($cfgTables as $row) {
			if (in_array($row["table"],$tables)) {
				$oTables[]=$row["table"];
				unset($tables[array_search($row["table"],$tables)]);
			}
		}
		$oTables=array_merge($oTables,$tables);
		foreach ($oTables as $name) {
			$menuName=$this->uiNames->get($name);
			$uri=api_uri('API_view_grid',$name);
			// if ($type!='tbl') $menuName='_'.$menuName;
			// if ($type=='res') $menuName='_'.$menuName;
			if (!in_array($name,$excluded) and $this->user->has_rights($name)) {
				$subUri=api_uri('API_view_form',$name);
				$sub=array($subUri=>array('uri'=>$subUri,'name'=>$menuName,'unique_uri'=>true));
				$a[$uri]=array("uri"=>$uri,'unique_uri'=>true,'name'=>$menuName,"class"=>$type,'sub'=>$sub);
				$tableHelp=$this->cfg->get("CFG_table",$name,"txt_help");
				if (!empty($tableHelp)) $a[$uri]["help"]=$tableHelp;
			}
		}
		return $a;
	}

	function _show_menu($currentMenuItem="") {
		$this->lang->load('help');
		$menu=array();
		if ($this->db->table_exists('cfg_admin_menu')) {
			$this->db->where('b_visible',1);
			$adminMenu=$this->db->get_result('cfg_admin_menu');
		}
		else {
			// minimal standard menu
			$adminMenu=array(
				"1"=>array("id"=>'1',"order"=>'0',"str_ui_name"=>'Home',"b_visible"=>'1', "str_type"=>'api', "api"=>'API_home', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"2"=>array( "id"=>'2', "order"=>'1', "str_ui_name"=>'Logout', "b_visible"=>'1', "str_type"=>'api', "api"=>'API_logout', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"8"=>array( "id"=>'8', "order"=>'4', "str_ui_name"=>'', "b_visible"=>'1', "str_type"=>'seperator', "api"=>'', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"4"=>array( "id"=>'4', "order"=>'5', "str_ui_name"=>'# all normal tables (if user has rights)', "b_visible"=>'1', "str_type"=>'all_tbl_tables', "api"=>'', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"5"=>array( "id"=>'5', "order"=>'6', "str_ui_name"=>'# all media (if user has rights)', "b_visible"=>'1', "str_type"=>'all_media', "api"=>'', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"9"=>array( "id"=>'9', "order"=>'7', "str_ui_name"=>'', "b_visible"=>'1', "str_type"=>'seperator', "api"=>'', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"11"=>array( "id"=>'11', "order"=>'8', "str_ui_name"=>'_stats_menu', "b_visible"=>'1', "str_type"=>'api', "api"=>'API_plugin_stats', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"12"=>array( "id"=>'12', "order"=>'9', "str_ui_name"=>'', "b_visible"=>'1', "str_type"=>'seperator', "api"=>'', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"6"=>array( "id"=>'6', "order"=>'10', "str_ui_name"=>'# all tools (if user has rights)', "b_visible"=>'1', "str_type"=>'tools', "api"=>'', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"10"=>array( "id"=>'10', "order"=>'11', "str_ui_name"=>'', "b_visible"=>'1', "str_type"=>'seperator', "api"=>'', "path"=>'', "table"=>'', "str_table_where"=>'' ),
				"7"=>array( "id"=>'7', "order"=>'12', "str_ui_name"=>'# all config tables (if user has rights)', "b_visible"=>'1', "str_type"=>'all_cfg_tables', "api"=>'', "path"=>'', "table"=>'', "str_table_where"=>'' ) );
		}
		
		// trace_($adminMenu);

		foreach ($adminMenu as $item) {
			switch($item['str_type']) {
				case 'api' :
					$uiName=$item['str_ui_name'];
					$args=array($item['path'],$item['table'],$item['str_table_where']);
					$args=implode('/',$args);
					if ($args=='/') $args='';
					$args=str_replace('//','/',$args);
					if (substr($uiName,0,1)=="_") $uiName=lang(substr($uiName,1));
					$uri=api_uri($item['api']).$args;
					$menu[$uri]=array('uri'=>$uri,'name'=>$uiName,'class'=>str_replace('/','_',$item['api']) );
					break;
					
				case 'seperator' :
					$menu[]=array();
					break;

				case 'tools':
					// Database import/export tools
					if ($this->user->is_super_admin()) {
						$uri=api_uri('API_db_export');
						$menu[$uri]=array("uri"=>$uri,'name'=>lang('db_export'), "class"=>"db db_backup");
						$uri=api_uri('API_db_import');
						$menu[$uri]=array("uri"=>$uri,'name'=>lang('db_import'),"class"=>"db");
					}
					elseif ($this->user->can_backup()) {
						$uri=api_uri('API_db_backup');
						$menu[$uri]=array("uri"=>$uri,'name'=>lang('db_backup'),"class"=>"db db_backup");
						$uri=api_uri('API_db_restore');
						$menu[$uri]=array("uri"=>api_uri('API_db_restore'),'name'=>lang('db_restore'),"class"=>"db");
					}
					// Search&Replace AND Bulkupload tools
					if ($this->user->can_use_tools()) {
						$uri=api_uri('API_search');
						$menu[$uri]=array("uri"=>$uri,'name'=>lang('sr_search_replace'),"class"=>"sr db_backup");
						$uri=api_uri('API_fill');
						$menu[$uri] =array("uri"=>$uri,'name'=>lang('fill_fill'),"class"=>"db db_fill");
						if (file_exists($this->config->item('BULKUPLOAD'))) {
							$uri=api_uri('API_bulk_upload');
							$menu[$uri]=array("uri"=>$uri,'name'=>'Bulk upload',"class"=>"media");
						}
					}
					break;
				
				case 'table' :
					$uri=api_uri('API_view_grid',$item['table']);
					$uri.='/info/'.$item['id'];
					$menu[$uri]=array("uri"=>$uri,'name'=>$item['str_ui_name'],"class"=>'tbl ');
					break;
					
				case 'all_tbl_tables' :
					$tables=$this->db->list_tables();
					// trace_($tables);
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('TABLE_prefix')));
					break;

				case 'all_cfg_tables' :
					$tables=$this->db->list_tables();
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('CFG_table_prefix')));
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('LOG_table_prefix')));
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('REL_table_prefix')));
					break;

				case 'all_res_tables' :
					$tables=$this->db->list_tables();
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('RES_table_prefix')));
					break;
				
				case 'media' :
					$uri=api_uri('API_filemanager','show',pathencode($item['path']));
					$menu[$uri]=array("uri"=>$uri,'name'=>$item['str_ui_name'],"class"=>'media ');
					break;
					
				case 'all_media':
					$mediaInfoTbl=$this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_media_info');
					if ($this->db->table_exists($mediaInfoTbl)) {
						$this->db->order_by("order");
						$query=$this->db->get($mediaInfoTbl);
						foreach($query->result_array() as $mediaInfo) {
							if (!isset($mediaInfo['path']) and isset($mediaInfo['str_path'])) $mediaInfo['path']=$mediaInfo['str_path'];
							$menuName=$this->uiNames->get($mediaInfo['path']);
							while (isset($a[$menuName])) {$menuName.=" ";}
							$rightsName=el('path',$mediaInfo);
							$uri=api_uri('API_filemanager',"show",pathencode(el('path',$mediaInfo)));
							if (!empty($menuName) and $this->user->has_rights("media_".$rightsName)) {
								$menu[$uri]=array("uri"=>$uri,'name'=>$menuName,"class"=>"media");
							}
							$mediaHelp=$this->cfg->get("CFG_media_info",$mediaInfo["path"],"txt_help");
							if (!empty($mediaHelp)) {
								$menu[$uri]["help"]=$mediaHelp;
							}
						}
					}
					break;
				
			}
		}

		// remove double seperators
		$firstSeperator=false;
		foreach ($menu as $key => $item) {
			$isSeperator = empty($item);
			if ($isSeperator) {
				if ( ! $firstSeperator)
					$firstSeperator=true;
				else
					unset($menu[$key]);
			}
			else
				$firstSeperator=false;
		}
		
		$this->menu->set_menu($menu);
		$uri=$this->uri->get();
		$this->menu->set_current($uri);
		$this->menu->set_current_name($currentMenuItem); // ??
		$menu=$this->menu->render();
		// trace_($this->menu);
		$this->load->view('admin/menu',array("menu"=>$menu));
	}


	function _show_message() {
		$message=$this->session->userdata("message");
		if ($message!="") {
			$message=$this->uiNames->replace_ui_names($message);
			$this->load->view('admin/message', array("message"=>$message));
		}
		$this->session->unset_userdata("message");
	}

	function _show_content() {
		if (empty($this->content))
			show_404();
			//$this->load->view('admin/no_page_'.$this->language);
		else
			$this->load->view('admin/content',array("content"=> $this->content));
	}

	function _show_trace() {
		$trace=$this->session->userdata('trace');
		if (IS_LOCALHOST and !empty($trace)) {
			$this->load->view('admin/trace',array('trace'=>$trace));
		}
		$this->session->unset_userdata('trace');
	}

	function _show_footer($extra_view="",$data=NULL) {
		$this->db->select("url_url");
		$query=$this->db->get("tbl_site");
		$siteInfo=$query->row_array();
		$this->lang->load("dialog");
		$lang=$this->lang->get_all();
		$footer=array(	"view"		=> $extra_view,
										"data"		=> $data,
										"dialog"  => $lang,
										"help"		=> $this->helpTexts,
										"local"		=> $this->config->item('LOCAL'),
										"site"		=> rtrim($siteInfo["url_url"],'/'),
										"user"		=> ucwords($this->user_name),
										"revision"=> $this->get_revision()
									);
		$this->load->view('admin/footer',$footer);
	}

	function get_revision() {
		$rev="";
		$svnfile="sys/.svn/entries";
		$revfile="sys/build.txt";
		if (file_exists($svnfile)) {
			$svn = read_file($svnfile);
			// trace_($svn);
			$svn=explode("\n",$svn);
			$matches=array_keys($svn,"jan");
			$fileKey=$matches[count($matches)-1];
			$fileKey=$matches[0];
			// trace_($matches);
			// trace_($fileKey);
			$revKey=$fileKey-1;
			$rev = $svn[$revKey];
			// trace_($rev);
			if (!empty($rev)) write_file($revfile, $rev);
			// $this->db->set('str_revision',$rev);
			// $this->db->update('cfg_configurations');
		}
		if (empty($rev) and file_exists($revfile)) {
			$rev = read_file($revfile);
		}
		if (empty($rev)) $rev="#";
		return $rev;
	}

	function _show_type($type) {
		$this->showType=add_string($this->showType,$type,' ');
	}

	function _show_all($currentMenuItem="") {
		$this->_show_header();
		$this->_show_message();
		$this->_show_menu($currentMenuItem);
		$this->_show_content();
		$this->_show_trace();
		$this->_show_footer();
	}

	function _show_view($theView,$data=NULL,$bFooter=true) {
		if ($data==NULL) $data=array("content"=>$this->content);
		$this->load->view($theView,$data);
		if ($bFooter) $this->_show_footer();
	}

	function _show_dialog($data,$all=true) {
		$this->_show_view('admin/dialog',$data);
	}

	// content helpers

	function _set_content($content) {
		$this->content=$content;
	}
	function _add_content($add) {
		$this->content.=$add;
	}
	
	function _add_help($help,$name="") {
		static $counter=0;
		$found=array_search($help,$this->helpTexts);
		if (!$found) {
			if (empty($name))
				$name=safe_string($help,20);
			else
				$name=safe_string($name);
			$name='help_'.$counter++.'_'.$name;
			$this->helpTexts[$name]=$help;
			return $name;
		}
		return $found;
	}

	function _add_js_variable($name,$value) {
		$this->js[$name]=$value;
	}
	
	

	function _before_grid($table,&$data) {
		// First check a specific table
		$func="_before_grid_$table";
		if (method_exists($this,$func)) {
			$this->$func($data);
		}
		// common table function
	}

	function _before_grid_tbl_links(&$data) {
		/**
		 * Reset link list
		 */
		$this->load->library("editor_lists");
		$result=$this->editor_lists->create_list("links");
		if (!$result) $this->set_message("Could not update Links List. Check file rights.");
		return $result;
	}

	function _before_filemanager($path,&$files) {
		/**
		 * Reset img/media list
		 */
		$this->load->library("editor_lists");
		$result=$this->editor_lists->create_list("img");
		if ($result) $result=$this->editor_lists->create_list("media");
		if ($result) $result=$this->editor_lists->create_list("downloads");
		if (!$result) $this->set_message("Could not update img/media List. Check file rights.");
		return $result;
	}


}

?>