flexyAdmin.controller('GridController', ['$scope','$routeParams','$http', '$log', function($scope,$routeParams,$http,$log) {

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
  }

  // AJAX REQUEST
  $http.post('__api/get_table',{'table':$scope.table}).success(function(result){
    $scope.grid=result.data;
    $scope.afterLoading();
  }).error(function(data){
    $log.log('AJAX error -> Grid');
  });

  // ACTIONS AFTER LOADING
  $scope.afterLoading = function() {
    // Copy the references (you could clone ie angular.copy but then have to go through a dirty checking for the matches)
    $scope.displayedItems = [].concat($scope.grid.items);
    // Fieldtypes
    angular.forEach($scope.grid.field_info, function(value, key) {
      $scope.grid.field_info[key].type = value.field.prefix();
    });
    
    
    // Calc stats/pagination
    $scope.pagination.totalItems = $scope.grid.items.length;
    $scope.pagination.totalPages = Math.ceil($scope.pagination.totalItems / $scope.pagination.itemsPerPage) ;
  }

  // Order an array to its default
  $scope.keys = function(obj){
    if (!obj) return [];
    var keys=Object.keys(obj);
    keys.pop(); // Remove $$hashKey
    return keys;
  }
    
}]);