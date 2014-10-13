'use strict';

flexyAdmin.controller('flexyLoginController', ['flexyAuthService','authService', function(flexyAuthService,authService){
  var self=this;
  self.user = {
    username:'',
    password:'',
  }
  
  self.login = function(){
    flexyAuthService.login(self.user).then(
      function(success){
        // first reset form
        self.user.username='';
        self.user.password='';
        authService.loginConfirmed();
      },
      function(error) {
        authService.loginCancelled(error);
      }
    );
  }
  
}]);