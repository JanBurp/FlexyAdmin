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




/**
 * CONTROLLER
 */
flexyMenu.controller( 'flexyMenuCtrl', [ 'flexySettingsService','flexyMenuService','$scope', function(settings,menuService,$scope) {
  
  // Default menu
  $scope.menu = {
    header  : [ { href: settings.item('base_url')+"/logout", name: 'Logout' } ],
    sidebar : [],
    footer  : []
  };
  
  /**
   * Make sure menu data is loaded
   */
  if ( ! menuService.isLoaded() ) {
    menuService.load().then(function(data){
      var menu = {
        header  : menuService.get_processed( 'header' ),
        sidebar : menuService.get_processed( 'sidebar' ),
        footer  : menuService.get_processed( 'footer' ),
      };
      $scope.menu = menu;
    });
  }
  
}]);


/**
 * DIRECTIVE
 */
flexyMenu.directive( "flexyMenu", ['flexySettingsService','$location',function(settings,$location) {
  'use strict';
  
  return {
    restrict: "E",
    scope: {
      type    : "@",
      uiclass : "@",
      items   : "="
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
    }
    
  };
}]);
