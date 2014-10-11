/**
 * Make AngularJS $http service behave like jQuery.ajax()
 * http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
 */

flexyAdmin.config(['$httpProvider',function($httpProvider){

  // Use x-www-form-urlencoded Content-Type
  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
  
  // Override $http service's default transformRequest so each param is jquery like
  $httpProvider.defaults.transformRequest.push(function(data) {
    var requestString;
    if (data) {
      data=JSON.parse(data);
      for (var key in data) {
        if (requestString) requestString+='&';
        requestString = '&'+key+'='+data[key];
      }
      // Add _type=json& - JdB 21 september 2014
      requestString+='&_type=json&';
    }
    return requestString;
  });

  
}]);
