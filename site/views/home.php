<?
/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2009, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 */

// ------------------------------------------------------------------------

/**
 * FlexyAdmin home.php
 *
 * This file is the main frontend file of the site.
 *
 * All uri's are to controller.php which dicides what to do and loads this (or another) view.
 *
 * You can use all the funcionality from Code Igniter and the special FlexyAdmin classes and helper functions.
 * But you are encouriged to do this all in controller.php and leave this file as a simple html file with just some php to put the content.
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf8" />
	<base href="<?=base_url()?>" />
	<title><?=$title;?></title>
	<meta http-equiv="imagetoolbar" content="no" />
	<meta http-equiv="content-language" content="nl" />
	<meta name="Description" content="<?=$description;?>" />
	<meta name="Keywords" content="<?=$keywords;?>" />
	<meta name="Author" content="<?=$author;?>" />
	<meta name="web_author" content="Jan den Besten" />
	<meta name="copyright" content="Jan den Besten,<?=$author;?>" />
	<meta name="robots" content="index,follow" />
	<meta name="revisit-after" content="7 days" />
	<script language="javascript" type="text/javascript" src="<?=admin_assets()?>js/nospam.js"></script>
	<link href="<?=$assets;?>/css/text.css" rel="stylesheet" type="text/css" />
	<link href="<?=$assets;?>/css/layout.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div id="wrapper">
	<div id="container">

		<div class="banner">
		</div>

		<div id="menu">
		<?=$menu;?>
		</div>

		<div id="random">
		<hr />
		<div class="italic"><?=$random;?></div>
		<hr />
		</div>

		<div id="content" class="<?=$module;?>">
		<?=$content;?>
		<hr/>
		</div>

		<div class="banner">
		</div>

	</div>
</div>

</body>
</html>
