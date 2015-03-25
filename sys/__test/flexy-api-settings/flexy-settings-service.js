/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * 
 * Wrapper service for the settings, see for the settings in flexy-settings.js
 * 
 * item( key's ) 
 * set_item( value, key's )
 * has_item( key's )
 * delete_item( key's )
 * 
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 */

flexyAdmin.factory( 'flexySettingsService', ['flexyConstants','flexySettings', function(constants,settings) {
  'use strict';
  
  var flexy_settings_service = {};
  
  /**
   * Gets an item from the settings object.
   * If not found, returns 'undefined', else returns the settings value.
   * 
   * item('part','subpart', ... ); // as multiple arguments, each argument is a key
   * or
   * item(  ['part','subpart', ...] ); // as one array arguments, each array part is a key
   * 
   */
  flexy_settings_service.item = function() {
    var args = angular.copy( Array.prototype.slice.call(arguments) );
    var setting;
    if (args.length>0) {
      if (angular.isArray(args[0])) args=args[0]; // if first arg is an array that one holds all args
      setting = settings;
      for (var i = 0; i < args.length; i++) {
        // Test if setting and setting[arg] is defined, if so, setting will become this item
        if (angular.isDefined(setting)) {
          if (angular.isDefined( setting[ args[i] ] )) {
            setting = setting[ args[i] ];
          }
          else {
            setting = undefined;
          }
        }
      }
    }
    return setting;
  };
  

  /**
   * Test if item exists.
   * Returns TRUE or FALSE
   */
  flexy_settings_service.has_item = function() {
    var args = angular.copy( Array.prototype.slice.call(arguments) );
    if (angular.isArray(args[0])) args=args[0]; // if first arg is an array that one holds all args
    // test if exist allready
    return angular.isDefined( flexy_settings_service.item(args) );
  };
  

  /**
   * Sets an item in the settings object, overwriting if existed. Returns TRUE if a new item, FALSE if an existing item.
   * 
   * set_item( value, 'part','subpart' ...)
   * or
   * set_item( value, ['part','subpart' ...]) // as array
   */
  flexy_settings_service.set_item = function() {
    var args = angular.copy( Array.prototype.slice.call(arguments) );
    var value = args.shift();
    if (angular.isArray(args[0])) args=args[0]; // if first arg is an array that one holds all args
    // test if exist allready
    var exists = flexy_settings_service.has_item(args);
    
    // traverse to settings object
    var copy=settings;
    while (args.length) {
      var arg = args.shift();
      if (arg in copy) {
        copy = copy[arg];
      }
      else {
        if (args.length===0) {
          copy[arg] = value;
        }
        else {
          copy[arg] = {};
          copy = copy[arg];
        }
      }
    }
    return !exists;
  };
  
  
  /**
   * Deletes an item in the settings, rerturning old value if set, or undefined
   * 
   * delete_item( 'part','subpart' ...)
   * or
   * delete_item( ['part','subpart' ...]) // as array
   */
  flexy_settings_service.delete_item = function() {
    var args = angular.copy( Array.prototype.slice.call(arguments) );
    if (angular.isArray(args[0])) args=args[0]; // if first arg is an array that one holds all args
    // test if exist allready
    var exists = flexy_settings_service.has_item(args);
    if (!exists) return undefined;
    // exists, get current value and then delete it
    var value = flexy_settings_service.item(args);
    // delete it, traverse to settings object
    var copy=settings;
    while (args.length) {
      var arg = args.shift();
      if (arg in copy) {
        if (args.length===0) {
          // delete it
          delete copy[arg];
        }
        else {
          copy = copy[arg];
        }
      }
    }
    return value;
  };
  
  
  
  /**
   * Return flexy_settings_service
   */
  return flexy_settings_service;
}]);
