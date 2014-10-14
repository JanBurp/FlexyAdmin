// UNIT TEST FOR flexy-admin-app.js
'use strict';

describe('flexy-admin-app', function(){
  beforeEach(module('flexyAdmin'));

  it('flexy-admin-app: flexyAdmin - bestaat', function() {
    expect( flexyAdmin ).toBeDefined();
  });
  it('flexy-admin-app: flexyAdmin.config - bestaat', function() {
    expect( flexyAdmin.config ).toBeDefined();
  });
  it('flexy-admin-app: flexyAdmin.directive - bestaat', function() {
    expect( flexyAdmin.directive ).toBeDefined();
  });

  
});