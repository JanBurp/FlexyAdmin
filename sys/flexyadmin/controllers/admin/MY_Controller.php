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

class MY_Controller extends Controller {


	function MY_Controller($isAdmin=false) {
		parent::Controller();
		if ($this->_check_if_flexy_database_exists())
			$this->_init_flexy_admin($isAdmin);
		else {
			show_error('Database login: correct.<br/>But no tables (for flexyadmin) found.');
		}
	}

	function _check_if_flexy_database_exists() {
		return $this->db->table_exists('cfg_configurations');
	}

	function _init_flexy_admin($isAdmin=false) {
		// $this->output->enable_profiler(TRUE);
		$this->load->model('cfg');
		if ($isAdmin) {
			$this->cfg->load('CFG_configurations');
			$this->cfg->load('CFG_table',$this->config->item('CFG_table_name'));
			$this->cfg->load('CFG_field',$this->config->item('CFG_field_name'));
			$this->cfg->load('CFG_media_info',array("path","fields_media_fields"));
			$this->cfg->load('CFG_img_info','path');
			$this->cfg->load('cfg_admin_menu',array('id'));
		}
		else {
			$this->cfg->load('CFG_configurations','','email_webmaster_email,str_menu_table,b_logout_to_site,b_query_urls');
			$this->cfg->load('CFG_table',$this->config->item('CFG_table_name'),'id,table,str_order_by');
		}
		// trace_($this->cfg);
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

	function FrontEndController() {
		/**
		 * Init controller, and load all libraries
		 */
		parent::MY_Controller();
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
		$menuTable=$this->cfg->get("CFG_configurations","str_menu_table");
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
	
	function add($key,$value) {
		$this->site[$key]=$value;
	}

	function get($key) {
		return el($key,$this->site);
	}


	function show($v="home",$data="",$return=FALSE) {
		if (empty($data)) $data=$this->site;
		return $this->load->site_view($v,$data,$return);
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

	var $user;
	var $user_id;
	var $rights;
	var $language;
	var $plugins;

	function BasicController($isAdmin=false) {
		parent::MY_Controller($isAdmin);
		$this->load->library("session");
		$this->load->helper("language");
		
		$this->user="";
		if (!$this->_user_logged_in()) {
			// redirect($this->config->item('API_login'));
		}
		// trace_($this->rights);
		$lang=$this->language."_".strtoupper($this->language);
		setlocale(LC_ALL, $lang);
		
		// load plugins
		$this->_load_plugins();
	}

	function _user_logged_in() {
		$out=false;
		$this->user=$this->session->userdata("user");
		$this->user_id=$this->session->userdata("user_id");
		$this->rights=$this->session->userdata("rights");
		$this->language=$this->session->userdata("language");
		$out=(!empty($this->user));
		return $out;
	}

	function _is_super_admin() {
		reset($this->rights);
		$rights=current($this->rights);
		return ($rights["rights"]=="*");
	}

	function _can_backup() {
		reset($this->rights);
		$rights=current($this->rights);
		if ($rights['b_backup']) return TRUE;
		return FALSE;
	}

	function _can_use_tools() {
		reset($this->rights);
		$rights=current($this->rights);
		if ($rights['b_tools']) return TRUE;
		return FALSE;
	}


	/**
		* Returns rights:
		*		RIGHTS_ALL		= 15 (all added)
		*		RIGHTS_DELETE	= 8
		*		RIGHTS_ADD		= 4
		*		RIGHTS_EDIT		= 2
		*		RIGHTS_SHOW		= 1
		*		RIGHTS_NO			= 0 
		* Or FALSE/TRUE if it has minimal these rights
		*/
	function _change_rights(&$found,$rights) {
		foreach ($found as $key => $value) {
			if ($rights[$key]) $found[$key]=TRUE;
		}
	}
	function has_rights($item,$id="",$whatRight=0) {
		// No rights if cfg_users and id is smaller (higher rights)
		if ($item=="cfg_users" and !empty($id) and ($id!=-1) and ($id<$this->user_id)) return false;
		
		$found=array('b_delete'=>FALSE,'b_add'=>FALSE,'b_edit'=>FALSE,'b_show'=>FALSE);
		$pre=get_prefix($item);
		$preAll=$pre."_*";

		$foundRights=RIGHTS_NO;
		
		// $condition=($item=='media_knipsels');
		// trace_if($condition,$item);
		// trace_if($condition,$this->rights);
		// trace_if($condition,array('item'=>$item,'pre'=>$pre,'preAll'=>$preAll));

		if (is_array($this->rights)) {
			foreach ($this->rights as $key => $rights) {
				if ($rights['rights']=="*" or (strpos($rights['rights'],$preAll)!==FALSE) or (strpos($rights['rights'],$item)!==FALSE) ) {
					$this->_change_rights($found,$rights);
					// trace_if($condition,array('item'=>$item,'found'=>$found,'key'=>$key,'rights'=>$rights));
				}
			}
			// trace_if($condition,$found);
			if (!empty($found['b_delete'])	and $found['b_delete'])	$foundRights+=RIGHTS_DELETE;
			if (!empty($found['b_add']) 		and $found['b_add'])		$foundRights+=RIGHTS_ADD;
			if (!empty($found['b_edit'])		and $found['b_edit'])		$foundRights+=RIGHTS_EDIT;
			if (!empty($found['b_show'])		and $found['b_show'])		$foundRights+=RIGHTS_SHOW;
		}
		// trace_if($condition,$foundRights);
		// trace_if($condition,$whatRight);

		if ($whatRight==0)
			return $foundRights;
		else
			return ($foundRights>=$whatRight);
	}
	
	// returns NULL if no user restrictions, else it gives back the user_id
	function user_restriction_id($table) {
		$restricted=TRUE;
		$pre=get_prefix($table);
		$preAll=$pre."_*";
		foreach ($this->rights as $key => $rights) {
			if ($rights['user_rights']=="")
				$restricted=FALSE;
			if ($rights['user_rights']=="*" or (strpos($rights['user_rights'],$preAll)!==FALSE) or (strpos($rights['user_rights'],$table)!==FALSE) )
				$restricted=$restricted and TRUE;
		}
		if ($restricted) {
			return $this->user_id;
		}
		else
			return FALSE;
	}

	function _get_table_rights($atLeast=RIGHTS_ALL) {
		$tables=$this->db->list_tables();
		$tableRights=array();
		foreach ($tables as $key => $table) {
			$pre=get_prefix($table);
			if ($pre==$this->config->item('REL_table_prefix')) {
				$rTable=table_from_rel_table($table);
				$rights=$this->has_rights($rTable);
			}
			else {
				$rights=$this->has_rights($table);
			}
			if ($rights>=$atLeast) $tableRights[]=$table;
		}
		return $tableRights;
	}

	function _has_key($table="") {
		return TRUE;
	}
	// TODO: Haal Licentie echt weg!
	
	// 	
	// 	if ($table=='cfg_configurations' or IS_LOCALHOST) return true;
	// 	$k=$this->cfg->get('CFG_configurations',$this->_decode('==QOwAjM5V2a'));
	// 	if (empty($k)) return false;
	// 	$h=strtolower($_SERVER[$this->_decode('==QOwAjMUN1TI9FUURFS')]);
	// 	$h=explode('/',str_replace(array($this->_decode('=kDMwIzLvoDc0RHa'),$this->_decode('=kDMwIjL3d3d')),"",$h));
	// 	$h=$h[0];
	// 	if ($k==$this->_encode($h)) return true;
	// 	return false;
	// }

	function _no_key($table="") {
		$out=$this->_decode('5ADMy4TYvwTbvNmLulWbkFWe4VGbm5yd3dnPn02bj5ibp1GZhlHelxmZuc3d39yL6AHd0h2J9YWZyhGIhxDIvRHIvdGIy9GI+E2L8IXZ0NXYtJWZ35zJjMyIjozb0xWah12J9YWZyhGIhxDIyV3b5BCdjFGdu92Q+8icixjLzlGa0BicvZGIlNnblNWasBSYgQWZl5GI19WW');
		$out=str_replace('####',$this->cfg->get('CFG_configurations','email_webmaster_email'),$out);
		return $out;
	}

	function _encode($tekst,$v="2009") {
		$tekst.=$v;
		$base=base64_encode($tekst);
		$code="";
		for ($c=strlen($base)-1;$c>=0;$c--) { $code.=$base[$c]; }
		return $code;
	}

	function _decode($tekst,$v="2009") {
		$out="";
		for ($c=strlen($tekst)-1;$c>=0;$c--) { $out.=$tekst[$c]; }
		$out=base64_decode($out);
		return substr($out,0,strlen($out)-strlen($v));
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
			$files=read_map(APPPATH.'plugins');
			// load plugins from site
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
				$file='plugin_'.$plugin.'_pi.php';
				if (isset($files[$file])) {
					$pluginFiles[$file]=$files[$file];
					unset($files[$file]);
				}
			}
			// add other plugins
			$pluginFiles=array_merge($pluginFiles,$files);
			// check last order
			foreach ($pluginOrder['last'] as $plugin) {
				$file='plugin_'.$plugin.'_pi.php';
				if (isset($pluginFiles[$file])) {
					$swap=$pluginFiles[$file];
					unset($pluginFiles[$file]);
					$pluginFiles[$file]=$files[$file];
				}
			}
			// remove templates and parent class
			unset($pluginFiles['plugin_template_pi.php']);
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
			foreach ($pluginFiles as $file => $plugin) {
				$Name=get_file_without_extension($file);
				if (substr($Name,0,6)=='plugin') {
					if (isset($plugin['site']))
						$this->load->site_plugin($Name,$plugin['site']);
					else
						$this->load->plugin($Name);
					$pluginName=str_replace('_pi','',$Name);
					$shortName=str_replace('plugin_','',$pluginName);
					$this->$pluginName = new $pluginName($pluginName);
					$this->plugins[]=$pluginName;
					// set config in plugin
					if (isset($pluginCfg[$shortName])) $this->$pluginName->cfg=$pluginCfg[$shortName];
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
		// strace_($this->plugins);
		return $this->plugins;
	}

	function _get_parent_uri($table,$uri,$parent) {
		if ($parent!=0) {
			$this->db->select('id,uri,self_parent');
			$this->db->where(pk(),$parent);
			$parentRow=$this->db->get_row($table);
			$uri=$parentRow['uri']."/".$uri;
			if ($parentRow['self_parent']!=0) $uri=$this->_get_parent_uri($table,$uri,$parentRow['self_parent']);
		}
		return $uri;
	}

	function _clean_plugin_data($data) {
		// clean up many and foreign fields in data
		$cleanUp=array('rel','tbl','cfg');
		foreach ($data as $field => $value) {
			$pre=get_prefix($field);
			if (in_array($pre,$cleanUp)) unset($data[$field]);
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

	function AdminController() {
		parent::BasicController(true);
		if (!$this->_user_logged_in()) {
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
		if ($this->_is_super_admin()) {
			if (strpos($buttons1,"code")===FALSE) $buttons1.=",|,code";
		}
		$formats=$this->cfg->get('CFG_editor',"str_formats");
		$styles=$this->cfg->get('CFG_editor',"str_styles");
		$this->load->view('admin/header', array("title"=>$title,"url"=>$url,"jsVars"=>$this->js,"show_type"=>$type,"show_editor"=>$editor,"buttons1"=>$buttons1,"buttons2"=>$buttons2,"buttons3"=>$buttons3,'preview_width'=>$previewWidth,'preview_height'=>$previewHeight,"formats"=>$formats,"styles"=>$styles,"language"=>$this->language));
	}

	function _show_table_menu($tables,$type) {
		$a=array();
		$tables=filter_by($tables,$type."_");
		$excluded=$this->config->item('MENU_excluded');
		$cfgTables=$this->cfg->get("CFG_table");
		// trace_($cfgTables);
		$cfgTables=filter_by($cfgTables,$type);
		$cfgTables=sort_by($cfgTables,"order");
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
			if ($type!='tbl') $menuName='_'.$menuName;
			if ($type=='res') $menuName='_'.$menuName;
			if (!in_array($name,$excluded) and $this->has_rights($name)) {
				$a[$uri]=array("uri"=>$uri,'name'=>$menuName,"class"=>$type);
				$tableHelp=$this->cfg->get("CFG_table",$name,"txt_help");
				if (!empty($tableHelp)) $a[$uri]["help"]=$tableHelp;
			}
		}
		return $a;
	}

	function _show_menu($currentMenuItem="") {
		$this->lang->load('help');
		$menu=array();
		$this->db->where('b_visible',1);
		$adminMenu=$this->db->get_result('cfg_admin_menu');
		
		// trace_($adminMenu);

		foreach ($adminMenu as $item) {
			switch($item['str_type']) {
				case 'api' :
					$uiName=$item['str_ui_name'];
					$args=array($item['path'],$item['table']);
					$args=implode('/',$args);
					if ($args=='/') $args='';
					if (substr($uiName,0,1)=="_") $uiName=lang(substr($uiName,1));
					$uri=api_uri($item['api']).$args;
					$menu[$uri]=array('uri'=>$uri,'name'=>$uiName,'class'=>str_replace('/','_',$item['api']) );
					break;
					
				case 'seperator' :
					$menu[]=array();
					break;

				case 'tools':
					// Database import/export tools
					if ($this->_is_super_admin()) {
						$uri=api_uri('API_db_export');
						$menu[$uri]=array("uri"=>$uri,'name'=>lang('db_export'), "class"=>"db db_backup");
						$uri=api_uri('API_db_import');
						$menu[$uri]=array("uri"=>$uri,'name'=>lang('db_import'),"class"=>"db");
					}
					elseif ($this->_can_backup()) {
						$uri=api_uri('API_db_import');
						$menu[$uri]=array("uri"=>$uri,'name'=>lang('db_backup'),"class"=>"db db_backup");
						$uri=api_uri('API_db_restore');
						$menu[$uri]=array("uri"=>api_uri('API_db_restore'),'name'=>lang('db_restore'),"class"=>"db");
					}
					// Search&Replace AND Bulkupload tools
					if ($this->_can_use_tools()) {
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
							$menuName=$this->uiNames->get($mediaInfo['path']);
							while (isset($a[$menuName])) {$menuName.=" ";}
							$rightsName=el('path',$mediaInfo);
							$uri=api_uri('API_filemanager',"show",pathencode(el('path',$mediaInfo)));
							if (!empty($menuName) and $this->has_rights("media_".$rightsName)) {
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
		// trace_($menu);
		$this->menu->set_menu($menu);
		$this->menu->set_current_name($currentMenuItem);
		$menu=$this->menu->render();
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
										"site"		=> $siteInfo["url_url"],
										"user"		=> ucwords($this->user),
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