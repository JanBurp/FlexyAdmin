<?

require_once(APPPATH."core/BasicController.php");


/**
 * AdminController Class extends BasicController
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
		
		if ( ! $this->_user_logged_in() ) {
			redirect($this->config->item('API_login'));
		}
		if ( ! $this->_user_can_use_admin() ) {
			$this->user->logout();
			redirect(site_url());
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

	private function _user_can_use_admin() {
		return ($this->user_group_id<4); // restrict admin use only to users that ar at least a user (visitors are not allowed)
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
						$query->free_result();
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
		$query->free_result();
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