/*jshint -W083 */

/**
 * flexy-grid-service test
 */
describe('flexy-grid-service-http', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var service, settings, http, mock;
  
  // count all $http calls
  var callCount = 0;
  
  beforeEach(inject(function(flexyGridService,flexySettingsService, flexyApiMock, $httpBackend ) {
    service  = flexyGridService;
    settings = flexySettingsService;
    mock     = flexyApiMock;
    http     = $httpBackend;
    
    // MOCK
    var args  = { 'as_grid':true, limit:20, offset:false, table:'tbl_menu' };
    var url   = mock.api_url( 'table', { 'as_grid':true,  limit:20, offset:false, settings:true, table:'tbl_menu' } );
    http.when( 'GET', url ).respond( mock.api_get_data_response(args, 'table') );
    // console.log('MOCK:',url,mock.api_get_data_response({table:'tbl_menu'}, 'table'));
  }));
  

  /**
   * Only test when mocking is on!
   */
  it('flexy-grid-service: only test if mocking is on!', function() {
    expect( settings.has_item('use_mock') ).toEqual(true);
    expect( settings.item( 'use_mock' ) ).toEqual(true);
  });




  /**
   * Test get_admin_nav
   */
  it('flexy-grid-service', function() {
    
    // At start, the grid data should be undefined
    var data = service.get_grid_data('tbl_menu');
    expect( data ).not.toBeDefined();
    
    // Load the menu
    service.load('tbl_menu');
    http.flush(1);

    // Get the data data
    data = service.get_grid_data('tbl_menu');
    expect( data ).toBeDefined();
    //
    // settings in Settings?
    expect( settings.has_item('settings') ).toEqual( true);

    // Get the grid info
    var info = service.get_info('tbl_menu');
    expect( info ).toBeDefined();
    expect( info ).toEqual( { rows:data.length, total_rows:data.length, table_rows:data.length, num_pages:1, limit:20} );

    // Get the grid
    var grid = service.get_grid_data('tbl_menu');
    // Test the processed grid data
    expect( grid ).toBeDefined();
    expect( grid.length ).toEqual( data.length );

    // expect( grid[0]._info ).toEqual( {level: 0, is_child: false, has_children: false} );
    // expect( grid[1]._info ).toEqual( {level: 0, is_child: false, has_children: true} );
    // expect( grid[2]._info ).toEqual( {level: 1, is_child: true,  has_children: true} );
    // expect( grid[3]._info ).toEqual( {level: 2, is_child: true,  has_children: false} );
    // expect( grid[4]._info ).toEqual( {level: 1, is_child: true,  has_children: false} );
    // expect( grid[5]._info ).toEqual( {level: 0, is_child: false, has_children: false} );
    // expect( grid[6]._info ).toEqual( {level: 0, is_child: false, has_children: false} );
    
    angular.forEach( grid, function(item,id) {
      console.log('ITEM',item.id,item.self_parent,item._info,item.uri);
    });
    
  });

  
  afterEach(function(){
    http.verifyNoOutstandingExpectation();
    http.verifyNoOutstandingRequest();
  });


});
