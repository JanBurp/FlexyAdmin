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

flexyAdmin.controller('GridController', ['flexySettingsService','flexyApiService','flexyGridService','$scope','$routeParams','$translate','dialogs','flexyAlertService', function(settings,api,grid,$scope,$routeParams,$translate,dialogs,alertService) {
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
    limit           : 0,
    num_pages       : 0,
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
   * Copy of tableState
   */
  $scope.tableState = {};
  
  /**
   * References of the grid data: https://lorenzofox3.github.io/smart-table-website/#section-intro stSafeSrc attribute
   * Set when data is present
   */
  $scope.gridItems = [];
  $scope.displayedItems  = [];
  
  /**
   * LOAD FROM SERVER
   */
  $scope.pipe = function(tableState) {
    $scope.tableState = tableState;
    
    // pagination
    var args = {
      offset  : tableState.pagination.start,
      limit   : settings.item(['screen','pagination'])
    };
    // sorting
    if ( angular.isDefined( tableState.sort.predicate ) ) {
      args.sort = tableState.sort.predicate;
      if (tableState.sort.reverse===true) args.sort = '_' + args.sort;
    }
    // filter
    if ( angular.isDefined( tableState.search.predicateObject ) ) {
      args.filter = tableState.search.predicateObject.$;
    }
    
    // console.log(tableState);
    // console.log('load args:',args);
    // console.log( settings.item( ['settings','table', $scope.table] ) );
    
    grid.load( $scope.table, args ).then(function(response){
      // ui_name
      $scope.ui_name = settings.item( 'settings','table',$scope.table,'table_info','ui_name' );
      // table type
      $scope.type.is_tree = settings.item( 'settings','table',$scope.table,'table_info','tree');
      $scope.type.is_sortable = settings.item( 'settings','table',$scope.table,'table_info','sortable');
      // info & pagination
      $scope.info = grid.get_info($scope.table);
      $scope.info.num_pages = Math.ceil($scope.info.total_rows / $scope.info.limit);
      tableState.pagination.numberOfPages = $scope.info.num_pages;
      // data
      $scope.gridItems = grid.get_grid_data($scope.table);
      // Copy the references, needed for smart-table to watch for changes in the data
      $scope.displayedItems = [].concat($scope.gridItems);
      // field_info, show only the fields in the gridItems
      $scope.fields = settings.item('config','field_info',$scope.table);
      var first_item = jdb.firstArrayItem( $scope.gridItems );
      if ( angular.isDefined( first_item )) {
        angular.forEach( $scope.fields, function(value, field) {
          if ( angular.isUndefined( first_item[field] )) {
            delete $scope.fields[field];
          }
        });
      }

    });
  };
  
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
   * DELETE ITEM
   */
  $scope.delete = function( table, id ) {
    var abstract = grid.get_abstract( table,id );
    $translate(['DIALOGS_SURE','DIALOGS_DELETE_ITEM','DIALOGS_DELETED','DIALOGS_DELETE_ERROR']).then(function (translations) {
      var confirm = dialogs.confirm( translations.DIALOGS_SURE, '<b>'+translations.DIALOGS_DELETE_ITEM+'</b><br>'+abstract, { 'size' : 'sm' } );
      confirm.result.then(function(btn){
        api.delete( { 'table':table, 'where':id }).then(function(response){
          if (response.success===true && response.data===true) {
            // Reload page
            $scope.pipe( $scope.tableState );
            alertService.add( 'success', abstract+' <b>'+translations.DIALOGS_DELETED+'</b>');
          }
        });
  		},function(btn){
        // alertService.add( 'danger', abstract+' <b>'+translations.DIALOGS_DELETE_ERROR+'</b>');
  		});
    });
  };
  
  /**
   * DELETE SELECTED ITEMS
   */
  $scope.deleteSelected = function( table ) {
    var selected = [];
    angular.forEach($scope.gridItems, function(item,key) {
      if ($scope.gridItems[key].isSelected) {
        selected.push(item['id']);
      }
    });
    
    if (selected.length>0) {
      $translate(['DIALOGS_SURE','DIALOGS_DELETE_SELECTED','DIALOGS_DELETED_SELECTED','DIALOGS_DELETE_ERROR'],{num:selected.length}).then(function (translations) {
        var confirm = dialogs.confirm( translations.DIALOGS_SURE, '<b>'+translations.DIALOGS_DELETE_SELECTED+'</b>', { 'size' : 'sm' } );
        confirm.result.then(function(btn){
          api.delete( { 'table':table, 'where':selected }).then(function(response){
            if (response.success===true && response.data===true) {
              // Reload page
              $scope.pipe( $scope.tableState );
              alertService.add( 'success', selected.length+' <b>'+translations.DIALOGS_DELETED_SELECTED+'</b>');
            }
          });
    		},function(btn){
          // alertService.add( 'danger', abstract+' <b>'+translations.DIALOGS_DELETE_ERROR+'</b>');
    		});
      });
    }
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
