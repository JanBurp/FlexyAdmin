<!DOCTYPE html>
<html data-ng-app="flexyAdmin">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf8" />
	<base href="<?=base_url()?>" />

	<title>FlexyAdmin with AngularJS</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="imagetoolbar" content="no" />
	<meta http-equiv="content-language" content="nl" />

  <!--Bootsrtrap -->
  <link rel="stylesheet" href="sys/__test/public/bootstrap/dist/css/bootstrap.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/public/bootstrap/dist/css/bootstrap-theme.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <!--Angular modules -->
  <link rel="stylesheet" href="sys/__test/public/angular-loading-bar/src/loading-bar.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/public/trNgGrid/release/trNgGrid.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <!--FlexyAdmin -->
  <link rel="stylesheet" href="sys/__test/css/admin.css" type="text/css" media="screen" title="no title" charset="utf-8">

  <!-- <link rel="stylesheet" href="sys/__test/css/normalize.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/css/bootstrap-glyphicons.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/css/theme_classic.css" type="text/css" media="screen" title="no title" charset="utf-8"> -->
</head>

<body>
  
  <div id="container" class="container-fluid">
    
    <div id="header" class="navbar navbar-default navbar-fixed-top">
      <div class="pull-left navbar-text"><a href="./admin/__test" data-flexy-blocks="">AngularJS Test</a></div>
      <div class="pull-right navbar-text"><flexy-menu type="header" uiclass="nav-pills"></flexy-menu></div>
    </div>

    <div id="content" class="row">
      <div id="menu" class="col-md-2"><flexy-menu type="sidebar" uiclass="nav-pills nav-stacked"></flexy-menu></div>
      <div id="view" class="col-md-10" data-ng-view=""></div>
    </div>
  
    <div id="footer" class="navbar navbar-default navbar-fixed-bottom">
      <div class="pull-left navbar-text"><a href="./admin/__test" data-flexy-blocks="">FlexyAdmin</a></div>
      <div class="pull-right  navbar-text"><flexy-menu type="footer" uiclass="nav-pills"></flexy-menu></div>
    </div>
    
  </div>


<!-- Angular JS -->
<script src="sys/__test/public/angular/angular.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/public/angular-route/angular-route.js" type="text/javascript" charset="utf-8"></script>
<!-- Angular Modules -->
<script src="sys/__test/public/angular-loading-bar/src/loading-bar.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/public/trNgGrid/release/trNgGrid.min.js" type="text/javascript" charset="utf-8"></script>
<!--FlexyAdmin -->
<script src="sys/__test/flexyAdmin.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/modules/flexy-blocks.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/modules/flexy-menu.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/controllers/grid.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/controllers/form.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>
