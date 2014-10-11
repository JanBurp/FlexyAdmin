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
 * ROUTING
 */

flexyAdmin.config( function($routeProvider){
  $routeProvider
    .when('/home',{
      controller  : '',
      templateUrl : 'sys/__test/flexy-ui/flexy-home.html'
    })
    .when('/grid/:table',{
      controller  : '',
      templateUrl : 'sys/__test/flexy-grid/flexy-grid.html'
    })
    .when('/form/:table/:id',{
      controller  : 'FormController',
      templateUrl : 'sys/__test/flexy-form/flexy-form.html'
    })
    // .when('/plugin/:plugin',{
    //   controller  : 'PluginController',
    //   templateUrl : 'sys/__test/views/html.html'
    // })
    
    .otherwise({ redirectTo: '/home' });
});



/**
* This directive will find itself inside HTML as a class,
* and will remove that class, so CSS will remove loading image and show app content.
* It is also responsible for showing/hiding login form.
*/
flexyAdmin.directive('flexyAdmin-auth', function() {
  return {
    restrict: 'C',
    link: function(scope, elem, attrs) {
      elem.removeClass('flexy-waiting-for-angular');
      
      var login = elem.find('#login');
      var main = elem.find('#container');
      console.log(login,main);
      
      login.hide();
      
      scope.$on('event:auth-loginRequired', function() {
        login.slideDown('slow', function() {
          main.hide();
        });
      });
      
      scope.$on('event:auth-loginConfirmed', function() {
        main.show();
        login.slideUp();
      });
    }
  }
});

