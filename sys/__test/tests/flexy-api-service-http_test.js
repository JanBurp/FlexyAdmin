/*jshint -W083 */

/**
 * flexy-api-service $http test
 */
describe('flexy-api-service-http', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var service, setting, http, mock;
  
  // count all $http calls
  var callCount = 0;
  var randomArgs=[];
  
  beforeEach(inject(function(flexyApiService,flexySettingsService, flexyApiMock, $httpBackend ) {
    service  = flexyApiService;
    setting  = flexySettingsService;
    mock     = flexyApiMock;
    http     = $httpBackend;
    
    // settings
    var api   = setting.item('api_base_url');
    var url   = '';
    var args  = {};
    
    // Spies
    spyOn( service, 'get' ).andCallThrough();
    
    // MOCKS
    
    // GET_ADMIN_NAV
    url   = mock.api_url( 'get_admin_nav' );
    http.when( 'GET', url ).respond( mock.api_get_admin_nav_response() );
    
    // Multiple mocks for each table
    angular.forEach(mock.tables(),function(table,key){
      
      // random - ERROR
      args  = mock.api_random_args();
      args.settings = true;
      url   = mock.api_get_table_url( args );
      http.when( 'GET', url ).respond( mock.api_error_wrong_args( args ) );
      randomArgs.push(args);
      // console.log('MOCK:',url);

      // TABLE without config
      args  = {'table':table};
      url   = mock.api_get_table_url( args );
      http.when( 'GET', url ).respond( mock.api_get_data_response( args, 'table' ) );

      // TABLE with config
      args  = {'table':table,'settings':true};
      url   = mock.api_get_table_url( args );
      http.when( 'GET', url ).respond( mock.api_get_data_response( args, 'table' ) );
      // console.log('MOCK:',url);
      
      
    });
    
  }));
  

  /**
   * Only test when mocking is on!
   */
  it('flexy-api-service: only test if mocking is on!', function() {
    expect( setting.has_item('use_mock') ).toEqual(true);
    expect( setting.item( 'use_mock' ) ).toEqual(true);
  });



  /**
   * Test get_admin_nav
   */
  it('flexy-api-service: Test admin_nav', function() {
    var result = null;
    result = undefined;
    expect( result ).toBeUndefined();
    service.get( 'get_admin_nav' ).then(function(response){
      result=response;
    });
    http.flush(1);
    // callCount++;
    // console.log(result);
    expect( result ).toBeDefined();
    expect( result.success ).toBeDefined();
    expect( result.success ).toEqual(true);
    expect( result.args ).toBeDefined();
    expect( result.args ).toEqual({});
    expect( result.config ).not.toBeDefined();
    expect( result.data ).toBeDefined();
    expect( result.data.header ).toBeDefined();
    expect( result.data.sidebar ).toBeDefined();
    expect( result.data.footer ).toBeDefined();
    
  });




  /**
   * get(table)
   */
  it('flexy-api-service: testing get(table)', function() {
    var result;
    var args = {};

    // Roep alle get(table) met alle tables aan
    angular.forEach(mock.tables(),function(table,key){

      /**
       * Start zonder resultaat
       */
      result=undefined;
      expect( result ).toBeUndefined();

      /**
       * Test een foute API call
       */
      args = randomArgs.pop();
      service.get( 'table', args ).then(function(response){
        result=response;
      });
      http.flush(1);
      callCount++;
      // TEST ERROR response
      expect( result ).toBeDefined();
      expect( result.success ).toBeDefined();
      expect( result.success ).toEqual(false);
      expect( result.error ).toBeDefined();
      expect( result.error ).toEqual('WRONG ARGUMENTS');
      expect( result.args ).toBeDefined();
      expect( result.data ).not.toBeDefined();


      /**
       * Roep de API aan, ZONDER CONFIG vraag -> wordt toch meegenomen...?
       */
      service.get( 'table', {'table':table} ).then(function(response){
        result=response;
      });
      http.flush(1);
      callCount++;
      // TEST response zonder config
      expect( result ).toBeDefined();
      expect( result.success ).toBeDefined();
      expect( result.success ).toEqual(true);
      expect( result.args ).toBeDefined();
      expect( result.args ).toEqual({'table':table,'settings':true});
      expect( result.data ).toBeDefined();
      expect( result.data ).toEqual( mock.table(table) );
      // expect( result.settings ).not.toBeDefined();


      /**
       * Roep de API aan, MET CONFIG vraag
       */
      service.get( 'table', {'table':table,settings:true} ).then(function(response){
        result=response;
      });
      http.flush(1);
      callCount++;
      // TEST response met config
      expect( result ).toBeDefined();
      expect( result.success ).toBeDefined();
      expect( result.success ).toEqual(true);
      expect( result.args ).toBeDefined();
      expect( result.args.table ).toBeDefined();
      expect( result.args.table ).toEqual(table);
      expect( result.data ).toBeDefined();
      expect( result.data ).toEqual( mock.table(table) );
      expect( result.settings ).toBeDefined();
    });

    // TEST Spies
    expect( service.get ).toHaveBeenCalled();
    expect( service.get.callCount ).toEqual( callCount );
  });
  
  
  
  afterEach(function(){
    http.verifyNoOutstandingExpectation();
    http.verifyNoOutstandingRequest();
  });


});
