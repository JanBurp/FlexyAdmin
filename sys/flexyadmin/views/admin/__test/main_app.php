<!DOCTYPE html>
<html lang="<?=$language?>" data-ng-app="flexyAdmin">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?=$title?></title>
	<base href="<?=base_url()?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="sys/__test/css/flexyadmin.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
</head>

<body class="hidden" ng-controller="stateController">
  
  <div id="debug" class="panel panel-danger hidden">
    <div class="panel-heading panel-title"><h3>{{ 'ERROR' | translate }}</h3></div>
    <div class="panel-content"></div>
  </div>

  <div id="login" class="panel panel-primary" ng-if="!isLoggedIn" ng-controller="flexyLoginController as loginCtrl">
    <div class="panel-heading panel-title"><h3>{{ 'LOGIN_TITLE' | translate }}</h3></div>
    <div class="panel-content">
      <form class="form-horizontal" name="loginForm" ng-submit="loginCtrl.login()">
        <div class="form-group">
          <label class="control-label" for="login-username">{{ 'LOGIN_USERNAME' | translate }}</label>
          <div><input class="form-control" id="login-username" name="login-username" required class="form-control" placeholder="" ng-model="loginCtrl.user.username"></div>
        </div>
        <div class="form-group">
          <label class="control-label" for="login-password">{{ 'LOGIN_PASSWORD' | translate }}</label>
          <div><input class="form-control" id="login-password" name="login-password" type="password" required class="form-control" placeholder="" ng-model="loginCtrl.user.password"></div>
        </div>
        <div class="form-group button-group">
          <div class="">
            <a class="flexy-login-forgot action-click" ng-click="loginCtrl.askMail=!loginCtrl.askMail;loginCtrl.mailSend=false">{{ 'LOGIN_FORGOT' | translate }}</a>
            <button type="submit" id="login-button" class="btn btn-primary pull-right" ng-disabled="loginForm.$invalid">{{ 'LOGIN_SUBMIT' | translate }}</button>
          </div>
        </div>
      </form>
    </div>
    <div class="panel-footer" ng-show="loginCtrl.askMail">
      <form class="form-inline" name="loginEmailForm" ng-submit="loginCtrl.sendNewPassword()">
        <div class="form-group" show-errors>
          <div>
            <input class="form-control" name="login-email" type="email" required placeholder="{{ 'EMAIL' | translate }}" ng-model="loginCtrl.user.email">
            <button type="submit" class="btn btn-primary" ng-disabled="loginEmailForm.$invalid">{{ 'LOGIN_SEND_PASSWORD' | translate }}</button>
            <div class="btn text-success" ng-show="loginCtrl.mailSend"><span class="glyphicon glyphicon-ok"></span></div>
            <div class="btn text-danger" ng-show="loginCtrl.mailError"><span class="glyphicon glyphicon-remove"></span></div>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <div id="container" class="container-fluid" ng-if="isLoggedIn" ng-controller="flexyMenuCtrl">
    
    <header class="navbar navbar-default navbar-fixed-top">
      <div class="navbar-text"><a href="./admin/__test#/home" data-flexy-blocks=""><?=$title?></a></div>
      <nav class="menu-header navbar-text"><flexy-menu type="header" uiclass="nav-pills" items="menu.header"></flexy-menu></nav>
    </header>
    
    <div class="flexy-alerts">
      <div ng-repeat="alert in alerts" class="alert alert-{{alert.type}}" ng-bind-html="alert.msg">{{alert.msg}} <button type="button" class="close" ng-click="closeAlert($index)"><span aria-hidden="true">&times;</span></button></div>
    </div>

    <div id="content" class="row">
      <nav class="menu-side"><flexy-menu type="sidebar" uiclass="nav-pills nav-stacked" items="menu.sidebar"></flexy-menu></nav>
      <main data-ng-view="" autoscroll="true"></main>
    </div>
  
    <footer class="navbar navbar-default navbar-fixed-bottom">
      <div class="navbar-text"><a href="./admin/__test#/home" data-flexy-blocks="">TOKPIT</a></div>
      <nav class="menu-footer navbar-text"><flexy-menu type="footer" uiclass="nav-pills" items="menu.footer"></flexy-menu></nav>
    </footer>
    
  </div>


<!-- External JS -->
<script src="sys/__test/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/angular.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/angular-route.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/angular-filter.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/ui-bootstrap-tpls.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/showErrors.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/http-auth-interceptor.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/toArrayFilter.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/loading-bar.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/angular-translate.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/dialogs.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/smart-table.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/ng-sortable.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/angular-file-upload.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/angular-sanitize.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/tv4.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/ObjectPath.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/schema-form.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/bootstrap-decorator.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-form/bootstrap-decorator-froala.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/froala_editor.min.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/angular-froala.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/js/froala-sanitize.js" type="text/javascript" charset="utf-8"></script>

<!-- <script src="sys/__test/js/externals.min.js" type="text/javascript" charset="utf-8"></script> -->


<!-- FlexyAdmin -->
<script src="sys/__test/jdb.extend.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-admin-app.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-api-settings/flexy-settings.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-api-settings/flexy-settings-service.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-api-settings/flexy-api-service.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-api-settings/flexy-translate.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-http/http-request.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-http/http-interceptor-auth-check.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-http/http-interceptor-logging.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-auth/flexy-auth-service.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-auth/flexy-login-controller.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-auth/flexy-logout-controller.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-ui/flexy-alert-service.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-menu/flexy-menu-service.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-menu/flexy-menu-controller.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-menu/flexy-menu-directive.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-table/flexy-sortable.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-table/flexy-table-service.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-table/flexy-table-controller.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-table/flexy-table-directive.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-table/flexy-field.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-table/flexy-field-thumb.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-media/flexy-media.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-media/flexy-media-controller.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-form/flexy-form-service.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-form/flexy-form-controller.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-form/flexy-form-directive.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-form/flexy-file-upload.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-form/bootstrap-decorator-media.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-help/flexy-help.js" type="text/javascript" charset="utf-8"></script>
<script src="sys/__test/flexy-plugin/flexy-plugin.js" type="text/javascript" charset="utf-8"></script>

<script src="sys/__test/flexy-ui/flexy-blocks.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>
