var flexyAdmin = angular.module( 'flexyAdmin', [
  // Angular JS
  'ngRoute',
  
  // Angular Modules
  'http-auth-interceptor',
  'angular-toArrayFilter',
  'angular-loading-bar',
  'ui.bootstrap',
  'smart-table',
  'ui.sortable',
  
  // flexyAdmin Modules
  'flexyMenu',
  'flexyBlocks',
  ]
);


/**
 * SOME SPARE GLOBALS as a constant
 */

flexyAdmin.constant('flexyAdminGlobals',{
  base_url      : 'admin/__test',  
  api_base_url  : '__api/',
  sys_folder    : 'sys/__test/',
  log_prefix    : 'FA ',
});



/**
 * ROUTING
 */

flexyAdmin.config( function($routeProvider){
  $routeProvider
    .when('/home',{
      controller  : '',
      templateUrl : 'flexy-ui/flexy-home.html',
    })
    .when('/logout',{
      controller  : 'flexyLogoutController',
      template    : '',
    })
    .when('/grid/:table',{
      controller  : '',
      templateUrl : 'flexy-grid/flexy-grid.html'
    })
    .when('/form/:table/:id',{
      controller  : 'FormController',
      templateUrl : 'flexy-form/flexy-form.html'
    })
    .otherwise({ redirectTo: '/home' });
});





/**
 * Taken from demo of: https://github.com/witoldsz/angular-http-auth
 * 
 * Hide/shows login-form if needed
 * Show app if angular is ready
 */
flexyAdmin.directive('flexyAuthenticate', ['flexyAuthService',function(flexyAuthService) {

  var login = angular.element(document.querySelector('#login'));
  var container  = angular.element(document.querySelector('#container'));

  function hide_all() {
    login.addClass('hidden');
    container.addClass('hidden');
  }
  function show_login() {
    login.removeClass('hidden');
    container.addClass('hidden');
  }
  function hide_login() {
    login.addClass('hidden');
    container.removeClass('hidden');
  }
  
  return {
    restrict: 'C',
    link: function(scope, elem, attrs) {
      hide_all();
      flexyAuthService.check().then(
        function(success) {
          if (flexyAuthService.loggedIn()) {
            hide_login();
          }
          else {
            show_login();
          }
        },
        function(error) {
          show_login();
        }
      );
      
      // EVENT RESPONSE
      scope.$on('event:auth-loginRequired', function() {
        show_login();
      });
      scope.$on('event:auth-loginConfirmed', function() {
        hide_login();
      });

      // READY LOADING ANGULAR
      elem.removeClass('hidden');
    }
  }
}]);
