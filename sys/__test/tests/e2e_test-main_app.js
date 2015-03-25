// E2E test

describe('Main E2E test', function(){
  'use strict';
  
  it('LOGIN dialog is shown', function() {
    browser.get('/');
    expect( element(by.css('#login')).isDisplayed() ).toBe(true);
    
    
  });

});