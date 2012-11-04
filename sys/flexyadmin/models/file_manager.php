<?

/**
 * Met dit model kun je bestanden tonen (in een grid), uploaden, verwijderen etc.
 *
 * @package default
 * @author Jan den Besten
 */
 
class File_manager Extends CI_Model {

  /**
   * Titel in het grid
   *
   * @var string
   */
  private $caption;
  
  /**
   * Bestanden
   *
   * @var array
   */
  private $files;
  
  /**
   * Huidig pad
   *
   * @var string
   */
  private $path;
  
  /**
   * Map
   *
   * @var string
   */
  private $map;
  
  /**
   * View
   *
   * @var string
   */
  private $view;
  
  /**
   * Pagination
   *
   * @var string
   */
  private $pagin;
  
  /**
   * Bestandsoorten
   *
   * @var string
   */
  private $fileTypes;
  
  /**
   * id
   *
   * @var int
   */
  private $currentId;
  
  /**
   * Moet upload knop getoont worden?
   *
   * @var bool
   */
  private $showUploadButton;
  
  /**
   * Moet delete knop getoont worden?
   *
   * @var string
   */
  private $showDeleteButtons;
  
  /**
   * Soort output
   *
   * @var string
   */
  private $type;

  /**
   * @ignore
   */
	public function __construct($path="",$types="",$view="list") {
		parent::__construct();
		$this->init($path,$types,$view);
	}

	/**
	 * Initialiseer
	 *
	 * @param string $path['']
	 * @param string $types[''] Bestandstypen
	 * @param string $view['list]
	 * @return object $this
	 * @author Jan den Besten
	 */
  public function init($path="",$types="",$view="list") {
		$this->set_caption($path);
		$this->files=array();
		$this->set_path($path);
		$this->set_types($types);
		$this->set_view($view);
		$this->set_type();
		$this->set_current();
		$this->show_upload_button();
		$this->show_delete_buttons();
    return $this;
	}

  /**
   * Stel titel in
   *
   * @param string $caption[''] 
   * @return object $this
   * @author Jan den Besten
   */
	public function set_caption($caption="") {
		$this->caption=$caption;
    return $this;
	}

  /**
   * Stel pad in
   *
   * @param string $path['']
   * @return object $this
   * @author Jan den Besten
   */
	public function set_path($path="") {
		$this->path=$path;
		$this->map=$this->config->item('ASSETS').$path;
    return $this;
	}

  /**
   * Stel bestandstypen in
   *
   * @param string $types['']
   * @return object $this
   * @author Jan den Besten
   */
	public function set_types($types="") {
		if (!is_array($types)) {
			$types=str_replace(",","|",$types);
			$types=explode("|",$types);
		}
		$this->fileTypes=$types;
    return $this;
	}

  /**
   * Stel viewsoort in
   *
   * @param string $view['list']
   * @return object $this
   * @author Jan den Besten
   */
	public function set_view($view="list") {
		$this->view=$view;
    return $this;
	}

  /**
   * Stel heading in
   *
   * @param string $name Welke heading?
   * @param string $heading
   * @return object $this
   * @author Jan den Besten
   */
	public function set_heading($name,$heading) {
		$this->headings[$name]=$heading;
    return $this;
	}

  /**
   * Type output
   *
   * @param string $type 
   * @return object $this
   * @author Jan den Besten
   * @ignore
   * @internal
   */
	public function set_type($type="html") {
		$this->type=$type;
    return $this;
	}

  /**
   * Stel huidig bestand in
   *
   * @param string $current 
   * @return object $this
   * @author Jan den Besten
   */
	public function set_current($current="") {
		$this->currentId=$current;
    return $this;
	}

  /**
   * Moet upload button getoond worden?
   *
   * @param bool $show[TRUE]
   * @return object $this
   * @author Jan den Besten
   */
	public function show_upload_button($show=TRUE) {
		$this->showUploadButton=$show;
    return $this;
	}

  /**
   * Moet delete buttons getoond worden?
   *
   * @param bool $show[TRUE]
   * @return object $this
   * @author Jan den Besten
   */
	public function show_delete_buttons($show=TRUE) {
		$this->showDeleteButtons=$show;
    return $this;
	}

