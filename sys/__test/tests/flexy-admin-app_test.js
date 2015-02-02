// UNIT TEST FOR flexy-admin-app.js

describe('flexy-admin-app', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));
  
  /**
   * Test of flexyAdmin bestaat
   */
  it('bestaat', function() {
    expect( flexyAdmin ).toBeDefined();
  });
  it("niet null", function() {
    expect( flexyAdmin ).not.toEqual(null);
  });
  
  it('.constant - bestaat', function() {
    expect( flexyAdmin.constant ).toBeDefined();
  });
  
  it('.config - bestaat', function() {
    expect( flexyAdmin.config ).toBeDefined();
  });
  it('.directive - bestaat', function() {
    expect( flexyAdmin.directive ).toBeDefined();
  });
  it('.controller - bestaat', function() {
    expect( flexyAdmin.controller ).toBeDefined();
  });


});