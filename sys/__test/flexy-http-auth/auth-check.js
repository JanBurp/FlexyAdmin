/**
 * Test response if user is authenticated
 */

flexyAdmin.factory('authInterceptor',['$q',function($q){
  return {
    responseError : function(rejection) {
      var status=rejection.status;
      if (status=='401') {
        console.error('flexyAdmin ERROR ------------- NOT LOGGED IN - AUTHENTICATION NEEDED ---------');
      }
      return $q.reject(rejection);
    },
  };
}]);


flexyAdmin.config(['$httpProvider',function($httpProvider) {
  $httpProvider.interceptors.push('authInterceptor');
}]);
