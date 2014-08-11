flexyAdmin.controller('menuController', function($scope) {
  
  $scope.root = "admin/_test";

  $scope.menu = [
    { href: $scope.root+"#/form/tbl_site/first", title: 'Site' },
    { href: $scope.root+"#/grid/tbl_menu", title: 'Pages' },
    { href: $scope.root+"#/grid/tbl_links", title: 'Links' }
  ];

});
