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
