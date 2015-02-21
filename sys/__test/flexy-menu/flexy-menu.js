/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 * $HeadURL$ 
 */


/**
 * flexy-menu
 */
var flexyMenu = angular.module( 'flexyMenu', []);

flexyMenu.process = function(root,menu) {
  'use strict';
  
  var classes = {
    'media' :{'class':'','glyphicon':'glyphicon glyphicon-picture'},
    'tools' :{'class':'text-muted','glyphicon':'glyphicon glyphicon-wrench'},
    'config':{'class':'text-muted','glyphicon':'glyphicon glyphicon-cog'},
    'log'   :{'class':'text-muted','glyphicon':'glyphicon glyphicon-stats'},
    'result':{'class':'text-info','glyphicon':'glyphicon glyphicon-cloud'},
    'rel'   :{'class':'text-muted','glyphicon':'glyphicon glyphicon-link'}
  };
  
  var item_classes = {
    'help/index' :{'class':'','glyphicon':'glyphicon glyphicon-question-sign'},
    'form/cfg_users/current' :{'class':'','glyphicon':'glyphicon glyphicon-user'},
    'logout' :{'class':'','glyphicon':'glyphicon glyphicon-off'},
    'form/tbl_site/first' :{'class':'','glyphicon':'glyphicon glyphicon-cog'},
    'plugin/stats' :{'class':'','glyphicon':'glyphicon glyphicon-stats'},
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
      var thisClass=item_classes[menu[i].uri];
      if (!thisClass) thisClass=classes[menu[i].type];

      if (angular.isDefined(thisClass)) {
        processed[navbar][item].class += ' '+thisClass.class;
        processed[navbar][item].glyphicon = thisClass.glyphicon;
      }
      item++;
    }
  }
  
  return processed;
};    


flexyMenu.directive( "flexyMenu", ['flexyAdminGlobals','$location',function(flexyAdminGlobals,$location) {
  'use strict';
  
  return {
    restrict: "E",
    scope: {
      type   : "@",
      uiclass : "@",
    },
    templateUrl:'flexy-menu/flexy-menu.html',
    
    // Test of item is het actieve menu item
    link: function($scope, element, attrs) {
      $scope.isActive = function(href) {
        if (angular.isDefined(href)) {
          var path=$location.path();
          href=href.substr(href.indexOf('#')+1);
          return (href==path);
        }
        return false;
      };
    },

    controller : ['$scope', '$http', function($scope, $http) {
      $scope.menu = [];
      $scope.menu.header = [ { href: flexyAdminGlobals.base_url+"/logout", name: 'Logout' } ];
      $scope.menu.sidebar = [];
      $scope.menu.footer = [];
      $http.get('get_admin_nav',{cache:true}).then(function(result){
        var menu=result.data.data.menu;
        $scope.menu.header  = flexyMenu.process( flexyAdminGlobals.base_url, menu.header );
        $scope.menu.sidebar = flexyMenu.process( flexyAdminGlobals.base_url, menu.sidebar );
        $scope.menu.footer  = flexyMenu.process( flexyAdminGlobals.base_url, menu.footer );
      });
      
    }],
    
  };
}]);
