#!/usr/bin/php -q
<?php

echo "Old folder structer.\n\n";

// Check if it is the safe folder structure
if (file_exists('.htaccess')) {
  echo ".htaccess exists, so it is allready the old folder structure!\n";
  die();
}

echo "Move files from `public` to root\n";


// echo "Merge `public/assets` to `site/assets`\n";
// echo "Use htaccess.htaccess\n";
// echo "Set `SAFE_INSTALL` in `index.php[83]` to `FALSE`\n";
// echo "Change `var assets` in `gulpfile.js[50]`\n";

  
?>
