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


'use strict';

flexyAdmin.config(['flexyConstants','$httpProvider',function(constants,$httpProvider) {
  
  // Globaly sets all base URL's
  $httpProvider.interceptors.push(function ($q) {
    return {
      'request': function (config) {
        if (config.url.substr(-5)=='.html') {
          if (config.url.indexOf('flexy')>=0) {
            // Redirect flexy HTML views
            config.url = constants.sys_folder + config.url;
          }
        }
        else {
          // API calls
          config.url = constants.api_base_url + config.url.replace( constants.api_base_url ,''); // TODO hack to prevend double, why?
        }
        return config || $q.when(config);
      }
    }
  });


  // Always a Ajax request: https://stackoverflow.com/questions/12312659/how-to-prevent-angular-js-http-object-from-sending-x-requested-with-header
  $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';


  // Use x-www-form-urlencoded Content-Type
  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

  
  // Override $http service's default transformRequest so each POST param is jquery like: http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
  $httpProvider.defaults.transformRequest.push(function(data) {
    var requestString='';
    if (data) {
      data=JSON.parse(data);
      requestString=jdb.serializeJSON(data);
    }
    return requestString;
  });

  
}]);
