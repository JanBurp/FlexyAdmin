#!/usr/bin/php -q
<?php

echo "Old folder structure.\n\n";

// Check if it is the safe folder structure
if (file_exists('index.php')) {
  echo "ERROR `index.php` exists, so it is allready the old folder structure!\n\n";
}
else {
  $path = 'public';
  $files = scandir($path);
  foreach ($files as $file) {

    if (is_file($path.'/'.$file)) {
      // Just move the file
      if (rename($path.'/'.$file, $file)) {
        echo "- moved $file\n";
      }
      else {
        echo "ERROR while moving $file\n";
      }
    }
    else {
      if (is_dir($path.'/'.$file) and $file=='assets') {
        // Merge assets folders
        if (rename($path.'/'.$file, $file)) {
          echo "- Merged `assets` folders: $file\n";
        }
        else {
          echo "ERROR while merging `assets` folders.\n";
        }
      }
    }
  }
}

if ( copy('htaccess.htaccess','.htaccess') ) {
  echo "- `.htaccess` changed\n";
}

$index = file_get_contents('index.php');
$index = preg_replace( "/\bdefine\(\'SAFE_INSTALL\', true\)/u","define('SAFE_INSTALL', false)", $index);
file_put_contents('index.php',$index);
echo "- Set `SAFE_INSTALL` in `index.php` to `FALSE`\n";

$gulp = file_get_contents('gulpfile.js');
$gulp = preg_replace("/\bvar assets\s=\s\'public\/assets\'/u", "var assets = 'site/assets'", $gulp);
file_put_contents('gulpfile.js',$gulp);
echo "- Set `assets` in `gulpfile.js` to `site/assets`\n";
  
?>
