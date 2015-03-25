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
    
    // Creating mocked api-service responses
    angular.forEach(mock.tables(),function(table,key){
      
      // without config
      args  = {'table':table};
      url   = mock.api_get_table_url( args );
      http.when( 'GET', url ).respond( mock.api_get_data_response( args) );
      
      // with config
      args  = {'table':table,'config':['table_info','field_info']};
      url   = mock.api_get_table_url( args );
      http.when( 'GET', url ).respond( mock.api_get_data_response( args ) );
      
      // random - error
      args  = mock.api_random_args();
      url   = mock.api_get_table_url( args );
      http.when( 'GET', url ).respond( mock.api_error_wrong_args( args ) );
      randomArgs.push(args);
      
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
       * Roep de API aan, ZONDER CONFIG vraag
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
      expect( result.args ).toEqual({'table':table});
      expect( result.data ).toBeDefined();
      expect( result.data ).toEqual( mock.table(table) );
      expect( result.config ).not.toBeDefined();


      /**
       * Roep de API aan, MET CONFIG vraag
       */
      service.get( 'table', {'table':table}, ['table_info','field_info'] ).then(function(response){
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
      expect( result.config ).toBeDefined();
      
      
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
