flexyAdmin.controller('GridController', ['$scope','$routeParams','$http', '$log', function($scope,$routeParams,$http,$log) {
  
  $scope.table = $routeParams.table;
  
  $scope.grid = {
    items: []
  };
  
  $http.post('__api/get_table',{'table':$scope.table}).success(function(result){
    $scope.grid.items=result.data.items;
  }).error(function(data){
    $log.log('AJAX error -> Grid');
  });
  
  
  
  
}]);