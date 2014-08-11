var flexyAdmin = angular.module( 'flexyAdmin', ['ngRoute']);

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

//
// DIRECTIVES
//


/**
 * flexy-blocks
 * 
 * Maakt van elk karakter in een tekst binnen een element een span met die letter
 */
flexyAdmin.directive("flexyBlocks", function() {
  var blocks = function(scope, element, attributes) {
    element.addClass('flexy-blocks');
    var text = element.text();
    var html = '';
    angular.forEach(text,function(value,key){
      var c=value;
      if (value==' ') c='space';
      html += '<span class="size height-1 width-1 flexy-block heading char_'+c+'">'+value+'</span>';
    });
    element.html(html);
  };
  return {
    restrict: "A",
    link: blocks
  };
});

