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
 * $HeadURL$ 
 */


'use strict';

flexyAdmin.controller('PluginController', ['$scope','$routeParams','$http', '$log', '$sce', function($scope,$routeParams,$http,$log,$sce) {
  
  var self = this;
  
  $scope.plugin = $routeParams.plugin;
  $scope.uri = 'plugin/'+$scope.plugin;
  
  $scope.title    = $scope.plugin;
  $scope.html  = '';
  
  $http.post('get_html',{'uri':$scope.uri,'name':$scope.plugin}).success(function(result){
    $log.log(result);
    $scope.title   = result.title;
    $scope.html = $sce.trustAsHtml(result.html);
  }).error(function(data){
    $log.log('AJAX error -> Plugin');
  });
  
  
}]);