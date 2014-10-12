/**
 * Log all $http calls
 */

flexyAdmin.factory('logInterceptor',['$q',function($q){
  var pre='FA ';
  return {
    
    request : function(config) {
      var message=pre;
      var method=config.method;
      var url=config.url;
      message+='-> '+method;
      if (url.substr(-5)=='.html') message+=' HTML ';
      message+=' - '+url;
      var data=undefined;
      if (method=='POST') data=config.data;
      console.log(message, data );
      return config;
    },
    
    requestError : function(rejection) {
      console.error(pre+' Request ERROR due to', rejection);
      return $q.reject(rejection);
    },
    
    response : function(response) {
      var message=pre;
      var method=response.config.method;
      var url=response.config.url;
      message+='<- '+method+' - '+url;
      if (url.substr(-5)!='.html') console.log(message, response.data );
      return response || $q.when(response);
    },
    
    responseError : function(rejection) {
      var method=rejection.config.method;
      var url=rejection.config.url;
      console.error(pre+'ERROR '+rejection.status+' -> '+method+' - '+url, rejection);
      return $q.reject(rejection);
    },
    
  };
}]);


flexyAdmin.config(['$httpProvider',function($httpProvider) {
  $httpProvider.interceptors.push('logInterceptor');
}]);
