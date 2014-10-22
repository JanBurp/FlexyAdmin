// UNIT TEST FOR flexy-login-controller.js
'use strict';

describe('flexy-auth-service', function(){
  beforeEach(module('flexyAdmin'));

  var mockHttp;

  beforeEach(inject(function($controller,$httpBackend){

    mockHttp = $httpBackend;
    mockHttp.expectGET('auth/check').respond({data:{}});
    
  }));


  // it('flexy-auth-service: check', function(){
  //
  // });


  //
  // it('flexy-login-controller: exists', function(){
  //   expect( ctrl ).toBeDefined();
  // });
  //
  // it('flexy-login-controller: user defined', function() {
  //   expect( ctrl.user ).toEqual({
  //     username:'',
  //     password:'',
  //     email:''
  //   });
  // });
  //
  // it('flexy-login-controller: askMail & mailSend defined', function() {
  //   expect( ctrl.askMail ).toBeDefined();
  //   expect( ctrl.mailSend ).toEqual(false);
  // });
  
  
  afterEach(function(){
    mockHttp.verifyNoOutstandingExpectations();
    mockHttp.verifyNoOutstandingRequests();
  });
  
});