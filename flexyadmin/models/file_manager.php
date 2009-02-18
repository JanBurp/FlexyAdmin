<?
/**
 * FlexyAdmin V1
 *
 * grid.php Created on 21-okt-2008
 *
 * @author Jan den Besten
 */


/**
 * Class File_manager (model)
 *
 * Handles File Manager rendering, deleting, uploading etc
 *
 */

class File_manager Extends Model {

	var $caption;
	var $files;
	var $path;
	var $map;
	var $view;
	var $fileTypes;
	var $currentId;

	var $type;

	var $tmpStart;
	var $tmpEnd;
	var $tmpCaptionStart;
	var $tmpCaptionEnd;
	var $tmpUploadStart;
	var $tmpUploadEnd;
	var $tmpFileStart;
	var $tmpFileEnd;

	function File_manager($path="",$types="",$view="list") {
		parent::Model();
		$this->init($path,$types,$view);
	}

	function init($path="",$types="",$view="list") {
		$this->set_caption($path);
		$this->files=array();
		$this->set_path($path);
		$this->set_types($types);
		$this->set_view($view);
		$this->set_type();
		$this->set_current();
	}

	function set_caption($caption="") {
		$this->caption=$caption;
	}

	function set_path($path="") {
		$this->path=$path;
		$this->map=$this->config->item('ASSETS').$path;
	}

	function set_types($types="") {
		if (!is_array($types)) $types=explode(",",$types);
		$this->fileTypes=$types;
	}

	function set_view($view="list") {
		$this->view=$view;
	}

	function set_heading($name,$heading) {
		$this->headings[$name]=$heading;
	}

	function set_type($type="html") {
		$this->type=$type;
		$func="set_".$type."_templates";
		$this->$func();
	}

	function set_current($currentId=NULL) {
		$this->currentId=$currentId;
	}


	function set_files($files=NULL,$name="") {
		if (isset($files) and !empty($files)) {
			$this->files=$files;
		}
		$this->set_caption($name);
	}

/**
 * HTML template functions
 */
	function set_html_templates() {
		$this->set_html_manager_templates();
		$this->set_html_caption_templates();
		$this->set_html_upload_templates();
		$this->set_html_file_templates();
	}

	function set_html_manager_templates($start="<div id=\"filemanager\" class=\"%s\">",$end="</div>") {
		$this->tmpStart=$start;
		$this->tmpEnd=$end;
	}

	function set_html_caption_templates($start="<div class=\"filemanager title %s\">",$end="</div>") {
		$this->tmpCaptionStart=$start;
		$this->tmpCaptionEnd=$end;
	}

	function set_html_upload_templates($start="<div class=\"filemanager fileupload %s\">",$end="</div>") {
		$this->tmpUploadStart=$start;
		$this->tmpUploadEnd=$end;
	}

	function set_html_file_templates($start="<div class=\"file %s\">",$end="</div>") {
		$this->tmpFileStart=$start;
		$this->tmpFileEnd=$end;
	}

	function tmp($tmp,$class="") {
		return str_replace("%s",$class,$tmp);
	}

/**
 * function delete_file($file)
 *
 * Delelet given file
 *
 * @param string $file
 * @return result
 */
	function delete_file($file) {
		$name=$this->map."/".$file;
		if (is_dir($name))
			$result=rmdir($name);
		else {
			$result=unlink($name);
			// check if there are other sizes...
			$big	= $this->map.$this->config->item('FILES_big_path').$file;
			if (file_exists($big)) unlink($big);
			$thumb= $this->map.$this->config->item('FILES_thumb_path').$file;
			if (file_exists($thumb)) unlink($thumb);
		}

		if ($result) {
			log_("info","[FM] delete file/dir '$name'");
		}
		else {
			log_("info","[FM] ERROR deleting file/dir '$name'");
		}
		$this->set_lists();
		return $result;
	}

/**
 * function upload_file()
 *
 * Uploads a file from information given by filemanager form
 *
 * @return result
 */
	function upload_file() {
		$config['upload_path'] = $this->map;
		$config['allowed_types'] = implode("|",$this->fileTypes);
		$this->upload->config($config);
		$ok=$this->upload->upload_file('file');
		$error=$this->upload->get_error();
		$file=$this->upload->get_file();
		if (!$ok) {
			log_("info","[FM] error while uploading: '$file' [$error]");
		}
		else {
			log_("info","[FM] uploaded: '$file'");
		}
		$this->set_lists();
		return array("error"=>$error,"file"=>$file);
	}

