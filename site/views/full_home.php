<?
/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin 2009
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2009, Jan den Besten
 * @link		http://flexyadmin.com
 */

// ------------------------------------------------------------------------

/**
 * FlexyAdmin home.php
 *
 * This file is the main frontend file of the site.
 *
 * You can use all the funcionality from Code Igniter and the special FlexyAdmin classes and helper functions.
 * But you are encouriged to do this all in controller.php and leave this file as a simple html file with just some php to put the content.
 * 
 * 
 * ========= Variables which are set =============
 * 
 * 	$assets						Assets folder
 *  $admin_assets			FlexyAdmin assets folder (which holds jQuery)
 * 
 *  Set in tbl_site:
 * 	$title						
 * 	$author
 *  $url
 * 	$email
 *  $description
 * 	$keywords
 * 
 *  Extra: 
 *  $uri							Url of the page that is called (a string)
 *  $uri_array        Url of the page diveded in pieces and stored in this array
 * 
 * 
 * ========= Some functions / objects ================
 * 
 * $this->db->...							Database class, use this to fetch your information (http://codeigniter.com/user_guide/database/index.html)
 * $this->uri->...						Uri class, use this to fetch information out of the URL (http://codeigniter.com/user_guide/libraries/uri.html)
 * $this->show($view,$data)		Show a view in the folder views with given array ($data) (see CodeIgniters $this->load->view($view, $data);  http://codeigniter.com/user_guide/general/views.html)
 * trace_()										Shows variable and its type.
 * 
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf8" />
	<base href="<?=base_url()?>" />
	<title><?=ascii_to_entities($title);?></title>
	<link rel="shortcut icon" href="<?=$assets?>/img/favicon.ico" type="image/x-icon" />

	<meta http-equiv="imagetoolbar" content="no" />
	<meta http-equiv="content-language" content="nl" />
	<meta name="Description" content="<?=ascii_to_entities($description);?>" />
	<meta name="Keywords" content="<?=$keywords;?>" />
	<meta name="Author" content="<?=$author;?>" />
	<meta name="web_author" content="Jan den Besten" />
	<meta name="copyright" content="Jan den Besten,<?=$author;?>" />
	<meta name="robots" content="index,follow" />
	<meta name="revisit-after" content="7 days" />

	<script language="javascript" type="text/javascript" src="<?=$admin_assets?>js/nospam.js"></script>
	<!-- <script language="javascript" type="text/javascript" src="sys/jquery/jquery-1.3.2.min.js"></script> -->
	
	<!-- <link href="<?=$assets?>js/fullsize/fullsize.css" media="screen" rel="stylesheet" type="text/css" /> -->
	<!-- <script type="text/javascript" src="<?=$assets?>js/fullsize/jquery.fullsize.pack.js"></script> -->
	<!-- <script language="javascript" type="text/javascript" src="<?=$assets?>js/menu.js"></script> -->
	<!-- <script language="javascript" type="text/javascript" src="<?=$assets?>js/popup.js"></script> -->
	
	<link href="<?=$assets;?>/css/text.css" rel="stylesheet" type="text/css" />
	<link href="<?=$assets;?>/css/layout.css" rel="stylesheet" type="text/css" />

	<!--[if lte IE 6]><style type="text/css" media="screen">@import url(<?=$assets;?>/css/ie6.css);</style><![endif]-->
	<!--[if IE 7]><style type="text/css" media="screen">@import url(<?=$assets;?>/css/ie7.css);</style><![endif]-->
	<!--[if IE 8]><style type="text/css" media="screen">@import url(<?=$assets;?>/css/ie7.css);</style><![endif]-->
</head>

<body>

	<div id="title">
	<p><?=$title?></p>
	</div>

	<div id="menu">
	<p>Menu</p>
	</div>

	<div id="content">
	<p>Hier kan bijvoorbeeld je inhoud</p>
	</div>

</body>
</html>
