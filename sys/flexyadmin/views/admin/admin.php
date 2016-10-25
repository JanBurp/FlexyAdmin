<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title>FlexyAdmin - <?=$str_title?></title>
	<base href="<?=base_url()?>" />
  
  <link rel="shortcut icon" href="<?=admin_assets()?>img/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="<?=admin_assets()?>css/bootstrap.min.css" type="text/css" />
  <link rel="stylesheet" href="<?=admin_assets()?>css/font-awesome.min.css" type="text/css" media="screen">
  <link rel="stylesheet" href="<?=admin_assets()?>css/flexyadmin.css" type="text/css" />
</head>

<body>

<div id="main" class="container-fluid">
  
  <div id="header" class="navbar navbar-light bg-faded navbar-fixed-top">
    <!-- Site title -->
    <div class="navbar-text"><flexy-blocks href="admin" text="<?=$str_title?>"/></div>
    <!--Top menu -->
    <?=$headermenu?>
  </div>
  
  <div class="flexy-alerts"></div>

  <div id="content" class="row">
    
    <div id="flexy-menu-side">
      <!--side menu -->
      <?=$sidemenu?>
    </div>
    
    <div id="main">
      <!--  Main content -->
    </div>
    
    
  </div>
  
  <div id="footer" class="navbar navbar-light bg-faded navbar-fixed-bottom">
    <div class="navbar-text"><flexy-blocks href="admin" text="TokPit"/></div>
    <div class="navbar-text"><span class="flexy-block btn btn-outline-danger text-lowercase"><?=$version?></span></div>
    <?=$footermenu?>
  </div>

</div>

<script src="<?=admin_assets()?>js/vue.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=admin_assets()?>js/vue-components/flexy-blocks.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=admin_assets()?>js/flexyadmin-main.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>

