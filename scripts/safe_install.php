<?php
if (!isset($_SERVER['SHELL'])) die('Only available from Terminal.');
echo "\e[4;32mFlexyAdmin\n\n\e[0m";

echo "Safe folder structure.\n\n";

// Check if it is the safe folder structure
// if (!file_exists('index.php')) {
if (false) {
  die("ERROR `index.php` does not exists, so it is allready the safe folder structure!\n\n");
}
else {
  $public = 'public';
  $site = 'site';
  $root_files = array(
    '.htaccess',
    'htaccess.htaccess',
    'htaccess_safe.htaccess',
    'temp.htaccess',
    'index_temp.html',
    'index.php',
    'robots.txt',
    'sitemap.xml',
  );
  $public_assets = array(
    'css',
    'fonts',
    'js',
    'less-bootstrap',
    'less-default',
  );
  
  foreach ($root_files as $file) {
    // Just move the file
    if (rename($file, $public.'/'.$file)) {
      echo "- moved $file\n";
    }
    else {
      echo "ERROR while moving $file\n";
    }
  }
  
  @mkdir($public.'/assets');
  foreach ($public_assets as $asset) {
    recurse_copy( $site.'/assets/'.$asset, $public.'/assets/'.$asset );
    recurse_rmdir( $site.'/assets/'.$asset );
    echo "- moved assets/$asset\n";
  }
  
}

// Choose right .htaccess
if ( copy($public.'/htaccess_safe.htaccess',$public.'/.htaccess') ) {
  echo "- `.htaccess` changed\n";
}

// Set SAFE_INSTALL in index.php
$index = file_get_contents($public.'/index.php');
$index = preg_replace( "/\bdefine\(\'SAFE_INSTALL\', false\)/u","define('SAFE_INSTALL', true)", $index);
file_put_contents($public.'/index.php',$index);
echo "- Set `SAFE_INSTALL` in `index.php` to `TRUE`\n";

// Set asstes in gulpfile.js
$gulp = file_get_contents('gulpfile.js');
$gulp = preg_replace("/\bvar assets\s=\s\'site\/assets\'/u", "var assets = 'public/assets'", $gulp);
file_put_contents('gulpfile.js',$gulp);
echo "- Set `assets` in `gulpfile.js` to `public/assets`\n";


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
