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
		$this->_init_flexy_admin($isAdmin);
	}

	function _init_flexy_admin($isAdmin=false) {
		//$this->output->enable_profiler(TRUE);
		$this->load->model("cfg");
		$this->cfg->load('CFG_configurations');
		$this->cfg->load('CFG_table',$this->config->item('CFG_table_name'));
		$this->cfg->load('CFG_field',$this->config->item('CFG_field_name'));
		$this->cfg->load('CFG_media_info',array("str_path","fields"));
		$this->cfg->load('CFG_img_info','str_path');
		$lang=$this->cfg->get('CFG_configurations','str_language');
		$lang=$lang."_".strtoupper($lang);
		setlocale(LC_ALL, $lang);
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
		$this->load->model("flexy_data","fd");
		$this->load->library("menu");
		$this->load->library("content");

		/**
		 * Init global site data
		 */
		$this->_init_globals();

		/**
		 * Add this page to statistics, if statistisc table exists
		 */
		if ($this->db->table_exists("cfg_stats")) {
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
		 * Set home uri (is set in tbl_site)
		 */
		if (isset($this->site["str_start_uri"])) {
			$this->uri->set_home($this->site["str_start_uri"]);
		}
	}

	function add_keywords($words) {
		$this->site["keywords"]=add_string($this->site["keywords"],$words,",");
	}

	function add($key,$value) {
		$this->site[$key]=$value;
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

	var $table_rights;
	var $media_rights;
	var $user;

	function BasicController($isAdmin=false) {
		parent::MY_Controller($isAdmin);
		$this->load->library("session");
		$this->user="";
		$this->table_rights="";
		$this->media_rights="";
		if (!$this->_user_logged_in()) {
			redirect($this->config->item('API_login'));
		}
	}

	function _user_logged_in() {
		$out=false;
		$this->user=$this->session->userdata("user");
		$this->table_rights=$this->session->userdata("table_rights");
		$this->media_rights=$this->session->userdata("media_rights");
		$out=(!empty($this->user));
		return $out;
	}

	function has_rights($table,$id="") {
		$ok=FALSE;
		$pre=get_prefix($table);
		$preAll=$pre."_*";
		// has admin rights?
		if ($id==="MEDIA")
			$rights=$this->media_rights;
		else
			$rights=$this->table_rights;
		if ($rights=="*")
			$ok=TRUE;
		// has rights for exactly this table?
		elseif (strpos($rights,$table)!==FALSE)
			$ok=TRUE;
		// has rights for all tables with this prefix
		elseif ($id!=="MEDIA" and strpos($rights,$preAll)!==FALSE)
			$ok=TRUE;
		// has rights for own user form
		elseif ($table==$this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_users') and $id==$this->session->userdata("user_id"))
			$ok=TRUE;
		return $ok;
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

	function AdminController() {
		parent::BasicController(true);
		$this->currentTable="";
		$this->currentId="";
		$this->currentUser="";
		$this->currentMenuItem="";
		$this->content="";
		$this->showEditor=false;
		$this->load->model("ui_names","uiNames");
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
		$title=$this->cfg->get('CFG_configurations',"str_name");
		$url=$this->cfg->get('CFG_configurations',"url_site_url");
		$editor=$this->showEditor and $this->cfg->get('CFG_editor',"b_use_editor");
		$type="";
		if (isset($this->showType)) $type=$this->showType;
		$buttons1=$this->cfg->get('CFG_editor',"str_buttons1");
		$buttons2=$this->cfg->get('CFG_editor',"str_buttons2");
		$buttons3=$this->cfg->get('CFG_editor',"str_buttons3");
		$styles=$this->cfg->get('CFG_editor',"str_styles");
		$this->load->view('admin/header', array("title"=>$title,"url"=>$url,"show_type"=>$type,"show_editor"=>$editor,"buttons1"=>$buttons1,"buttons2"=>$buttons2,"buttons3"=>$buttons3,"styles"=>$styles));
	}

	function _show_table_menu($tables,$type) {
		$a=array();
		$tables=filter_by($tables,$type);
		$excluded=$this->config->item('MENU_excluded');
		// first the ordered tables
		$this->db->order_by("order");
		$this->db->like("table","$type%");
		$this->db->select("table");
		$query=$this->db->get($this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_table'));
		$oTables=array();
		foreach ($query->result_array() as $row) {
			$oTables[]=$row["table"];
			unset($tables[array_search($row["table"],$tables)]);
		}
		$oTables=array_merge($oTables,$tables);
		foreach ($oTables as $name) {
			if (!in_array($name,$excluded) and $this->has_rights($name)) {
				$a[$this->uiNames->get($name)]=array("uri"=>api_uri('API_view_grid',$name),"class"=>$type);
			}
		}
		return $a;
	}

	function _show_menu($currentMenuItem="") {
		$this->load->library('menu');
		// load menu items
		$a=array();
		// standard items

		$a["Home"]			=array("uri"=>api_uri('API_home'));
		$a["Logout"]		=array("uri"=>api_uri('API_logout'));

		// normal tables
		$tables=$this->db->list_tables();
		$a=array_merge($a,$this->_show_table_menu($tables,$this->config->item('TABLE_prefix')));

		// media
		$mediaInfoTbl=$this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_media_info');
		if ($this->db->table_exists($mediaInfoTbl)) {
			$this->db->order_by("order");
			$query=$this->db->get($mediaInfoTbl);
			foreach($query->result_array() as $mediaInfo) {
				$menuName=el('str_menu_name',$mediaInfo);
				$rightsName=el('str_name',$mediaInfo);
				if (!empty($menuName) and $this->has_rights($rightsName,"MEDIA")) {
					$a[$menuName]=array("uri"=>api_uri('API_filemanager',"show",pathencode(el('str_path',$mediaInfo))),"class"=>"media");
				}
			}
		}

		// cfg tables
		$a=array_merge($a,$this->_show_table_menu($tables,$this->config->item('CFG_table_prefix')));
		$a=array_merge($a,$this->_show_table_menu($tables,$this->config->item('REL_table_prefix')));

		$this->menu->set_menu($a);
		// if ($currentMenuItem=="") $currentMenuItem="Home";
		$this->menu->set_current_name($currentMenuItem);
		$menu=$this->menu->render();
		$this->load->view('admin/menu',array("menu"=>$menu));
	}

	function _show_message() {
		$message=$this->session->userdata("message");
		if ($message!="") {
			// $message=replace_ui_names($message);
			$this->load->view('admin/message', array("message"=>$message));
		}
		$this->session->unset_userdata("message");
	}

	function _show_content() {
		$this->load->view('admin/content',array("content"=> $this->content));
	}

	function _show_footer($extra_view="",$data=NULL) {
		$this->db->select("url_url");
		$query=$this->db->get("tbl_site");
		$siteInfo=$query->row_array();
		$footer=array(	"view"		=> $extra_view,
										"data"		=> $data,
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
			$svn=explode("\n",$svn);
			//$fileKey=array_search(str_replace("sys/","",$revfile),$svn);
			//$fileKey=array_search("jan",$svn);
			$matches=array_keys($svn,"jan");
			$fileKey=$matches[count($matches)-1];
			// trace_($matches);
			// $revKey=$fileKey+2;
			$revKey=$fileKey-1;
			$rev = $svn[$revKey];
			if (!empty($rev)) write_file($revfile, $rev);
		}
		if (empty($rev) and file_exists($revfile)) {
			$rev = read_file($revfile);
		}
		if (empty($rev)) $rev="#";
		return $rev;
	}

	function _show_type($type) {
		$this->showType=$type;
	}

	function _show_all($currentMenuItem="") {
		$this->_show_header();
		$this->_show_message();
		$this->_show_menu($currentMenuItem);
		$this->_show_content();
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

	/**
	 * Here are fuctions that hook into the grid/form/update proces.
	 * They check if a standard hook method for the current table/field/id, if so call it
	 */

	function _before_grid($table,&$data) {
		$func="_before_grid_$table";
		if (method_exists($this,$func)) {
			$this->$func($data);
		}
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
		if (!$result) $this->set_message("Could not update img/media List. Check file rights.");
		return $result;
	}


}

?>
