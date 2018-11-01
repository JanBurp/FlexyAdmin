<?php 
/**
 * site.php - the main view
 * All uri's are to controller.php which dicides what to do and loads this (or another) view.
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */
?><!DOCTYPE HTML>
<html lang="<?=$language?>">
<head>
  <meta charset="utf-8">
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="<?=base_url()?>">
  <link rel="shortcut icon" href="<?=$publicassets?>img/favicon.ico" type="image/x-icon">
  <!-- <link rel="alternate" type="application/rss+xml" title="<?=ascii_to_entities($title);?>" href="<?=site_url('_rss');?>" /> -->

  <title><?=ascii_to_entities($title);?></title>
  <meta name="Description" content="<?=ascii_to_entities($description);?>">
  <meta name="Keywords" content="<?=$keywords;?>">
  <meta name="Author" content="<?=$author;?>">
  <meta name="dcterms.rightsHolder" content="Jan den Besten,<?=$author;?>">
  <meta name="robots" content="index,follow">
  <meta name="revisit-after" content="7 days">

<?php if ($use_minimized): ?>
  <link href="<?=$publicassets;?>css/styles.min.css?<?=$int_version?>" rel="stylesheet" type="text/css">
<?php else: ?>
  <link href="<?=$publicassets;?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="<?=$publicassets;?>css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="<?=$publicassets;?>css/style.css?<?=$int_version?>" rel="stylesheet" type="text/css">
<?php endif ?>
  <!--[if lte IE 8]><style type="text/css" media="screen">@import url(<?=$publicassets;?>css/ie8.css);</style><![endif]-->
  <!--[if IE 9]><style type="text/css" media="screen">@import url(<?=$publicassets;?>css/ie9.css);</style><![endif]-->
</head>

<body class="<?=$class?>">

<!-- start of container -->
<div class="container main-container">

  <!-- header -->
  <div class="page-header">
		<h1><a href="./"><?=ascii_to_entities($str_title)?></a></h1>
  </div>
  
  <!-- main navigation -->
  <div class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <div class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
          <span class="fa fa-bars"></span>
        </div>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <?=$menu?>
      </div>
    </div>
  </div>

  <!-- content -->
	<div class="content"><?=$content;?></div>

  <!-- footer -->
  <footer class="footer navbar navbar-default">
    <div class="container">
      <p class="navbar-text">a flexyadmin site</p>
    </div>
  </footer>

</div>
<!-- end of container -->

<!-- Javascript -->
<?php if ($use_minimized): ?>
  <script src="<?=$publicassets?>js/scripts.min.js?<?=$int_version?>" type="text/javascript" charset="utf-8"></script>
<?php else: ?>
  <script src="<?=$publicassets?>js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=$publicassets?>js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=$publicassets?>js/site.js?<?=$int_version?>" type="text/javascript" charset="utf-8"></script>
<?php endif ?>

</body>
</html>
