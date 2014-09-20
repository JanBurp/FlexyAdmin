flexyAdmin.controller('GridController', ['$scope','$routeParams','$http', '$log', function($scope,$routeParams,$http,$log) {
  
  $scope.table = $routeParams.table;
  
  $log.log($scope.table);
  
  $scope.grid = {
    items: [
      // { str_title: "Start", txt_text: 'Lorem ipsum dolor sit amet.' },
      // { str_title: "Pagina 1", txt_text: 'Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.' },
      // { str_title: "Pagina 2", txt_text: 'Sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.' },
      // { str_title: "Laatste Pagina", txt_text: 'Tempor invidunt ut labore et dolore magna aliquyam erat.' },
    ]
  };
  
  $http.get('__api/get_table?_ajax=1&table='+$scope.table,{cache:true}).success(function(result){
    $log.log(result);
    $scope.grid.items=result.data.items;
  }).error(function(data){
    $log.log('AJAX error -> Grid');
  });
  
  
  
  
}]);