var flexyAdmin = angular.module( 'flexyAdmin', [

  // Angular JS
  'ngRoute',
  
  // Angular Modules
  'angular-loading-bar',
  'trNgGrid',
  
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
