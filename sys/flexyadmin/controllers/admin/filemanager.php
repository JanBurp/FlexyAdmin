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

	function show() {
		$args=$this->uri->uri_to_assoc();
		$keys=array_keys($args);
		if ($keys[0]=='setview') {
			$path=$keys[1];
			$offset=0;
			$order='';
			$search='';
		}
		else {
			$path=el('show',$args);
			$id=el('current',$args);
			$info=el('info',$args);
			// $sub=el('sub',$args);
			$offset=el('offset',$args,0);
			$order=el('order',$args,'name');
			$search=el('search',$args,'');
		}
		
		if (!empty($path)) {
			$path=pathdecode($path,TRUE);
			$name="";
			$map=$this->config->item('ASSETS').$path;
			$cfg=$this->cfg->get('CFG_media_info',$path);
			if (!empty($cfg) and $right=$this->_has_rights($path)) {
				$this->load->helper('html');
				$this->load->model("file_manager");
				$this->load->library('image_lib');
				
				$this->load->library("pagination");
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
				if (isset($cfg['b_user_restricted']) and $cfg['b_user_restricted']) {
					$restrictedToUser=$this->user_restriction_id($path);
					$files=$this->_filter_restricted_files($files,$restrictedToUser);
				}

				// Include extra data from cfg_media_files if any
				// if ($this->db->table_exists('cfg_media_files')) {
				// 	foreach ($files as $name => $file) {
				// 		$this->db->where('file', str_replace('site/assets/','',$file['path']));
				// 		$info=$this->db->get_row('cfg_media_files');
				// 		if ($info) {
				// 			$files[$name]=array_merge($file,$info);
				// 		}
				// 	}
				// }

				// Search in files
				if (!empty($search)) {
					foreach ($files as $name => $file) {
						if (!in_array_like($search,$file)) unset($files[$name]);
					}
				}
				// Sort files
				if (!empty($order)) {
					$sorder=$order;
					$sorder=str_replace(array('size','filewidth'),array('width','size'),$order);
					$desc=(substr($order,0,1)=='_');
					$files=sort_by($files,ltrim($sorder,'_'),$desc);
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
				$fileManager->set_pagination(array('offset'=>$offset,'order'=>$order,'search'=>$search));
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

	// function confirm($files="",$confirmed="") {
	// 	if ($confirmed=="confirmed") {
	// 		$this->session->set_userdata("confirmed",true);
	// 		$this->delete($files);
	// 	}
	// 	else {
	// 		$this->set_message("Not confirmed... ".anchor(api_uri('API_filemanager_confirm',$file,"confirm"),"confirm"));
	// 		redirect(api_uri('API_filemanager_view',$files));
	// 	}
	// }

	function confirm($path='') {
		$confirmed=$this->input->post('confirm');
		$files=$this->input->post('items');
		if ($confirmed=="confirmed") {
			$this->session->set_userdata("confirmed",true);
			$this->delete($path,$files);
		}
		else {
			$this->set_message("Not confirmed... ".anchor(api_uri('API_filemanager_confirm'),"confirm"));
			redirect(api_uri('API_filemanager_view'));
		}
	}



	function delete($path,$files="") {
		$files=explode(':',$files);
		// $path=array_shift($files);
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
				$mediaCfg=$this->cfg->get('CFG_media_info');
				$types=$mediaCfg[$path]['str_types'];
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
					// autofill
					if ($mediaCfg[$path]['str_autofill']=='single upload' or $mediaCfg[$path]['str_autofill']=='both') {
						$autoFill=$this->upload->auto_fill_fields($file,$path);
					}
					// fill in media table if any
					$userRestricted=$this->cfg->get('CFG_media_info',$path,'b_user_restricted');
					if ($userRestricted and $this->db->table_exists("cfg_media_files")) {
						$this->db->set('user',$this->user_id);
						$this->db->set('file',$path."/".$file);
						$this->db->insert('cfg_media_files');
					}
					// message
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



	function edit($path="",$file='',$new='',$newDate='') {
		if (empty($new)) {
			$ext=$this->input->post('ext');
			$new=$this->input->post('name').'.'.$ext;
		}

		$map=$this->config->item('ASSETS').pathdecode($path);
		$this->lang->load("update_delete");
		$succes=false;
		
		$oldFile=$map.'/'.$file;
		if (file_exists($oldFile)) {
			$new=clean_file_name($new);
			$newFile=$map.'/'.$new;
			// rename
			$succes=rename($oldFile,$newFile);
			
			if ($succes) {
				// new date if set
				if (!empty($newDate)) {
					$this->load->helper('date');
					$newDate=strtotime($newDate);
					touch($newFile,$newDate);
				}

				// remove from thumbcache if exists
				if (file_exists($this->config->item('THUMBCACHE')) ) {
					$thumbName=$this->config->item('THUMBCACHE').pathencode('site/assets/'.$path.'/'.$file);
					if (file_exists($thumbName)) unlink($thumbName);
				}
				// put file in sr array
				$sr=array();
				$sr[$file]=$new;

				// rename other size of same file
				$cfg=$this->cfg->get('cfg_img_info',str_replace('site/assets/','',$map));
				if (!empty($cfg)) {
					$sizes=1;
					while(isset($cfg['b_create_'.$sizes]) and $cfg['b_create_'.$sizes]) {
						$thisFile=add_file_prepostfix($file,$cfg['str_prefix_'.$sizes],$cfg['str_postfix_'.$sizes]);
						$thisNewFile=add_file_prepostfix($new,$cfg['str_prefix_'.$sizes],$cfg['str_postfix_'.$sizes]);
						rename($map.'/'.$thisFile, $map.'/'.$thisNewFile);
						$sr[$thisFile]=$thisNewFile;
						$sizes++;
					}
				}

				// replace all filenames in media(s) and txt_ etc
				$tables=$this->db->get_tables();
				if (!empty($tables)) {
					foreach ($tables as $table) {
						$fields=$this->db->list_fields($table);
						$selectFields=array();
						$selectFields[]=pk();
						foreach ($fields as $field) {
							$pre=get_prefix($field);
							if (in_array($pre,array('txt','stx','media','medias'))) $selectFields[]=$field;
						}
						if (!empty($selectFields)) {
							$selectFields[]=pk();
							$this->db->select($selectFields);
							$currentData=$this->db->get_result($table);
							foreach ($currentData as $row) {
								foreach ($row as $field=>$data) {
									if ($field==pk())
										$id=$data;
									else {
										// replace filenames
										$pre=get_prefix($field);
										if (in_array($pre,array('txt','stx'))) {
											// make sure it replaces only filenames
											if (!isset($txtsr)) {
												$txtsr=array();
												foreach ($sr as $key => $value) {$txtsr['/'.$key]='/'.$value;}
											}
											$newData=str_replace(array_keys($txtsr),array_values($txtsr) ,$data);
										}
										else {
											$newData=str_replace(array_keys($sr),array_values($sr) ,$data);
										}
										$this->db->set($field,$newData);
										$this->db->where(pk(),$id);
										$this->db->update($table);
									}
								}
							}
						}
					}
				}
				// new media list
				// will go automatic after redirect
			}
		}
		if ($succes)
			$this->set_message(langp("rename_succes",$new));
		else
			$this->set_message(langp("rename_error",$file));
		redirect(api_uri('API_filemanager_view',pathencode($path),$new));
	}

}

?>
