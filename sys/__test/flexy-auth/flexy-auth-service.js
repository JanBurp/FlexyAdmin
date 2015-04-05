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


flexyAdmin.factory('flexyAuthService',['flexyApiService',function(api){
  'use strict';
  
  var isLoggedIn = false;
  
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
      return api.auth_check().then(
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
      return api.auth_login( user ).then(function(response){
        isLoggedIn = true;
        return response;
      });
    },
    
    /**
     * Logout
     */
    logout : function(){
      return api.auth_logout().then(function(response){
        isLoggedIn = false;
        return response;
      });
    },
    
    /**
     * Send new password to user
     */
    send_password : function(email) {
      return api.auth_send_new_password( email ).then(function(response){
        return response;
      });
    }

  };
}]);
