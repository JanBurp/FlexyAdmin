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
   * The table
   */
  $scope.table = $routeParams.table;
  
  /**
   * UI Name
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
   * Info for Pagination
   */
  $scope.info = {
    num_rows        : 0,
    limit           : 0,
    num_pages       : 0,
    offset          : 0,
  };
  
  /**
   * Kan er een knop komen om naar vandaag te springen?
   */
  $scope.jump_to_today = false;
  
  /**
   * Information about the fields
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
   * Last TableState
   */
  self.tableState = {};
  
  
  /**
   * LOAD REQUESTED DATA
   */
  $scope.pipe = function(tableState) {

    // Bewaar deze tableState
    self.tableState = tableState;

    // Laad
    grid.load( $scope.table, tableState ).then(function(response){

      // table ui_name
      $scope.ui_name = settings.item( 'settings','table',$scope.table,'table_info','ui_name' );

      // table type (tree, sortable)
      $scope.type.is_tree = settings.item( 'settings','table',$scope.table,'table_info','tree');
      $scope.type.is_sortable = settings.item( 'settings','table',$scope.table,'table_info','sortable');

      // Pagination
      $scope.info = grid.get_info($scope.table);
      $scope.info.num_pages = Math.ceil($scope.info.total_rows / $scope.info.limit);
      tableState.pagination.start = $scope.info.offset;
      tableState.pagination.numberOfPages = $scope.info.num_pages;
      
      // Jump to today
      $scope.jump_to_today = settings.item( 'settings','table',$scope.table,'grid_set','jump_to_today');
      
      // Search
      $scope.search = '';
      if ( angular.isDefined( tableState.search.predicateObject )) {
        $scope.search = tableState.search.predicateObject.$;
      }
    
      // Grid data & references
      $scope.gridItems = response.data;
      $scope.displayedItems = [].concat($scope.gridItems);
      
      // Show only the fields that exists in the gridItems (remove the field info of fields that are not in there)
      var fields = settings.item('settings','table',$scope.table,'field_info');
      // Verwijder id,self_parent,uri,order uit fields
      delete fields['id'];
      delete fields['self_parent'];
      delete fields['order'];
      delete fields['uri'];
      // Verwijder niet zichtbare velden
      var first_item = jdb.firstArrayItem( response.data );
      if ( angular.isDefined( first_item )) {
        angular.forEach( fields, function(info, field) {
          if ( angular.isUndefined( first_item[field] )) {
            delete fields[field];
          }
        });
      }
      $scope.fields = fields;
    });
    
  };
  
  
  /**
   * Jump to today
   */
  $scope.jump_to_page_with_today = function() {
    var tableState = self.tableState;
    tableState.pagination.start = false;
    $scope.pipe( tableState );
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
   * DELETE (selected) ITEM(s), maar eerst CONFIRM
   */
  $scope.delete = function( table, id ) {
    var abstract = '';
    var selected = [];
    // één item, of selected?
    if (angular.isDefined(id)) {
      selected.push(id);
    }
    else {
      angular.forEach($scope.gridItems, function(item,key) {
        if ($scope.gridItems[key].isSelected) {
          selected.push(item['id']);
        }
      });
    }
    // Alleen verder als er minimaal één item wordt gedelete
    if (selected.length>0) {
      $translate(['DIALOGS_SURE','DIALOGS_DELETE_ITEM','DIALOGS_DELETED','DIALOGS_DELETE_SELECTED','DIALOGS_DELETED_SELECTED','DIALOGS_DELETE_ERROR'],{num:selected.length}).then(function (translations) {
        // prepare conform dialog
        var title   = translations.DIALOGS_SURE;
        var message = '<b>';
        if (selected.length>1) {
          message+= translations.DIALOGS_DELETE_SELECTED + '</b>';
        }
        else {
          abstract = grid.get_abstract( table, selected[0] );
          message+= translations.DIALOGS_DELETE_ITEM + '</b><br>' + abstract;
        }
        // Show confirm dialog
        var confirm = dialogs.confirm( title, message, {'size':'sm'} );
        confirm.result.then(function(btn){
          api.delete( { 'table':table, 'where':selected }).then(function(response){
            if (response.success===true && response.data===true) {
              // Reload page, door eerst de huidige data te verwijderen en dan opnieuw te laden
              grid.remove( $scope.table );
              $scope.pipe( $scope.tableState );
              // Message
              if (selected.length>1)
                alertService.add( 'success', selected.length+' <b>'+translations.DIALOGS_DELETED_SELECTED+'</b>');
              else
                alertService.add( 'success', abstract + ' <b>'+translations.DIALOGS_DELETED+'</b>');
            }
          });
    		},function(btn){
          // alertService.add( 'danger', '<b>'+translations.DIALOGS_DELETE_ERROR+'</b>');
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
