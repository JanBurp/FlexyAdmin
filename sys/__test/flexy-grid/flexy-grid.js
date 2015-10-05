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


flexyAdmin.controller('GridController', ['flexySettingsService','flexyGridService','$scope','$routeParams', function(settings,grid,$scope,$routeParams) {
  'use strict';
  var self=this;
  
  /**
   * Basic settings to make it work
   */
  $scope.base_url = settings.item('base_url');
  /**
   * The table
   */
  $scope.table = $routeParams.table;
  /**
   * UI Name (changed when data is present)
   */
  $scope.ui_name = '';
  /**
   * Table Type
   */
  $scope.type = {
    is_sortable  : false,
    is_tree      : false
  };
  /**
   * Info for Pagination (changed when data is present)
   */
  $scope.info = {
    num_rows        : 0,
    limit           : 10,
    total_pages     : 0,
    displayed_pages : 5,
  };
  /**
   * Information about the fields (field_info)
   */
  $scope.fields = [];
  /**
   * Search term
   */
  $scope.search = '';
  
  /**
   * References of the grid data: https://lorenzofox3.github.io/smart-table-website/#section-intro stSafeSrc attribute
   * Set when data is present
   */
  $scope.gridItems = [];
  $scope.displayedItems  = [];
  
  /**
   * LOAD FROM SERVER
   */
  grid.load( $scope.table ).then(function(response){
    // ui_name
    $scope.ui_name = settings.item('config','table_info',$scope.table,'ui_name');
    // table type
    $scope.type.is_tree = settings.item('config','table_info',$scope.table,'tree');
    $scope.type.is_sortable = settings.item('config','table_info',$scope.table,'sortable');
    // info & pagination TODO: pagination is calculated by grid-server
    $scope.info = grid.get_info($scope.table);
    $scope.info.displayed_pages = 5;
    $scope.info.limit = 10;
    $scope.info.total_pages = Math.ceil($scope.info.num_rows / $scope.info.limit);
    // field_info
    $scope.fields = settings.item('config','field_info',$scope.table);
    // data
    $scope.gridItems = grid.get_grid_data($scope.table);
    // Copy the references, needed for smart-table to watch for changes in the data
    $scope.displayedItems = [].concat($scope.gridItems);
    
  });
  
  /**
   * SELECT ALL TOGGLE
   */
  $scope.toggleSelection = function() {
    angular.forEach($scope.gridItems, function(item,key) {
      var selected=$scope.gridItems[key].isSelected;
      if (!selected) selected=true; else selected=false;
      $scope.gridItems[key].isSelected=selected;
    });
  };

  /**
   * MAKE SURE ORDER OF ROWS IS ORIGINAL (keys) : https://stackoverflow.com/questions/19676694/ng-repeat-directive-sort-the-data-when-using-key-value
   * And remove $$hashKey & isSelected & _info
   */
  $scope.orderedKeys = function(obj){
    if (!obj) return [];
    var keys=Object.keys(obj);
    keys.splice( keys.indexOf('_info') ,keys.length); // Remove all keys from '_info'
    return keys;
  };
  
}]);
