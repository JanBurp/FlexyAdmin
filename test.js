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

browser.ignoreSynchronization = true; // do not test for Angular

describe('FlexyAdmin DEMO', function() {

  it('Homepage heeft een titel', function() {
    browser.get('http://localhost/FlexyAdmin/FlexyAdminDEMO/');
    expect(browser.getTitle()).toEqual('FlexyAdmin Demo - Gelukt!');
  });

  it('Homepage heeft alle elementen', function() {
    browser.get('http://localhost/FlexyAdmin/FlexyAdminDEMO/');
    // element(by.id('container'))....;
  });


});