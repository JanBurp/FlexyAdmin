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
  <!--FlexyAdmin -->
  <link rel="stylesheet" href="sys/__test/css/flexy-main.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/css/flexy-grid.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="sys/__test/css/flexy-ui.css" type="text/css" media="screen" title="no title" charset="utf-8">

</head>

<body class="flexy-authenticate hidden">
  
  
  <div id="login" class="panel panel-primary" ng-controller="flexyLoginController as loginCtrl">
    <div class="panel-heading panel-title"><h3>Login</h3></div>
    <div class="panel-content">
      <form class="form-horizontal" name="loginForm" ng-submit="loginCtrl.login()">
        <div class="form-group">
          <label class="col-sm-3 control-label" for="login-username">Username</label>
          <div class="col-sm-9">
            <input class="form-control" name="login-username" required class="form-control" placeholder="" ng-model="loginCtrl.user.username">
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label" for="login-password">Password</label>
          <div class="col-sm-9">
            <input class="form-control" name="login-password" type="password" required class="form-control" placeholder="" ng-model="loginCtrl.user.password">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-default" ng-disabled="loginForm.$invalid">Login</button>
            <a class="flexy-login-forgot action-click pull-right" ng-click="loginCtrl.askMail=!loginCtrl.askMail;loginCtrl.mailSend=false">wachtwoord vergeten?</a>
          </div>
        </div>
      </form>
    </div>
    <div class="panel-footer" ng-show="loginCtrl.askMail">
      <form class="form-inline" name="loginEmailForm" ng-submit="loginCtrl.sendNewPassword()">
        <div class="form-group" show-errors>
          <div class="col-sm-offset-3">
            <input class="form-control" name="login-email" type="email" required placeholder="E-mail" ng-model="loginCtrl.user.email">
            <button type="submit" class="btn btn-default" ng-disabled="loginEmailForm.$invalid">Stuur wachtwoord</button>
            <div class="btn text-success" ng-show="loginCtrl.mailSend"><span class="glyphicon glyphicon-ok"></span></div>
          </div>
        </div>
      </form>
    </div>
  </div>
  
    
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
<script src="sys/__test/external/angular-bootstrap-show-errors/src/showErrors.min.js" type="text/javascript" charset="utf-8"></script>

<!-- Angular External Modules -->
<script src="sys/__test/external/angular-http-auth/src/http-auth-interceptor.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/external/angular-toArrayFilter/toArrayFilter.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/external/angular-loading-bar/src/loading-bar.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/external/angular-smart-table/dist/smart-table.min.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/external/ng-sortable/dist/ng-sortable.min.js" type="text/javascript" charset="utf-8"></script>

<!--FlexyAdmin -->
<script src="sys/__test/jdb.extend.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexyAdmin.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-http-auth/http-request.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-http-auth/http-interceptor-auth-check.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-http-auth/http-interceptor-logging.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-http-auth/flexy-auth-service.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-http-auth/flexy-login-controller.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-http-auth/flexy-logout-controller.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-ui/flexy-blocks.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-menu/flexy-menu.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-grid/flexy-grid.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-form/flexy-form.js" type="text/javascript" charset="utf-8"></script>
<!-- <script src="sys/__test/others/plugin.js" type="text/javascript" charset="utf-8"></script> -->

</body>
</html>
