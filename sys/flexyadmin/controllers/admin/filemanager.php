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
		$this->_show_all();
	}

	function _has_rights($path,$whatRight=0) {
		$ok=FALSE;
		$mediaName=$this->cfg->get('CFG_media_info',$path,"path");
		return $this->has_rights("media_".$mediaName,"",$whatRight);
	}

	// A (older) copy of underlying methods sits in flexy_field
	function _get_unrestricted_files($restrictedToUser) {
		$this->db->where('user',$restrictedToUser);
		$this->db->set_key('file'); 
		return $this->db->get_result("cfg_media_files");
	}
	
	function _get_user_files() {
		$this->db->set_key('file');
		return $this->db->get_result("cfg_media_files");
	}
	
	function _filter_restricted_files($files,$restrictedToUser) {
		if ($this->db->table_exists("cfg_media_files")) {
			$assetsPath=assets();
			$userFiles=$this->_get_user_files();
			ksort($userFiles);
			// trace_($userFiles);
			foreach ($files as $name => $file) {
				if (substr($name,0,1)!="_") {
					$file=str_replace($assetsPath,"",$file['path']);
					if ($restrictedToUser) {
						if ( (!isset($userFiles[$file]['user'])) or
								 (isset($userFiles[$file]['user']) and $userFiles[$file]['user']!=$restrictedToUser) ) unset($files[$name]);
					}
					else {
						if (isset($userFiles[$file]['user'])) {
							$this->db->where('id',$userFiles[$file]['user']);
							$this->db->select('str_user_name');
							$user=$this->db->get_row('cfg_users');
							$files[$name]['user']=$user['str_user_name'];
						}
						else
							$files[$name]['user']='';
					}
				}
			}
			// trace_($files);

			// if ($restrictedToUser) {
			// 	$unrestrictedFiles=$this->_get_unrestricted_files($restrictedToUser);
			// 	$unrestrictedFiles=array_keys($unrestrictedFiles);
			// 	$assetsPath=assets();
			// 	foreach ($files as $name => $file) {
			// 		$file=str_replace($assetsPath,"",$file['path']);
			// 		if (!in_array($file,$unrestrictedFiles)) unset($files[$name]);
			// 	}
			// }
		}
		return $files;
	}

/**
 * This controls the filemanager view
 *
 * @param string $path Path name
 */

	function show($path="",$idFile="") {
		if (!empty($path)) {
			$path=pathdecode($path,TRUE);
			$name="";
			$map=$this->config->item('ASSETS').$path;
			$cfg=$this->cfg->get('CFG_media_info',$path);
			if (!empty($cfg) and $right=$this->_has_rights($path)) {
				$this->load->helper('html');
				$this->load->model("file_manager");
				$this->load->model("grid");
				$this->lang->load("help");
				$this->_add_js_variable("help_filter",$this->_add_help(langp('grid_filter')));

				/**
				 * get files and info
				 */
				$types=$cfg['str_types'];
				$uiName=$cfg['str_ui_name'];
				$files=read_map($map);
				/**
				 * update img/media_lists
				 */
				$this->_before_filemanager($path,$files);

				/**
					* Exclude files that are not owned by user
					*/
				if ($cfg['b_user_restricted']) {
					$restrictedToUser=$this->user_restriction_id($path);
					$files=$this->_filter_restricted_files($files,$restrictedToUser);
				}

				/**
				 * Start file manager
				 */
	 			$fileManagerView=$this->session->userdata("fileview");
			
				$fileManager=new file_manager($path,$types,$fileManagerView);
				if ($right<RIGHTS_ADD) 		$fileManager->show_upload_button(FALSE);
				if ($right<RIGHTS_DELETE)	$fileManager->show_delete_buttons(FALSE);
				$fileManager->set_files($files);
				if (!empty($idFile)) $fileManager->set_current($idFile);
				$Help=$this->cfg->get("CFG_media_info",$path,"txt_help");
				if (!empty($Help)) {
					$uiName=help($uiName,$Help);
				}
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
				$this->_show_type("filemanager ".$fileManagerView);
			}
		}
		if (!isset($name)) $name="";
		$this->_show_all($name);
	}

