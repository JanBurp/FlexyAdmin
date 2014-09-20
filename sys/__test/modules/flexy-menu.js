/**
 * flexy-menu
 */
var flexyMenu = angular.module( 'flexyMenu', []);

flexyMenu.uris_to_href = function(root,menu) {
  var processed = [];
  for (var i = 0; i < menu.length; i++) {
    processed[i]=menu[i];
    processed[i].href = root + '#/' + menu[i].uri;
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
    templateUrl:'sys/__test/views/flexy-menu.html',

    controller : ['$scope', '$http', '$log', function($scope, $http, $log) {
      $scope.root = "admin/__test";
      $scope.menu = [];
      $scope.menu.header = [ { href: $scope.root+"/logout", name: 'Logout' } ];
      $scope.menu.sidebar = [];
      $scope.menu.footer = [];
      $http.get('__api/get_admin_nav?_ajax=1',{cache:true}).success(function(result){
        $scope.menu.header  = flexyMenu.uris_to_href($scope.root,result.data.header);
        $scope.menu.sidebar = flexyMenu.uris_to_href($scope.root,result.data.sidebar);
        $scope.menu.footer  = flexyMenu.uris_to_href($scope.root,result.data.footer);
      }).error(function(data){
        $log.log('AJAX error -> flexyMenu');
      });
      
    }],
    
  }
});
