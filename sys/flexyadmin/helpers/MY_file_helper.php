<?php 
/** \ingroup helpers
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/file_helper.html" target="_blank">File_helper van CodeIgniter</a>
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 * @file
 **/


/**
 * Hiermee kun je een tekstbestand lezen waarvan alleen de regels worden teruggegeven die getest worden door de meegegeven functie
 *
 * @param string $file De te laden bestandsnaam 
 * @param mixed $function De functie die elke regel test. De functie krijgt een string mee (de regel) en geeft FALSE als de regel niet moet worden meegenomen, of TRUE als dat wel moet.
 * @param string $max_length default=65536 Eventueel mee te geven maximale lengte
 * @return string Het gelezen en gefilterde bestand
 * @author Jan den Besten
 */
function read_file_filter($file,$function,$max_length=65536) {
  $out='';
  $handle = @fopen($file, "r");
  if ($handle) {
    while (($line=fgets($handle))!==false and strlen($out)<$max_length) {
      if(is_array($function))
        $keepline=$function[0]->{$function[1]}($line);
      else
        $keepline=$function($line);
      if ($keepline) $out.=$line;
    }
    if (!feof($handle)) {
      $out.="\n".'function read_file_filter() - ERROR: ';
      if (strlen($out)>=$max_length)
        $out.='MAX_LENGHT['.$max_length.'] EXCEEDED';
      else
        $out.='NOT EOF';
    }
    fclose($handle);
  }
  return $out;
}


/**
 * Kopieer een bestand
 *
 * @param string $source bronbestand
 * @param string $dest 
 * @return bool status
 * @author Jan den Besten
 */
function copy_file($source,$dest) {
	$content=@file_get_contents($source);
	if ($content === FALSE) {
		$status=false;
	}
	else {
		$copy=fopen($dest, "w");
		fwrite($copy, $content);
		fclose($copy);
		$status=true;
	}
	return $status;
}


/**
 * Geef file informatie terug in een array
 *
 * @param string $file (hele pad naar bestand) 
 * @param bool $getInfo default=TRUE 
 * @param bool $metaInfo  default=FALSE
 * @return array $info
 * @author Jan den Besten
 */
function get_full_file_info($file,$getInfo=TRUE,$metaInfo=FALSE) {
  $name=get_suffix($file,'/');
	if (is_dir($file))
    $type="dir";
  else
    $type=strtolower(get_file_extension($file));  
  $info=array(
    'path'    => $file,
    'name'    => $name,
    'type'    => $type
  );

  if (strpos($file,'sys/flexyadmin')===FALSE and strpos($file,SITEPATH.'cache')===FALSE) $file=add_assets($file);

  if ($type!='dir' AND $getInfo) {
    $info['alt']     = get_prefix($name,".");
  	$info['size']    = sprintf("%d",filesize($file)/1024);
  	$info['rawdate'] = date("Y m d",filemtime($file));
  	$info['date']    = date("j M Y",filemtime($file));
  
  	// add img dimensions
  	$CI =& get_instance();
  	if (in_array($info["type"],$CI->config->item('FILE_types_img'))) {
  		$errorReporting=error_reporting(E_ALL);
  		error_reporting($errorReporting - E_WARNING - E_NOTICE);
  		$size=@getimagesize($file);
  		error_reporting($errorReporting);
  		$info["width"]=$size[0];
  		$info["height"]=$size[1];
      // meta info
			if ($metaInfo and in_array($info['type'],array('jpg','tiff'))) {
        if (function_exists('exif_read_data')) {
          // set warnings off...
          $errorReporting=error_reporting(E_ALL);
          error_reporting($errorReporting - E_WARNING);
          $exif=exif_read_data($file);
          error_reporting($errorReporting);
          if ($exif) $info['meta']=$exif;
        }
			}
  	}
  };
  return $info;
}

/**
 * Convert image exif data to an readible string
 *
 * @param string $exif 
 * @return string
 * @author Jan den Besten
 */
function exif2string($exif) {
  $s=array2json($exif);
  return $s;
}


/**
 * Test of bestand is een verborgen bestand (beginnend met '_')
 *
 * @param string $filename Kan met of zonder pad zijn
 * @return bool TRUE als het een verborgen bestand is
 * @author Jan den Besten
 */
function is_hidden_file($filename) {
  $filename=get_suffix($filename,'/');
  return substr($filename,0,1)=='_';
}

/**
 * Test of bestand niet verborgen is (zie is_hidden_file)
 *
 * @param string $filename Kan met of zonder pad zijn
 * @return bool TRUE als het een zichtbaar bestand
 * @author Jan den Besten
 */
function is_visible_file($filename) {
  return !is_hidden_file($filename);
}


/**
 * Test of (tekst)bestanden verschillend zijn
 *
 * @param string $newfile 
 * @param string $oldfile 
 * @return bool TRUE als bestanden verschillden zijn wat inhoud betreft
 * @author Jan den Besten
 */
function is_different($newfile,$oldfile) {
  $new=file_get_contents($newfile);
  $old=file_get_contents($oldfile);
  return ($new!=$old);
}


