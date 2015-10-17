/**
 * flexy-api-service basic (just the methods without $http)
 */
describe('flexy-api-service-basic', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var service, setting;
  
  beforeEach(inject(function(flexyApiService,flexySettingsService) {
    service = flexyApiService;
    setting = flexySettingsService;
  }));

  /**
   * Exists?
   */
  it('flexy-api-service: exists', function(){
    expect( service ).toBeDefined();
  });

  /**
   * has_settings
   */
  it('flexy-api-service: testing has_settings', function() {
    expect( service.has_settings('table') ).toEqual(false);
    expect( service.has_settings('path') ).toEqual(false);
    expect( service.has_settings('some_random_info') ).toEqual(false);
    //
    setting.set_item({'test':'_test'},['setting','table']);
    // expect( service.has_settings('table') ).toEqual(true);
  });

});
