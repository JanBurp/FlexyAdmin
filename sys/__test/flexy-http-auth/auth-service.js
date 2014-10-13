flexyAdmin.factory('authService',['$http',function($http){
  var service = {
    isLoggedIn: false,
    
    /**
     * Check user session
     */
    session : function(){
      return $http.get('auth').then(function(response) {
        service.isLoggedIn = true;
        return response;
      });
    },
    
    /**
     * Try to login a user
     */
    login : function(user){
      return $http.post('auth', user).then(function(response){
        service.isLoggedIn = true;
        return response;
      });
      
    }
    
  };
  return service;
}]);