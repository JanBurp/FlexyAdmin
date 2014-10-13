/**
 * Test response if user is authenticated
 */

flexyAdmin.factory('authInterceptor',['$q','$location',function($q,$location){
  return {
    responseError : function(rejection) {
      var status=rejection.status;
      if (status=='401') {
        console.error('flexyAdmin ERROR ------------- AUTHENTICATION NEEDED ---------');
      }
      if (status=='404') {
        console.error('flexyAdmin ERROR ------------- API/FILE NOT FOUND ---------');
      }

      // Proceed as normal
      if ($location.path()=='/logout') {
        return(rejection);
      }
      
      return $q.reject(rejection);
    },
  };
}]);


flexyAdmin.config(['$httpProvider',function($httpProvider) {
  $httpProvider.interceptors.push('authInterceptor');
}]);
