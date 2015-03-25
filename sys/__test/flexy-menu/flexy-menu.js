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


flexyMenu.directive( "flexyMenu", ['flexySettingsService','$location',function(Settings,$location) {
  'use strict';
  
  // Default menu
  var menu = {
    header  : [ { href: Settings.item('base_url')+"/logout", name: 'Logout' } ],
    sidebar : [],
    footer  : [],
  };
  
  return {
    restrict: "E",
    scope: {
      type    : "@",
      uiclass : "@",
    },
    templateUrl:'flexy-menu/flexy-menu.html',
    
    /**
     * Test of item is het actieve menu item
     * 
     * @return bool
     */
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
    
    /**
     * Internal menu controller
     */
    controller : ['$scope','flexyMenuService', function($scope,MenuService) {
      $scope.menu = menu;

      /**
       * Make sure menu data is loaded
       */
      if ( ! MenuService.isLoaded() ) {
        MenuService.load().then(function(data){
          var menu = {
            header  : MenuService.get_processed( 'header' ),
            sidebar : MenuService.get_processed( 'sidebar' ),
            footer  : MenuService.get_processed( 'footer' ),
          };
          $scope.menu = menu;
        });
      };
    }],
    
  };
}]);
