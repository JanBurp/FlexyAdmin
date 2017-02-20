<?php require_once(APPPATH."core/AdminController.php");

/**
 * Special Controller Class
 *
 * This Controller shows a grid or form
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @version V1 0.1
 *
 */

class Plugin_bulkupload extends Plugin {

	var $renameCount;
  var $doAction=false;

	public function __construct() {
		parent::__construct();
		$this->CI->load->library('session');
    $this->CI->load->model('mediatable');
    $this->CI->load->model('actiongrid');
		$this->resetRenameCount();
	}

	public function _admin_api() {
    $this->doAction=false;
		if ($this->CI->flexy_auth->can_use_tools()) {

      // Collect files
			$bulkMap=$this->config['folder'];
			$files=scan_map($bulkMap);
			if (empty($files)) {
				$this->add_message(p()."No files found in '".$bulkMap."'."._p());
        return $this->view();
			}
			asort($files);

			// is form submitted?
			$path=$this->CI->input->post('path');
			$rename=$this->CI->input->post('rename');
			
			// FORM
			if (empty($path)) {

				// create form
				$this->CI->lang->load('form');
				$this->CI->load->library('form');
				$form=new form();
				$options=array();
        // TODO: check if this works
        $options=$this->CI->assets->get_assets_folders( FALSE );
        $options = array_combine($options,$options);
				$form_fields=array(
          "path"				=> array("label"=>'Move to:','type'=>'dropdown','options'=>$options),
					"rename"			=> array('label'=>"Autorename"),
				);
				$form->set_data($form_fields,'Bulk upload settings');
				$this->add_message($form->render());			
				$this->add_message(p().nbs()._p().p().nbs()._p());
        
        $this->doAction=true;
			}
			
      // Create GRID
			$grid=new actiongrid();
      $grid->caption = 'Files';
			$this->resetRenameCount();
			
			foreach ($files as $file) {
        $file=str_replace($bulkMap.'/','',$file);
				$renameThis='';
				if (!empty($path)) $renameThis=$this->_newName($path,$file,$rename);
        $grid->add_action(array(
          'action_url' => $this->config->item('API_home').'ajax/plugin/bulkupload/'.pathencode($path).'/'.$file.'/'.$renameThis,
          'title'      => $file,
        ));
			}
      
			$this->add_message( $grid->view() );
			$this->CI->session->set_userdata('fileRenameCount',-1);
		}
    
    return $this->view();
	}

	private function resetRenameCount($reset=0) {
		$this->renameCount=$reset;
	}

	private function _newName($path,$name,$rename,$strict=FALSE) {
		$renameCount=$this->renameCount;
		
		$name=$name;
		$ext=get_file_extension($name);
		if (!empty($rename)) {
			if ($strict) {
				$saveFile=$rename;
			}
			else {
				$renameCount++;
				$saveFile=$rename.'_'.sprintf('%03d',$renameCount);
				// check if a file with same name and numbering exists, if so, count further...
				while (file_exists($path.'/'.$saveFile.'.'.$ext)) {
					$renameCount++;
					$saveFile=$rename.'_'.sprintf('%03d',$renameCount);
				}
			}
		}
		else {
			$saveFile=clean_file_name(get_file_without_extension($name));
		}
		// check if name exists, if so, add number
		$existsCount=0;
		$newFile=$saveFile.'.'.$ext;
		while (file_exists($path.'/'.$newFile)) {
			$newFile=$saveFile.'_'.$existsCount++.'.'.$ext;
		}
		$fileName=$newFile;
		$this->renameCount=$renameCount;
		return $fileName;
	}




  
	public function _ajax_api($args) {
    if (!$this->CI->flexy_auth->can_use_tools()) return array('plugin'=>'bulkupload','message'=>'no rights');
    
		$args = func_get_args();
    $path = pathdecode($args[0]);
    $map  = add_assets($path);
    $file = $args[1];
    $rename = get_file_without_extension(el(2,$args,''));
    
    $this->CI->load->library('upload');
    $this->CI->load->library('session');

    $config['upload_path'] = $path;
    $config['allowed_types'] = implode("|",$this->CI->config->item('FILE_types_img'));
    $this->CI->upload->config($config);
    
    // $mediaCfg=$this->CI->cfg->get('CFG_media_info');
    $bulkMap=$this->config['folder'];
    $saveFile=$this->_newName($path,$file,$rename,TRUE);

    $moved=copy_file($bulkMap.'/'.$file, $map.'/'.$saveFile);
    if ($moved) {
      // resize
      $resized=$this->CI->upload->resize_image($path,$saveFile);
      // TODO: autofill
      // $cfg=$mediaCfg[str_replace(SITEPATH.'assets/','',$path)];
      // if (!isset($cfg['str_autofill']) or $cfg['str_autofill']=='bulk upload' or $cfg['str_autofill']=='both') {
      //   $autoFill=$this->CI->upload->auto_fill_fields($saveFile,$path);
      // }
      // TODO: $userRestricted... fill in media table
      $userRestricted = FALSE; //$this->CI->cfg->get('CFG_media_info',$path,'b_user_restricted');
      if ($userRestricted)
        $this->CI->assets->insert_file($path,$saveFile,$userRestricted);
      else
        $this->CI->assets->insert_file($path,$saveFile);
      // delete original from Bulk map
      unlink($bulkMap.'/'.$file);
    }

    $result = array();
    $result['plugin']='bulkupload';
    $result['args']=$args;
    $result['filename']=$file;
    $result['success']=true;
    $result['_message']='Moved to: `'.$path.'/'.$saveFile.'`';
    return $result;
  }
  
  
	/**
	 * Depricated
	 *
	 * @return void
	 * @author Jan den Besten
   * @internal
	 */
	public function get_show_type() {
    if ($this->doAction===false) return 'grid';
    return '';
	}
	
	

}

?>
