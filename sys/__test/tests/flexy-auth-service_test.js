// UNIT TEST FOR flexy-login-controller.js

describe('flexy-auth-service', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var mockHttp;

  beforeEach(inject(function($controller,$httpBackend){
    mockHttp = $httpBackend;
    mockHttp.expectGET('auth/check').respond({data:{}});
  }));


  // it('flexy-auth-service: check', function(){
  //
  // });

  
  afterEach(function(){
    mockHttp.verifyNoOutstandingExpectations();
    mockHttp.verifyNoOutstandingRequests();
  });
  
});