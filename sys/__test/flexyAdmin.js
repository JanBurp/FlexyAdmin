var flexyAdmin = angular.module( 'flexyAdmin', [
  // Angular JS
  'ngRoute',
  
  // Angular Modules
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
  sys_folder    : 'sys/__test/',
  api_base_url  : '__api/',
  log_prefix    : 'FA ',
});



/**
 * ROUTING
 */

flexyAdmin.config( function($routeProvider){
  $routeProvider
    .when('/login',{
      templateUrl:'flexy-http-auth/login-form.html'
    })
    .when('/home',{
      controller  : '',
      templateUrl : 'flexy-ui/flexy-home.html',
      resolve     : {
        auth : ['$q','$location','authService',
        function($q,$location,authService) {
          return authService.session().then(
            function(success){},
            function(error) {
              $location.path('/login');
              $location.replace();
              return $q.reject(error);
            }
          );
        }]
      }
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
