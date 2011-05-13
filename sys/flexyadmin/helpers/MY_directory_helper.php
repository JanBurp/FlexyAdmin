<?
/**
 * FlexyAdmin V1
 *
 * MY_directory_helper.php
 *
 * @author Jan den Besten
 */


function read_map($path,$types="",$recursive=FALSE, $getInfo=TRUE, $getMetaData=FALSE) {
	if (!is_array($types) and !empty($types)) $types=explode(",",$types);
	$files=directory_map($path);
	$prepFiles=array();
	if (!empty($files)) {
		foreach($files as $id=>$file) {
			$data=array();
			if (is_array($file)) {
				$name=$id;
				$data["name"]=$name;
				$data["type"]="dir";
				$data["alt"]=$name;
				if ($recursive) {
					$data["."]=read_map($path."/".$name,$types,$recursive);
				}
			}
			else {
				$name=$file;
				$data["name"]=$name;
				$data["type"]=strtolower(get_file_extension($file));
				$data["alt"]=get_prefix($file,".");
				if ($getInfo) {
					$data["size"]=sprintf("%d k",filesize($path."/".$name)/1024);
					$data["rawdate"]=date("Y m d",filemtime($path."/".$name));
					$data["date"]=date("j M Y",filemtime($path."/".$name));
					$CI =& get_instance();
					if (in_array($data["type"],$CI->config->item('FILE_types_img'))) {
						// add img dimensions
						$size=getimagesize($path."/".$file);
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
			$data["path"]=$path."/".$name;
			if (empty($types) or (in_array($data["type"],$types)))
				$prepFiles[strtolower($name)]=$data;
		}
	}
	return $prepFiles;
}

function empty_map($dir,$remove=false) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir") empty_map($dir."/".$object,true); else unlink($dir."/".$object);
      }
    }
    reset($objects);
		if ($remove) rmdir($dir);
  }
}


?>