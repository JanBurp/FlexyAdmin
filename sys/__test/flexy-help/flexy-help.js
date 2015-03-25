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


flexyAdmin.controller('flexyHelpController', ['$scope','$routeParams','$http','$sce', function($scope,$routeParams,$http,$sce) {
  'use strict';
  
  var self = this;
  
  $scope.page = $routeParams.page;
  $scope.title = '';
  $scope.help  = '';

  $http.get('get_help?page='+$scope.page).success(function(result){
    $scope.title  = result.data.title;
    $scope.help   = $sce.trustAsHtml(result.data.help);
  }).error(function(data){
    $log.log('AJAX error -> Help');
  });
  
}]);