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


'use strict';

/**
 * Test response if user is authenticated
 */

flexyAdmin.factory('authInterceptor',['flexySettingsService','$q','$location',function(settings,$q,$location){
  return {
    responseError : function(rejection) {
      var status=rejection.status;
      
      // Proceed as normal when logout
      if ($location.path()=='/logout') {
        return(rejection);
      }
      // Or bad login
      if (rejection.config.url==settings.item('api_base_url')+'auth/login') {
        return(rejection);
      }
      
      if (status=='401') {
        console.error('ERROR ------------- AUTHENTICATION NEEDED ---------');
      }
      if (status=='404') {
        console.error('ERROR ------------- API/FILE NOT FOUND ---------');
      }
      return $q.reject(rejection);
    },
  };
}]);


flexyAdmin.config(['$httpProvider',function($httpProvider) {
  $httpProvider.interceptors.push('authInterceptor');
}]);