/**
 * This controls the filemanager view
 *
 * @param string $path Path name
 */

	function setview($viewType="",$path="") {
		if (!empty($viewType) and in_array($viewType,$this->config->item('FILES_view_types'))) {
			$this->db->set("str_filemanager_view",$viewType);
			$this->db->where("str_user_name",$this->session->userdata("user"));
			$this->db->update($this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_users'));
			$this->session->set_userdata("fileview",$viewType);
		}
		$this->show($path);
	}


	/**
	 * FileManager controller
	 */

	function confirm($files="",$confirmed="") {
		if ($confirmed=="confirmed") {
			$this->session->set_userdata("confirmed",true);
			$this->delete($files);
		}
		else {
			$this->set_message("Not confirmed... ".anchor(api_uri('API_filemanager_confirm',$file,"confirm"),"confirm"));
			redirect(api_uri('API_filemanager_view',$files));
		}
	}

	function delete($files="") {
		$files=explode(':',$files);
		$path=array_shift($files);
		$path=pathdecode($path);
		if (!empty($path) and !empty($files)) {
			$confirmed=$this->session->userdata("confirmed");
			if ($this->_has_rights($path)>=RIGHTS_DELETE) {
				if ($confirmed) {
					$deletedFiles='';
					foreach ($files as $file) {
						$DoDelete=TRUE;
						$mediaTableExists=$this->db->table_exists("cfg_media_files");
						if ($mediaTableExists) {
							$restrictedToUser=$this->user_restriction_id($path);
							if ($restrictedToUser>0) {
								$DoDelete=FALSE;
								$unrestrictedFiles=$this->_get_unrestricted_files($restrictedToUser);
								if (array_key_exists($path."/".$file,$unrestrictedFiles)) {
									$DoDelete=TRUE;
								}
							}
						}
						if ($DoDelete) {
							$this->lang->load("update_delete");
							$this->load->model("file_manager");
							$fileManager=new file_manager(pathdecode($path,TRUE));
							$result=$fileManager->delete_file($file);
							if ($result) {
								if ($mediaTableExists) {
									$this->db->where('file',$path."/".$file);
									$this->db->delete('cfg_media_files');
								}
								$this->load->model("login_log");
								$this->login_log->update($path);
								$deletedFiles.=', '.$file;
								// $this->set_message(langp("delete_file_succes",$file));
							}
							else {
								$this->set_message(langp("delete_file_error",$file));
							}
						}
						else {
							$this->lang->load("rights");
							$this->set_message(lang("rights_no_rights"));
						}
						if (!empty($deletedFiles)) $this->set_message(langp("delete_file_succes",substr($deletedFiles,2)));
					}
				}
			}
			else {
				$this->lang->load("rights");
				$this->set_message(lang("rights_no_rights"));
			}
		}
		redirect(api_uri('API_filemanager_view',$path));
	}

	function upload($path="",$file="") {
		if (!empty($path)) {
			if ($this->_has_rights($path)>=RIGHTS_ADD) {
				$this->lang->load("update_delete");
				$this->load->library("upload");
				$this->load->model("file_manager");
				$types=$this->cfg->get('CFG_media_info',$path,'str_types');
				$fileManager=new file_manager($path,$types);
				$result=$fileManager->upload_file();
				$error=$result["error"];
				$file=$result["file"];
				if (!empty($error)) {
					if (is_string($error))
						$this->set_message($error,$file);
					else
						$this->set_message(langp("upload_error",$file));
				}
				else {
					$userRestricted=$this->cfg->get('CFG_media_info',$path,'b_user_restricted');
					if ($userRestricted and $this->db->table_exists("cfg_media_files")) {
						$this->db->set('user',$this->user_id);
						$this->db->set('file',$path."/".$file);
						$this->db->insert('cfg_media_files');
					}
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
		}
		redirect(api_uri('API_filemanager_view',pathencode($path)));
	}


}

?>
