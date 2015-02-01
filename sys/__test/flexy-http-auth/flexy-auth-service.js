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


flexyAdmin.factory('flexyAuthService',['$http','$cacheFactory',function($http,$cacheFactory){
  'use strict';
  
  var isLoggedIn = null;
  
  return  {

    /**
     * Return login state
     */
    loggedIn : function() {
      return isLoggedIn;
    },

    /**
     * Check user session
     */
    check : function(){
      return $http.get('auth/check').then(
        function(response) {
          isLoggedIn = true;
          return response;
        },
        function(error) {
          isLoggedIn = false;
          return error;
        }
      );
    },
    
    /**
     * Try to login a user
     */
    login : function(user){
      return $http.post('auth/login', user).then(function(response){
        isLoggedIn = true;
        return response;
      });
    },
    
    /**
     * Logout
     */
    logout : function(){
      return $http.post('auth/logout').then(function(response){
        isLoggedIn = false;
        $cacheFactory.get('$http').removeAll(); // remove cache
        return response;
      });
    },
    
    /**
     * Send new password to user
     */
    send_password : function(email) {
      return $http.post('auth/send_new_password',{'email':email}).then(function(response){
        return response;
      });
    }

  };
}]);
