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


flexyAdmin.controller('flexyHelpController', ['$scope','$routeParams','$http','$sce', function($scope,$routeParams,$http,$sce) {
  'use strict';
  
  var self = this;
  
  $scope.page = $routeParams.page;
  $scope.title = '';
  $scope.help  = '';

  $http.post('get_help',{'page':$scope.page}).success(function(result){
    $scope.title  = result.title;
    $scope.help   = $sce.trustAsHtml(result.help);
    // $scope.help   = result.help;
  }).error(function(data){
    $log.log('AJAX error -> Plugin');
  });
  
}]);