browser.ignoreSynchronization = true; // do not test for Angular

describe('FlexyAdmin DEMO', function() {

  it('Homepage heeft een titel', function() {
    browser.get('http://localhost/FlexyAdmin/FlexyAdminDEMO/');
    expect(browser.getTitle()).toEqual('FlexyAdmin Demo - Gelukt!');
  });

  it('Homepage heeft alle elementen', function() {
    browser.get('http://localhost/FlexyAdmin/FlexyAdminDEMO/');
    element(by.id('container'))....;
  });


});