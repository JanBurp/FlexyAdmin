<?php
if (!isset($_SERVER['SHELL'])) die('Only available from Terminal.');
echo "\e[4;32mFlexyAdmin\n\n\e[0m";

array_shift($argv);
$root = array_shift($argv);
$from = array_shift($argv);
$to   = array_shift($argv);

echo "Git remove tags '".$root.$from."' to '".$root.$to.":'\n\n";

for ($tag=$from; $tag <= $to; $tag++) { 
	$git = 'git push https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git :refs/tags/'.$root.$tag;
	echo "\e[0;32m> $git\n\e[0m";	
	exec($git);
}

  
?>
