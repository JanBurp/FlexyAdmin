describe('E2E test for sortable', function(){
  'use strict';

  var table = element(by.css('#content .flexy-grid .panel-content table'));
  var rows  = element.all(by.css('tbody tr',table));


   beforeEach(function(){
     browser.driver.manage().window().maximize();
     // Login
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
   
   afterEach(function(){
     // Logout
     browser.get('#logout');
   });

  
  it('Show Menu', function() {

    browser.get('#/grid/tbl_menu');
    expect( element(by.css('#content')).isDisplayed() ).toBe(true);
    expect( element(by.css('#content .flexy-grid')).isDisplayed() ).toBe(true);

    expect( table.isDisplayed() ).toBe(true);
    expect( rows.count() ).toEqual(5);
    
    // Verplaats eerste item naar eind
    // var first = element.all(by.css('tbody tr:first',table));
    //
    // var yourOffset = {x:5,y:5};
    // browser.actions()
        // .mouseMove(first,yourOffset)
        // .mouseDown()
        // .mouseMove(first,{x:0,y:0}) // Initial move to trigger drag start
        // .mouseMove(first,{x:0,y:50}) // [] optional
        // .mouseUp()
        // .perform();
    
    // var pos = table.getPosition(first);
    
    // console.log(pos);
    
    
    
    
    
    
    
  });


});