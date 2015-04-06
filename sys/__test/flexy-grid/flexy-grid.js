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
    rows         : 0,
    limit        : 0,
    total_pages  : 0,
  };
  
  /**
   * Information about the fields (field_info)
   */
  $scope.fields = [];
  
  /**
   * Table has selection
   */
  $scope.has_selection = false;
  
  /**
   * References of the grid data: https://lorenzofox3.github.io/smart-table-website/#section-intro stSafeSrc attribute
   * Set when data is present
   */
  $scope.gridItems = [];
  $scope.displayedItems  = [];
  
  
  /**
   * LOAD FROM SERVER
   */
  // TODO server side: https://lorenzofox3.github.io/smart-table-website/#section-pipe: set st-pipe="callServer" in grid.html
  grid.load( $scope.table ).then(function(response){
    // ui_name
    $scope.ui_name = settings.item('config','table_info',$scope.table,'ui_name');
    // table type
    $scope.type.is_tree = settings.item('config','table_info',$scope.table,'tree');
    $scope.type.is_sortable = settings.item('config','table_info',$scope.table,'sortable');
    // info
    $scope.info = grid.get_info($scope.table);
    // field_info
    $scope.fields = settings.item('config','field_info',$scope.table);
    // data
    $scope.gridItems = grid.get_grid_data($scope.table);
    // Copy the references, needed for smart-table to watch for changes in the data
    $scope.displayedItems = [].concat($scope.gridItems);
    
    // angular.forEach( $scope.gridItems, function(item,id) {
    //   console.log(item.id,item.self_parent,item.order,item._info,item.uri);
    // });
  });
  
  
  
  
  /**
   * METHODS FOR ngSortable
   *
   * Mainly for dragging in tree's
   *
   */
  $scope.dragged_children = [];
  $scope.sortableOptions = {
    containment: '.flexy-grid tbody',

    /**
     * START DRAGGING
     * -  preserve width of drag handler
     * -  if has children, hide and remember them
     */
    dragStart : function(obj) {
      var row=obj.source.itemScope.element;
      var table=angular.element(document.querySelector('.flexy-grid.'+$scope.table+' table'));

      // Tree? 'Drag' whole branch
      console.log('dragStart', $scope.type, row.hasClass('flexy-tree-has_children'),row);
      
      if ($scope.type.is_tree && row.hasClass('flexy-tree-has_children')) {
        // find all nodes
        var index=obj.source.index;
        var level=$scope.gridItems[index]._info.level;
        $scope.dragged_children=[];
        var node_level=0;
        var next=index;
        console.log('dragStart',index,level);
        
        do {
          next++;
          if (angular.isDefined($scope.gridItems[next])) {
            node_level=$scope.gridItems[next]._info.level;
            if (node_level>level) $scope.dragged_children.push($scope.gridItems[next].id);
          }
        } while (node_level>level && angular.isDefined($scope.gridItems[next]));
        // 'hide' the nodes
        angular.forEach($scope.dragged_children,function(node,key){
          var el=angular.element(document.querySelector('.flexy-grid.'+$scope.table+' table tbody tr[id="'+node+'"]'));
          el.addClass('hidden');
        });
      }

      // Preserve width of the elements, first get width of headers
      var header=table.find('thead');
      var thCells = header.find('th');
      var widths = [];
      angular.forEach(thCells, function(cell, key) { widths.push(cell.offsetWidth+"px"); });

      // Preserve cell widths: sets the widths
      var cells = row.find('td');
      var i=0;
      angular.forEach(cells, function(cell, key) { angular.element(cell).css({'width':widths[i++]}); });

      // nice color of dragged row
      angular.element(row).addClass('bg-primary');
    },



    /**
     * ORDER HAS CHANGED
     * -  if branch, make sure nodes are on right place and determine new level & parent
     */
    orderChanged : function(obj) {
      var row=obj.source.itemScope.element;
      var new_index=obj.dest.index;
      var needsUpdate=false;
      var number_of_nodes = $scope.dragged_children.length;
      var old_level=obj.dest.sortableScope.modelValue[new_index]._info.level;
      var new_level=0;
      var new_parent_id=0;

      // if tree, UPDATE LEVEL
      if ($scope.type.is_tree) {
        // level=0 except when the next item has a higher level: use that level and parent
        var next_index=new_index+1;
        var number_of_items=obj.dest.sortableScope.modelValue.length;
        if (next_index<number_of_items) {
          var next_item=obj.dest.sortableScope.modelValue[next_index];
          new_level=next_item._info.level;
          new_parent_id=next_item._info.self_parent;
        }
        // save in item
        obj.dest.sortableScope.modelValue[new_index]._info.level=new_level;
        if (new_level===0) obj.dest.sortableScope.modelValue[new_index]._info.is_child=false;
        if (new_level>0) obj.dest.sortableScope.modelValue[new_index]._info.is_child=true;
        obj.dest.sortableScope.modelValue[new_index].self_parent=new_parent_id;
        needsUpdate=true;
      }

      // if tree, MOVE dragged NODES after new index
      if ($scope.type.is_tree && number_of_nodes>0) {
        var level_diff = old_level-new_level;
        var old_index=obj.source.index;
        var up=(new_index<old_index);
        var old_nodes_index = old_index;
        var new_nodes_index = new_index;
        if (up) {
          old_nodes_index++;
          new_nodes_index++;
        }
        // collect dragged nodes
        for (var i = 0; i < number_of_nodes; i++) {
          // adjust level & copy the node from dest
          obj.dest.sortableScope.modelValue[old_nodes_index]._info.level-=level_diff;
          $scope.dragged_children.push(obj.dest.sortableScope.modelValue[old_nodes_index]);
          // remove node from dest
          obj.dest.sortableScope.removeItem(old_nodes_index);
        }
        // insert new items in dest after new index
        $scope.dragged_children.reverse();
        if (!up) new_nodes_index=new_nodes_index-number_of_nodes+1;
        for (i = 0; i < number_of_nodes; i++) {
          obj.dest.sortableScope.insertItem(new_nodes_index, $scope.dragged_children[i]);
        }

        needsUpdate=true;
      }

      if (needsUpdate) $scope.displayedItems = [].concat($scope.gridItems);
    },



    /**
     * DRAG END
     * -  if branch show nodes again
     */
    dragEnd: function (obj) {
      var row=obj.source.itemScope.element;
      // Tree? show hidden nodes again
      if ($scope.type.is_tree && row.hasClass('flexy-tree-has_children')) {
        angular.forEach($scope.dragged_children,function(node,key){
          var hidden=angular.element(document.querySelector('.flexy-grid.'+$scope.table+' table tbody tr[id="'+node+'"]'));
          hidden.removeClass('hidden');
        });
        $scope.dragged_children=[];
      }
    },


    // accept: function (sourceItemHandleScope, destSortableScope) {return true},
    // orderChanged: function(event) {},
  };



  /**
   * SELECT TOGGLE
   */
  $scope.toggleSelection = function(index) {
    if (angular.isUndefined(index)) {
      // toggle all
      $scope.has_selection = false;
      angular.forEach($scope.gridItems, function(item,key) {
        var selected=$scope.gridItems[key]._info.selected;
        if (!selected) selected=true; else selected=false;
        $scope.gridItems[key]._info.selected=selected;
        // has selection?
        if (selected) $scope.has_selection = true;
      });
    }
    else {
      // toggle one
      var selected=$scope.gridItems[index]._info.selected;
      if (!selected) selected=true; else selected=false;
      $scope.gridItems[index]._info.selected=selected;
      // see if there is a selection at all
      $scope.has_selection = false;
      angular.forEach($scope.gridItems, function(item,key) {
        var selected=$scope.gridItems[key]._info.selected;
        if (selected) $scope.has_selection = true;
      });
    }
  };


  /**
   * MAKE SURE ORDER OF ROWS IS ORIGINAL (keys) : https://stackoverflow.com/questions/19676694/ng-repeat-directive-sort-the-data-when-using-key-value
   */
  $scope.keys = function(obj){
    if (!obj) return [];
    var keys=Object.keys(obj);
    keys.pop(); // Remove $$hashKey
    return keys;
  };
    
}]);