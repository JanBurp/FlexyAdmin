/*jshint -W083 */

/**
 * flexy-api-service $http test
 */
describe('flexy-api-service-http', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var service, setting, http, mock;
  
  beforeEach(inject(function(flexyApiService,flexySettingsService, flexyApiMock, $httpBackend ) {
    service  = flexyApiService;
    setting  = flexySettingsService;
    mock = flexyApiMock;
    http     = $httpBackend;
    
    // settings
    var api = setting.item('api_base_url');
    
    // Spies
    spyOn( service, 'get' ).andCallThrough();
    
    // Mocking responses
    angular.forEach(mock.tables(),function(table,key){
      var url = mock.api_get_table_url(table);
      // console.log('MOCK', 'GET', url);
      http.when( 'GET', url ).respond( mock.api_get_table_response(table) );
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
    
    // Roep alle get(table) met alle tables aan
    angular.forEach(mock.tables(),function(table,key){
      // Start zonder resultaat
      result=undefined;
      expect( result ).toBeUndefined();
      // Roep de API aan
      service.get( 'table', {'table':table} ).then(function(response){
        result=response;
      });
      http.flush(1);
      // TEST response
      expect( result ).toBeDefined();
      expect( result.success ).toBeDefined();
      expect( result.success ).toEqual(true);
      expect( result.args ).toBeDefined();
      expect( result.data ).toBeDefined();
    });
    
    // TEST Spies
    expect( service.get ).toHaveBeenCalled();
    expect( service.get.callCount ).toEqual( mock.tables().length );
  });
  
  
  afterEach(function(){
    http.verifyNoOutstandingExpectation();
    http.verifyNoOutstandingRequest();
  });


});
