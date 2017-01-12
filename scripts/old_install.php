<?php
if (!isset($_SERVER['SHELL'])) die('Only available from Terminal.');
echo "\e[4;32mFlexyAdmin\n\n\e[0m";

echo "Old folder structure.\n\n";

// Check if it is the safe folder structure
if (file_exists('index.php')) {
  echo "ERROR `index.php` exists, so it is allready the old folder structure!\n\n";
}
else {
  $public = 'public';
  $site = 'site';
  $files = scandir($public);
  foreach ($files as $file) {

    if (is_file($public.'/'.$file)) {
      // Just move the file
      if (rename($public.'/'.$file, $file)) {
        echo "- moved $file\n";
      }
      else {
        echo "ERROR while moving $file\n";
      }
    }
    else {
      if (is_dir($public.'/'.$file) and $file=='assets') {
        // Merge assets folders
        recurse_copy($public.'/'.$file, $site.'/'.$file);
        recurse_rmdir($public.'/'.$file);
        echo "- Merged `assets` folders: $file\n";
      }
    }
  }
}

// Choose right .htaccess
if ( copy('htaccess.htaccess','.htaccess') ) {
  echo "- `.htaccess` changed\n";
}

// Set SAFE_INSTALL in index.php
$index = file_get_contents('index.php');
$index = preg_replace( "/\bdefine\(\'SAFE_INSTALL\', true\)/u","define('SAFE_INSTALL', false)", $index);
file_put_contents('index.php',$index);
echo "- Set `SAFE_INSTALL` in `index.php` to `FALSE`\n";

// Set asstes in gulpfile.js
$gulp = file_get_contents('gulpfile.js');
$gulp = preg_replace("/\bvar assets\s=\s\'public\/assets\'/u", "var assets = 'site/assets'", $gulp);
file_put_contents('gulpfile.js',$gulp);
echo "- Set `assets` in `gulpfile.js` to `site/assets`\n";


function recurse_copy($src,$dst) { 
  $dir = opendir($src);
  if (!file_exists($dst)) @mkdir($dst); 
  while(false !== ( $file = readdir($dir)) ) { 
    if (( $file != '.' ) && ( $file != '..' )) { 
      if ( is_dir($src . '/' . $file) ) { 
        recurse_copy($src . '/' . $file,$dst . '/' . $file); 
      } 
      else { 
        copy($src . '/' . $file,$dst . '/' . $file); 
      } 
    } 
  } 
  closedir($dir);
}

function recurse_rmdir($dir) { 
  $files = array_diff(scandir($dir), array('.','..')); 
  foreach ($files as $file) { 
    (is_dir("$dir/$file")) ? recurse_rmdir("$dir/$file") : unlink("$dir/$file"); 
  }
  return rmdir($dir); 
} 
  
?>
