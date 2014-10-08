<!DOCTYPE html>
<html data-ng-app="flexyAdmin">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<base href="<?=base_url()?>" />

	<title>FlexyAdmin with AngularJS</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="imagetoolbar" content="no" />
	<meta http-equiv="content-language" content="nl" />

  <!--Bootstrap -->
  <link rel="stylesheet" href="sys/__test/external/bootstrap/dist/css/bootstrap.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/external/bootstrap/dist/css/bootstrap-theme.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <!--Angular modules -->
  <link rel="stylesheet" href="sys/__test/external/angular-loading-bar/src/loading-bar.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <!-- <link rel="stylesheet" href="sys/__test/external/ng-sortable/dist/ng-sortable.min.css" type="text/css" media="screen" title="no title" charset="utf-8"> -->
  <!--FlexyAdmin -->
  <link rel="stylesheet" href="sys/__test/css/flexy-main.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/css/flexy-grid.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/css/flexy-ui.css" type="text/css" media="screen" title="no title" charset="utf-8">

</head>

<body>
  
  <div id="container" class="container-fluid">
    
    <header class="navbar navbar-default navbar-fixed-top">
      <div class="pull-left navbar-text"><a href="./admin/__test" data-flexy-blocks="">AngularJS Test</a></div>
      <nav class="menu-header pull-right navbar-text"><flexy-menu type="header" uiclass="nav-pills"></flexy-menu></nav>
    </header>

    <div class="row">
      <nav class="menu-side col-xs-2"><flexy-menu type="sidebar" uiclass="nav-pills nav-stacked"></flexy-menu></nav>
      <main class="col-xs-10" data-ng-view=""></main>
    </div>
  
    <footer class="navbar navbar-default navbar-fixed-bottom">
      <div class="pull-left navbar-text"><a href="./admin/__test" data-flexy-blocks="">FlexyAdmin</a></div>
      <nav class="menu-footer pull-right  navbar-text"><flexy-menu type="footer" uiclass="nav-pills"></flexy-menu></nav>
    </footer>
    
  </div>


<!-- Angular JS -->
<script src="sys/__test/external/angular/angular.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/external/angular-route/angular-route.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/external/angular-bootstrap/ui-bootstrap-tpls.min.js" type="text/javascript" charset="utf-8"></script>

<!-- Angular Modules -->
<script src="sys/__test/external/angular-toArrayFilter/toArrayFilter.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/external/angular-loading-bar/src/loading-bar.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/external/angular-smart-table/dist/smart-table.min.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/external/ng-sortable/dist/ng-sortable.min.js" type="text/javascript" charset="utf-8"></script>

<!--FlexyAdmin -->
<script src="sys/__test/jdb.extend.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexyAdmin.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-ui/flexy-blocks.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-menu/flexy-menu.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-grid/flexy-grid.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-form/flexy-form.js" type="text/javascript" charset="utf-8"></script>
<!-- <script src="sys/__test/others/plugin.js" type="text/javascript" charset="utf-8"></script> -->

</body>
</html>
