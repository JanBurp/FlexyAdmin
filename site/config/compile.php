<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Settings for admin/compile
 * 
 * Compile .less files. And combine and minify .css
 */

$config['less_files'] = array(
  'site/assets/css/text.less'   => 'site/assets/css/text.css',
  'site/assets/css/layout.less' => 'site/assets/css/layout.css'
);

$config['banner'] = "/* Minified styles. Created by FlexyAdmin (www.flexyadmin.com) ".date("Y-m-d")." */\n";

$config['css_files'] = array(
  'site/assets/css/normalize.css',
  'site/assets/css/text.css',
  'site/assets/css/layout.css',
  'site/assets/css/style.css'
);

$config['dest_file'] = 'site/assets/css/styles.min.css';

?>