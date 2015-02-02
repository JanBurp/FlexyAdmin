// UNIT TEST FOR flexy-admin-app.js

describe('flexy-admin-app', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  /**
   * Test of flexyAdmin bestaat
   */
  it('flexy-admin-app: flexyAdmin - bestaat', function() {
    expect( flexyAdmin ).toBeDefined();
  });
  it('flexy-admin-app: flexyAdmin.constant - bestaat', function() {
    expect( flexyAdmin.constant ).toBeDefined();
  });
  it('flexy-admin-app: flexyAdmin.config - bestaat', function() {
    expect( flexyAdmin.config ).toBeDefined();
  });
  it('flexy-admin-app: flexyAdmin.directive - bestaat', function() {
    expect( flexyAdmin.directive ).toBeDefined();
  });
  it('flexy-admin-app: flexyAdmin.controller - bestaat', function() {
    expect( flexyAdmin.controller ).toBeDefined();
  });
  
  /**
   * Test of constanten bestaan
   */
  it('flexy-admin-app: flexyAdmin.constant - bestaat', function() {
    expect( flexyAdmin.constant ).toBeDefined();
  });
  
  
  
  

  
});