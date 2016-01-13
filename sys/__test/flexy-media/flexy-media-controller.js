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

flexyAdmin.controller('MediaController', ['$scope','$routeParams', function($scope,$routeParams) {
  'use strict';
  
  /**
   * The path
   */
  $scope.table = 'res_media_files';
  $scope.path = $routeParams.path;
  
}]);
