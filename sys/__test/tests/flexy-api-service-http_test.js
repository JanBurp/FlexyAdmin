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
    
    // Mocks
    for (var i = 0; i < mockdata.length; i++) {
      var url=api + mockdata[i].api +'?'+ jdb.serializeJSON(mockdata[i].params);
      console.log('MOCK', mockdata[i].type, url);
      mock.when( mockdata[i].type, url ).respond( mockdata[i].respond );
    }
    
    
  }));



  /**
   * get
   */
  it('flexy-api-service: testing get', function() {
    var result=[];
    
    // Roep alle $http aan
    for (var i = 0; i < mockdata.length; i++) {
      // Start zonder resultaat
      result[i]=undefined;
      expect( result[i] ).toBeUndefined();
      
      // Roep de API aan, die maakt resultaat
      service.get( mockdata[i].api, mockdata[i].params ).then(function(response){
        result[i]=response;
      });

      mock.flush(1);

      // Results
      expect( result[i] ).toBeDefined();
      expect( result[i].success ).toBeDefined();
      expect( result[i].success ).toEqual(true);
      expect( result[i].args ).toBeDefined();
      expect( result[i].data ).toBeDefined();
    }
    
    // Spies
    expect( service.get ).toHaveBeenCalled();
    expect( service.get.callCount ).toEqual( mockdata.length );

  });
  
  
  
  
  
  afterEach(function(){
    mock.verifyNoOutstandingExpectation();
    mock.verifyNoOutstandingRequest();
  });


});
