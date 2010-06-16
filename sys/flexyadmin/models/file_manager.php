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
	var $showUploadButton;
	var $showDeleteButtons;

	var $type;

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
		$this->show_upload_button();
		$this->show_delete_buttons();
	}

	function set_caption($caption="") {
		$this->caption=$caption;
	}

	function set_path($path="") {
		$this->path=$path;
		$this->map=$this->config->item('ASSETS').$path;
	}

	function set_types($types="") {
		if (!is_array($types)) {
			$types=str_replace(",","|",$types);
			$types=explode("|",$types);
		}
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
	}

	function set_current($current="") {
		$this->currentId=$current;
	}

	function show_upload_button($show=TRUE) {
		$this->showUploadButton=$show;
	}

	function show_delete_buttons($show=TRUE) {
		$this->showDeleteButtons=$show;
	}

	function set_files($files=NULL,$name="") {
		if (isset($files) and !empty($files)) {
			$this->files=$files;
		}
		$this->set_caption($name);
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
			// $result=true;
			if ($result) {
				/**
				 * Check if cached thumb exists, if so, delete it
				 */
				$cachedThumb=$this->config->item('THUMBCACHE').pathencode($name);
				if (file_exists($cachedThumb)) {
					unlink($cachedThumb);
				}
				/**
				 * Check if other sizes exists and if they are hidden, delete them
				 */
				$info=$this->cfg->get('CFG_img_info',$this->path);
				$nr=1;
				$names=array();
				while (isset($info["str_prefix_$nr"])) {
					if (!empty($info["str_prefix_$nr"])) $names[]=$info["str_prefix_$nr"].$file;
					if (!empty($info["str_postfix_$nr"])) $names[]=get_file_without_extension($file).$info["str_postfix_$nr"].get_file_extension($file);
					$nr++;
				}
				$names=filter_by($names,"_");
				foreach($names as $name) {
					if (file_exists($this->map."/".$name)) unlink($this->map."/".$name);
				}
				
				/**
				 * Check if some data has this mediafile as content, remove it
				 */
				$tables=$this->db->get_tables();
				if (!empty($tables)) {
					foreach ($tables as $table) {
						$fields=$this->db->list_fields($table);
						$selectFields=array();
						$selectFields[]=pk();
						foreach ($fields as $field) {
							$pre=get_prefix($field);
							if (in_array($pre,array("txt","media","medias"))) $selectFields[]=$field;
						}
						$this->db->select($selectFields);
						$currentData=$this->db->get_result($table);
						foreach ($currentData as $row) {
							foreach ($row as $field=>$data) {
								if ($field==pk())
									$id=$data;
								else {
									$newdata=$data;
									$pre=get_prefix($field);
									switch ($pre) {
										case 'media':
											if ($data==$file) $newdata='';
											break;
										case 'medias':
											$arrData=explode('|',$data);
											foreach ($arrData as $key => $value) {if ($value==$file) unset($arrData[$key]);}
											$newdata=implode('|',$arrData);
											break;
										case 'txt':
											$preg_name=str_replace("/","\/",$name);
											// remove all img tags with this media
											$newdata=preg_replace("/<img(.*)".$preg_name."(.*)>/","",$data);
											// remove all flash objects with this media
											$newdata=preg_replace("/<object(.*)".$preg_name."(.*)<\/object>/","",$newdata);
											break;
									}
									// if changed, put in db
									if ($newdata!=$data) {
										$this->db->set($field,$newdata);
										$this->db->where(pk(),$id);
										$this->db->update($table);
									}

								}
							}
						}
					}
				}				
			}			
		}

		if ($result) {
			log_("info","[FM] delete file/dir '$name'");
		}
		else {
			log_("info","[FM] ERROR deleting file/dir '$name'");
		}
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
		$error='';
		// UPLOAD 
		$config['upload_path'] = $this->map;
		$config['allowed_types'] = implode("|",$this->fileTypes);
		// CI bug work around, make sure all imagesfiletypes are at the end
		$types=explode('|',$config['allowed_types']);
		$imgtypes=array();
		$cfg=$this->config->item('FILE_types_img');
		foreach ($types as $key=>$type) {
			if (in_array($type,$cfg)) {
				$imgtypes[]=$type;
				unset($types[$key]);
			}
		}
		$types=array_merge($types,$imgtypes);
		$config['allowed_types']=implode('|',$types);
		// trace_($config);
		// trace_($_FILES);
		//
		$this->upload->config($config);
		$ok=$this->upload->upload_file('file');
		$file=$this->upload->get_file();
		$ext=get_file_extension($file);
		$saveName=clean_file_name($file);
		if ($file!=$saveName) {
			if (rename($this->map.'/'.$file, $this->map.'/'.$saveName))
				$file=$saveName;
		}
		if (!$ok) {
			log_("info","[FM] error while uploading: '$file' [$error]");
			$error=$this->upload->get_error();
		}
		// RESIZING and AUTO FILL
		else {
			log_("info","[FM] uploaded: '$file'");
			if (in_array(strtolower($ext),$this->config->item('FILE_types_img'))) {
				// is image, maybe resizing
				$ok=$this->upload->resize_image($file,$this->map);
				if (!($ok)) {
					$error=$this->upload->get_error();
				}
			}
			if ($ok) {
				// auto fill
				// $this->upload->auto_fill_fields($file,$this->map);
			}
			
		}
		return array("error"=>$error,"file"=>$file);
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
	if (is_array($attr))
		$a=$attr;
	else {
		$a['src']=$attr;
		$a['alt']=$attr;
		// $a['title']=$attr;
	}
	$a['src']=$a['path'].'/'.$a['src'];
	if (empty($a['alt'])) $a['alt']=$a['src'];
	$a['longdesc']=$a['src'];

	$thumbPath=$this->config->item('THUMBCACHE');
	$thumb=$thumbPath.pathencode($a['src']);
	if (file_exists($thumb)) {
		$a['src']=$thumb;
	}
	elseif (file_exists($thumbPath)) {
		/**
		 * Create new thumb, if its bigger, else make a copy with thumb name
		 */
		$thumbSize=$this->config->item('THUMBSIZE');
		$config['width'] = $thumbSize[0];
		$config['height'] = $thumbSize[1];
		// $config['image_library'] = 'gd2';
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
		$pdfTypes=$this->config->item('FILE_types_pdf');
		$docTypes=$this->config->item('FILE_types_docs');
		$xlsTypes=$this->config->item('FILE_types_xls');

		$nr=1;
		// trace_($files);
		foreach($files as $id=>$file) {
			$fileData=array();
			$name=$file["name"];
			if (substr($name,0,1)!="_") {
				$type=$file["type"];
				if (in_array($type,$this->fileTypes)) {
					$isImg=in_array($type,$imgTypes);
					$isFlash=in_array($type,$flashTypes);

					// size types (images, flash)
					if ($isImg or $isFlash) {
						if (isset($file["width"]) and isset($file["height"]))
							$imgSize=array($file["width"],$file["height"]);
						else
							$imgSize=getimagesize($this->map."/".$name);
					}

					// icon
					if ($isImg) {
						// $img=$this->map."/".$name;
						// $cachedThumb=$this->config->item('THUMBCACHE').pathencode($img);
						// if (file_exists($cachedThumb)) $img=$cachedThumb;
						$icon=div(array("class"=>"thumb")).$this->thumb(array("src"=>$name,"path"=>$this->map,"alt"=>$name,"class"=>"zoom","zwidth"=>$imgSize[0],"zheight"=>$imgSize[1])).end_div();
					} elseif ($isFlash) {
						$icon=div(array("class"=>"flash")).icon("flash $name",$name,"zoom","src=\"".$this->map."/".$name."\" zwidth=\"".$imgSize[0]."\" zheight=\"".$imgSize[1]."\"")._div(); //flash($this->map."/".$name).end_div();
					} elseif (in_array($type,$mp3Types)) {
						$icon=div(array("class"=>"sound")).icon("sound $name")._div();
					} elseif (in_array($type,$movTypes)) {
						$icon=div(array("class"=>"movie")).icon("movie $name")._div();

					} elseif (in_array($type,$docTypes)) {
						$downloadFile=$this->map."/".$name;
						$icon=div(array("class"=>"doc")).'<a href="'.$downloadFile.'" target="_blank">'.icon("doc $name").'</a>'._div();
					} elseif (in_array($type,$xlsTypes)) {
						$downloadFile=$this->map."/".$name;
						$icon=div(array("class"=>"xls")).'<a href="'.$downloadFile.'" target="_blank">'.icon("xls $name").'</a>'._div();
					} elseif (in_array($type,$pdfTypes)) {
						$downloadFile=$this->map."/".$name;
						$icon=div(array("class"=>"pdf")).'<a href="'.$downloadFile.'" target="_blank">'.icon("pdf $name").'</a>'._div();

					} elseif ($file["type"]=="dir") {
						$icon=div(array("class"=>"image")).img(array("src" => admin_assets("icons/folder.gif"),"alt"=>$name,"title"=>$name))._div();
					} 
					// default
					else {
						$icon=div(array("class"=>"file")).icon("file $name")._div();;
					}
					// path
					$icon.=div('hidden path').pathencode($this->path)._div();

					$edit="";
					if ($this->showDeleteButtons)	$edit.=help(icon("edit"),lang('grid_edit')).help(icon("select"),lang('grid_select')).help(icon("delete item"),lang('grid_delete'));
					if (empty($edit)) $edit="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

					$fileData["edit"]=$edit;
					$fileData["thumb"]=$icon;
					$fileData["name"]=$name;
					// details
					if ($details) {
						// show user if needed
						if (isset($file['user'])) $fileData['user']=$file['user'];
						$fileData["type"]=$type;
						// size types (images, flash)
						if ($isImg or $isFlash) {
							$fileData["size"]="(".$imgSize[0]." x ".$imgSize[1].")";
						}
						$fileData["filesize"]=$file["size"];
						$fileData["date"]=span('hidden').$file['rawdate']._span().$file["date"];
					}
					$nr++;
					$data[$name]=$fileData;
				}
			}
		}
		// trace_($data);
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
		$current=$this->currentId;

		/**
		 * Prepare file data
		 */
		$renderData=$this->_create_render_data();

		/**
		 * Header (caption and buttons)
		 */
		if (empty($this->caption))	$this->set_caption($this->path);
		// Buttons (with Viewtype switcher)
		$buttons="";
		if ($this->showUploadButton) $buttons=help(icon("new","upload","upload path_".$this->path),lang("file_upload"));
		// view types
		$types=$this->config->item('FILES_view_types');
		foreach($types as $view) {
			$icon="list";
			if ($view!=$icon) $icon.=$view;
			if ($this->view==$view) $extra="current"; else $extra="";
			$buttons.=anchor(api_uri('API_filemanager_set_view',$view,$this->path),help(icon($icon,$view,$extra),lang("file_list_$view")) );
		}
		// delete?
		if ($this->view=='icons') {
			$buttons.=div('seperator')._div().help(icon("select all"),lang('grid_select_all')).help(icon("delete"),lang('grid_delete'));
		}
		
		$grid=new grid();
		$grid->set_data($renderData,$this->caption);
		if (!empty($renderData)) {
			$keys=array_keys(current($renderData));
			$keys=combine($keys,$keys);
		}
		$grid->prepend_to_captions($buttons);
		$grid->set_heading("edit",help(icon("select all"),lang('grid_select_all')).help(icon("delete"),lang('grid_delete'), array("class"=>"delete") ));
		$grid->set_heading("thumb","");
		$grid->set_current($current);
		$out=$grid->render("html","","grid files");

		log_('info',"filemaneger: rendering");
		return $out;
	}

}

?>
