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
   * @param string api
   * @return bool
   * @private
   */
  flexy_api_service.has_cfg = function(api) {
    return settings.has_item('cfg',api);
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
   * GET. Eenvoudige wrapper voor call()
   * 
   * @param string api    Welke api aanroep: 'table', 'row' etc.
   * @param object args   Object met alle mee te sturen paramaters/data
   * @param object cfg    Object met de gevraagd config, bijvoorbeeld {table_info:true,field_info:true}
   * @return Promise
   */
  flexy_api_service.get = function(api,args,cfg) {
    return flexy_api_service.call('GET',api,args,cfg);
  };
  

  /**
   * POST. Eenvoudige wrapper voor call()
   * 
   * @param string api    Welke api aanroep: 'table', 'row' etc.
   * @param object args   Object met alle mee te sturen paramaters/data
   * @param object cfg    Object met de gevraagd config, bijvoorbeeld {table_info:true,field_info:true}
   * @return Promise
   */
  flexy_api_service.post = function(api,args,cfg) {
   return flexy_api_service.call('POST',api,args,cfg);
  };
  
  
  /**
   * De $htpp call gebeurt hier.
   * Alles wordt klaargezet en uitgevoerd.
   * 
   * @param string method 'GET','POST'
   * @param string api    Welke api aanroep: 'table', 'row' etc.
   * @param object args   Object met alle mee te sturen paramaters/data
   * @param object cfg    Object met de gevraagd config, bijvoorbeeld {table_info:true,field_info:true}
   * @return Promise
   */
  flexy_api_service.call = function(method,api,args,cfg) {
    method.toUpperCase();
    // Check if cfg is needed
    var needs = flexy_api_service.needs_these_cfg( cfg );
    if (needs.length>0) args['config[]']=needs;
    // setup
    var config = {
      method : method,
      url    : settings.item('api_base_url') + api,
      params : (method=='GET'?args:undefined),
      data   : (method=='POST'?args:undefined),
    };
    
    // call
    return $http(config).then(function(response){
      return response.data;
    },function(errResponse){
      return errResponse;
    });
  };
  
  
  
  
  
  
  
  
  

  return flexy_api_service;
}]);
