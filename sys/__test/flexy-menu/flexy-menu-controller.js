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