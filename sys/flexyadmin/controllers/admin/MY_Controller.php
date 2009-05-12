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
		// trace_($this->cfg->data);
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
		$this->load->library("menu");
		$this->load->library("content");

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
			$this->db->order_as_tree();
			$this->db->select("uri");
			$top2=$this->db->get_result($menuTable,2);
			$top=current($top2);
			$this->uri->set_home($top["uri"]);
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
	function get_uri() {
		return $this->site["uri"];
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

	var $table_rights;
	var $media_rights;
	
	var $user;
	var $rights;
	var $language;

	function BasicController($isAdmin=false) {
		parent::MY_Controller($isAdmin);
		$this->load->library("session");
		$this->user="";
		$this->table_rights="";
		$this->media_rights="";
		if (!$this->_user_logged_in()) {
			// redirect($this->config->item('API_login'));
		}
		$lang=$this->language."_".strtoupper($this->language);
		setlocale(LC_ALL, $lang);
	}

	function _user_logged_in() {
		$out=false;
		$this->user=$this->session->userdata("user");
		$this->rights=$this->session->userdata("rights");
		$this->language=$this->session->userdata("language");
		$out=(!empty($this->user));
		return $out;
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
		$found=array('b_delete'=>FALSE,'b_add'=>FALSE,'b_edit'=>FALSE,'b_show'=>FALSE);
		$pre=get_prefix($item);
		$preAll=$pre."_*";

		foreach ($this->rights as $key => $rights) {
			if ($rights['rights']=="*" or (strpos($rights['rights'],$preAll)!==FALSE) or (strpos($rights['rights'],$item)!==FALSE) )
				$this->_change_rights($found,$rights);
		}
		$foundRights=RIGHTS_NO;
		if ($found['b_delete'])	$foundRights+=RIGHTS_DELETE;
		if ($found['b_add'])		$foundRights+=RIGHTS_ADD;
		if ($found['b_edit'])		$foundRights+=RIGHTS_EDIT;
		if ($found['b_show'])		$foundRights+=RIGHTS_SHOW;

		if ($whatRight==0)
			return $foundRights;
		else
			return ($foundRights>=$whatRight);
	}

	function _has_key($table="") {
		if ($table=='cfg_configurations') return true;
		$k=$this->cfg->get('CFG_configurations',$this->_decode('==QOwAjM5V2a'));
		if (empty($k)) return false;
		$h=strtolower($_SERVER[$this->_decode('==QOwAjMUN1TI9FUURFS')]);
		$h=explode('/',str_replace(array($this->_decode('=kDMwIzLvoDc0RHa'),$this->_decode('=kDMwIjL3d3d')),"",$h));
		$h=$h[0];
		if ($k==$this->_encode($h)) return true;
		if (IS_LOCALHOST) return true;
		return false;
	}

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
		$this->load->helper("language");
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
		$title=$this->cfg->get('CFG_configurations',"str_name");
		$url=$this->cfg->get('CFG_configurations',"url_site_url");
		$editor=$this->showEditor and $this->cfg->get('CFG_editor',"b_use_editor");
		$type="";
		if (isset($this->showType)) $type=$this->showType;
		$buttons1=$this->cfg->get('CFG_editor',"str_buttons1");
		$buttons2=$this->cfg->get('CFG_editor',"str_buttons2");
		$buttons3=$this->cfg->get('CFG_editor',"str_buttons3");
		$formats=$this->cfg->get('CFG_editor',"str_formats");
		$styles=$this->cfg->get('CFG_editor',"str_styles");
		$this->load->view('admin/header', array("title"=>$title,"url"=>$url,"show_type"=>$type,"show_editor"=>$editor,"buttons1"=>$buttons1,"buttons2"=>$buttons2,"buttons3"=>$buttons3,"formats"=>$formats,"styles"=>$styles,"language"=>$this->language));
	}

	function _show_table_menu($tables,$type) {
		$a=array();
		$tables=filter_by($tables,$type);
		$excluded=$this->config->item('MENU_excluded');
		$cfgTables=$this->cfg->get("CFG_table");
		$cfgTables=filter_by($cfgTables,$type);
		$cfgTables=sort_by($cfgTables,"order");
		// trace_($cfgTables);
		$oTables=array();
		foreach ($cfgTables as $row) {
			$oTables[]=$row["table"];
			unset($tables[array_search($row["table"],$tables)]);
		}
		$oTables=array_merge($oTables,$tables);
		foreach ($oTables as $name) {
			if (!in_array($name,$excluded) and $this->has_rights($name)) {
				// if ($name=="tbl_menu")
				// 	$a[$this->uiNames->get($name)]=array("uri"=>api_uri('API_view_tree',$name),"class"=>$type);
				// else
					$a[$this->uiNames->get($name)]=array("uri"=>api_uri('API_view_grid',$name),"class"=>$type);
				$tableHelp=$this->cfg->get("CFG_table",$name,"txt_help");
				if (!empty($tableHelp)) {
					$a[$this->uiNames->get($name)]["help"]=$tableHelp;
				}
			}
		}
		return $a;
	}

	function _show_menu($currentMenuItem="") {
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
				if (!empty($menuName) and $this->has_rights("media_".$rightsName)) {
					$a[$menuName]=array("uri"=>api_uri('API_filemanager',"show",pathencode(el('str_path',$mediaInfo))),"class"=>"media");
				}
			}
		}

		// cfg tables
		$a=array_merge($a,$this->_show_table_menu($tables,$this->config->item('CFG_table_prefix')));
		$a=array_merge($a,$this->_show_table_menu($tables,$this->config->item('LOG_table_prefix')));
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
			$message=$this->uiNames->replace_ui_names($message);
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
	
	function _add_help($help,$name="") {
		static $counter=0;
		if (empty($name)) {
			$name=safe_string($help,10);
		}
		$name="help_".$counter++."_$name";
		$this->helpTexts[$name]=$help;
		return $name;
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
