#!/usr/bin/php -q
<?php

echo "Old folder structere.\n\n";

// Check if it is the safe folder structure
if (file_exists('index.php')) {
  echo "index.php exists, so it is allready the old folder structure!\n";
  die();
}

echo "Move files from `public` to root\n\n";

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
      echo "- merged Assets folders\n";
    }
  }
}


// echo "Use htaccess.htaccess\n";
// echo "Set `SAFE_INSTALL` in `index.php[83]` to `FALSE`\n";
// echo "Change `var assets` in `gulpfile.js[50]`\n";

  
?>
