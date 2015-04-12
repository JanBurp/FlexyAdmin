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
   * ngSortable OPTIONS & CALLBACKS
   */
  var dragged_children = [];
  var table = angular.element(document.querySelector('.flexy-grid.'+$scope.table+' table'));
  
  $scope.sortableOptions = {
    containment: '.flexy-grid tbody',
    // containerPositioning: 'relative',

    /**
     * START DRAGGING
     */
    dragStart : function(obj) {
      dragged_children=[];
      var row=obj.source.itemScope.row._info;
      // Tree? Find children and hide them
      if ($scope.type.is_tree && row.has_children) {
        var index=obj.source.index;
        var node_level=0;
        do {
          index++;
          if (angular.isDefined($scope.gridItems[index])) {
            node_level=$scope.gridItems[index]._info.level;
            if (node_level>row.level) dragged_children.push($scope.gridItems[index].id);
          }
        } while (node_level>row.level && angular.isDefined($scope.gridItems[index]));
        // Hide the children
        angular.forEach(dragged_children,function(node,key){
          angular.element(document.querySelector('tbody tr[id="flexy-grid-row_'+node+'"]',table)).hide();
        });
      }
      
      // Preserve width of the elements
      var widths = [];
      angular.forEach( table.find('th') , function(cell, key) {
        widths.push(cell.offsetWidth+"px");
      });
      angular.forEach( obj.source.itemScope.element.find('td'), function(cell, key) {
        angular.element(cell).css({'width':widths.shift()});
      });
      // Preserve position & size of dragging row
      var panel = angular.element(document.querySelector('.panel.flexy-grid.'+$scope.table));
      var pos   = panel.offset();
      var width = panel.width()+1;
      angular.element(document.querySelector('.as-sortable-drag',panel)).css({'margin-top':(-pos.top),'margin-left':(-pos.left+13),'max-width':width});
    },

    /**
     * ORDER HAS CHANGED, if has children make sure they are on right place and determine new level & parent
     */
    orderChanged : function(obj) {
      if ($scope.type.is_tree) {
        var new_index=obj.dest.index;
        var number_of_children = dragged_children.length;
        var new_level=0;
        var new_parent_id=0;
        
        // level = 0 except when the next item has a higher level: use that level and parent
        var next_index = new_index+1;
        if (next_index < obj.dest.sortableScope.modelValue.length) {
          var next_item = obj.dest.sortableScope.modelValue[next_index];
          new_level     = next_item._info.level;
          new_parent_id = next_item._info.self_parent;
        }
        // set level, parent and is_child
        obj.dest.sortableScope.modelValue[new_index]._info.level = new_level;
        obj.dest.sortableScope.modelValue[new_index].self_parent = new_parent_id;
        // is_child when level > 0
        obj.dest.sortableScope.modelValue[new_index]._info.is_child = false;
        if (new_level>0) obj.dest.sortableScope.modelValue[new_index]._info.is_child = true;
        
        // MOVE dragged NODES after new index
        if (number_of_children>0) {
          var level_diff = obj.dest.sortableScope.modelValue[new_index]._info.level - new_level; // old level - new level
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
            dragged_children.push(obj.dest.sortableScope.modelValue[old_index]);
            // remove node from dest
            obj.dest.sortableScope.removeItem(old_index);
          }
          // insert new items in dest after new index
          dragged_children.reverse();
          if (!up) new_index = new_index-number_of_children + 1;
          for (i = 0; i < number_of_children; i++) {
            obj.dest.sortableScope.insertItem(new_index, dragged_children[i]);
          }
        }
        
        // Update Grid
        $scope.displayedItems = [].concat($scope.gridItems);
      }

    },

    /**
     * DRAG END -  if tree show hidden children again
     */
    dragEnd: function (obj) {
      angular.forEach(dragged_children,function(node,key){
        angular.element(document.querySelector('tbody tr[id="flexy-grid-row_'+node+'"]',table)).show();
      });
    },

  };


    
}]);
