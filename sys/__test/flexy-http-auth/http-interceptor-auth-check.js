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
      if (status=='404') {
        console.error('flexyAdmin ERROR ------------- API/FILE NOT FOUND ---------');
      }
      return $q.reject(rejection);
    },
  };
}]);


flexyAdmin.config(['$httpProvider',function($httpProvider) {
  $httpProvider.interceptors.push('authInterceptor');
}]);
