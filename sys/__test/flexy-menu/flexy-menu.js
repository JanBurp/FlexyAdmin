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
  var navbar=0;
  var item=0;
  processed[navbar]=[];
  
  for (var i = 0; i < menu.length; i++) {
    // seperator starts new navbar
    if (menu[i].type=='seperator') {
      // only if not the first
      if (processed[navbar].length>0) {
        navbar++;
        processed[navbar]=[];
        item=0;
      }
    }
    else {
      processed[navbar][item]=menu[i];
      processed[navbar][item].href   = root + '#/' + menu[i].uri;
      processed[navbar][item].class  = 'menu-type-'+menu[i].type;
      processed[navbar][item].glyphicon = '';
      var thisClass=classes[menu[i].type];
      if (angular.isDefined(thisClass)) {
        processed[navbar][item].class += ' '+thisClass.class;
        processed[navbar][item].glyphicon = thisClass.glyphicon;
      }
      item++;
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
    templateUrl:'flexy-menu/flexy-menu.html',

    controller : ['$scope', '$http', '$log', function($scope, $http, $log) {
      $scope.root = "admin/__test";
      $scope.menu = [];
      $scope.menu.header = [ { href: $scope.root+"/logout", name: 'Logout' } ];
      $scope.menu.sidebar = [];
      $scope.menu.footer = [];
      $http.get('get_admin_nav',{cache:true}).then(function(result){
        var data=result.data.data;
        $scope.menu.header  = flexyMenu.process( $scope.root, data.header );
        $scope.menu.sidebar = flexyMenu.process( $scope.root, data.sidebar );
        $scope.menu.footer  = flexyMenu.process( $scope.root, data.footer );
      });
      
    }],
    
  }
});