/**
 * Test of een bestand is nieuwer dan een ander bestand
 *
 * @param string $newfile 
 * @param string $oldfile 
 * @return bool TRUE als $newfile nieuwer dan $oldfile
 * @author Jan den Besten
 */
function is_newer_than($newfile,$oldfile) {
  $newStamp=filemtime($newfile);
  $oldStamp=filemtime($oldfile);
  return ($newStamp>$oldStamp);
}


/**
 * Geeft extensie van bestandsnaam
 *
 * @param string $f 
 * @return string
 * @author Jan den Besten
 */
function get_file_extension($f) {
	$p=strrpos($f,".");
	if ($p===FALSE) return "";
	return strtolower(substr($f,$p+1));
}

/**
 * Geeft bestandsnaam zonder extensie
 *
 * @param string $f 
 * @return string
 * @author Jan den Besten
 */
function get_file_without_extension($f) {
	$p=strrpos($f,".");
	if ($p===FALSE) return $f;
	return substr($f,0,$p);
}

/**
 * Voegt een prefix toe aan bestandsnaam in een heel pad
 *
 * @param string $f Bestandsnaam met pad 
 * @param string $pre prefix
 * @return string
 * @author Jan den Besten
 */
function add_file_prefix($f,$pre) {
	$p=strrpos($f,"/");
	if ($p===false) {
		$path='';
		$file=$f;
	}
	else {
		$path=substr($f,0,$p+1);
		$file=substr($f,$p+1);
	}
	return $path.$pre.$file;
}

/**
 * Voegt suffix aan bestandsnaa (in pad) toe (voor extentie)
 *
 * @param string $f Bestandsnaam (met pad)
 * @param string $post Suffix 
 * @return string
 * @author Jan den Besten
 */
function add_file_suffix($f,$post) {
	$p=strrpos($f,"/");
	if ($p===false) {
		$path='';
		$file=$f;
	}
	else {
		$path=substr($f,0,$p+1);
		$file=substr($f,$p+1);
	}
	$ext=get_file_extension($file);
	$file=get_file_without_extension($file);
	return $path.$file.$post.'.'.$ext;
}

/**
 * Voegt een prefix en suffix toe aan bestandsnaam (in pad)
 *
 * @param string $f bestandsnaam
 * @param string $pre prefix
 * @param string $post suffix
 * @return string
 * @author Jan den Besten
 */
function add_file_presuffix($f,$pre='',$post='') {
	$p=strrpos($f,"/");
	if ($p===false) {
		$path='';
		$file=$f;
	}
	else {
		$path=substr($f,0,$p+1);
		$file=substr($f,$p+1);
	}
	$ext=get_file_extension($file);
	$file=get_file_without_extension($file);
	return $path.$pre.$file.$post.'.'.$ext;
}

/**
 * Maakt van gegeven naam een schone bestandsnaam
 * 
 * - Spaties worden vervangen door '_'
 * - Geen underscores aan begin van de naam
 * - Vervang meer dan dubbele underscores met dubbele underscores
 *
 * @param string $f 
 * @return string
 * @author Jan den Besten
 */
function clean_file_name($f) {
	$ext=get_file_extension($f);
	if (!empty($ext))
		$name=substr($f,0,strlen($f)-strlen($ext));
	else
		$name=$f;
	// replace spaces with underscores
	$name=str_replace(' ','_',$name);
	$name=clean_string($name);
	if (!empty($ext))	$name="$name.$ext";
	// remove underscores off the beginning
	$name=trim($name,'_');
	// remove more than double underscores, safer for path encode/decode
	$name=preg_replace('/(__(_+))/','__',$name);
	return $name;
}

/**
 * Test of file-types afbeeldingen zijn
 *
 * @param array $types 
 * @return bool
 * @author Jan den Besten
 */
function file_types_are_images($types) {
	$CI=&get_instance();
	return file_types_in_array($types,$CI->config->item('FILE_types_img'));
}

/**
 * Test of file-types flash-bestanden zijn
 *
 * @param array $types 
 * @return bool
 * @author Jan den Besten
 */
function file_types_are_flash($types) {
	$CI=&get_instance();
	return file_types_in_array($types,$CI->config->item('FILE_types_flash'));
}

/**
 * Test of file-types van bepaalde types zijn
 *
 * @param array $types
 * @param array $config_types types
 * @return bool
 * @author Jan den Besten
 */
 function file_types_in_array($types,$config_types) {
	$in = FALSE;
	if (!empty($types)) {
		if (is_string($types)) $types=explode(',',$types);
    $in = TRUE;
		foreach ($types as $c) {
			$in = ($in AND in_array(strtolower($c),$config_types));
		}
	}
	return $in;
}

/**
 * Geeft een array met pad en bestandsnaam
 *
 * @param string $name 
 * @return array
 * @author Jan den Besten
 */
function get_path_and_file($name) {
	$explode=explode("/",$name);
	$file=$explode[count($explode)-1];
	array_pop($explode);
	$path=implode("/",$explode);
	return array("path"=>$path,"file"=>$file);
}

?>