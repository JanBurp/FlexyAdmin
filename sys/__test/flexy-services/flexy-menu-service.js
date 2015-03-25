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
 * flexy-menu-service
 */

flexyMenu.factory( 'flexyMenuService', ['flexyApiService',function(api) {
  'use strict';
  
  var loaded_menu = undefined;
  
  var flexy_menu_service = {};
  
  
  /**
   * Returns a menu object. Loads menu if not present
   * 
   * @param   string menutype 'header', 'sidebar' or 'footer'
   * @return  object the menuobject
   */
  flexy_menu_service.load = function(menu) {
    if (angular.isUndefined(loaded_menu)) {
      return api.get( 'get_admin_nav' ).then(function(response){
        loaded_menu = response.data;
        return true;
      });
    }
    // return flexy_menu_service._get(menu);
  };

  
  /**
   * Returns a menu object.
   * 
   * @param   string menutype 'header', 'sidebar' or 'footer'
   * @return  object the menuobject
   */
  flexy_menu_service.get = function(menu) {
    if (angular.isUndefined(loaded_menu)) return undefined;
    if (angular.isDefined(menu)) return loaded_menu[menu];
    return loaded_menu;
  }
  
  
  flexy_menu_service.get_header = function() {
    
  }
  
  

  return flexy_menu_service;
}]);
