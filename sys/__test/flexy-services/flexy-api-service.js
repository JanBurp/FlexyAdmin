/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * flexy-api-service handles core api requests.
 * Also ask for cfg info if asked for and not present
 * 
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

flexyAdmin.factory( 'flexyApiService', ['flexySettingsService','$http',function(settings,$http) {
  'use strict';
  
  var flexy_api_service = {};
  
  /**
   * Test if a certain cfg time is available (table_info|field_info|...).
   * Returns true/false
   * 
   * @param string type
   * @return bool
   * @private
   */
  flexy_api_service.has_cfg = function(type) {
    return settings.has_item('cfg',type);
  };


  /**
   * Checks if given cfg are available. Returns the cfg that ar not available
   * 
   * @param array cfg
   * @return array
   * @private
   */
  flexy_api_service.needs_these_cfg = function(cfg) {
    if (! angular.isDefined(cfg)) return false;
    var needs=[];
    for (var i = 0; i < cfg.length; i++) {
      if ( ! flexy_api_service.has_cfg(cfg[i]) ) {
        needs.push(cfg[i]);
      }
    }
    return needs;
  };
  

  
  /**
   * Core api.get call
   * 
   */
  flexy_api_service.get = function(type,params,cfg) {
    // Check if cfg is needed
    var needs = flexy_api_service.needs_these_cfg( cfg );
    if (needs.length>0) {
      params.config=needs;
    }
    // url
    var url = settings.item('api_base_url') + type;
    // call
    return $http.get( url, {params:params} ).then(function(response){
      return response.data;
    },function(errResponse){
      return errResponse;
    });
  };
  
  
  
  
  
  
  
  
  

  return flexy_api_service;
}]);