	function set_lists() {
		$CI =& get_instance();
		$CI->load->library("editor_lists");
		$CI->editor_lists->create_list("img");
		$CI->editor_lists->create_list("media");
	}

/**
 * function file_sort($files,$order)
 *
 * Returns sorted array of files
 *
 * @param string $files array of files and maps
 * @param string $order order type (name|type|size|date)
 * @return string	ordered array
 */
	function file_sort($f,$order="name") {
		// name
		ksort($f);
		$maps=array();
		$files=array();
		foreach($f as $id=>$name) {
			if (is_array($name)) {
				$maps[$id]=$name;
			}
			else {
				$files[$id]=$name;
			}
		}
		return array_merge($files,$maps);
	}

function thumb($attr,$index=FALSE) {
	if (is_array($attr)) {
		$a=$attr;
	}
	else {
		$a['src']=$attr;
		$a['alt']=$attr;
		$a['title']=$attr;
	}
	if (empty($a['alt'])) $a['alt']=$a['src'];

	if (!empty($a['height']) and !empty($a['width'])) {
		$size="(".$a['width']."x".$a['height'].")";
	}
	elseif (empty($a['height'])) {
		$size="(w".$a['width'].")";
	}
	elseif (empty($a['width'])) {
		$size="(h".$a['height'].")";
	}

	$thumbPath="site/assets/_thumbs";
	$thumb=$thumbPath."/".$size."__".str_replace("/","__",$a['src']);
	if (file_exists($thumb)) {
		$a['src']=$thumb;
	}
	elseif (file_exists($thumbPath)) {
		/**
		 * Create new thumb, if its bigger, else make a copy with thumb name
		 */
		$orgSize=getimagesize($a['src']);
		if ($a['width']>=$orgSize[0] and $a['height']>=$orgSize[1]) {
			$config['width'] = $orgSize[0];
			$config['height'] = $orgSize[1];
		}
		else {
			$config['width'] = $a['width'];
			$config['height'] = $a['height'];
		}
		$config['image_library'] = 'gd2';
		$config['source_image'] = $a['src'];
		$config['maintain_ratio'] = TRUE;
		$config['new_image'] = $thumb;
		$config['master_dim'] = 'auto';
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
		if (file_exists($thumb)) {
			$a['src']=$thumb;
		}
	}
	$a['width']='';
	$a['height']='';
	return img($a,$index);
}


/**
 * function _create_render_data($details)
 *
 * Returns array from files in map with info, ready for rendering.
 *
 * @return array
 */

