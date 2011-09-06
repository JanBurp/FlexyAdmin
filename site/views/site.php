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
 * All uri's are to controller.php which dicides what to do and loads this (or another) view.
 *
 * You can use all the funcionality from Code Igniter and the special FlexyAdmin classes and helper functions.
 * But you are encouriged to do this all in controller.php and leave this file as a simple html file with just some php to put the content.
 */
?><!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf8" />
	<base href="<?=base_url()?>" />
	<title><?=ascii_to_entities($title);?></title>
	<link rel="shortcut icon" href="site/assets/img/favicon.ico" type="image/x-icon" />
	
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
	<!-- <script language="javascript" type="text/javascript" src="sys/jquery/jquery-1.6.2.min.js"></script> -->
	<!-- <script language="javascript" type="text/javascript" src="site/assets/js/site.js"></script> -->
	
	<link href="<?=$assets;?>/css/text.css" rel="stylesheet" type="text/css" />
	<link href="<?=$assets;?>/css/layout.css" rel="stylesheet" type="text/css" />
	<!--[if lte IE 6]><style type="text/css" media="screen">@import url(<?=$assets;?>/css/ie6.css);</style><![endif]-->
	<!--[if IE 7]><style type="text/css" media="screen">@import url(<?=$assets;?>/css/ie7.css);</style><![endif]-->
	<!--[if IE 8]><style type="text/css" media="screen">@import url(<?=$assets;?>/css/ie8.css);</style><![endif]-->
	<!--[if IE 9]><style type="text/css" media="screen">@import url(<?=$assets;?>/css/ie9.css);</style><![endif]-->
	
	<? if (isset($str_google_analytics) and !empty($str_google_analytics)): ?><script type="text/javascript">
	// Google Analytics
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?=$str_google_analytics?>']);
	_gaq.push(['_trackPageview']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	</script><? endif ?>
</head>

<body class="<?=$class?>">

	<div id="container">
		<div id="title">
		<p><a href="./"><?=$title?></a></p>
		</div>

		<div id="menu">
		<?=$menu;?>
		</div>

		<div id="content">
		<?=$content;?>
		</div>
	</div>

	<div id="benchmark"><p>Time: <?php echo $this->benchmark->elapsed_time(); ?><br />Mem: <?php echo $this->benchmark->memory_usage(); ?></p></div>

</body>
</html>
