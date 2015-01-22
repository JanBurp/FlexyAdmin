/**
 * FrontEnd End2End test.
 * 
 * Installeer alle benodigdheden: Quickstart: https://angular.github.io/protractor/#/tutorial
 * 
 * Hoe start je een test:
 * - Open een Terminal en zorg ervoor dat je in de rootmap van de site bent
 * - Type: webdriver-manager start
 * - Open een nieuwe terminal window/tab, en zorg er weer voor dat je in de rootmap van de site bent
 * - Type: protractor conf.js
 */

browser.ignoreSynchronization = true; // do not test with Angular


describe('FlexyAdmin DEMO basic test', function() {

  beforeEach(function() {
    browser.get('http://localhost/FlexyAdmin/FlexyAdminDEMO/');
  });

  it('Homepage heeft een titel', function() {
    expect(browser.getTitle()).toEqual('FlexyAdmin Demo - Gelukt!');
  });

  it('Heeft alle standaard elementen', function() {
    expect(element(by.id('menu')).isPresent()).toBe(true);
    expect(element(by.css('#menu a.last')).isPresent()).toBe(true);
    expect(element(by.id('content')).isPresent()).toBe(true);
  });
  
  

});