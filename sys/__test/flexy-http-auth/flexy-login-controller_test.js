// UNIT TEST FOR flexy-login-controller.js
'use strict';

describe('flexy-login-controller', function(){
  beforeEach(module('flexyAdmin'));

  var ctrl, authMock;

  beforeEach(inject(function($controller){
    ctrl = $controller('flexyLoginController');
  }));

  it('flexy-login-controller: exists', function(){
    expect( ctrl ).toBeDefined();
  });
  
  it('flexy-login-controller: user defined', function() {
    expect( ctrl.user ).toEqual({
      username:'',
      password:'',
      email:''
    });
  });

  it('flexy-login-controller: askMail & mailSend defined', function() {
    expect( ctrl.askMail ).toBeDefined();
    expect( ctrl.mailSend ).toEqual(false);
  });
  
  
});