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

flexyAdmin.directive('flexyTable', ['flexySettingsService','flexyApiService','flexyTableService','$routeParams', function(settings,api,flexyTable,$routeParams) {
  'use strict';
  
  return {
    restrict:     'E',
    templateUrl:  'flexy-table/flexy-table.html',
    replace:      true,
    scope:        {},
    
    /**
     * DIRECTIVE LINK
     */
    link: function($scope, element, attrs) {

      /**
       * The table, or path
       */
      $scope.table = attrs.table;
      $scope.path  = attrs.path;
      
      /**
       * UI Name
       */
      $scope.ui_name = '';

      /**
       * Table Type
       */
      $scope.type = {
        is_media     : angular.isDefined($scope.path),
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
      $scope.tableItems = [];
      $scope.displayedItems  = [];
  
      /**
       * Last TableState
       */
      $scope.tableState = {};
      
      /**
       * LINK METHODS
       */
      

      /**
       * SELECT ROW TOGGLE
       */
      $scope.select = function(index) {
        // Niet nodig, gaat automatisch
        // console.log('select',index);
        // var selected = $scope.tableItems[index].isSelected;
        // $scope.tableItems[index].isSelected = !$scope.tableItems[index].isSelected;
      };


      /**
       * SELECT ALL TOGGLE
       */
      $scope.toggleSelection = function() {
        angular.forEach($scope.tableItems, function(item,key) {
          var selected=$scope.tableItems[key].isSelected;
          if (!selected) selected=true; else selected=false;
          $scope.tableItems[key].isSelected=selected;
        });
      };
      
      
      
      /**
       * DELETE (selected) ITEM(s)
       */
      $scope.delete = function( table, id ) {
        var selected = [];
        // één item, of selected?
        if (angular.isDefined(id)) {
          selected.push(id);
        }
        else {
          angular.forEach($scope.tableItems, function(item,key) {
            if ($scope.tableItems[key].isSelected) {
              selected.push(item['id']);
            }
          });
        }

        flexyTable.delete( table, selected ).then(function(response){
          if (response!==false) {
            var tableState = $scope.tableState;
            // Reload huidige pagina in tabel (eerst huidige verwijderen)
            flexyTable.remove( $scope.table );
            $scope.pipe( tableState );
          }
        });
      };

      /**
       * Jump to today
       */
      $scope.jump_to_page_with_today = function() {
        var tableState = $scope.tableState;
        tableState.pagination.start = false;
        $scope.pipe( tableState );
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
      
    },
    
    
    /**
     * DIRECTIVE CONTROLLER
     */
    controller: function($scope) {
      
      /**
       * LOAD REQUESTED DATA
       */
      $scope.pipe = function(tableState) {

        // Bewaar deze tableState
        $scope.tableState = tableState;
        
        flexyTable.load( $scope.table, tableState ).then(function(response){
          // table ui_name
          $scope.ui_name = settings.item( 'settings','table',$scope.table,'table_info','ui_name' );
          // table type (tree, sortable)
          $scope.type.is_tree = settings.item( 'settings','table',$scope.table,'table_info','tree');
          $scope.type.is_sortable = settings.item( 'settings','table',$scope.table,'table_info','sortable');
          // Pagination en update tableState
          $scope.info = flexyTable.get_info($scope.table);
          $scope.info.num_pages = Math.ceil($scope.info.total_rows / $scope.info.limit);
          tableState.pagination.start = $scope.info.offset;
          tableState.pagination.totalItemCount = $scope.info.total_rows;
          tableState.pagination.numberOfPages = $scope.info.num_pages;
  
          // Jump to today, kan alleen als op volgorde van jump_to_today veld
          $scope.jump_to_today = settings.item( 'settings','table',$scope.table,'grid_set','jump_to_today');
          var order_by = settings.item( 'settings','table',$scope.table,'grid_set','order_by').prefix(' ');
          if ( angular.isDefined( tableState.sort.predicate ) ) {
            order_by = tableState.sort.predicate;
          }
          if ( order_by!==$scope.jump_to_today ) $scope.jump_to_today = false;
  
          // Search
          $scope.search = '';
          if ( angular.isDefined( tableState.search.predicateObject )) {
            $scope.search = tableState.search.predicateObject.$;
          }

          // Grid data & references
          $scope.tableItems = response.data;
          $scope.displayedItems = [].concat($scope.tableItems);
  
          // Show only the fields that exists in the tableItems (remove the field info of fields that are not in there)
          var fields = settings.item('settings','table',$scope.table,'field_info');
          // Verwijder enkele standaard velden
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
              else {
                fields[field].field = field; // Voeg naam van het veld toe
                fields[field].type = field.prefix(); // Voeg type van het veld toe
              }
            });
          }
          $scope.fields = fields;
        });
      };
    }
  };
  
}]);



//
// angular.module('smart-table').directive('stPaginationScroll', ['$timeout', function (timeout) {
//   return{
//     require: 'stTable',
//     link: function (scope, element, attr, ctrl) {
//       var itemByPage = Number(5); // || ctrl.tableState().number;
//       var pagination = ctrl.tableState().pagination;
//       var lengthThreshold = 50;
//       var timeThreshold = 400;
//       var handler = function () {
//         //call next page
//         console.log('stPaginationScroll',pagination,itemByPage);
//
//         ctrl.slice( Number(pagination.start) + itemByPage, itemByPage);
//       };
//       var promise = null;
//       var lastRemaining = 9999;
//       var container = angular.element(element.find('.panel-content'));
//
//       container.bind('scroll', function () {
//         var remaining = container[0].scrollHeight - (container[0].clientHeight + container[0].scrollTop);
//
//         // if we have reached the threshold and we scroll down
//         if (remaining < lengthThreshold && (remaining - lastRemaining) < 0) {
//           //if there is already a timer running which has no expired yet we have to cancel it and restart the timer
//           if (promise !== null) {
//             timeout.cancel(promise);
//           }
//           promise = timeout(function () {
//             handler();
//             //scroll a bit up
//             container[0].scrollTop -= 70;
//             promise = null;
//           }, timeThreshold);
//         }
//         lastRemaining = remaining;
//       });
//     }
//   };
// }]);