	/**
	 * Geeft bestanden
	 *
	 * @param array $files[NULL]
	 * @param string $name[''] Geef ook eventueel de titel mee
	 * @return object $this
	 * @author Jan den Besten
	 */
  public function set_files($files=NULL,$name="") {
		if (isset($files) and !empty($files)) {
			$this->files=$files;
		}
		$this->set_caption($name);
    return $this;
	}

	/**
	 * Verwijder meegegeven bestand, en alle eventuele varianten (thumbs etc)
	 *
	 * @param string $file 
	 * @return array $result
	 * @author Jan den Besten
	 */
  public function delete_file($file) {
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
					if (!empty($info["str_suffix_$nr"])) $names[]=get_file_without_extension($file).$info["str_suffix_$nr"].get_file_extension($file);
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
						$selectFields[]=PRIMARY_KEY;
						foreach ($fields as $field) {
							$pre=get_prefix($field);
							if (in_array($pre,array("txt","media","medias"))) $selectFields[]=$field;
						}
						$this->db->select($selectFields);
						$currentData=$this->db->get_result($table);
						foreach ($currentData as $row) {
							foreach ($row as $field=>$data) {
								if ($field==PRIMARY_KEY)
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
										$this->db->where(PRIMARY_KEY,$id);
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
   * Upload file van meegegeven file-veld, ook worden meteen thumbs etc. aangemaakt voor geuploade bestanden en wordt de minimale omvang gecheckt
   *
   * @param string $file_field 
   * @return array $result
   * @author Jan den Besten
   */
	public function upload_file($file_field='file') {
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
		$ok=$this->upload->upload_file($file_field);
		$file=$this->upload->get_file();
    // strace_($file);
		$ext=get_file_extension($file);
		if (!$ok) {
			log_("info","[FM] error while uploading: '$file' [$error]");
			$error=$this->upload->get_error();
		}
		// RESIZING and AUTO FILL
		else {
			log_("info","[FM] uploaded: '$file'");
			$saveName=clean_file_name($file);
			if ($file!=$saveName) {
				if (rename($this->map.'/'.$file, $this->map.'/'.$saveName))
					$file=$saveName;
			}

			// is image?
      // strace_($ext);
			if (in_array(strtolower($ext),$this->config->item('FILE_types_img'))) {
        // check minimal size
        if ($this->upload->check_size($file,$this->map)) {
          // if ok: resizing
  				$ok=$this->upload->resize_image($file,$this->map);
  				if (!($ok)) {
  					$error=$this->upload->get_error();
  				}
        }
        else {
          $this->delete_file($file);
          $error=langp('upload_img_too_small',$file);
        }
			}
			if ($ok) {
				// auto fill
				// $this->upload->auto_fill_fields($file,$this->map);
			}
			
		}
		return array("error"=>$error,"file"=>$file, 'extra_files'=>$this->upload->get_created_files() );
	}

  /**
   * Geeft gesorteerde array van bestandsnamen terug
   *
   * @param array $files files/bestanden
   * @param string $order hoe moet er worden gesorteerd?: (name|type|size|date)
   * @return array
   */
	public function file_sort($f,$order="name") {
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

  
  /**
   * Geeft HTML img tag terug voor een Thumbnail, als thumbnail niet bestaat dan wordt die aangemaakt
   *
   * @param mixed $attr/$src
   * @param bool $index[FALSE]
   * @return string
   * @author Jan den Besten
   */
  public function thumb($attr,$index=FALSE) {
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
   * @internal
   * @ignore
   */
	private function _create_render_data($details=TRUE) {
		$data=array();
		$files=$this->files;
    $used_field=lang('USED');
    
		$imgTypes=$this->config->item('FILE_types_img');
		$flashTypes=$this->config->item('FILE_types_flash');
		$mp3Types=$this->config->item('FILE_types_mp3');
		$movTypes=$this->config->item('FILE_types_movies');
		$pdfTypes=$this->config->item('FILE_types_pdf');
		$docTypes=$this->config->item('FILE_types_docs');
		$xlsTypes=$this->config->item('FILE_types_xls');

		$nr=1;
		// trace_($files);
		$showImgSize=false;
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
						else {
							$errorReporting=error_reporting(E_ALL);
							error_reporting($errorReporting - E_WARNING - E_NOTICE);
              $imgSize=getimagesize($this->map."/".$name);
							error_reporting($errorReporting);
            }
							
					}

					// icon
					if ($isImg) {
						// $img=$this->map."/".$name;
						// $cachedThumb=$this->config->item('THUMBCACHE').pathencode($img);
						// if (file_exists($cachedThumb)) $img=$cachedThumb;
						$icon=div(array("class"=>"thumb")).$this->thumb(array("src"=>$name,"path"=>$this->map,"alt"=>$name,"title"=>$name,"class"=>"zoom","zwidth"=>$imgSize[0],"zheight"=>$imgSize[1])).end_div();
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
          // if ($this->showDeleteButtons)  $edit.=help(icon("edit"),lang('grid_edit')).help(icon("select"),lang('grid_select')).help(icon("delete item"),lang('grid_delete'));
					if ($this->showDeleteButtons)	{
            $uri=api_uri('API_filemanager_edit',pathencode($this->path).'/'.$name);
            $edit.= anchor( $uri, help(icon("edit"),lang('grid_edit')), array("class"=>"edit"));
            $edit.= help(icon("select"),lang('grid_select')).help(icon("delete item"),lang('grid_delete'));
					}
					if (empty($edit)) $edit="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

					$fileData["edit"]=$edit;
					$fileData["thumb"]=$icon;
          
          if (isset($file['str_title'])) $fileData['str_title']=$file['str_title'];
          
					$fileData["name"]=$name;
					// details
					if ($details) {
						// show user if needed
						if (isset($file['user'])) $fileData['user']=$file['user'];
						$fileData["type"]=$type;
						// size types (images, flash)
						if ($isImg or $isFlash) {
							$fileData["size"]="(".$imgSize[0]."&nbsp;x&nbsp;".$imgSize[1].")";
							$showImgSize=true;
						}
						$fileData["filesize"]=$file["size"];
						$fileData["date"]=span('hidden').$file['rawdate']._span().str_replace(' ','&nbsp;',$file["date"]);
					}

          // Check if file is used somewhere
          if (isset($file[$used_field])) {
            if ($file[$used_field])
              $fileData[$used_field]=icon('yes');
            else
              $fileData[$used_field]=icon('no');
          }
          
					$nr++;
					$data[$name]=$fileData;
				}
			}
		}
		if ($showImgSize) {
			foreach ($data as $name => $info) {
				if (!isset($info['size'])) {
					$newInfo=array();
					foreach ($info as $key => $value) {
						$newInfo[$key]=$value;
						if ($key=='type') $newInfo['size']='&nbsp;';
					}
					$data[$name]=$newInfo;
				}
			}
		}
		return $data;
	}


  /**
   * Zet pagination
   *
   * @param mixed $pagin
   * @return object $this
   * @author Jan den Besten
   */
	public function set_pagination($pagin=NULL) {
		$this->pagin=$pagin;
    return $this;
	}

  /**
   * Geeft grid met bestanden (en knoppen)
   *
   * @param string $type['']
   * @param string $class['']
   * @return string	output
   */
	public function render($type="", $class="") {
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
		
		$pagination=$this->cfg->get("CFG_media_info",$this->path,'b_pagination');
		if ($pagination) $pagination=$this->cfg->get('cfg_configurations','int_pagination');
		$offset=0;

		$grid=new grid();
		$grid->set_current($current);

		if ($pagination) {
			$pagination=array('base_url'=>api_url('API_filemanager_view',$this->path),'per_page'=>$pagination,'total_rows'=>count($renderData));
			$pagination=array_merge($pagination,$this->pagin);
			$grid->set_pagination($pagination);
			$renderData=array_slice($renderData,$pagination['offset'],$pagination['per_page'],true);
		}
		// strace_($pagination);
		
		$grid->set_data($renderData,$this->caption);
		$grid->set_order($pagination['order']);
		$grid->set_search($pagination['search']);
		
		if (!empty($renderData)) {
			$keys=array_keys(current($renderData));
			$keys=array_combine($keys,$keys);
		}
		$grid->prepend_to_captions($buttons);
		$grid->set_heading("edit",help(icon("select all"),lang('grid_select_all')).help(icon("delete"),lang('grid_delete'), array("class"=>"delete") ));
		$grid->set_heading("thumb","");
    $firstRow=current($renderData);
    if (isset($firstRow['str_title'])) $grid->set_heading('str_title',$this->ui->get('str_title'));
		$out=$grid->render("html","","grid files");

		log_('info',"filemaneger: rendering");
		return $out;
	}

}

?>
