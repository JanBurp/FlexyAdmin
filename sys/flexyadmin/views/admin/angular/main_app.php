<!DOCTYPE html>
<html data-ng-app="flexyAdmin">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf8" />
	<base href="<?=base_url()?>" />

	<title>FlexyAdmin with AngularJS</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="imagetoolbar" content="no" />
	<meta http-equiv="content-language" content="nl" />

  <link rel="stylesheet" href="sys/flexyadmin_js/css/normalize.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/flexyadmin_js/css/bootstrap-glyphicons.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/flexyadmin_js/css/admin.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/flexyadmin_js/css/theme_classic.css" type="text/css" media="screen" title="no title" charset="utf-8">
</head>

<body>
  
  <div id="container" class="size margin-top-1 margin-right-1 margin-bottom-1 margin-left-1 ">
  
    <div id="header" class="size width-full height-1 margin-bottom-1 left">
      <div class="left"><a href="./admin/test" data-flexy-blocks="">AngularJS Test</a></div>
      <div class="right" data-ng-include="'sys/flexyadmin_js/views/menu_top.html'"></div>
    </div>

    <div id="content" class="size width-full margin-bottom-1 left">
      <div id="menu" class="size width-5 margin-right-1 left" data-ng-include="'sys/flexyadmin_js/views/menu.html'"></div>
      <div id="view" class="size no-margin-right left rounded" data-ng-view=""></div>
    </div>
  
    <div id="footer" class="size width-full left">
      <div class="left"><a href="./admin/test" data-flexy-blocks="">FlexyAdmin</a></div>
      <div class="right" data-ng-include="'sys/flexyadmin_js/views/menu_footer.html'"></div>
    </div>
    
  </div>


<!--Less -->
<!-- <script src="sys/flexyadmin_js/less-1.7.3.min.js" type="text/javascript" charset="utf-8"></script> -->
<!-- Angular -->
<script src="sys/angular/angular.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/angular/angular-route.js" type="text/javascript" charset="utf-8"></script>
<!--FlexyAdmin -->
<script src="sys/flexyadmin_js/flexyAdmin.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/flexyadmin_js/controllers/menu.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/flexyadmin_js/controllers/grid.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/flexyadmin_js/controllers/form.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>
