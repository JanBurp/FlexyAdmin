// E2E test

describe('Main E2E test', function(){
  'use strict';
  
  it('App is started - not logged in', function() {
    browser.driver.manage().window().maximize();
    browser.get('#/home');
    expect( element(by.css('#login')).isDisplayed() ).toBe(true);
    expect( element(by.css('#container')).isDisplayed() ).toBe(false);
  });

  it('LOGIN', function() {
    browser.get('#home');
    var loginButtonExists = by.id('login-button');
    browser.driver.wait(function() {
      return browser.driver.isElementPresent(loginButtonExists);
    }, 5000);
    browser.driver.findElement(by.id('login-username')).sendKeys( 'admin' );
    browser.driver.findElement(by.id('login-password')).sendKeys( 'admin' );
    element(by.id('login-button')).click();
    expect( element(by.css('#container')).isDisplayed() ).toBe(true);
  });

  it('CHECK HOMESCREEN ELEMENTS', function() {
    expect( element(by.css('#container')).isDisplayed() ).toBe(true);
    expect( element(by.css('#content')).isDisplayed() ).toBe(true);
  });

  it('LOGOUT', function() {
    browser.get('#logout');
    expect( element(by.css('#login')).isDisplayed() ).toBe(true);
    expect( element(by.css('#container')).isDisplayed() ).toBe(false);
  });





});