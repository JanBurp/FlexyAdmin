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
 * @param bool $resultAsTree[TRUE] als TRUE dan wordt het resultaat ook als een tree weergegeven, anders als een platte array met als key het hele pad
 * @return array een multidimensinale array: een lijst van de gevonden bestanden met per bestand een array met info
 * @author Jan den Besten
 */
function read_map($path,$types='',$recursive=FALSE,$getInfo=TRUE,$getMetaData=FALSE,$resultAsTree=TRUE) {
	if (!empty($types) and !is_array($types)) $types=explode(',',$types);
	if ($getInfo) $CI =& get_instance();
	$files=array();
	if(is_dir($path)) {
		if($dh = opendir($path)) {
			while(($file = readdir($dh)) !== false) {
				if ($file!='.' and $file!='..' and substr($file,0,1)!='.') {

					$data=get_full_file_info($path.'/'.$file, $getInfo, $getMetaData);

          if ($data['type']=='dir') {
						if ($recursive) {
              $subfiles=read_map($path."/".$data['name'],$types,$recursive,$getInfo,$getMetaData,$resultAsTree);
              if ($resultAsTree) {
                $data["."]=$subfiles;
              }
              else {
                $files=array_merge($files,$subfiles);
              }
						}
					}
					if (empty($types) or in_array($data['type'],$types)) {
            if ($resultAsTree) {
              $files[strtolower($data['name'])]=$data;
            }
            else {
              $files[strtolower($data['path'])]=$data;
            }
					}
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
      if (filetype($dir."/".$object) == "dir") {
        if ($object!='.' && $object!='..') empty_map($dir."/".$object, $remove, $remove_hidden);
      }
      else {
        if (substr($object,0,1)!='.' or $remove_hidden) {
          unlink($dir."/".$object);
        }
      }
    }
    reset($objects);
		if ($remove) {
      if (substr($object,0,1)!='.' or $remove_hidden) rmdir($dir);
    }
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



/**
 * Copy a directory (also creating directories if needed)
 *
 * @param string $source 
 * @param string $destination 
 * @return void
 */
function copy_directory( $source,$destination, $exclude=array('/.') ) {
  if ( is_dir( $source ) ) {
    @mkdir( $destination );
    $directory = dir( $source );
    while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
      if ( $readdirectory == '.' || $readdirectory == '..' ) {
        continue;
      }
      $PathDir = $source . '/' . $readdirectory;
      if ( is_dir( $PathDir ) ) {
        if (!has_string($exclude,$PathDir)) copy_directory( $PathDir, $destination . '/' . $readdirectory, $exclude );
        continue;
      }
      if (!has_string($exclude,$PathDir)) copy( $PathDir, $destination . '/' . $readdirectory );  
    }
    $directory->close();
  }
  else {
    if (!has_string($exclude,$source)) copy( $source, $destination );
  }
}


?>