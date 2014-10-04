flexyAdmin.controller('GridController', ['$scope','$routeParams','$http', function($scope,$routeParams,$http) {

  // URI PARAMS
  $scope.table = $routeParams.table;
  
  // INIT DATA
  $scope.grid = {
    'table_info'      : {},
    'field_info'      : {},
    'items'           : [],
    'displayedItems'  : []
  };
  // PAGINATION
  $scope.pagination = {
    'itemsPerPage'  : 10,
    'displayedPages': 7,
    'totalItems'    : 0,
    'totalPages'    : 0
  };
  

  /**
   * SORTABLE
   */
  $scope.dragged_nodes = [];
  $scope.sortableOptions = {
    containment: '.flexy-grid tbody',
    
    dragStart : function(obj) {
      console.log('dragStart');
      
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
          node_level=$scope.grid.items[next]._info['level'];
          if (node_level>level) $scope.dragged_nodes.push($scope.grid.items[next].id);
        } while (node_level>level);
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
    
    dragEnd: function (obj) {
      console.log('dragEnd');
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
    
    orderChanged : function(obj) {
      console.log('orderChanged',obj);
      var row=obj.source.itemScope.element;
      // Tree? move dragged nodes after new index
      if ($scope.grid.table_info.tree && row.hasClass('flexy-tree-branch')) {
        var old_index=obj.source.index;
        var new_index=obj.dest.index;
        var number_of_nodes = $scope.dragged_nodes.length;
        var old_nodes_index = old_index + 1;
        var new_nodes_indes = new_index + 1;
        // doit
        console.log('MOVING',old_index,new_index,number_of_nodes);
        var moving_node={};
        for (var i = 0; i < number_of_nodes; i++) {
          // copy node
          moving_node = obj.dest.sortableScope.modelValue[old_nodes_index+i];
          // remove node from list
          obj.dest.sortableScope.removeItem(old_nodes_index+i);
          // add node after new index
          obj.source.itemScope.sortableScope.insertItem(new_index+1+i, moving_node);
        }
        // update grid.displayedItems
        $scope.grid.displayedItems = [].concat($scope.grid.items);
      }
    },
    
    // accept: function (sourceItemHandleScope, destSortableScope) {return true},
    // orderChanged: function(event) {},
  };
  
  
  /**
   * LOAD FROM SERVER
   */
  $scope.callServer = function(tableState) {
    
    // TODO server side: https://lorenzofox3.github.io/smart-table-website/#section-pipe: set st-pipe="callServer" in grid.html
    $http.post('__api/get_table',{'table':$scope.table}).success(function(result){
      
      $scope.grid=result.data;

      // Copy the references, needed for smart-table to wacht for changes in the data
      $scope.grid.displayedItems = [].concat($scope.grid.items);
      
      // Fieldtypes
      angular.forEach($scope.grid.field_info, function(value, key) {
        $scope.grid.field_info[key].type = value.field.prefix();
      });
      
      // Calc stats/pagination
      $scope.pagination.totalItems = $scope.grid.items.length;
      $scope.pagination.totalPages = Math.ceil($scope.pagination.totalItems / $scope.pagination.itemsPerPage) ;
      
    }).error(function(data){
      $log.log('AJAX error -> Grid');
    });
  };
  
  $scope.callServer(); // TODO comment this when serverside pagination/order etc.



  /**
   * MAKE SURE ORDER OF ROWS IS ORIGINAL (keys)
   */
  $scope.keys = function(obj){
    if (!obj) return [];
    var keys=Object.keys(obj);
    keys.pop(); // Remove $$hashKey
    return keys;
  }
    
}]);