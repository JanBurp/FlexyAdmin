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
  $scope.ui_name = $routeParams.table;
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
    rows            : 0,
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
    $scope.info.total_pages = Math.ceil($scope.info.rows / $scope.info.limit);
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
  


  /**
   * NG.SORTABLE OPTIONS
   */
  $scope.sortableOptions = {
    containment:    '.flexy-grid tbody',
    dragStart :     function(obj) { sortable.dragStart(obj); },
    orderChanged :  function(obj) { sortable.orderChanged(obj); },
    dragEnd:        function(obj) { sortable.dragEnd(obj); },
  };

  var sortable = {
    dragged_children : [],
    table            : angular.element(document.querySelector('.flexy-grid.'+$scope.table+' table'))
  };

  /**
   * START DRAGGING: hide and remember children
   */
  sortable.dragStart = function(obj) {
    // Reset dragged children
    sortable.dragged_children=[];
    // Is table a tree and row has children?
    if ($scope.type.is_tree && obj.source.itemScope.row._info.has_children) {
      // Find the children
      sortable.dragged_children = sortable.find_children(obj);
      // Hide them
      sortable.hide_rows( sortable.dragged_children );
    }
    // Preserve width of cells in dragged row
    var widths = [];
    angular.forEach( sortable.table.find('th') ,              function(cell, key) {  widths.push(cell.offsetWidth+"px");                    });
    angular.forEach( obj.source.itemScope.element.find('td'), function(cell, key) {  angular.element(cell).css({'width':widths.shift()});   });
    // Preserve position & size of dragged row
    var panel = angular.element(document.querySelector('.panel.flexy-grid.'+$scope.table));
    var pos   = panel.offset();
    var width = panel.width()+1;
    angular.element(document.querySelector('.as-sortable-drag',panel)).css({'margin-top':(-pos.top),'margin-left':(-pos.left+13),'max-width':width});
  };

  /**
   * ORDER HAS CHANGED, if has children make sure they are on right place and determine new level & parent
   */
  sortable.orderChanged = function(obj) {
    if ($scope.type.is_tree) {
      var number_of_children = sortable.dragged_children.length;
      var new_index          = obj.dest.index;
      
      // set (new) level, parent and is_child
      var next_index    = new_index+1;
      var nr_of_items   = obj.dest.sortableScope.modelValue.length;
      var old_level     = obj.dest.sortableScope.modelValue[new_index]._info.level;
      var new_level     = 0; // level = 0 except when the next item has a higher level: use that level
      var new_parent_id = 0; // parent = 0 except when the next item has a higher level: use that parent
      if (next_index < nr_of_items) {
        new_level     = obj.dest.sortableScope.modelValue[next_index]._info.level;
        new_parent_id = obj.dest.sortableScope.modelValue[next_index]._info.self_parent;
      }
      // set level, parent and is_child
      obj.dest.sortableScope.modelValue[new_index].self_parent    = new_parent_id;
      obj.dest.sortableScope.modelValue[new_index]._info.level    = new_level;
      obj.dest.sortableScope.modelValue[new_index]._info.is_child = (new_level>0);  // is_child when level > 0
      
      // MOVE dragged NODES after new index
      if (number_of_children>0) {
        var level_diff = old_level - new_level;
        var old_index = obj.source.index;
        var up = (new_index<old_index);
        if (up) {
          old_index++;
          new_index++;
        }
        // collect dragged nodes
        for (var i = 0; i < number_of_children; i++) {
          // adjust level & copy the node from dest
          obj.dest.sortableScope.modelValue[old_index]._info.level -= level_diff;
          sortable.dragged_children.push(obj.dest.sortableScope.modelValue[old_index]);
          // remove node from dest
          obj.dest.sortableScope.removeItem(old_index);
        }
        // insert new items in dest after new index
        sortable.dragged_children.reverse();
        if (!up) new_index = new_index-number_of_children + 1;
        for (i = 0; i < number_of_children; i++) {
          obj.dest.sortableScope.insertItem(new_index, sortable.dragged_children[i]);
        }
      }
      
      // Update Grid
      $scope.displayedItems = [].concat($scope.gridItems);
    }
  };
  
  /**
   * DRAG END -  Show hidden children again
   */
  sortable.dragEnd = function(obj) {
    sortable.show_rows( sortable.dragged_children );
  };




  /**
   * Find children of row object
   */
  sortable.find_children = function(obj) {
    var children = [];
    var row=obj.source.itemScope.row._info;
    var index=obj.source.index;
    var node_level=0;
    do {
      index++;
      if (angular.isDefined($scope.gridItems[index])) {
        node_level=$scope.gridItems[index]._info.level;
        if (node_level>row.level) children.push($scope.gridItems[index].id);
      }
    } while (node_level>row.level && angular.isDefined($scope.gridItems[index]));
    return children;
  };
  
  /**
   * Hide rows
   */
  sortable.hide_rows = function(rows) {
    angular.forEach(rows,function(node,key){
      angular.element(document.querySelector('tbody tr[id="flexy-grid-row_'+node+'"]', sortable.table )).hide();
    });
  };

  /**
   * Show rows
   */
  sortable.show_rows = function(rows) {
    angular.forEach(rows,function(node,key){
      angular.element(document.querySelector('tbody tr[id="flexy-grid-row_'+node+'"]',sortable.table)).show();
    });
  };
  
  

    
}]);
