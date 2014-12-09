<!DOCTYPE html>
<html lang="<?=$language?>" data-ng-app="flexyAdmin">
<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>FlexyAdmin with AngularJS</title>
	<base href="<?=base_url()?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="imagetoolbar" content="no" />
	<meta http-equiv="content-language" content="<?=$language?>" />
  <link rel="stylesheet" href="sys/__test/css/flexyadmin.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
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
<script src="sys/__test/js/angular.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/angular-route.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/ui-bootstrap-tpls.min.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/showErrors.min.js" type="text/javascript" charset="utf-8"></script>
<!-- Angular External Modules -->
<script src="sys/__test/js/http-auth-interceptor.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/toArrayFilter.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/loading-bar.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/smart-table.min.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/ng-sortable.min.js" type="text/javascript" charset="utf-8"></script>

<!--FlexyAdmin -->
<script src="sys/__test/jdb.extend.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-admin-app.js" type="text/javascript" charset="utf-8"></script>

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

</body>
</html>
