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
 * Log all $http calls
 */

flexyAdmin.factory('logInterceptor',['flexyAdminGlobals','$q',function(flexyAdminGlobals,$q){

  'use strict';
  
  function _url(config)     { return config.url.replace(flexyAdminGlobals.sys_folder,'').replace(flexyAdminGlobals.api_base_url,''); }
  function _isHTML(config)  { return (config.url.substr(-5)=='.html'); }
  function _isPOST(config)  { return (config.method=='POST'); }
  
  function message(config, delimeter, data) {
    if (!_isHTML(config)) {
      var message=flexyAdminGlobals.log_prefix + ' ' + delimeter + ' ' + config.method;
      if ( _isHTML(config) ) message+=' HTML ';
      message+=' - ' + _url(config);
      if ( angular.isDefined(data) ) {
        if (typeof(data)=='string') {
          angular.element(document.querySelector('#debug')).removeClass('hidden');
          angular.element(document.querySelector('#debug .panel-content')).html(data);
        }
        console.info(message, data);
      }
      else {
        console.info(message);
      }
    }
  }


  return {
    
    request : function(config) {
      message(config,'->');
      return config;
    },
    
    requestError : function(rejection) {
      console.error(flexyAdminGlobals.log_prefix+' Request ERROR due to', rejection);
      return $q.reject(rejection);
    },
    
    response : function(response) {
      message(response.config,'<-',response.data);
      return response || $q.when(response);
    },
    
    responseError : function(rejection) {
      var method=rejection.config.method;
      var url = _url(rejection.config);
      console.error(flexyAdminGlobals.log_prefix+'ERROR '+rejection.status+' -> '+method+' - '+url, rejection);
      return $q.reject(rejection);
    },
    
  };
}]);


flexyAdmin.config(['$httpProvider',function($httpProvider) {
  $httpProvider.interceptors.push('logInterceptor');
}]);
