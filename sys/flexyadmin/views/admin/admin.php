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
  
  <link rel="stylesheet" href="<?=admin_assets()?>css/flexyadmin.min.css" type="text/css" />
</head>

<body>

<div id="main" class="container-fluid">
  
  <div id="header" class="navbar navbar-fixed-top">
    <div class="navbar-text"><flexy-blocks href="<?=$base_url?>" text="<?=$str_title?>"/></div>
    <?=$headermenu?>
  </div>
  
  <div class="flexy-alerts"></div>


  <div id="content" class="row">
    <div id="flexy-menu-side" class="col-sm-2"><?=$sidemenu?></div>

    <div id="main" class="col-sm-10"><?=$content?></div>
    
  </div>
  
  <div id="footer" class="navbar navbar-fixed-bottom">
    <div class="navbar-text"><flexy-blocks href="<?=$base_url?>" text="TokPit"/></div>
    <div class="navbar-text"><span class="flexy-block btn btn-outline-danger text-lowercase"><?=$version?></span></div>
    <?=$footermenu?>
  </div>

</div>

<script src="<?=admin_assets()?>dist/bundle.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>

