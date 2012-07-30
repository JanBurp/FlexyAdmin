<?

/**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/directory_helper.html" target="_blank">Directory_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * @link http://codeigniter.com/user_guide/helpers/directory_helper.html
 */

/**
 * Telt aantal bestanden in meegegeven map
 *
 * @param string $path 
 * @param bool $recursive[FALSE]
 * @param int $counter[0] Als optie kun je teller een startwaarde meegeven
 * @return int
 * @author Jan den Besten
 */
function count_files($path,$recursive=FALSE,$counter=0) {
	static $counter;
	if(is_dir($path)) {
		if($dh = opendir($path)) {
			while(($file = readdir($dh)) !== false) {
				if ($file!='.' and $file!='..' and substr($file,0,1)!='.') {
					$counter=(is_dir($path."/".$file)) ? count_files($path.'/'.$file,$types,$recursive,$counter) : $counter+1;
				}
			}
			closedir($dh);
		}
	}
	return $counter;
}

/**
 * Leest alle bestanden in meegegeven map en geeft per bestand een array met diverse info als resultaat
 *
 * @param string $path 
 * @param string $types['']
 * @param bool $recursive[FALSE] 
 * @param bool $getInfo[TRUE]
 * @param bool $getMetaData[FALSE] als TRUE dan worden ook metadata van afbeeldingen meegegeven
 * @return array een multidimensinale array: een lijst van de gevonden bestanden met per bestand een array met info
 * @author Jan den Besten
 */
function read_map($path,$types='',$recursive=FALSE,$getInfo=TRUE,$getMetaData=FALSE) {
	if (!empty($types) and !is_array($types)) $types=explode(',',$types);
	if ($getInfo) $CI =& get_instance();
	$files=array();
	if(is_dir($path)) {
		if($dh = opendir($path)) {
			while(($file = readdir($dh)) !== false) {
				if ($file!='.' and $file!='..' and substr($file,0,1)!='.') {
					$data=array();
					$data['path']=$path.'/'.$file;
					if (is_dir($data['path'])) {
						$data['name']=$file;
						$data['type']="dir";
						// $data['alt']=$file;
						if ($recursive) $data["."]=read_map($path."/".$data['name'],$types,$recursive);
					}
					else {
						$data['name']=$file;
						$data['type']=strtolower(get_file_extension($file));
						$data['alt']=get_prefix($file,".");
						if ($getInfo) {
							$data['size']=sprintf("%d k",filesize($data['path'])/1024);
							$data['rawdate']=date("Y m d",filemtime($data['path']));
							$data['date']=date("j M Y",filemtime($data['path']));
							if (in_array($data["type"],$CI->config->item('FILE_types_img'))) {
								// add img dimensions
  							$errorReporting=error_reporting(E_ALL);
  							error_reporting($errorReporting - E_WARNING - E_NOTICE);
								$size=getimagesize($path."/".$file);
  							error_reporting($errorReporting);
								$data["width"]=$size[0];
								$data["height"]=$size[1];
							}
						}
						if ($getMetaData and in_array($data['type'],array('jpg','tiff'))) {
							// set warnings off...
							$errorReporting=error_reporting(E_ALL);
							error_reporting($errorReporting - E_WARNING);
							$exif=exif_read_data($path.'/'.$file);
							error_reporting($errorReporting);
							if ($exif) $data['meta']=$exif;
						}
						
					}
					if (empty($types) or in_array($data['type'],$types))	$files[strtolower($data['name'])]=$data;
				}
			}
			closedir($dh);
		}
	}
	return $files;
}

/**
 * Maakt de meegegeven directory leeg
 *
 * @param string $dir 
 * @param bool $remove[FALSE] als TRUE dan wordt ook de map zelf en alle submappen verwijderd
 * @param bool $remove_hidden[FALSE]
 * @return void
 * @author Jan den Besten
 */
function empty_map($dir,$remove=false,$remove_hidden=false) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != ".." && ($remove_hidden and substr($object,0,1)!='.')) {
        if (filetype($dir."/".$object) == "dir") empty_map($dir."/".$object,true); else unlink($dir."/".$object);
      }
    }
    reset($objects);
		if ($remove) rmdir($dir);
  }
}

/**
 *
 * @param array $files 
 * @param bool $tree 
 * @param string $path 
 * @return array
 * @author Jan den Besten
 */
function clean_file_list($files,$tree=FALSE,$path='') {
	$clean=array();
	foreach ($files as $key => $value) {
		if ($value['type']=='dir') {
			$sub=clean_file_list($value['.'], $tree, trim($path.'/'.$key,'/') );
			if ($tree)
				$clean[]=$sub;
			else
				$clean=array_merge($clean,$sub);
		}
		else {
			$clean[]=trim($path.'/'.$key,'/');
		}
	}
	return $clean;
}


?>