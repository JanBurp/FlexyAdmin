<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * -------------------------------------------------------------------------
 * Search Replace
 *
 * Set Search Replace config
 *
 * -------------------------------------------------------------------------
 */

$config['Cleanup HTML (remove double tags etc.)'] = array(
  'search'  => "(<b[^>]*>\s<\/b>|<strong[^>]*>\s<\/strong>|<i[^>]*>\s<\/i>|<em[^>]*>\s<\/em>|<h\d[^>]*>\s<\/h\d>|<span[^>]*>\s<\/span>)",
  'replace' => "",
  'regex'   => true,
  'fields'  => '*.txt_text'
);

$config['Remove styling (bold,italic,span) from headings (h1..h7)'] = array(
  'search'  => "(<h(\d)([^>]*)>)(<(strong|b|em|i|span)[^>]*>)+([^<]*)(<\/(strong|b|em|i|span)>)+(<\/h\d>)",
  'replace' => "<h$2>$6$9",
  'regex'   => true,
  'fields'  => '*.txt_text'
);


?>