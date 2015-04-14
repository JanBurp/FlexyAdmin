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


/**
 * Overrides ng-sortable to make sorting as a tree possible
 * - options
 * - callbacks
 */
flexyAdmin.directive('asSortable', [ function () {
  return {
    restrict: 'A',
    priority: 500,
    link: function ($scope, $element, $attrs) {
      
      /**
       * Here the Options & Callbacks are set
       */
      $scope.sortableOptions = {
        containment:    '.flexy-grid tbody',
        dragStart :     function(obj) { sortable.dragStart(obj); },
        orderChanged :  function(obj) { sortable.orderChanged(obj); },
        dragEnd:        function(obj) { sortable.dragEnd(obj); },
      };
      
      /**
       * Decleration of closure with all callback functions and needed data. After that the callback functions are declared
       */
      var sortable = {
        dragged_children : [],      // The children that are dragged with the parent
        table            : null,    // Table element
      };

      /**
       * START DRAGGING: hide and remember children
       */
      sortable.dragStart = function(obj) {
        // Re(set) global data to remember
        sortable.dragged_children=[];
        sortable.table = angular.element(document.querySelector('.flexy-grid.'+$scope.$parent.table+' table'));
        // Is table a tree and row has children?
        if ($scope.$parent.type.is_tree && obj.source.itemScope.row._info.has_children) {
          // Find the children
          sortable.dragged_children = sortable.find_children(obj);
          // Hide them
          sortable.hide_rows( sortable.dragged_children );
        }
        // Keep styling of dragged item intact
        sortable.style_drag_item(obj);
      };

      /**
       * ORDER HAS CHANGED, if has children make sure they are on right place and determine new level & parent
       */
      sortable.orderChanged = function(obj) {
        if ($scope.$parent.type.is_tree) {
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
          $scope.$parent.displayedItems = [].concat($scope.$parent.gridItems);
        }
      };
  
      /**
       * DRAG END -  Show hidden children again
       */
      sortable.dragEnd = function(obj) {
        sortable.show_rows( sortable.dragged_children );
      };
      
      /**
       * HELPER FUNCTIONS:
       */

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
          if (angular.isDefined($scope.$parent.gridItems[index])) {
            node_level=$scope.$parent.gridItems[index]._info.level;
            if (node_level>row.level) children.push($scope.$parent.gridItems[index].id);
          }
        } while (node_level>row.level && angular.isDefined($scope.$parent.gridItems[index]));
        return children;
      };
  
      /**
       * Hide rows
       */
      sortable.hide_rows = function(rows) {
        angular.forEach(rows,function(node,key){
          angular.element(document.querySelector('tbody tr[id="flexy-grid-row_'+node+'"]', sortable.table )).addClass('hidden');
        });
      };

      /**
       * Show rows
       */
      sortable.show_rows = function(rows) {
        angular.forEach(rows,function(node,key){
          angular.element(document.querySelector('tbody tr[id="flexy-grid-row_'+node+'"]',sortable.table)).removeClass('hidden');
        });
      };
  
      /**
       * Preserve width of cells in dragged row (when containment is set)
       */
      sortable.style_drag_item = function(obj) {
        if (angular.isDefined($scope.sortableOptions.containment)) {
          var widths = [];
          // get widths of headers
          angular.forEach( sortable.table.find('th'), function(cell, key) {
            widths.push(cell.offsetWidth+"px");
          });
          // set width of dragged cells
          angular.forEach( obj.source.itemScope.element.find('td'), function(cell, key) {  
            var w = widths.shift();
            angular.element(cell).css({'width':w,'min-width':w,'max-width':w});
          });
          // Preserve position & size of dragged row
          var panel = angular.element(document.querySelector('.panel.flexy-grid.'+$scope.$parent.table));
          var pos   = panel.offset();
          var width = panel.width()+1;
          angular.element(document.querySelector('.as-sortable-drag',panel)).css({'margin-top':(-pos.top),'margin-left':(-pos.left+13),'max-width':width});
        }
      };
  
  
    }
  };
}]);

