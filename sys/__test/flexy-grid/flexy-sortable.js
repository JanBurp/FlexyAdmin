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
flexyAdmin.directive('asSortable', [ 'flexyGridService', function (grid) {
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
       * Decleration of closure with all callback functions and needed data.
       */
      var sortable = {
        order_start     : 0,       // order value of first item (for calculating new order fields)
        oldItems        : [],      // Items before moving
        draggedChildren : [],      // The children that are dragged with the parent
        table           : null,    // Table element
      };

      /**
       * CALLBACKS ====================================================================
       */

      /**
       * START DRAGGING: hide and remember children
       */
      sortable.dragStart = function(obj) {
        // Remember order start
        sortable.order_start = $scope.$parent.gridItems[0].order;
        // Remember current items
        sortable.oldItems = obj.source.sortableScope.modelValue.slice();
        // Re(set) draggedChildren
        sortable.draggedChildren=[];
        // Set current table element
        sortable.table = angular.element(document.querySelector('.flexy-grid.'+$scope.$parent.table+' table'));
        // Is table a tree and row has children?
        if ($scope.$parent.type.is_tree && obj.source.itemScope.row._info.has_children) {
          // Find the children
          sortable.draggedChildren = sortable.find_children(obj);
          // Hide them
          sortable.hide_rows( sortable.draggedChildren );
        }
        // Keep styling of dragged item intact
        sortable.style_drag_item(obj);
      };

      /**
       * ORDER HAS CHANGED, if has children make sure they are on right place and determine new level & parent
       */
      sortable.orderChanged = function(obj) {
        if ($scope.$parent.type.is_tree) {

          // 0) Vars
          var oldIndex = obj.source.index;
          var newIndex = obj.dest.index;
          var number_of_children = sortable.draggedChildren.length;
          
          // 1) Kopie van oude items & nieuwe items
          var items = sortable.oldItems;
          var newItems = obj.dest.sortableScope.modelValue;

          // 3) Pas parent van verplaatste item aan
          // Bijna altijd 0
          // Behalve als het volgende item een hoger level heeft: dan heeft het dezelfde parent als dat item, dus als er een item na komt, neem die parent.
          // Check eerst of het niet de laatste is, want dan hoeven we al niet verder te kijken
          var parent_id = 0; 
          if (newIndex+1 < newItems.length) {
            // Het is niet de laatste, dus pak de parent van het volgende item
            parent_id = newItems[newIndex+1].self_parent;
          }
          // Bewaar nieuwe parent in oud item
          items[oldIndex].self_parent = parent_id;
          
          // 4) Verplaats items
          items = jdb.moveMultipleArrayItems(items, oldIndex, number_of_children+1, newIndex);

          // 5) Vernieuw de grid info & bewaar in grid
          $scope.$parent.gridItems = grid.add_tree_info(items, true);
        }
        
        // Update order fields
        var order = sortable.order_start;
        angular.forEach( $scope.$parent.gridItems, function(item,key) {
          $scope.$parent.gridItems[key].order = order;
          order++;
        });
        
        // Update sortable
        obj.dest.sortableScope.modelValue = $scope.$parent.gridItems;
        // Update grid UI
        $scope.$parent.displayedItems = [].concat($scope.$parent.gridItems);
        
        // Call server to change the order
        grid.change_order( $scope.$parent.table, $scope.$parent.gridItems, sortable.order_start );
      };
  
  
      /**
       * DRAG END -  Show hidden children again
       */
      sortable.dragEnd = function(obj) {
        sortable.show_rows( sortable.draggedChildren );
      };

      
      /**
       * HELPER FUNCTIONS ====================================================================
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

