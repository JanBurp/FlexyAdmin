var flexyAdmin = angular.module( 'flexyAdmin', [
  // Angular JS
  'ngRoute',
  // Angular Modules
  'angular-loading-bar',
  'trNgGrid',
  // flexyAdmin Modules
  'flexyMenu',
  'flexyBlocks',
  ],
  
/**
 * Make AngularJS $http service behave like jQuery.ajax()
 * http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
 */
  function($httpProvider) {
    // Use x-www-form-urlencoded Content-Type
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    /**
     * The workhorse; converts an object to x-www-form-urlencoded serialization.
     * @param {Object} obj
     * @return {String}
     */ 
    var param = function(obj) {
      var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

      for(name in obj) {
        value = obj[name];
        
        if(value instanceof Array) {
          for(i=0; i<value.length; ++i) {
            subValue = value[i];
            fullSubName = name + '[' + i + ']';
            innerObj = {};
            innerObj[fullSubName] = subValue;
            query += param(innerObj) + '&';
          }
        }
        else if(value instanceof Object) {
          for(subName in value) {
            subValue = value[subName];
            fullSubName = name + '[' + subName + ']';
            innerObj = {};
            innerObj[fullSubName] = subValue;
            query += param(innerObj) + '&';
          }
        }
        else if(value !== undefined && value !== null)
          query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
      }
      
      // Add _type=json& - JdB 21 september 2014
      query+='_type=json&';
      
      return query.length ? query.substr(0, query.length - 1) : query;
    };
    
    // Override $http service's default transformRequest
    $httpProvider.defaults.transformRequest = [function(data) {
      return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
    }];
  }

);
  





/**
 * ROUTING
 */

flexyAdmin.config( function($routeProvider){
  $routeProvider
    .when('/grid/:table',{
      controller  : 'GridController',
      templateUrl : 'sys/__test/views/grid.html'
    })
    .when('/form/:table/:id',{
      controller  : 'FormController',
      templateUrl : 'sys/__test/views/form.html'
    })
    // .when('/plugin/:plugin',{
    //   controller  : 'PluginController',
    //   templateUrl : 'sys/__test/views/html.html'
    // })
    
    .otherwise({ redirectTo: '/grid' });
});
