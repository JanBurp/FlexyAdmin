<?
/**
 * FlexyAdmin V1
 *
 * MY_directory_helper.php
 *
 * @author Jan den Besten
 *
 * adds some functions to the array helper
 *
 */


function read_map($path,$types="",$recursive=FALSE) {
	if (!is_array($types) and !empty($types)) $types=explode(",",$types);
	$files=directory_map($path);
	$prepFiles=array();
	if (!empty($files)) {
		foreach($files as $id=>$file) {
			$data=array();
			if (is_array($file)) {
				$name=$id;
				$data["type"]="dir";
				$data["alt"]=$name;
				if ($recursive) {
					$data["."]=read_map($path."/".$name,$types,$recursive);
				}
			}
			else {
				$data["type"]=strtolower(get_file_extension($file));
				$name=$file;
				$data["alt"]=get_prefix($file,".");
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
			$data["name"]=$name;
			$data["path"]=$path."/".$name;
			if (empty($types) or (in_array($data["type"],$types)))
				$prepFiles[strtolower($name)]=$data;
		}
	}
	return $prepFiles;
}


?>