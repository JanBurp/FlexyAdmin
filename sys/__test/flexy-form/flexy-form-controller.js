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


flexyAdmin.controller('FormController', ['$scope','$routeParams', function($scope,$routeParams) {
  'use strict';

  /**
   * Table & Id
   */
  $scope.table  = $routeParams.table;
  $scope.id     = $routeParams.id;
  
}]);