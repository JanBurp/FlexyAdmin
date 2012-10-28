<?
require_once(APPPATH."core/AdminController.php");

class Filemanager extends AdminController {

	public function __construct() {
		parent::__construct();
    $this->load->model('mediatable');
    $this->load->model('grid_set');
    $this->grid_set->set_api('API_filemanager_view');
	}

	function index() {
		$this->_show_all();
	}

	function _has_rights($path,$whatRight=0) {
		$ok=FALSE;
		$mediaName=$this->cfg->get('CFG_media_info',$path,"path");
		return $this->user->has_rights("media_".$mediaName,"",$whatRight);
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
			$idFile=el('current',$args);
			$info=el('info',$args);
			// $sub=el('sub',$args);
			$offset=el('offset',$args,0);
			$order=el('order',$args,'name');
			$search=el('search',$args,'');
		}
    $this->grid_set->save(array('table'=>$path,'offset'=>$offset,'order'=>$order,'search'=>$search));
		
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
        $this->_add_js_variable("file_types",$cfg['str_types']);

				/**
				 * get files and info
				 */
				$types=$cfg['str_types'];
				$uiName=$this->ui->get($path);
				
        if ($this->mediatable->exists())
          $files=$this->mediatable->get_files($map);
        else
          $files=read_map($map);
        
				/**
				 * update img/media_lists
				 */
				$this->_before_filemanager($path,$files);

				/**
					* Exclude files that are not owned by user
					*/
				if (isset($cfg['b_user_restricted']) and $cfg['b_user_restricted']) {
					$restrictedToUser=$this->user->restricted_id($path);
					$files=$this->mediatable->filter_restricted_files($files,$restrictedToUser);
				}

