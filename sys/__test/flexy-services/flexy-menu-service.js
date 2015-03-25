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

flexyAdmin.factory( 'flexyMenuService', ['flexySettingsService','flexyApiService',function(settings,api) {
  'use strict';

  var loaded_menu = undefined;
  
  /**
   * Classes & glyphicons
   */
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

  
  /**
   * METHODS
   */
  var flexy_menu_service = {};
  

  /**
   * Test if menu is loaded.
   * 
   * @return bool TRUE if menu is loaded
   */
  flexy_menu_service.isLoaded = function() {
    return angular.isDefined(loaded_menu);
  }
  
  
  /**
   * Loads menu if not present
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
  };
  
  /**
   * Returns a processed menu object.
   * 
   * @param   string menutype 'header', 'sidebar' or 'footer'
   * @return  object the menuobject
   */
  flexy_menu_service.get_processed = function(menu) {
    return flexy_menu_service.process(loaded_menu[menu]);
  };
  
  
  /**
   * Process menu: sets classes, glyphicons etc.
   */
  flexy_menu_service.process = function(menu) {
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
        processed[navbar][item].href   = settings.item('base_url') + '#/' + menu[i].uri;
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



  return flexy_menu_service;
}]);
