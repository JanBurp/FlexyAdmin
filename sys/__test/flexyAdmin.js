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
      var btn_style="btn-primary";
      if (value==' ') {
        c='space';
        btn_style='btn-default';
        value="&nbsp;";
      }
      html += '<span class="flexyblock btn '+btn_style+' char_'+c+'">'+value+'</span>';
    });
    element.html(html);
  };
  return {
    restrict: "A",
    link: blocks
  };
});

