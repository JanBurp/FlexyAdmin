flexyAdmin.controller('GridController', function($scope,$routeParams) {
  
  $scope.table = $routeParams.table;
  
  $scope.grid = {
    items: [
      { str_title: "Start", txt_text: 'Lorem ipsum dolor sit amet.' },
      { str_title: "Pagina 1", txt_text: 'Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.' },
      { str_title: "Pagina 2", txt_text: 'Sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.' },
      { str_title: "Laatste Pagina", txt_text: 'Tempor invidunt ut labore et dolore magna aliquyam erat.' },
    ]
  };
  
  /**
   * Set ordering of columns in grid
   */
  $scope.order='';
  $scope.reverse=false;
  $scope.setOrder = function(header) {
    if ($scope.order==header) {
      $scope.reverse=!$scope.reverse;
    }
    else {
      $scope.order=header;
      $scope.reverse=false;
    }
  };

  /**
   * Return headers of grid
   */
  $scope.headers = function() {
    var headers=[];
    var row=angular.copy($scope.grid.items[0]);
    angular.forEach(row,function(value,key){
      headers.push(key);
    });
    return headers;
  };

  
});