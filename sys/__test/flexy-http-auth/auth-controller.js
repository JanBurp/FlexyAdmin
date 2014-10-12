flexyAdmin.controller('authCtrl', ['authService','$location', function(authService,$location){
  var self=this;
  self.user = {
    username:'',
    password:'',
  }
  
  self.login = function(){
    authService.login(self.user).then(
    function(success){
      $location.path('/');
    },
    function(error) {
      self.errorMessage = 'Login is incorrect';
    });
  }
  
}]);