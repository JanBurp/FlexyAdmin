<?php 
/**
 * FlexyAdmin
 *
 * site.php - the main view
 * All uri's are to controller.php which dicides what to do and loads this (or another) view.
 *
 * @package FlexyAdmin
 * @author Jan den Besten
 * @copyright Copyright (c) 2009-2014, Jan den Besten
 * @link http://www.flexyadmin.com
 */
?><!DOCTYPE html>
<html lang="<?=$language?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="imagetoolbar" content="no">
  <meta http-equiv="content-language" content="<?=$language?>">
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="<?=base_url()?>">
  <link rel="shortcut icon" href="<?=$assets?>img/favicon.ico" type="image/x-icon">

  <title><?=ascii_to_entities($title);?></title>
  <meta name="Description" content="<?=ascii_to_entities($description);?>">
  <meta name="Keywords" content="<?=$keywords;?>">
  <meta name="Author" content="<?=$author;?>">
  <meta name="web_author" content="Jan den Besten">
  <meta name="copyright" content="Jan den Besten,<?=$author;?>">
  <meta name="robots" content="index,follow">
  <meta name="revisit-after" content="7 days">

<?php if ($use_minimized): ?>
  <link href="<?=$assets;?>css/styles.min.css?<?=$int_version?>" rel="stylesheet" type="text/css">
<?php else: ?>
  <?php if ($framework=='default'): ?>
  <link href="<?=$assets;?>css/normalize.css" rel="stylesheet" type="text/css">
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
  <nav id="menu" class="navbar navbar-default" role="navigation">
    <div class="container">
      <?=$menu?>
    </div>
  </nav>

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
  <script src="<?=$assets?>js/rem.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="sys/jquery/jquery-1.11.1.min.js" type="text/javascript" charset="utf-8"></script>
  <?php if ($framework=='bootstrap'): ?><script src="<?=$assets?>js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script><?php endif ?>
  <script src="<?=$assets?>js/site.js?<?=$int_version?>" type="text/javascript" charset="utf-8"></script>
<?php endif ?>

</body>
</html>
