/**
 * flexy-menu
 */
var flexyMenu = angular.module( 'flexyMenu', []);

flexyMenu.process = function(root,menu) {
  var classes = {
    'media' :{'class':'','glyphicon':'glyphicon glyphicon-picture'},
    'tools' :{'class':'text-muted','glyphicon':'glyphicon glyphicon-wrench'},
    'config':{'class':'text-muted','glyphicon':'glyphicon glyphicon-cog'},
    'log'   :{'class':'text-muted','glyphicon':'glyphicon glyphicon-stats'},
    'result':{'class':'text-info','glyphicon':'glyphicon glyphicon-cloud'},
    'rel'   :{'class':'text-muted','glyphicon':'glyphicon glyphicon-link'}
  };
  var processed = [];
  for (var i = 0; i < menu.length; i++) {
    processed[i]=menu[i];
    processed[i].href   = root + '#/' + menu[i].uri;
    processed[i].class  = 'menu-type-'+menu[i].type;
    processed[i].glyphicon = '';
    var thisClass=classes[menu[i].type];
    if (angular.isDefined(thisClass)) {
      processed[i].class += ' '+thisClass.class;
      processed[i].glyphicon = thisClass.glyphicon;
    }
  }
  return processed;
}    


flexyMenu.directive( "flexyMenu", function() {
  return {
    restrict: "E",
    scope: {
      type   : "@",
      uiclass : "@",
    },
    templateUrl:'sys/__test/flexy-menu/flexy-menu.html',

    controller : ['$scope', '$http', '$log', function($scope, $http, $log) {
      $scope.root = "admin/__test";
      $scope.menu = [];
      $scope.menu.header = [ { href: $scope.root+"/logout", name: 'Logout' } ];
      $scope.menu.sidebar = [];
      $scope.menu.footer = [];
      $http.get('__api/get_admin_nav?_type=json',{cache:true}).success(function(result){
        $scope.menu.header  = flexyMenu.process($scope.root,result.data.header);
        $scope.menu.sidebar = flexyMenu.process($scope.root,result.data.sidebar);
        $scope.menu.footer  = flexyMenu.process($scope.root,result.data.footer);
      }).error(function(data){
        $log.log('AJAX error -> flexyMenu');
      });
      
    }],
    
  }
});
