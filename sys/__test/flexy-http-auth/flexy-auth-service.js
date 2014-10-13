'use strict';

flexyAdmin.factory('flexyAuthService',['$http',function($http){
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
      return $http.get('auth/check').then(
        function(response) {
          isLoggedIn = true;
          return response;
        },
        function(error) {
          isLoggedIn = false;
          return error;
        }
      )
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
        return response;
      });
    },

  };
}]);
