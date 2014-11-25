<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * - Compile .less files to .css files
 * - Combine .css files to one .css file
 * - Minify the one .css file and add a banner above
 * 
 * - Combine .js files to one .js file
 * - Uglify the .js file
 * 
 * NB Dit is een beperkte builder, als je meer wilt gebruik dan gulp: zeker bij gebruik van bootstrap.
 * Stel dan alle bestanden in in gulpfile.js
 */


/**
 * Check bij elke pageload of er een nieuwe build gemaakt moet worden. Alleen lokaal.
 * 
 * Mogelijke opties:
 * - 'dev'  - (default) build when developing and some source file is changed
 * - 'css'  - same as 'dev' but only build css (when changed)
 * - 'js'   - same as 'dev' but only build js (when changed)
 * - true   - always build (even on production site and even when no file is changed)
 * - false  - never build
 * 
 * Adds the variable $_build to $this->site (true if build)
 */
$config['watch'] = 'dev';
$config['add_report'] = false; // Adds _build_report to $this->site


/**
 * An array of less files to compile to css
 */
$config['less_files'] = array(
  'site/assets/less-default/text.less'   => 'site/assets/css/text.css',
  'site/assets/less-default/layout.less' => 'site/assets/css/layout.css'
);

/**
 * An array of css files (compiled and others) to combine to one file, in given order.
 * Instead of an array set 'auto' to find .css files used in the main view (views/site.php)
 */
$config['css_files'] = array(
  'site/assets/css/normalize.css',
  'site/assets/css/text.css',
  'site/assets/css/layout.css'
);

/**
 * Resulting css file, and banner to include
 */
$config['banner'] = "/* Minified styles. Created by FlexyAdmin (www.flexyadmin.com) on {date} in {execution_time} secs. */\n";
$config['dest_file'] = 'site/assets/css/styles.min.css';


/**
 * Javascript files to combine & uglify
 * Instead of an array set 'auto' to find .js files used in the main view (views/site.php)
 */
$config['js_files'] = array(
  'site/assets/js/rem.min.js',
  'site/assets/js/jquery.min.js',
  'site/assets/js/site.js'
);

/**
 * Banner
 */
$config['js_banner'] = "/* Minified Javascripts. Created by FlexyAdmin (www.flexyadmin.com) on {date} in {execution_time} secs. */\n";
/**
 * Javascript destination file
 */
$config['js_dest_file'] = 'site/assets/js/scripts.min.js';

?>