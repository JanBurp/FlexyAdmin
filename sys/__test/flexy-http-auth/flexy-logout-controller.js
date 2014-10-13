flexyAdmin.controller('flexyLogoutController', ['flexyAuthService','authService','$location','$window', function(flexyAuthService,authService,$location,$window){
  flexyAuthService.logout().then(
    function(succes) {
      authService.loginCancelled();
      $location.path('/');
      flexyAuthService.check();
    }
  );
}]);