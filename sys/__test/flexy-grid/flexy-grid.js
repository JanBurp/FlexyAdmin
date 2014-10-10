flexyAdmin.controller('GridController', ['$scope','$routeParams','$http', function($scope,$routeParams,$http) {
  
  /**
   * GLOBAL GRID PARAMS
   */
  var self=this;
  $scope.table = $routeParams.table;
  $scope.has_selection = false;
  
  /**
   * GRID DATA
   */
  $scope.grid = {
    /**
     * Information about the table, will have:
     *  
     * table_info = { 
     *  table:        'string with table name',
     *  ui_name:      'name for table in user interface'
     *  str_order_by: 'fieldname DESC',
     *  sortable:     TRUE/FALSE if the data is sortable by the user   
     *  tree:         TRUE/FALSE if the data is in a tree (with a parent id)
     * }
     * 
     */
    'table_info'      : {},
    
    /**
     * Information about the fields
     * field_info = [
     *  'field_name' : { 
     *                  field:        'string with field name',
     *                  table:        'string with table name where the field in de database exists',
     *                  ui_name:      'name for field in user interface'
     *                  editable:     TRUE/FALSE if the value in the field can be editted by the user
     *                  incomplete:   TRUE/FALSE if the data that is loaded is incomplete (needs to be loaded before editting), for large data
     *                  info: {
     *                    b_show_in_grid:         TRUE/FALSE,
     *                    b_show_in_form:         TRUE/FALSE,
     *                    str_show_in_form_where: '',
     *                    str_fieldset:           '',
     *                    b_editable_in_grid:     TRUE/FALSE,
     *                    str_options:            '',
     *                    b_multi_options:        TRUE/FALSE,
     *                    b_ordered_options:      TRUE/FALSE,
     *                    str_options_where:      '',
     *                    str_validation_rules:   '',
     *                    str_validation_parameters: ''
     *                  }
     * }]
     */
    'field_info'      : {},

    /**
     * The data rows itself = [
     *  id  : {
     *    [... fields : value, ]
     *    _info : {
     *              is_branch:  TRUE/FALSE,
     *              is_node:    TRUE/FALSE,
     *              level:      0
     *            }
     * }]
     */
    'items'           : [],
    
    /**
     * References of the data: https://lorenzofox3.github.io/smart-table-website/#section-intro stSafeSrc attribute
     */
    'displayedItems'  : []
  };
  
  /**
   * PAGINATION
   */
  $scope.pagination = {
    'itemsPerPage'  : 10,
    'displayedPages': 7,
    'totalItems'    : 0,
    'totalPages'    : 0
  };
  
  
  



  /**
   * METHODS FOR ngSortable
   * 
   * Mainly for dragging in tree's
   * 
   */
  $scope.dragged_nodes = [];
  $scope.sortableOptions = {
    containment: '.flexy-grid tbody',

    /**
     * START DRAGGING
     * -  preserve width of drag handler
     * -  if branch, hide nodes and remember them
     */
    dragStart : function(obj) {
      var row=obj.source.itemScope.element;
      var table=angular.element(document.querySelector('.flexy-grid.'+$scope.table+' table'));

      // Tree? 'Drag' whole branch
      if ($scope.grid.table_info.tree && row.hasClass('flexy-tree-branch')) {
        // find all nodes
        var index=obj.source.index;
        var level=$scope.grid.items[index]._info['level'];
        $scope.dragged_nodes=[];
        var node_level=0;
        var next=index;
        do {
          next++;
          if (angular.isDefined($scope.grid.items[next])) {
            node_level=$scope.grid.items[next]._info['level'];
            if (node_level>level) $scope.dragged_nodes.push($scope.grid.items[next].id);
          }
        } while (node_level>level && angular.isDefined($scope.grid.items[next]));
        // 'hide' the nodes
        angular.forEach($scope.dragged_nodes,function(node,key){
          var el=angular.element(document.querySelector('.flexy-grid.'+$scope.table+' table tbody tr[id="'+node+'"]'));
          el.addClass('hidden');
        })
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
      var number_of_nodes = $scope.dragged_nodes.length;
      var old_level=obj.dest.sortableScope.modelValue[new_index]._info['level'];
      var new_level=0;
      var new_parent_id=0;
      
      // if tree, UPDATE LEVEL
      if ($scope.grid.table_info.tree) {
        // level=0 except when the next item has a higher level: use that level and parent
        var next_index=new_index+1;
        var number_of_items=obj.dest.sortableScope.modelValue.length;
        if (next_index<number_of_items) {
          var next_item=obj.dest.sortableScope.modelValue[next_index];
          new_level=next_item._info['level'];
          new_parent_id=next_item._info['self_parent'];
        }
        // save in item
        obj.dest.sortableScope.modelValue[new_index]._info['level']=new_level;
        if (new_level==0) obj.dest.sortableScope.modelValue[new_index]._info['is_node']=false;
        if (new_level>0) obj.dest.sortableScope.modelValue[new_index]._info['is_node']=true;
        obj.dest.sortableScope.modelValue[new_index].self_parent=new_parent_id;
        needsUpdate=true;
      }
      
      // if tree, MOVE dragged NODES after new index
      if ($scope.grid.table_info.tree && number_of_nodes>0) {
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
          obj.dest.sortableScope.modelValue[old_nodes_index]._info['level']-=level_diff;
          $scope.dragged_nodes.push(obj.dest.sortableScope.modelValue[old_nodes_index]);
          // remove node from dest
          obj.dest.sortableScope.removeItem(old_nodes_index);
        }
        // insert new items in dest after new index
        $scope.dragged_nodes.reverse();
        if (!up) new_nodes_index=new_nodes_index-number_of_nodes+1;
        for (var i = 0; i < number_of_nodes; i++) {
          obj.dest.sortableScope.insertItem(new_nodes_index, $scope.dragged_nodes[i]);
        }

        needsUpdate=true;
      }
      
      if (needsUpdate) $scope.grid.displayedItems = [].concat($scope.grid.items);
    },
    
    
    
    /**
     * DRAG END
     * -  if branch show nodes again
     */
    dragEnd: function (obj) {
      var row=obj.source.itemScope.element;
      // Tree? show hidden nodes again
      if ($scope.grid.table_info.tree && row.hasClass('flexy-tree-branch')) {
        angular.forEach($scope.dragged_nodes,function(node,key){
          var hidden=angular.element(document.querySelector('.flexy-grid.'+$scope.table+' table tbody tr[id="'+node+'"]'));
          hidden.removeClass('hidden');
        });
        $scope.dragged_nodes=[];
      }
    },
    
    
    // accept: function (sourceItemHandleScope, destSortableScope) {return true},
    // orderChanged: function(event) {},
  };
  
  
  
  
  
  
  /**
   * LOAD FROM SERVER
   */
  callServer = function(tableState) {
    
    // TODO server side: https://lorenzofox3.github.io/smart-table-website/#section-pipe: set st-pipe="callServer" in grid.html
    $http.post('__api/get_table',{'table':$scope.table}).success(function(result){
      
      // Define _info on all items
      angular.forEach( result.data.items, function(item,key) {
        if (angular.isUndefined(result.data.items[key]._info)) result.data.items[key]._info={};
      });
      // keep items in Scope
      $scope.grid=result.data;

      // Copy the references, needed for smart-table to wacht for changes in the data
      $scope.grid.displayedItems = [].concat($scope.grid.items);
      
      // Fieldtypes
      angular.forEach( $scope.grid.field_info, function(value, key) {
        $scope.grid.field_info[key].type = value.field.prefix();
      });
      
      // Calc stats/pagination
      $scope.pagination.totalItems = $scope.grid.items.length;
      $scope.pagination.totalPages = Math.ceil($scope.pagination.totalItems / $scope.pagination.itemsPerPage) ;
      
    }).error(function(data){
      $log.log('AJAX error -> Grid');
    });
  };
  
  callServer(); // TODO comment this when serverside pagination/order etc.






  /**
   * SELECT ROW TOGGLE
   */
  $scope.toggleSelection = function(index) {
    if (angular.isUndefined(index)) {
      // toggle all
      $scope.has_selection = false;
      angular.forEach($scope.grid.items, function(item,key) {
        var selected=$scope.grid.items[key]._info['selected'];
        if (!selected) selected=true; else selected=false;
        $scope.grid.items[key]._info['selected']=selected;
        // has selection?
        if (selected) $scope.has_selection = true;
      });
    }
    else {
      // toggle one
      var selected=$scope.grid.items[index]._info['selected'];
      if (!selected) selected=true; else selected=false;
      $scope.grid.items[index]._info['selected']=selected;
      // see if there is a selection at all
      $scope.has_selection = false;
      angular.forEach($scope.grid.items, function(item,key) {
        var selected=$scope.grid.items[key]._info['selected'];
        if (selected) $scope.has_selection = true;
      });
    }
  }




  /**
   * MAKE SURE ORDER OF ROWS IS ORIGINAL (keys) : https://stackoverflow.com/questions/19676694/ng-repeat-directive-sort-the-data-when-using-key-value
   */
  $scope.keys = function(obj){
    if (!obj) return [];
    var keys=Object.keys(obj);
    keys.pop(); // Remove $$hashKey
    return keys;
  }
    
}]);