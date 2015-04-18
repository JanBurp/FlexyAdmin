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
  
  /**
   * Test of response van een succesvolle login
   */
  function testResponseAuth(response) {
    var loggedIn = (typeof(response)==='object' && response.success===true);
    return loggedIn;
  }
  
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
          isLoggedIn = testResponseAuth(response);
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
        isLoggedIn = testResponseAuth(response);
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
