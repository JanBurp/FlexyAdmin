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
  <link rel="stylesheet" href="<?=admin_assets()?>css/flexyadmin.css" type="text/css" />
</head>

<body>

<div class="container-fluid">
  
  <div id="header" class="navbar navbar-light bg-faded navbar-fixed-top">
    <!-- Site title -->
    <div class="navbar-text"><a id="flexyadmin" class="flexy-blocks" href="admin"><?=$str_title?></a></div>
    <!--Top menu -->
    <?=$headermenu?>
  </div>
  
  <div class="flexy-alerts"></div>

  <div id="content" class="row">
    
    <div id="flexy-menu-side">
      <!--side menu -->
    </div>
    
    <div id="main">
      <!--  Main content -->
    </div>
    
    
  </div>
  
  <div id="footer" class="navbar navbar-light bg-faded navbar-fixed-bottom">
    <div class="navbar-text">
      <a href="admin" class="flexy-blocks">FlexyAdmin</a>
    </div>
    <?=$footermenu?>
  </div>
  

  <script src="<?=admin_assets()?>js/jquery-3.1.1.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=admin_assets()?>js/tether.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=admin_assets()?>js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=admin_assets()?>js/flexyadmin.js" type="text/javascript" charset="utf-8"></script>
  
</div>

</body>
</html>

