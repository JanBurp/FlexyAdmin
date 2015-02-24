// UNIT TEST FOR flexy-settings-service.js

describe('flexy-settings-service', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  var service;

  beforeEach(inject(function(flexySettingsService){
    service = flexySettingsService;
  }));

  it('flexy-settings-service: exists', function(){
    expect( service ).toBeDefined();
  });

  /**
   * Test has_item
   */
  it('flexy-settings-service: testing has_item', function() {
    expect( service.has_item('base_url') ).toEqual(true);
    expect( service.has_item('cfg','table_info') ).toEqual(true);
    expect( service.has_item(['cfg','field_info']) ).toEqual(true);
    expect( service.has_item('this_paremeter_certainly_does_not_exists') ).toEqual(false);
    expect( service.has_item('this_paremeter_certainly_does_not_exists','test') ).toEqual(false);
    expect( service.has_item('cfg','this_paremeter_certainly_does_not_exists') ).toEqual(false);
  });

  /**
   * Test set_item and delete_item
   */
  it('flexy-settings-service: testing set_item', function() {
    var tests = [
      {'value':'TEST','path':'__test'},
      {'value':'TEST2','path':['__test2','__test3']},
      {'value':'TEST3','path':['__test2','__test3']},
    ];
    for (var i = 0; i < tests.length; i++) {
      var value=tests[i].value;
      var path=tests[i].path;
      // set
      expect( service.set_item( value, path ) ).toEqual(true);
      // test
      expect( service.item( path ) ).toEqual( value );
      // delete
      expect( service.delete_item( path ) ).toEqual( value );
      expect( service.item( path ) ).toBeUndefined();
    }
  });
  


  /**
   * Standard settings
   */
  it('flexy-settings-service: standard settings should be defined', function() {
    expect( service.item('base_url') ).toBeDefined();
    expect( service.has_item('base_url') ).toEqual(true);
    expect( service.has_item('this_paremeter_certainly_does_not_exists') ).toEqual(false);
    expect( service.item('base_url') ).toEqual('admin/__test');
    expect( service.item('api_base_url') ).toEqual('_api/');
    expect( service.item('sys_folder') ).toEqual('sys/__test/');
    expect( service.item('log_prefix') ).toEqual('FA ');
  });
  
  
  /**
   * cfg
   */
  it('flexy-settings-service: cfg should be defined but empty', function() {
    expect( service.item('cfg') ).toBeDefined();
    expect( service.item('cfg','table_info') ).toBeDefined();
    expect( service.item(['cfg','field_info']) ).toBeDefined();
    expect( service.item('cfg') ).toEqual({
      table_info : {},
      field_info : {},
    });
    expect( service.item('cfg','table_info') ).toEqual({});
    expect( service.item(['cfg','field_info']) ).toEqual({});
  });
  
  
  /**
   * form_field_types
   */
  it('flexy-settings-service: form_field_types be defined and filled', function() {
    expect( service.item('form_field_types') ).toBeDefined();
    expect( service.item('form_field_types','[default]') ).toBeDefined();
    expect( service.item('form_field_types','[default]') ).toEqual({
      'data-type'   : 'string',
      'format'      : 'string',
      'type'        : 'string',
      'readonly'    : false,
    });
    expect( service.item('form_field_types','[id]') ).toBeDefined();
    expect( service.item('form_field_types','[id]') ).toEqual({
        'readonly'    : true,
        'type'        : 'hidden',
      });
    expect( service.item('form_field_types','[order]') ).toBeDefined();
    expect( service.item('form_field_types','[order]') ).toEqual({
        'readonly'    : true,
        'type'        : 'hidden',
      });
    expect( service.item('form_field_types','[self_parent]') ).toBeDefined();
    expect( service.item('form_field_types','[uri]') ).toBeDefined();
    expect( service.item('form_field_types','[uri]','readonly') ).toEqual(true);
  });
  
  
  
  
  
  


});