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

	function Bulkupload() {
		parent::AdminController();
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
				$gridFiles=array();
				$mediaCfg=$this->cfg->get('CFG_media_info');

				// is form submitted?
				$path=$this->input->post('path');
				
				// Show form?
				if (empty($path)) {
					// create form
					$this->load->model('form');
					$form=new form($this->config->item('API_bulk_upload'));
					$options=array();
					foreach ($mediaCfg as $info) {
						$options[$info['path']]=$info['path'];
					}
					$data=array(	"path"				=> array("label"=>'Move to:','type'=>'dropdown','options'=>$options)
												// "do_resize"		=> array('type'=>'checkbox','value'=>'1'),
												// 		"do_autofill"	=> array("label"=>'Auto fill fields','type'=>'checkbox','value'=>'1')								
						);
					$form->set_data($data,'Bulk upload settings');
					$this->_add_content($form->render());			
					$this->_add_content(p().nbs()._p().p().nbs()._p());
				}
				
				
				// Show and move files
				if (!empty($path)) {
					$path=assets($path);
					$this->load->library('upload');
					$config['upload_path'] = $path;
					$config['allowed_types'] = implode("|",$this->config->item('FILE_types_img'));
					$this->upload->config($config);

					foreach ($files as $name => $file) {
						$gridFiles[$name]['File']=$file['name'];
						$moved=FALSE;
						$resized=FALSE;
						$autoFill=FALSE;
						// move
						$ext=get_file_extension($file['name']);
						$saveFile=clean_string(str_replace(".$ext","",$file['name'])).".$ext";
						$moved=copy($bulkMap.'/'.$file['name'],$path.'/'.$saveFile);
						if ($moved) {
							// resize
							$resized=$this->upload->resize_image($saveFile,$path);
							// autofill
							$autoFill=$this->upload->auto_fill_fields($saveFile,$path);
							// delete original from Bulk map
							unlink($bulkMap.'/'.$file['name']);
						}
						if ($moved)
							$gridFiles[$name]['Moved']=icon('yes');
						else
							$gridFiles[$name]['Moved']=icon('no');
						if ($resized)
							$gridFiles[$name]['Resized']=icon('yes');
						else
							$gridFiles[$name]['Resized']=icon('no');
						if ($autoFill)
							$gridFiles[$name]['Auto fill']=icon('yes');
						else
							$gridFiles[$name]['Auto fill']=icon('no');
					}
				
				}
				else {
					foreach ($files as $name => $file) {
						$gridFiles[$name]=array('File'=>$file['name'],'Moved'=>icon('no'),'Resized'=>icon('no'),'Auto fill'=>icon('no'));
					}
				}

				$this->load->model("grid");
				$grid=new grid();			
				$grid->set_data($gridFiles,'Files');			
				$renderData=$grid->render("html",'bulkupload',"grid");
				$this->_add_content($this->load->view("admin/grid",$renderData,true));

			}
		}
		$this->_show_type("form");
		$this->_show_all();
	}

}

?>
