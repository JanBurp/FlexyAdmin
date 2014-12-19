/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 * $HeadURL$ 
 */


'use strict';

flexyAdmin.controller('flexyLogoutController', ['flexyAuthService','authService','$location','$window', function(flexyAuthService,authService,$location,$window){
  flexyAuthService.logout().then(
    function(succes) {
      authService.loginCancelled();
      $location.path('/');
      flexyAuthService.check();
    }
  );
}]);