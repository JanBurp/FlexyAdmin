/*jshint -W083 */

/**
 * flexy-menu-service test
 */
describe('flexy-menu-service-http', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var service, setting, http, mock;
  
  // count all $http calls
  var callCount = 0;
  
  beforeEach(inject(function(flexyMenuService,flexySettingsService, flexyApiMock, $httpBackend ) {
    service  = flexyMenuService;
    setting  = flexySettingsService;
    mock     = flexyApiMock;
    http     = $httpBackend;
    
    // MOCK: GET_ADMIN_NAV
    var url   = mock.api_url( 'get_admin_nav' );
    http.when( 'GET', url ).respond( mock.api_get_admin_nav_response() );
  }));
  

  /**
   * Only test when mocking is on!
   */
  it('flexy-menu-service: only test if mocking is on!', function() {
    expect( setting.has_item('use_mock') ).toEqual(true);
    expect( setting.item( 'use_mock' ) ).toEqual(true);
  });



  /**
   * Test get_admin_nav
   */
  it('flexy-menu-service', function() {
    
    // At start, the menu should be undefined
    var menu = service.get();
    expect( menu ).not.toBeDefined();
    
    // Load the menu
    service.load();
    http.flush(1);
    // Get the menu
    menu = service.get();
    expect( menu ).toBeDefined();
    expect( menu.header ).toBeDefined();
    expect( menu.sidebar ).toBeDefined();
    expect( menu.footer ).toBeDefined();
    
  });

  
  afterEach(function(){
    http.verifyNoOutstandingExpectation();
    http.verifyNoOutstandingRequest();
  });


});
