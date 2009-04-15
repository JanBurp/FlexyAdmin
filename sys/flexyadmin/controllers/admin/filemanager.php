<?
require_once(APPPATH."controllers/admin/MY_Controller.php");

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
 * Filemanager Controller
 *
 * This Controller shows files and handles actions
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Filemanager extends AdminController {

	function Filemanager() {
		parent::AdminController();
		// $this->load->library('image_lib');
	}

	function index() {
		$this->_set_content("FILEMANAGER");
		$this->_show_all();
	}

	function _has_rights($path) {
		$ok=FALSE;
		$mediaName=$this->cfg->get('CFG_media_info',$path,"str_name");
		return $this->has_rights($mediaName,"MEDIA");
	}


/**
 * This controls the filemanager view
 *
 * @param string $path Path name
 */

	function show($path,$idFile="") {
		$path=pathdecode($path);
		$name="";
		if ($this->_has_rights($path)) {
			$map=$this->config->item('ASSETS').$path;
			$this->load->helper('html');
			$this->load->model("file_manager");
			$this->load->model("grid");

			/**
			 * get files and info
			 */
			$files=read_map($map);
			$cfg=$this->cfg->get('CFG_media_info',$path);
			$types=$cfg['str_types'];
			$uiName=$cfg['str_menu_name'];

			/**
			 * update img/media_lists
			 */
			$this->_before_filemanager($path,$files);

			/**
			 * Start file manager
			 */
 			$fileManagerView=$this->cfg->get('CFG_configurations','str_filemanager_view');
			
			$fileManager=new file_manager($path,$types,$fileManagerView);
			$fileManager->set_files($files);
			if (!empty($idFile)) $fileManager->set_current($idFile);
			if (!empty($uiName)) $fileManager->set_caption($uiName);
			$renderData=$fileManager->render();

			if ($fileManagerView=="list") {
				// Grid
				$html=$this->load->view("admin/grid",$renderData,true);
			}
			else {
				// Thumb List
				$html=$this->load->view("admin/thumbs",$renderData,true);
			}
			$this->_set_content($html);

			/**
			 * show
			 */
			$name=$this->cfg->get('CFG_media_info',$path,'str_menu_name');
		}
		else {
			$this->lang->load("rights");
			$this->set_message(lang("rights_no_rights"));
		}
		$this->_show_type("filemanager ".$fileManagerView);
		$this->_show_all($name);
	}

/**
 * This controls the filemanager view
 *
 * @param string $path Path name
 */

	function setview($viewType,$path) {
		$this->db->set("str_filemanager_view",$viewType);
		$this->db->update($this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_configurations'));
		$this->cfg->load('CFG_configurations');
		$this->show($path);
	}


	/**
	 * FileManager controller
	 */

	function confirm($path,$file,$confirmed="") {
		if ($confirmed=="confirm") {
			$this->session->set_userdata("confirmed",true);
			$this->delete($path,$file);
		}
		else {
			$this->set_message("Not confirmed... ".anchor(api_uri('API_filemanager_confirm',$path,$file,"confirm"),"confirm"));
			redirect(api_uri('API_filemanager_view',$path,$file));
		}
	}

	function delete($path,$file) {
		$confirmed=$this->session->userdata("confirmed");
		if ($this->_has_rights($path)) {
			if ($confirmed) {
				$this->lang->load("update_delete");
				$this->load->model("file_manager");
				$fileManager=new file_manager(pathdecode($path));
				$result=$fileManager->delete_file($file);
				if ($result) {
					$this->load->model("login_log");
					$this->login_log->update($path);
					$this->set_message(langp("delete_file_succes",$file));
				}
				else {
					$this->set_message(langp("delete_file_error",$file));
				}
			}
		}
		else {
			$this->lang->load("rights");
			$this->set_message(lang("rights_no_rights"));
		}
		redirect(api_uri('API_filemanager_view',$path));
	}

	function upload($path,$file="") {
		if ($this->_has_rights($path)) {
			$this->lang->load("update_delete");
			$path=pathdecode($path);
			$this->load->library("upload");
			$this->load->model("file_manager");
			$types=$this->cfg->get('CFG_media_info',$path,'str_types');
			$fileManager=new file_manager(pathdecode($path),$types);
			$result=$fileManager->upload_file();
			$error=$result["error"];
			$file=$result["file"];
			if (!empty($error)) {
				$this->set_message(langp("upload_error",$file));
			}
			else {
				$this->set_message(langp("upload_succes",$file));
				$this->load->model("login_log");
				$this->login_log->update($path);
				redirect(api_uri('API_filemanager_view',pathencode($path),$file));
			}
		}
		else {
			$this->lang->load("rights");
			$this->set_message(lang("rights_no_rights"));
		}
		redirect(api_uri('API_filemanager_view',pathencode($path)));
	}


}

?>
