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
 */

/**
 * flexy-menu
 */
var flexyMenu = angular.module( 'flexyMenu', []);


/**
 * CONTROLLER
 */
flexyMenu.controller( 'flexyMenuCtrl', [ 'flexySettingsService','flexyAuthService','flexyMenuService','$scope', function(settings,authService,menuService,$scope) {
  
  // Default menu
  var defaultMenu = {
    // header  : [ { href: settings.item('base_url')+"/logout", name: 'Logout' } ],
    header  : [],
    sidebar : [],
    footer  : []
  };

  // Keep Menu
  $scope.menu = defaultMenu;
  
  function load() {
    var menu = {
      header  : menuService.get_processed( 'header' ),
      sidebar : menuService.get_processed( 'sidebar' ),
      footer  : menuService.get_processed( 'footer' ),
    };
    $scope.menu = menu;
  }
  
  function unload() {
    menuService.unload();
    $scope.menu = defaultMenu;
  }
  
  
  /**
   * Make sure menu data is loaded at start
   */
  if ( ! menuService.isLoaded() ) {
    menuService.load().then(function(data){
      load();
    });
  }

  /**
   * Load menu on login
   */
  $scope.$on('event:auth-loginConfirmed', function() {
    menuService.load().then(function(data){
      load();
    });
  });

  /**
   * Reset menu on logout
   */
  $scope.$on('event:auth-loginRequired', function() {
    unload();
  });
  $scope.$on('event:auth-loginCancelled', function() {
    unload();
  });

}]);