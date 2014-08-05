flexyAdmin.controller('menuController', function($scope) {

  $scope.menu = [
    { href: "admin/test#/form/tbl_site/first", title: 'Site' },
    { href: "admin/test#/grid/tbl_menu", title: 'Pages' },
    { href: "admin/test#/grid/tbl_links", title: 'Links' }
  ];

});
