/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * Verzorgt de api aanroepen
 * En het verzamelen van settings van de gevraagde api als die nog niet voorhanden zijn.
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
   * Test of bepaalde api settings al bestaan
   * Geeft true/false
   * 
   * @param string type [table|path]
   * @param string what voorbeeld: 'tbl_menu' of 'pictures'
   * @return bool
   * @private
   */
  flexy_api_service.has_settings = function(type,what) {
    return settings.has_item(['settings',type,what]);
  };

  /**
   * GET wrapper voor call()
   * 
   * @param string api         Welke api aanroep: 'table', 'row' etc.
   * @param object args        Object met alle mee te sturen paramaters/data
   * @return Promise
   */
  flexy_api_service.get = function( api,args ) {
    return flexy_api_service.call('GET',api,args);
  };

  /**
   * POST wrapper voor call()
   * 
   * @param string api    Welke api aanroep: 'table', 'row' etc.
   * @param object args   Object met alle mee te sturen paramaters/data
   * @return Promise
   */
  flexy_api_service.post = function( api,args ) {
   return flexy_api_service.call('POST',api,args);
  };
  
  
  /**
   * De $htpp call gebeurt hier.
   * Alles wordt klaargezet en uitgevoerd.
   * Er wordt ook gekeken of er bij bepaalde api's nog settings moeten worden opgevraagd
   * 
   * @param string method       'GET','POST'
   * @param string api          Welke api aanroep: 'table', 'row' etc.
   * @return Promise
   */
  flexy_api_service.call = function( method,api,args ) {
    // args klaarzetten
    if (angular.isUndefined(args)) args = {};

    // Table settings
    if ((api==='table' || api==='row') && !flexy_api_service.has_settings( 'table', args.table )) {
      args.settings = true;
    }
    // Media settings
    if (api==='table' && args.table==='_media_' && !flexy_api_service.has_settings( 'path', args.path )) {
      args.settings = true;
    }
    if (api==='media' && !flexy_api_service.has_settings( 'path', args.path )) {
      args.settings = true;
    }
    
    // Klaar zetten van alle argumenten
    var config = {
      method : method.toUpperCase(),
      url    : settings.item('api_base_url') + api,
      params : (method=='GET'?args:undefined),
      data   : (method=='POST'?args:undefined),
    };
    
    // API CALL
    console.log('API '+api+' ? ',config);
    return $http(config).then(function(response){
      // Als er setting data is, bewaar die in settings
      if ( angular.isDefined(response.data) && response.data!==null) {
       console.log('API '+api+' ! ',response.data);
        if ( angular.isDefined(response.data.settings) ) {
          if (angular.isDefined(response.data.settings.media_info)) {
            settings.set_item( response.data.settings.media_info, ['settings','path', args.path ]);
            delete(response.data.settings.media_info);
          }
          // if (api=='media') {
          //   settings.set_item( response.data.settings, ['settings','path', args.path ]);
          // }
          if (api=='table' || api=='row') {
            settings.set_item( response.data.settings, ['settings','table', args.table ]);
          }
        }
      }
      
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
    return flexy_api_service.get('table', args );
  };
  
  /**
   * API call voor nieuwe volgorde van items in tabel
   * 
   * @return Promise
   */
  flexy_api_service.table_order = function(args) {
    return flexy_api_service.post('table_order', args);
  };


  /**
   * API call voor row (get)
   * 
   * @return Promise
   */
  flexy_api_service.row = function(args ) {
    return flexy_api_service.get('row',args );
  };
  

  /**
   * API call voor row delete (post)
   * 
   * @return Promise
   */
  flexy_api_service.delete = function(args ) {
    return flexy_api_service.post('row', args );
  };
  
  
  /**
   * API call voor row (update/insert)
   *
   * @return Promise
   */
  flexy_api_service.update = function(args) {
    return flexy_api_service.post( 'row', args );
  };
  
  return flexy_api_service;
}]);
