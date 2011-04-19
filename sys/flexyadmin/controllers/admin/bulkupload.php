<?
require_once(APPPATH."controllers/admin/MY_Controller.php");

/**
 * Special Controller Class
 *
 * This Controller shows a grid or form
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Bulkupload extends AdminController {

	var $renameCount;

	function Bulkupload() {
		parent::AdminController();
		$this->resetRenameCount();
	}

	function index() {
		if ($this->_can_use_tools()) {
			$this->_add_content(h('Bulk Upload'));
			$bulkMap=$this->config->item('BULKUPLOAD');
			$files=read_map($bulkMap);
			if (empty($files)) {
				$this->_add_content(p()."No files found in '".$bulkMap."'."._p());
			}
			else {
				$this->load->library('session');
				
				$gridFiles=array();
				$mediaCfg=$this->cfg->get('CFG_media_info');

				// is form submitted?
				$path=$this->input->post('path');
				$rename=$this->input->post('rename');
				
				// Show form?
				if (empty($path)) {
					// create form
					$this->lang->load('form');
					$this->load->model('form');
					$form=new form($this->config->item('API_bulk_upload'));
					$options=array();
					foreach ($mediaCfg as $info) {
						$options[$info['path']]=$info['path'];
					}
					$data=array(	"path"				=> array("label"=>'Move to:','type'=>'dropdown','options'=>$options),
												"rename"			=> array('label'=>"Autorename")
												// "do_resize"		=> array('type'=>'checkbox','value'=>'1'),
												// "do_autofill"	=> array("label"=>'Auto fill fields','type'=>'checkbox','value'=>'1')
												);
					$form->set_data($data,'Bulk upload settings');
					$form->set_old_templates();
					$this->_add_content($form->render());			
					$this->_add_content(p().nbs()._p().p().nbs()._p());
				}
				
				// Show and move files
				$map=$path;
				$path=assets($path);
				foreach ($files as $name => $file) {
					$gridFiles[$name]['File']=$file['name'];
				}
				asort($gridFiles);
				
				$this->resetRenameCount();
				
				foreach ($files as $name => $file) {
					$renameThis='';
					if (!empty($map)) $renameThis=$this->_newName($path,$name,$rename);
					$gridFiles[$name]=array('id'=>icon('no'),'File'=>$file['name'],'uri'=>'admin/bulkupload/ajaxUpload/'.pathencode($path).'/'.$name.'/'.$renameThis);
					if (!empty($map)) $gridFiles[$name]['Rename']=$renameThis;
				}

				// strace_($gridFiles);

				$this->load->model("grid");
				$grid=new grid();			
				$grid->set_data($gridFiles,'Files');
				$grid->set_heading('id','');
				if (empty($map))
					$renderData=$grid->render("html",'bulkupload',"grid");
				else
					$renderData=$grid->render("html",'bulkupload',"grid actionGrid");
				$this->_add_content($this->load->view("admin/grid",$renderData,true));
				$this->session->set_userdata('fileRenameCount',-1);
			}
		}
		$this->_show_type("form grid");
		$this->_show_all();
	}

	function resetRenameCount($reset=0) {
		$this->renameCount=$reset;
	}

	function _newName($path,$name,$rename,$strict=FALSE) {
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


	function ajaxUpload($args) {
		$args=func_get_args();
		$path=pathdecode($args[0]);
		$file=$args[1];
		$rename='';
		if (isset($args[2])) $rename=get_file_without_extension($args[2]);
		
		if ($this->_can_use_tools()) {
			$this->load->library('upload');
			$this->load->library('session');

			$config['upload_path'] = $path;
			$config['allowed_types'] = implode("|",$this->config->item('FILE_types_img'));
			$this->upload->config($config);
			
			$mediaCfg=$this->cfg->get('CFG_media_info');
			
			$bulkMap=$this->config->item('BULKUPLOAD');
			$saveFile=$this->_newName($path,$file,$rename,TRUE);
			$moved=copy($bulkMap.'/'.$file, $path.'/'.$saveFile);
			if ($moved) {
				// resize
				$resized=$this->upload->resize_image($saveFile,$path);
				// autofill
				$cfg=$mediaCfg[str_replace('site/assets/','',$path)];
				if (!isset($cfg['str_autofill']) or $cfg['str_autofill']=='bulk upload' or $cfg['str_autofill']=='both') {
					$autoFill=$this->upload->auto_fill_fields($saveFile,$path);
				}
				// fill in media table
				$userRestricted=$this->cfg->get('CFG_media_info',$path,'b_user_restricted');
				if ($userRestricted and $this->db->table_exists("cfg_media_files")) {
					$this->db->set('user',$this->user_id);
					$this->db->set('file',$map."/".$saveFile);
					$this->db->insert('cfg_media_files');
				}
				// delete original from Bulk map
				unlink($bulkMap.'/'.$file);
			}
			else {
				echo "Couldn't move: $file";
			}
		}
	}
	
	

}

?>
