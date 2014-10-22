'use strict';

flexyAdmin.controller('flexyLoginController', ['flexyAuthService','authService', function(flexyAuthService,authService){
  var self=this;
  
  self.user = {
    username:'',
    password:'',
    email:'',
  }
  
  self.askMail  = false;
  self.mailSend = false;
  
  
  /**
   * login
   */
  self.login = function(){
    flexyAuthService.login(self.user).then(
      function(success){
        self.user.username='';
        self.user.password='';
        self.user.email='';
        authService.loginConfirmed();
      },
      function(error) {
        authService.loginCancelled(error);
      }
    );
  },
  
  
  /**
   * Send new password
   */
  self.sendNewPassword = function(){
    flexyAuthService.send_password(self.user.email).then(
      function(success){
        self.mailSend = true;
      }
    );
  }
  
  
}]);