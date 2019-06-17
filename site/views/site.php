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
  <!-- <link rel="shortcut icon" href="<?=$publicassets?>img/favicon.ico" type="image/x-icon"> -->
  <!-- <link rel="alternate" type="application/rss+xml" title="<?=$title;?>" href="<?=site_url('_rss');?>" /> -->

  <title><?=$title?></title>
  <meta name="Description" content="<?=$description?>">
  <meta name="Keywords" content="<?=$keywords;?>">
  <meta name="Author" content="<?=$author;?>">
  <meta name="dcterms.rightsHolder" content="Jan den Besten,<?=$author;?>">
  <meta name="robots" content="index,follow">
  <meta name="revisit-after" content="7 days">

  <meta property="og:title" content="<?=$title?>"/>
  <meta property="og:type" content="website"/>
  <meta property="og:description" content="<?=$description?>"/>
  <meta property="og:locale" content="<?=$language?>"/>
  <meta property="og:image" content="<?=$image?>"/>

  <meta property="twitter:title" content="<?=$title?>"/>
  <meta property="twitter:card" content="summary"/>
  <meta property="twitter:description" content="<?=$description?>"/>
  <meta property="twitter:image" content="<?=$image?>"/>

  <link href="<?=mix_asset('styles.min.css')?>" rel="stylesheet" type="text/css">
</head>

<body class="<?=$class?>">

<!-- start of container -->
<div class="container" id="site">

<?php if ($framework == 'bootstrap3'): ?>

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

<?php else: ?>

  <!--  Header -->
  <div class="jumbotron jumbotron-fluid header">
    <div class="container">
      <a href="./" class="title"><?=ascii_to_entities($str_title)?></a>
      <p><?=ascii_to_entities($stx_description)?></p>
    </div>
  </div>

  <!-- main navigation -->
  <nav class="navbar navbar-expand-md navbar-light bg-primary">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="main-nav">
      <?=$menu?>
    </div>
  </nav>

  <!-- content -->
	<div class="content"><?=$content;?></div>

<?php endif ?>

</div>
<!-- end of container -->

<!-- Javascript -->
<script src="<?=mix_asset('scripts.min.js')?>" type="text/javascript" charset="utf-8"></script>

</body>
</html>
