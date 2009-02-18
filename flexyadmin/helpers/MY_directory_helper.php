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


function read_map($path) {
	$files=directory_map($path);
	$prepFiles=array();
	if (!empty($files)) {
		foreach($files as $id=>$file) {
			$data=array();
			if (is_array($file)) {
				$name=$id;
				$data["type"]="dir";
				$data["alt"]=$name;
			}
			else {
				$data["type"]=strtolower(remove_prefix($file,"."));
				$name=$file;
				$data["alt"]=get_prefix($file,".");
				$data["size"]=sprintf("%d k",filesize($path."/".$name)/1024);
				$data["date"]=date("j M Y",filemtime($path."/".$name));
			}
			$data["name"]=$name;
			$data["path"]=$path."/".$name;
			$prepFiles[strtolower($name)]=$data;
		}
	}
	return $prepFiles;
}


?>