        // Check if file is used somewhere
        if (isset($cfg['fields_check_if_used_in']) and !empty($cfg['fields_check_if_used_in'])) {
          $used_field=lang('USED');
          foreach ($files as $name => $file) {
            $this->load->model('search_replace');
            $found=$this->search_replace->has_text($name,$cfg['fields_check_if_used_in']);
            $files[$name][$used_field]=$found;
          }
        }
        

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
				$Help=$this->ui->get_help($path);
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
			$this->db->where("str_username",$this->session->userdata("user"));
			$this->db->update('cfg_users');
			$this->session->set_userdata("fileview",$viewType);
		}
    
		$redirectUri=$this->grid_set->open_uri();
    redirect($redirectUri);
    // redirect(api_uri('API_filemanager_view',$path));
	}


	/**
	 * FileManager controller
	 */

	function confirm($path='') {
		$confirmed=$this->input->post('confirm');
		$files=$this->input->post('items');
		if ($confirmed=="confirmed") {
			$this->session->set_userdata("confirmed",true);
			$this->delete($path,$files);
		}
		else {
			$this->message->add("Not confirmed... ".anchor(api_uri('API_filemanager_confirm'),"confirm"));
			redirect(api_uri('API_filemanager_view'));
		}
	}


  /**
   * Delete a file
   *
   * @param string $path 
   * @param string $files 
   * @return void
   * @author Jan den Besten
   */
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
						if ($this->mediatable->exists()) {
							if ($this->mediatable->is_user_restricted($path)) {
                $restrictedToUser=$this->user->restricted_id($path);
								$DoDelete=FALSE;
								$unrestrictedFiles=$this->mediatable->get_unrestricted_files($restrictedToUser);
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
								if ($this->mediatable->exists()) {
                  $this->mediatable->delete($file,$path);
								}
								$this->load->model("login_log");
								$this->login_log->update($path);
								$deletedFiles.=', '.$file;
							}
							else {
								$this->message->add_error(langp("delete_file_error",$file));
							}
						}
						else {
							$this->lang->load("rights");
							$this->message->add_error(lang("rights_no_rights"));
						}
					}
					if (!empty($deletedFiles)) $this->message->add(langp("delete_file_succes",substr($deletedFiles,2)));
				}
			}
			else {
				$this->lang->load("rights");
				$this->message->add_error(lang("rights_no_rights"));
			}
		}
		$redirectUri=$this->grid_set->open_uri();
		if (!empty($info)) $redirectUri.='/info/'.$info;
		redirect($redirectUri);
	}

  /**
   * Upload a file
   *
   * @param string $path 
   * @param string $file 
   * @return void
   * @author Jan den Besten
   */
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
						$this->message->add_error($error."<p><br/><i>".preg_replace('/(.*)(\..*)/','$1<b>$2</b>',$file)."</i></p>");
					else
						$this->message->add_error(langp("upload_error",$file));
				}
				else {
					// autofill
					if (isset($mediaCfg[$path]['str_autofill']) and ($mediaCfg[$path]['str_autofill']=='single upload' or $mediaCfg[$path]['str_autofill']=='both') ) {
						$autoFill=$this->upload->auto_fill_fields($file,$path);
					}
					// fill in media table if any
					$userRestricted=$this->cfg->get('CFG_media_info',$path,'b_user_restricted');
					if ($this->mediatable->exists()) {
            if ($userRestricted)
              $this->mediatable->add($file,$path,$this->user_id);
            else
              $this->mediatable->add($file,$path);
					}
					// message
					$this->message->add(langp("upload_succes",$file));
					$this->load->model("login_log");
					$this->login_log->update($path);
					redirect(api_uri('API_filemanager_view',pathencode($path),$file));
				}
			}
			else {
				$this->lang->load("rights");
				$this->message->add_error(lang("rights_no_rights"));
			}
		}
		redirect(api_uri('API_filemanager_view',pathencode($path)));
	}



  /**
   * Edit file properties by an AJAX post request
   *
   * @return void
   * @author Jan den Besten
   */
	function edit() {
    $path=$this->input->post('path');
    $ext=$this->input->post('ext');
    $file=$this->input->post('file').'.'.$ext;
    $newName=$this->input->post('name').'.'.$ext;
    $newDate=$this->input->post('date');
    $newTitle=$this->input->post('title');

		$map=assets().$path;
		$this->lang->load("update_delete");
		
		$oldFile=$map.'/'.$file;
    
    $returndata=array(
      'ajax'    =>IS_AJAX,
      'file'    =>$oldFile,
    );
    
		if (file_exists($oldFile) and $this->_has_rights($path,RIGHTS_EDIT)) {

      // new title if set
      if (!empty($newTitle)) {
        $returndata['newTitle']=$newTitle;
        $this->mediatable->edit_info($oldFile,'str_title',$newTitle);
      }
        
			// new date if set
			if (!empty($newDate)) {
        $returndata['newDate']=$newDate;
        $this->mediatable->edit_info($oldFile,'dat_date',$newDate);
				$this->load->helper('date');
				$newDate=strtotime($newDate);
				touch($oldFile,$newDate);
			}

      if ($newName!=$file) {
  			$newName=clean_file_name($newName);
  			$newFile=$map.'/'.$newName;
        if (file_exists($newFile)) {
          $this->message->add_error(langp("rename_exists",$newName));
        }
        else {
    			$succes=rename($oldFile,$newFile);
          if (!$succes) {
            $this->message->add_error(langp("rename_error",$file));
          }
          else {
            $this->message->add(langp("rename_succes",$newName));
            $returndata['newFile']=$newFile;
            // Rename in table
            $this->mediatable->edit_info($oldFile,'file',$newName);
            // remove from thumbcache if exists
    				if (file_exists($this->config->item('THUMBCACHE')) ) {
    					$thumbName=$this->config->item('THUMBCACHE').pathencode(SITEPATH.'assets/'.$path.'/'.$file);
    					if (file_exists($thumbName)) {
                unlink($thumbName);
                $returndata['thumbRemoved']=$thumbName;
              }
    				}
    				// put file in sr array
    				$sr=array();
    				$sr[$file]=$newName;

    				// rename other size of same file
    				$cfg=$this->cfg->get('cfg_img_info',str_replace(SITEPATH.'assets/','',$map));
    				if (!empty($cfg)) {
    					$sizes=1;
    					while(isset($cfg['b_create_'.$sizes]) and $cfg['b_create_'.$sizes]) {
    						$thisFile=add_file_presuffix($file,$cfg['str_prefix_'.$sizes],$cfg['str_suffix_'.$sizes]);
    						$thisNewFile=add_file_presuffix($new,$cfg['str_prefix_'.$sizes],$cfg['str_suffix_'.$sizes]);
    						rename($map.'/'.$thisFile, $map.'/'.$thisNewFile);
    						$sr[$thisFile]=$thisNewFile;
                $returndata['size_'.$sizes]=$thisNewFile;
    						$sizes++;
    					}
    				}

            // Search Replace in db
            $this->load->model('search_replace');
            $returndata['sr']=$this->search_replace->media($oldFile,$newFile);
    			}
        }
		  }

    }
    else {
      $this->message->add_error(langp("rename_error",$file));
    }
    $json=array2json($returndata);
    echo $json;
	}

}

?>
