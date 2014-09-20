<!DOCTYPE html>
<html data-ng-app="flexyAdmin">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf8" />
	<base href="<?=base_url()?>" />

	<title>FlexyAdmin with AngularJS</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="imagetoolbar" content="no" />
	<meta http-equiv="content-language" content="nl" />

  <link rel="stylesheet" href="sys/__test/public/bootstrap/dist/css/bootstrap.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/public/bootstrap/dist/css/bootstrap-theme.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/css/admin.css" type="text/css" media="screen" title="no title" charset="utf-8">

  <!-- <link rel="stylesheet" href="sys/__test/css/normalize.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/css/bootstrap-glyphicons.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/css/theme_classic.css" type="text/css" media="screen" title="no title" charset="utf-8"> -->
</head>

<body>
  
  <div id="container" class="container-fluid">
    
    <div id="header" class="navbar navbar-default navbar-fixed-top">
      <div class="pull-left navbar-text"><a href="./admin/__test" data-flexy-blocks="">AngularJS Test</a></div>
      <div class="pull-right navbar-text" data-ng-include="'sys/__test/views/menu_top.html'"></div>
    </div>

    <div id="content" class="row">
      <div id="menu" class="pull-left col-md-3" data-ng-include="'sys/__test/views/menu.html'"></div>
      <div id="view" class="pull-right col-md-9" data-ng-view=""></div>
    </div>
  
    <div id="footer" class="navbar navbar-default navbar-fixed-bottom">
      <div class="pull-left navbar-text"><a href="./admin/__test" data-flexy-blocks="">FlexyAdmin</a></div>
      <div class="pull-right  navbar-text" data-ng-include="'sys/__test/views/menu_footer.html'"></div>
    </div>
    
  </div>


<!-- Angular -->
<script src="sys/__test/public/angular/angular.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/public/angular-route/angular-route.js" type="text/javascript" charset="utf-8"></script>
<!--FlexyAdmin -->
<script src="sys/__test/flexyAdmin.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/controllers/menu.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/controllers/grid.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/controllers/form.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>
