/*jshint -W083 */

/**
 * flexy-grid-service test
 */
describe('flexy-grid-service-http', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var service, setting, http, mock;
  
  // count all $http calls
  var callCount = 0;
  
  beforeEach(inject(function(flexyGridService,flexySettingsService, flexyApiMock, $httpBackend ) {
    service  = flexyGridService;
    setting  = flexySettingsService;
    mock     = flexyApiMock;
    http     = $httpBackend;
    
    // MOCK: GET_ADMIN_NAV
    var url   = mock.api_url( 'table', {table:'tbl_site', txt_as_abstract:true, 'config':['table_info','field_info']} );
    http.when( 'GET', url ).respond( mock.api_get_data_response({table:'tbl_site'}, 'table') );
    // console.log('MOCK:',url,mock.api_get_data_response({table:'tbl_site'}, 'table'));
  }));
  

  /**
   * Only test when mocking is on!
   */
  it('flexy-grid-service: only test if mocking is on!', function() {
    expect( setting.has_item('use_mock') ).toEqual(true);
    expect( setting.item( 'use_mock' ) ).toEqual(true);
  });


  /**
   * Test get_admin_nav
   */
  it('flexy-grid-service', function() {
    
    // At start, the grid data should be undefined
    var data = service.get_raw_data();
    expect( data ).not.toBeDefined();
    
    // Load the menu
    service.load('tbl_site');
    http.flush(1);
    // Get the data data
    data = service.get_raw_data('tbl_site');
    expect( data ).toBeDefined();
    expect( data ).toEqual( mock.table('tbl_site') );
    // Get the grid info
    var info = service.get_info('tbl_site');
    expect( info ).toBeDefined();
    expect( info ).toEqual( {rows:1,total_rows:1,table_rows:1} );
    // Get the grid
    var grid = service.get_grid_data('tbl_site');
    expect( grid ).toBeDefined();
    // expect( grid ).toEqual( data );
    
    
  });

  
  afterEach(function(){
    http.verifyNoOutstandingExpectation();
    http.verifyNoOutstandingRequest();
  });


});
