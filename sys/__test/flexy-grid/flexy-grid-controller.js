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

/*jshint -W069 */

flexyAdmin.controller('GridController', ['$scope','$routeParams', function($scope,$routeParams) {
  'use strict';
  
  /**
   * The table
   */
  $scope.table = $routeParams.table;
  
}]);
