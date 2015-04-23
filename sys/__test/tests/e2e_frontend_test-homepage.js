// E2E test

describe('Main E2E frontend', function(){
  'use strict';
  
  // Wacht niet op Angular...
  beforeEach(function() { browser.ignoreSynchronization = true; });
  // En wacht wel weer, zodat andere test wel werken
  afterEach(function() {  browser.ignoreSynchronization = false;});
  
  it('Homepage', function() {
    browser.driver.manage().window().maximize();
    browser.get('');
    expect( element(by.css('#container')).isDisplayed() ).toBe(true);
    expect( element(by.css('#header')).isDisplayed() ).toBe(true);
    expect( element(by.css('#menu')).isDisplayed() ).toBe(true);
    expect( element(by.css('#content')).isDisplayed() ).toBe(true);
    expect( element(by.css('#footer')).isDisplayed() ).toBe(true);
  });

});