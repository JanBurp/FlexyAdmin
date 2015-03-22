/*jshint -W083 */

/**
 * flexy-api-service $http test
 */
describe('flexy-api-service-http', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var service, setting, mock, mockdata;
  
  beforeEach(inject(function(flexyApiService,flexySettingsService, flexyApiMock, $httpBackend ) {
    service  = flexyApiService;
    setting  = flexySettingsService;
    mockdata = flexyApiMock;
    mock     = $httpBackend;
    
    // settings
    var api = setting.item('api_base_url');
    
    // Spies
    spyOn( service, 'get' ).andCallThrough();
    
    // Good responses Mocks
    angular.forEach(mockdata.tables(),function(table,key){
      var url = mockdata.api_get_table_url(table);
      // console.log('MOCK', 'GET', url);
      mock.when( 'GET', url ).respond( mockdata.api_get_table_response(table) );
    });
    
  }));



  /**
   * get(table)
   */
  it('flexy-api-service: testing get(table)', function() {
    var result;
    
    // Roep alle get(table) met alle tables aan
    angular.forEach(mockdata.tables(),function(table,key){
      // Start zonder resultaat
      result=undefined;
      expect( result ).toBeUndefined();
      // Roep de API aan
      service.get( 'table', {'table':table} ).then(function(response){
        result=response;
      });
      mock.flush(1);
      // TEST response
      expect( result ).toBeDefined();
      expect( result.success ).toBeDefined();
      expect( result.success ).toEqual(true);
      expect( result.args ).toBeDefined();
      expect( result.data ).toBeDefined();
    });
    
    // TEST Spies
    expect( service.get ).toHaveBeenCalled();
    expect( service.get.callCount ).toEqual( mockdata.tables().length );
  });
  
  
  afterEach(function(){
    mock.verifyNoOutstandingExpectation();
    mock.verifyNoOutstandingRequest();
  });


});
