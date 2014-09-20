var flexyAdmin = angular.module( 'flexyAdmin', [

  // Standard Angular Modules
  'ngRoute',
  'angular-loading-bar',
  
  // flexyAdmin Modules
  'flexyMenu',
  'flexyBlocks',
  ]);


// ROUTES

flexyAdmin.config( function($routeProvider){
  $routeProvider
    .when('/grid/:table',{
      controller  : 'GridController',
      templateUrl : 'sys/__test/views/grid.html'
    })
    .when('/form/:table/:id',{
      controller  : 'FormController',
      templateUrl : 'sys/__test/views/form.html'
    })
    .otherwise({ redirectTo: '/grid' });
});
