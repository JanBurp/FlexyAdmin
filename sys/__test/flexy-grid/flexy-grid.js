flexyAdmin.controller('GridController', ['$scope','$routeParams','$http', function($scope,$routeParams,$http) {

  // URI PARAMS
  $scope.table = $routeParams.table;
  
  $scope.sortableOptions = {
    // accept: function (sourceItemHandleScope, destSortableScope) {return true},
    // itemMoved: function (event) {},
    // orderChanged: function(event) {},
    containment: '.flexy-grid tbody'
  };
  
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
  
  // LOAD FROM SERVER
  $scope.callServer = function(tableState) {
    // TODO server side: https://lorenzofox3.github.io/smart-table-website/#section-pipe: set st-pipe="callServer" in grid.html
    $http.post('__api/get_table',{'table':$scope.table}).success(function(result){
      $scope.grid=result.data;
      // Copy the references (you could clone ie angular.copy but then have to go through a dirty checking for the matches)
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
  
  $scope.callServer(); // TODO comment this when serverside


  // Order an array to its default
  $scope.keys = function(obj){
    if (!obj) return [];
    var keys=Object.keys(obj);
    keys.pop(); // Remove $$hashKey
    return keys;
  }
    
}]);