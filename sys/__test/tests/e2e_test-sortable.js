describe('E2E test for sortable', function(){
  'use strict';

  var table = element(by.css('#content .flexy-grid .panel-content table'));
  var rows  = element.all(by.css('tbody tr',table));


  
  it('Show Menu', function() {
    browser.driver.manage().window().maximize();
    browser.get('#/grid/tbl_menu');
    expect( element(by.css('#content')).isDisplayed() ).toBe(true);
    expect( element(by.css('#content .flexy-grid')).isDisplayed() ).toBe(true);

    expect( table.isDisplayed() ).toBe(true);
    expect( rows.count() ).toEqual(5);
    
    // Verplaats eerste item naar eind
    var first = element.all(by.css('tbody tr:first',table));
    
    var yourOffset = {x:5,y:5};
    browser.actions()
        .mouseMove(first,yourOffset)
        // .mouseDown()
        // .mouseMove(first,{x:0,y:0}) // Initial move to trigger drag start
        // .mouseMove(first,{x:0,y:50}) // [] optional
        // .mouseUp()
        .perform();
    
    // var pos = table.getPosition(first);
    
    // console.log(pos);
    
    
    
    
    
    
    
  });


});