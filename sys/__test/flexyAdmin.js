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
 * ROUTING
 */

flexyAdmin.config( function($routeProvider){
  $routeProvider
    .when('/login',{
      templateUrl:'sys/__test/flexy-http-auth/login-form.html'
    })
    .when('/home',{
      controller  : '',
      templateUrl : 'sys/__test/flexy-ui/flexy-home.html',
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
      templateUrl : 'sys/__test/flexy-grid/flexy-grid.html'
    })
    .when('/form/:table/:id',{
      controller  : 'FormController',
      templateUrl : 'sys/__test/flexy-form/flexy-form.html'
    })
    .otherwise({ redirectTo: '/home' });
});
