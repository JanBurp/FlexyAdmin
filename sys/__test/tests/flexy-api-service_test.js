describe('flexy-api-service', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var service, setting;
  
  beforeEach(inject(function(flexyApiService,flexySettingsService) {
    service = flexyApiService;
    setting = flexySettingsService;
    
    spyOn( service, 'get' ).andCallThrough();
  }));


  /**
   * Exists?
   */
  it('flexy-api-service: exists', function(){
    expect( service ).toBeDefined();
  });


  /**
   * has_cfg
   */
  it('flexy-api-service: testing has_cfg', function() {
    expect( service.has_cfg('table_info') ).toEqual(false);
    expect( service.has_cfg('field_info') ).toEqual(false);
    expect( service.has_cfg('some_random_info') ).toEqual(false);
    //
    setting.set_item({'test':'_test'},['cfg','table_info']);
    expect( service.has_cfg('table_info') ).toEqual(true);
  });


  /**
   * needs_these_cfg
   */
  it('flexy-api-service: testing needs_these_cfg', function() {
    expect( service.needs_these_cfg( ['table_info']) ).toEqual( ['table_info'] );
    expect( service.needs_these_cfg( ['table_info','field_info']) ).toEqual( ['table_info','field_info'] );
    //
    setting.set_item({'test':'_test'},['cfg','table_info']);
    expect( service.needs_these_cfg( ['table_info']) ).toEqual( [] );
    expect( service.needs_these_cfg( ['table_info','field_info']) ).toEqual( ['field_info'] );
  });


  /**
   * get
   */
  it('flexy-api-service: testing get', function() {
    service.get('table',{'table':'tbl_site'},['table_info','field_info']);
    var result=service.get('table',{'table':'tbl_site'},['table_info','field_info']);
    expect( service.get ).toHaveBeenCalled();
    expect( service.get.callCount ).toEqual(2);
    
    // En nu de $http testen...
    
    // expect( result.data ).toBeDefined();
    // expect( result.args ).toBeDefined();
    // expect( result.data ).toEqual( false );
  });


  


});