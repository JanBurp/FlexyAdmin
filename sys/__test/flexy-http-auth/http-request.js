/**
 * Make AngularJS $http service behave like jQuery.ajax()
 * http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
 */

flexyAdmin.config(['flexyAdminGlobals','$httpProvider',function(flexyAdminGlobals,$httpProvider) {
  
  // Globaly sets all base URL's
  $httpProvider.interceptors.push(function ($q) {
    return {
      'request': function (config) {
        if (config.url.substr(-5)=='.html') {
          // HTML views
          config.url = flexyAdminGlobals.sys_folder + config.url;
        }
        else {
          // API calls
          config.url = flexyAdminGlobals.api_base_url + config.url;
        }
        return config || $q.when(config);
      }
    }
  });


  // Always a Ajax request: https://stackoverflow.com/questions/12312659/how-to-prevent-angular-js-http-object-from-sending-x-requested-with-header
  $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';


  // Use x-www-form-urlencoded Content-Type
  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

  
  // Override $http service's default transformRequest so each POST param is jquery like
  $httpProvider.defaults.transformRequest.push(function(data) {
    var requestString='';
    if (data) {
      data=JSON.parse(data);
      for (var key in data) {
        if (requestString) requestString+='&';
        requestString += key+'='+data[key];
      }
    }
    return requestString;
  });

  
}]);
