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


flexyAdmin.controller('flexyPluginController', ['$scope','$routeParams','$http','$sce', function($scope,$routeParams,$http,$sce) {
  'use strict';
  
  var self = this;
  
  $scope.plugin = $routeParams.plugin;
  $scope.title = '';
  $scope.html  = '';

  $http.get('get_plugin?plugin='+$scope.plugin).success(function(result){
    $scope.title  = result.data.title;
    $scope.html   = $sce.trustAsHtml(result.data.html);
  }).error(function(data){
    $log.log('AJAX error -> Plugin');
  });
  
}]);