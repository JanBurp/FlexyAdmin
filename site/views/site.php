<?php 
/**
 * site.php - the main view
 * All uri's are to controller.php which dicides what to do and loads this (or another) view.
 *
 * $Author: $
 * $Revision:  $
 * @copyright (c) Jan den Besten
 */
?><!DOCTYPE HTML>
<html lang="<?=$language?>">
<head>
  <meta charset="utf-8">
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="<?=base_url()?>">
  <link rel="shortcut icon" href="<?=$assets?>img/favicon.ico" type="image/x-icon">
  <!-- <link rel="alternate" type="application/rss+xml" title="<?=ascii_to_entities($title);?>" href="<?=site_url('_rss');?>" /> -->
  

  <title><?=ascii_to_entities($title);?></title>
  <meta name="Description" content="<?=ascii_to_entities($description);?>">
  <meta name="Keywords" content="<?=$keywords;?>">
  <meta name="Author" content="<?=$author;?>">
  <meta name="dcterms.rightsHolder" content="Jan den Besten,<?=$author;?>">
  <meta name="robots" content="index,follow">
  <meta name="revisit-after" content="7 days">

<?php if ($use_minimized): ?>
  <link href="<?=$assets;?>css/styles.min.css?<?=$int_version?>" rel="stylesheet" type="text/css">
<?php else: ?>
  <?php if ($framework=='default'): ?>
  <link href="<?=$assets;?>css/normalize.css" rel="stylesheet" type="text/css">
  <link href="<?=$assets;?>css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="<?=$assets;?>css/text.css?<?=$int_version?>" rel="stylesheet" type="text/css">
  <link href="<?=$assets;?>css/layout.css?<?=$int_version?>" rel="stylesheet" type="text/css">
  <?php elseif ($framework=='bootstrap'): ?>
  <link href="<?=$assets;?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <?php endif ?>
  <link href="<?=$assets;?>css/style.css?<?=$int_version?>" rel="stylesheet" type="text/css">
<?php endif ?>
  <!--[if lte IE 8]><style type="text/css" media="screen">@import url(<?=$assets;?>css/ie8.css);</style><![endif]-->
  <!--[if IE 9]><style type="text/css" media="screen">@import url(<?=$assets;?>css/ie9.css);</style><![endif]-->
  <?php if (isset($str_google_analytics) and !empty($str_google_analytics)): ?><script type="text/javascript">
  // Google Analytics
  var _gaq = _gaq || [];_gaq.push(['_setAccount', '<?=$str_google_analytics?>']);_gaq.push(['_trackPageview']);(function() {
  	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
  </script><?php endif ?>
</head>

<body class="<?=$class?>">

<!-- start of container -->
<div id="container" class="container">

  <!-- header -->
  <div id="header" class="page-header">
		<h1 id="title"><a href="./"><?=ascii_to_entities($str_title)?></a></h1>
  </div>
  
  <!-- main navigation -->
  <div id="menu" class="navbar navbar-default">
    <div class="container">
      <span class="fa fa-bars mobile-only"></span>
      <?=$menu?>
    </div>
  </div>

  <!-- content -->
	<div id="content"><?=$content;?></div>

  <!-- footer -->
  <footer id="footer" class="navbar navbar-default">
    <div class="container">
      <p class="navbar-text">a flexyadmin site</p>
    </div>
  </footer>

</div>
<!-- end of container -->

<!-- Javascript -->
<?php if ($use_minimized): ?>
  <script src="<?=$assets?>js/scripts.min.js?<?=$int_version?>" type="text/javascript" charset="utf-8"></script>
<?php else: ?>
  <script src="<?=$assets?>js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
  <?php if ($framework=='bootstrap'): ?><script src="<?=$assets?>js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script><?php endif ?>
  <script src="<?=$assets?>js/site.js?<?=$int_version?>" type="text/javascript" charset="utf-8"></script>
<?php endif ?>

</body>
</html>
