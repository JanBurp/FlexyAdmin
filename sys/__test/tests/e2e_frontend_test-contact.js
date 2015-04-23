// E2E test

describe('Main E2E frontend', function(){
  'use strict';
  
  // Wacht niet op Angular...
  beforeEach(function() { browser.ignoreSynchronization = true; });
  // En wacht wel weer, zodat andere test wel werken
  afterEach(function() {  browser.ignoreSynchronization = false;});
  

  it('Contact form Zichtbaar', function() {
    browser.driver.manage().window().maximize();
    browser.get('contact');
    expect( element(by.css('#container')).isDisplayed() ).toBe(true);
    expect( element(by.css('#header')).isDisplayed() ).toBe(true);
    expect( element(by.css('#menu')).isDisplayed() ).toBe(true);
    expect( element(by.css('#content')).isDisplayed() ).toBe(true);
    expect( element(by.css('#footer')).isDisplayed() ).toBe(true);

    expect( element(by.css('form#contact')).isDisplayed() ).toBe(true);
  });
  
  it('Contact form Requered velden -> Geef foutmelding', function() {
    browser.driver.manage().window().maximize();
    browser.get('contact');
    expect( element(by.css('form#contact')).isDisplayed() ).toBe(true);
    
    // Vul formulier niet helemaal compleet
    browser.driver.findElement(by.id('str_name')).sendKeys( 'E2E Auto-Tester' );
    browser.driver.findElement(by.id('email_email')).sendKeys( 'test@flexyadmin.com' );
    element(by.css('input.button')).click();
    // Wacht totdat formuluer weer terugkomt met foutmeldingen
    var errorExists = by.css('span.error');
    browser.driver.wait(function() {
      return browser.driver.isElementPresent(errorExists);
    }, 5000);
    // Is foutmelding zichtbaar?
    expect( element(by.css('span.error')).isDisplayed() ).toBe(true);
  });

  it('Contact -> Stuur email', function() {
    browser.driver.manage().window().maximize();
    browser.get('contact');
    expect( element(by.css('form#contact')).isDisplayed() ).toBe(true);
    // Vul formulier compleet
    browser.driver.findElement(by.id('str_name')).sendKeys( 'E2E Auto-Tester' );
    browser.driver.findElement(by.id('email_email')).sendKeys( 'test@flexyadmin.com' );
    browser.driver.findElement(by.id('txt_text')).sendKeys( 'E2E Test @ ' + Date.now() );
    
    element(by.css('input.button')).click();
  });




});
