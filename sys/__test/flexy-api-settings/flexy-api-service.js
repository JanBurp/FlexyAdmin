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
   * GET wrapper voor call()
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
   * POST wrapper voor call()
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
   * response.config data word in settings gezet.
   * 
   * @param string method 'GET','POST'
   * @param string api    Welke api aanroep: 'table', 'row' etc.
   * @param object args   Object met alle mee te sturen paramaters/data
   * @param object cfg    Object met de gevraagd config, bijvoorbeeld {table_info:true,field_info:true}
   * @return Promise
   */
  flexy_api_service.call = function(method,api,args,cfg) {
    method = method.toUpperCase();
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
    // console.log('API service',config.url,config.params);
    
    // API CALL
    return $http(config).then(function(response){
      // Als er config data is, bewaar die in settings
      if (angular.isDefined(response.data.config)) {
        var config = response.data.config;
        // table_info
        if (angular.isDefined( config.table_info )) {
          settings.set_item( config.table_info, ['config','table_info', args.table ]);
        }
        // field_info
        if (angular.isDefined( config.field_info )) {
          settings.set_item( config.field_info, ['config','field_info', args.table ]);
        }
      }
      // console.log('API has config in settings', settings.has_item('config') , angular.isDefined(response.data.config), args );
      // Ga verder met Promise
      return response.data;
    },function(errResponse){
      return errResponse;
    });
  };
  
  
  
  //
  // Hieronder de specifieke API aanroepen.
  // Wat niet meer dan wrapper zijn voor de post en get.
  //


  /**
   * API call voor get_admin_nav
   * 
   * @return Promise
   */
  flexy_api_service.get_admin_nav = function() {
    return flexy_api_service.get( 'get_admin_nav' );
  };
  
  /**
   * API call voor auth/check
   * 
   * @return Promise
   */
  flexy_api_service.auth_check = function() {
    return flexy_api_service.get('auth/check');
  };
  
  /**
   * API call voor auth/login
   * 
   * @return Promise
   */
  flexy_api_service.auth_login = function(user) {
    return flexy_api_service.post('auth/login', user );
  };
  
  /**
   * API call voor auth/logout
   * 
   * @return Promise
   */
  flexy_api_service.auth_logout = function(user) {
    return flexy_api_service.get('auth/logout');
  };

  /**
   * API call voor auth/send_new_password
   * 
   * @return Promise
   */
  flexy_api_service.auth_send_new_password = function(email) {
    return flexy_api_service.get('auth/send_new_password',{'email':email});
  };
  
  
  /**
   * API call voor table
   * 
   * @return Promise
   */
  flexy_api_service.table = function(args) {
    return flexy_api_service.get('table', args, ['table_info','field_info'] );
  };

  /**
   * API call voor row (get)
   * 
   * @return Promise
   */
  flexy_api_service.row = function(args ) {
    return flexy_api_service.get('row',args, ['table_info','field_info'] );
  };

  // /**
  //  * API call voor row (insert)
  //  *
  //  * @return Promise
  //  */
  // flexy_api_service.row_insert = function(args,cfg) {
  //   return flexy_api_service.post('row',args,cfg);
  // };

  // TODO: meer api calls
  
  
  
  
  

  return flexy_api_service;
}]);