	function _create_render_data($details=TRUE) {
		$data=array();
		$files=$this->file_sort($this->files);
		$imgTypes=$this->config->item('FILE_types_img');
		$flashTypes=$this->config->item('FILE_types_flash');
		$mp3Types=$this->config->item('FILE_types_mp3');
		$movTypes=$this->config->item('FILE_types_movies');

		$nr=1;
		foreach($files as $id=>$file) {
			$fileData=array();
			$name=$file["name"];
			$type=$file["type"];
			if (in_array($type,$this->fileTypes)) {
				$isImg=in_array($type,$imgTypes);
				$isFlash=in_array($type,$flashTypes);

				// icon
				if ($isImg) {
					$icon=div(array("class"=>"thumb")).popup_img($this->map."/".$name,img(array("src" => $this->map."/".$name, "alt"=>$name,"title"=>$name))).end_div();
				} elseif ($isFlash) {
					$icon=div(array("class"=>"flash")).flash($this->map."/".$name).end_div();
				} elseif (in_array($type,$mp3Types)) {
					$icon=div(array("class"=>"sound")).icon("sound $name").end_div();
				} elseif (in_array($type,$movTypes)) {
					$icon=div(array("class"=>"movie")).icon("movie $name").end_div();
				} elseif ($file["type"]=="dir") {
					$icon=div(array("class"=>"image")).img(array("src" => admin_assets("icons/folder.gif"),"alt"=>$name,"title"=>$name)).end_div();
				}
				// default
				else {
					$icon=div(array("class"=>"file")).icon("file $name").end_div();;
				}

				$edit=anchor(api_uri('API_filemanager_confirm',pathencode($this->path),$name),icon("delete"),array("class"=>"delete"));

				$fileData["edit"]=$edit;
				$fileData["thumb"]=$icon;
				$fileData["name"]=$name;
				// details
				if ($details) {
					$fileData["type"]=$type;
					// size types (images, flash)
					if ($isImg or $isFlash) {
						$imgSize=getimagesize($this->map."/".$name);
						$fileData["size"]="(".$imgSize[0]." x ".$imgSize[1].")";
					}
					$fileData["filesize"]=$file["size"];
					$fileData["date"]=$file["date"];
				}
				$nr++;
				$data[$name]=$fileData;
			}
		}
		return $data;
	}



/**
 * function render()
 *
 * Returns output according to template
 *
 * @param string $type html or other format
 * @param string $class extra attributes such as class
 * @return string	output
 */

	function render($type="", $class="") {
		$out="";
		$class.=" ".$this->view;

		/**
		 * Prepare file data
		 */
		$renderData=$this->_create_render_data();

		/**
		 * Header (caption and buttons)
		 */
		if (empty($this->caption))	$this->set_caption($this->path);
		// Buttons (with Viewtype switcher)
		$buttons=icon("new","upload","upload path_".$this->path);
		$buttons.=div()._div();
		// view types
		$types=$this->config->item('API_filemanager_view_types');
		foreach($types as $view) {
			$icon="list";
			if ($view!=$icon) $icon.=$view;
			if ($this->view==$view) $extra="current"; else $extra="";
			$buttons.=anchor(api_uri('API_filemanager_set_view',$view,$this->path),icon($icon,$view,$extra));
		}

		/**
		 * Decide wich view and how to render
		 */
		if ($this->view=="list") {
			// Grid
			$grid=new grid();
			$grid->set_data($renderData,$this->caption);
			$keys=array_keys(current($renderData));
			$keys=combine($keys,$keys);
			$grid->prepend_to_caption($buttons);
			$grid->set_heading("thumb","");
			$grid->set_heading("edit","");
			$grid->set_current($this->currentId);
			$out=$grid->render("html",$this->path,"grid files");
		}
		else {
			$out=div("filemanager $class caption");
			// buttons
			$out.=div(array("class"=>"buttons")).$buttons.end_div();
			// Caption
			$out.=$this->tmp($this->tmpCaptionStart,$class) . ucfirst($this->caption) . $this->tmp($this->tmpCaptionEnd);
			$out.=_div();

			if (!empty($type)) $this->set_type($type);
			$out.=$this->tmp($this->tmpStart,$class);
			// All files
			$nr=1;
			foreach($renderData as $name=>$file) {
				if ($name==$this->currentId)
					$currClass="current";
				else
					$currClass="";
				$toolbar=div(array("class"=>"toolbar")).$file["edit"].end_div();
				$cell=$file["thumb"];
				$out.=$this->tmp($this->tmpFileStart,"$class nr$nr $name $currClass"). $toolbar. $cell . div(array("class"=>"name")).$name.end_div();
				$out.=$this->tmp($this->tmpFileEnd);
				$nr++;
			}
			$out.=$this->tmp($this->tmpEnd);
		}
		log_('info',"filemaneger: rendering");
		return $out;
	}

}

?>
